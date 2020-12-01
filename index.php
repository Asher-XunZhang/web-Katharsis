<?php
include 'top.php';
$getStuInfo="SELECT `fldFirstName`,`fldLastName`,`pmkNetId` FROM `tblStudents` ORDER BY `fldFirstName` ASC";
if ($thisDatabaseReader->querySecurityOk($getStuInfo,0,1,0,0,0)) {
    $thisDatabaseReader->sanitizeQuery($getStuInfo);
    $stuInfos = $thisDatabaseReader->select($getStuInfo, '');
}
$stuName=array();
foreach ($stuInfos as $stuInfo){
    array_push($stuName, $stuInfo["pmkNetId"]);
}

$stdId = "";
$dataIsGood = false;

// Sanatize funcion from the text
function getData($field){
    if (!isset($_GET[$field])){
        $data="";
    } else {
        $data = trim($_GET[$field]);
        $data = htmlspecialchars($data);
    }
    return $data;
}
?>


<main>
    <article>
        <h2>Schedule for 2020 Fall</h2>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == 'GET') {

            // we only save the data if it is good so we need to make a flag
            // notice if the data fails a validation check below I set this
            // flag to false so we dont save the invalid data
            $dataIsGood = True;

            //Server side Sanitization
            $stdId = getData("stdId");

            //Server side Validation
            if (!in_array($stdId, $stuName)) {
                print '<p class="mistake"><strong>Please choose a student.</strong></p>';
                $dataIsGood = false;
            }

            if ($dataIsGood) {
                try {
                    print '<h2>';
                    foreach ($stuInfos as $stuInfo) {
                        $stuInfo["pmkNetId"] == $stdId ? print $stuInfo["fldFirstName"] . " " . $stuInfo["fldLastName"] : '';
                    }
                    print '\'s Classes for Fall 2020</h2>';
                    print '<table>';
                    print "<tr>";
                    print "<th>Student's First Name</th>";
                    print "<th>Student's Middle Name</th>";
                    print "<th>Student's Last Name</th>";
                    print "<th>Subject</th>";
                    print "<th>#</th>";
                    print "<th>Title</th>";
                    print "<th>Days</th>";
                    print "<th>Start Time</th>";
                    print "<th>Bldg</th>";
                    print "<th>Room</th>";
                    print "<th>Teacher's First Name</th>";
                    print "<th>Teacher's Middle Name</th>";
                    print "<th>Teacher's Last Name</th>";
                    print "</tr>";
                    $sql = "SELECT `tblStudents`.`fldFirstName` AS `StudentsFirstName`, `tblStudents`.`fldMiddleName` AS `StudentsMiddleName`, `tblStudents`.`fldLastName` AS `StudentsLastName`, ` Subj`,`#`,`Title`,`Days`,`Start Time`,`End Time`,`Bldg`,`Room`, `tblTeachers`.`fldFirstName`AS`TeachersFirstName`, `tblTeachers`.`fldMiddleName`AS`TeachersMiddleName`, `tblTeachers`.`fldLastName` AS `TeachersLastName` FROM `tblStudents` JOIN `tblStudentEnrollments` ON `tblStudents`.`pmkNetId` = `tblStudentEnrollments`.`pfkStudentNetId` JOIN `tblEnrollments` ON `tblEnrollments`.`pmkEnrollmentId` = `tblStudentEnrollments`.`pfkEnrollmentId` JOIN `tblTeachers` ON `tblTeachers`.`pmkNetId` = `tblEnrollments`.`NetId` WHERE `tblStudents`.`pmkNetId` = ?";
                    if($thisDatabaseReader->querySecurityOk($sql)){
                        $thisDatabaseReader->sanitizeQuery($sql);
                        $records = $thisDatabaseReader->select($sql, array($stdId));
                    }

                    foreach ($records as $record) {
                        print '<tr>';
                        print '<td>' . $record["StudentsFirstName"] . "</td>";
                        print '<td>' . $record["StudentsMiddleName"] . "</td>";
                        print '<td>' . $record["StudentsLastName"] . "</td>";
                        print '<td>' . $record[" Subj"] . "</td>";
                        print '<td>' . $record["#"] . "</td>";
                        print '<td>' . $record["Title"] . "</td>";
                        print '<td>' . $record["Days"] . "</td>";
                        print '<td>' . $record["Start Time"] . "</td>";
                        print '<td>' . $record["Bldg"] . "</td>";
                        print '<td>' . $record["Room"] . "</td>";
                        print '<td>' . $record["TeachersFirstName"] . "</td>";
                        print '<td>' . $record["TeachersMiddleName"] . "</td>";
                        print '<td>' . $record["TeachersLastName"] . "</td>";
                        print '</tr>' . PHP_EOL;
                    }
                    print '</table>' . PHP_EOL;
                } catch (PDOException $e) {
                    print '<p>Couldn\'t find such a student, please contact someone.</p>';
                } //end try
            }
        }
        ?>
        <form action="index.php" method="get">
            <fieldset  class="listbox">
                <legend>Choose a student and look for the Register information</legend>
                <p>
                    <select id="stdId"
                            name="stdId"
                            tabindex="1" >
                        <?php
                        print "<option value= 'none' selected disabled hidden>select-a-student(FirstName, LastName)</option>".PHP_EOL;
                        foreach ($stuInfos as $stuInfo){
                            print "<option value='".$stuInfo["pmkNetId"]."'>";
                            print $stuInfo["fldFirstName"]." ".$stuInfo["fldLastName"]."</option>".PHP_EOL;
                        }
                        ?>
                    </select>
                </p>
            </fieldset>

            <fieldset class="buttons">
                <legend></legend>
                <input class = "button" id = "btnSubmit" name = "btnSubmit" tabindex = "2" type = "submit" value = "Register" >
            </fieldset>

        </form>
    </article>
</main>
<?php include ("footer.php"); ?>
</body>
</html>
