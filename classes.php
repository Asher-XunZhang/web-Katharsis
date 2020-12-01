<?php
include 'top.php';

$stdId = "";
$regClaIds = array();
// Sanatize funcion from the text
function getGData($field){
    if (!isset($_GET[$field])){
        $data="";
    } else {
        $data = trim($_GET[$field]);
        $data = htmlspecialchars($data);
    }
    return $data;
}

function getPData($field){
    if (!isset($_POST[$field])){
        $data='';
    }elseif(is_array($_POST[$field])){
        $data = array();
        if (!empty($_POST[$field])){
            foreach ($_POST[$field] as $f){
                array_push($data, htmlspecialchars(trim($f)));
            }
        }
    }else{
        $data = trim($_POST[$field]);
        $data = htmlspecialchars($data);
    }

    return $data;
}


$getStuInfo="SELECT `fldFirstName`,`fldLastName`,`pmkNetId` FROM `tblStudents` ORDER BY `fldFirstName` ASC";
if ($thisDatabaseReader->querySecurityOk($getStuInfo,0,1,0,0,0)) {
    $thisDatabaseReader->sanitizeQuery($getStuInfo);
    $stuInfos = $thisDatabaseReader->select($getStuInfo, '');
}
$stuName=array();
foreach ($stuInfos as $stuInfo){
    array_push($stuName, $stuInfo["pmkNetId"]);
}

//Server side Sanitization
$subj = getGData("subj");
$cheClaSql = "SELECT * FROM `tblEnrollments` WHERE ` Subj` = ?";
if ($thisDatabaseReader->querySecurityOk($cheClaSql)) {
    $thisDatabaseReader->sanitizeQuery($cheClaSql);
    $classInfos = $thisDatabaseReader->select($cheClaSql, array($subj));
}

$dataIsGood = false;

?>
<main>
    <article>
        <h2><?php print $subj?> classes for 2020 Fall</h2>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == 'POST') {
            // we only save the data if it is good so we need to make a flag
            // notice if the data fails a validation check below I set this
            // flag to false so we dont save the invalid data
            $dataIsGood = True;

            //Server side Sanitization
            $stdId = getPData("stdId");
            $regClaIds = getPData("regClaIds");

            //Server side Validation
            if(!isset($stdId)||empty($stdId)){
                $dataIsGood = false;
                if (!in_array($stdId, $stuName)) {
                    print '<p class="mistake"><strong>Please choose a student.</strong></p>';
                }
            }elseif(empty($regClaIds)){
                $dataIsGood = false;
                print '<p class="remind"><strong>No class has been chosen.</strong></p>';
            }

            $regFInfos = array();
            $regSInfos = array();
            $regRInfos = array();
            // Check if class is repeat
            $regedClas = array();
            $stuClaSql = "SELECT * FROM `tblStudentEnrollments` WHERE `pfkStudentNetId` = ?";
            if ($thisDatabaseReader->querySecurityOk($stuClaSql)) {
                $thisDatabaseReader->sanitizeQuery($stuClaSql);
                $stuClaInfos = $thisDatabaseReader->select($stuClaSql, array($stdId));
            }
            foreach ($stuClaInfos as $stuClaInfo){
                foreach ($classInfos as $classInfo) {
                    if ($classInfo["pmkEnrollmentId"]==$stuClaInfo["pfkEnrollmentId"]){
                        array_push($regedClas, array($classInfo[" Subj"],$classInfo["#"]));
                    }
                }
            }

            if ($dataIsGood){
                $regSql = "INSERT INTO `tblStudentEnrollments` SET `pfkStudentNetId` = ? ,`pfkEnrollmentId` = ?";
                foreach ($regClaIds as $regClaId){
                    if($thisDatabaseWriter->querySecurityOk($regSql,0,0,0,0,0)){
                        $thisDatabaseWriter->sanitizeQuery($regSql);
                        foreach ($classInfos as $classInfo){
                            if($classInfo["pmkEnrollmentId"]==$regClaId){
                                if(in_array(array($classInfo[" Subj"],$classInfo["#"]),$regedClas)){
                                    array_push($regRInfos,$classInfo[" Subj"].$classInfo["#"]);
                                }else {
                                    $insInfo=$thisDatabaseWriter->insert($regSql, array($stdId,$regClaId));
                                    array_push($regSInfos, $regClaId);
                                }
                            }
                        }
                    }else{
                        array_push($regFInfos, $regClaId);
                    }
                }
            }

            if ($dataIsGood){
                if(!empty($regRInfos)){
                    $regRInfos = array_unique($regRInfos);
                    print '<p class="repeat">';
                    foreach ($regRInfos as $regRInfo){
                        print $regRInfo.PHP_EOL;
                    }
                    print 'has been registered repeatedly in ';
                    foreach ($stuInfos as $stuInfo){
                        $stuInfo["pmkNetId"]==$stdId ?
                            print $stuInfo["fldFirstName"]." ".$stuInfo["fldLastName"]:'';}
                    print '\'s schedule. Please re-check!';
                }

                if(empty($regFInfos) && empty($regRInfos) && !empty($regSInfos)) {
                    print "<p class = 'correct'>All of ";
                    foreach ($stuInfos as $stuInfo){$stuInfo["pmkNetId"]==$stdId? print $stuInfo["fldFirstName"]." ".$stuInfo["fldLastName"]:'';}
                    print " registration:<br>";
                    foreach ($classInfos as $classInfo){in_array($classInfo["pmkEnrollmentId"],$regSInfos)?
                        print $classInfo[" Subj"].$classInfo["#"].'Section'.$classInfo["Sec"].PHP_EOL: '';}
                    print "<br>have been done!</p>";
                } else {
                    if(!empty($regSInfos)) {
                        print '<p class ="correct">';
                        foreach ($stuInfos as $stuInfo){
                            $stuInfo["pmkNetId"]==$stdId ?
                                print $stuInfo["fldFirstName"]." ".$stuInfo["fldLastName"]:'';}

                        print '\'s class(es):' . PHP_EOL;

                        foreach ($classInfos as $classInfo) {
                            in_array($classInfo["pmkEnrollmentId"], $regSInfos) ?
                                print $classInfo[" Subj"] . $classInfo["#"] .'Section'.$classInfo["Sec"]. PHP_EOL : '';
                        }

                        print 'has been registered!;</p>';
                    }
                    if (!empty($regFInfos)) {
                        print '<p class="mistake">';
                        foreach ($stuInfos as $stuInfo) {
                            $stuInfo["pmkNetId"] == $stdId ? print $stuInfo["fldFirstName"] . " " . $stuInfo["fldLastName"] : '';
                        }
                        print '\'s class(es):' . PHP_EOL;
                        foreach ($classInfos as $classInfo) {
                            in_array($classInfo["pmkEnrollmentId"], $regFInfos) ?
                                print $classInfo[" Subj"] . $classInfo["#"] . 'Section' . $classInfo["Sec"] . PHP_EOL : '';
                        }
                        print  'failed to be registered due to some reasons.</p>';
                    }
                }
            }

        }
        ?>

        <form action="classes.php?subj=<?php print $subj?>" method="POST">
            <fieldset  class="listbox">
                <legend>Choose a student for the Registering</legend>
                <p><input class = "button" id = "btnSubmit" type = "submit" value="Register"></p>
                <p>
                    <select id="stdId"
                            name="stdId"
                    >
                <?php
                print "<option value= 'none' selected disabled hidden>select-a-student(FirstName, LastName)</option>".PHP_EOL;
                foreach ($stuInfos as $stuInfo){
                    print "<option value='".$stuInfo["pmkNetId"]."'>";
                    print $stuInfo["fldFirstName"]." ".$stuInfo["fldLastName"]."</option>".PHP_EOL;
                }
                print"</select>".PHP_EOL."</p>".PHP_EOL."</fieldset>".PHP_EOL;

                print "<table>".PHP_EOL;
                print "<tr>";
                print "<th>Select</th>";
                print "<th>Subject&Number</th>";
                print "<th>Title</th>";
                print "<th>Section</th>";
                print "<th>Instructor</th>";
                print "<th>Days</th>";
                print "<th>Start Time</th>";
                print "<th>End Time</th>";
                print "</tr>";
                foreach ($classInfos as $claInfo) {
                    print "<tr";
                    if ($claInfo["Current Enrollment"]>=$claInfo["Max Enrollment"]){
                        print "hidden='hidden'";
                    }
                    print ">";
                    print "<td><input type=checkbox name='regClaIds[]' value='".$claInfo["pmkEnrollmentId"]."'>"."</td>";
                    print "<td>".$claInfo[" Subj"].$claInfo["#"]."</td>";
                    print "<td>".$claInfo["Title"]."</td>";
                    print "<td>".$claInfo["Sec"]."</td>";
                    print "<td>".$claInfo["Instructor"]."</td>";
                    print "<td>".$claInfo["Days"]."</td>";
                    print "<td>".$claInfo["Start Time"]."</td>";
                    print "<td>".$claInfo["End Time"]."</td>";
                    print "</tr>";
                }
                print "</table>".PHP_EOL;
                ?>
        </form>
    </article>
</main>
<?php
include 'footer.php';
print "</body>".PHP_EOL."</html>";
?>