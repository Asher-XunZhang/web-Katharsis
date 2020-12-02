<?php
require_once 'lib/security.php';
include_once 'lib/validation-functions.php';
include_once 'lib/mail-message.php';
include_once 'lib/constants.php';
include_once 'lib/sql.php';
include_once LIB_PATH . '/Connect-With-Database.php';
/* As the setcookie function must requires that you place calls to
 * this function prior to any output, including <html> and <head> tags
 * as well as any whitespace. Therefore, I put the top.php at the end
 * of these php codes. But the codes below still need the php files above,
 * so I INCLUDE them here one by one.
*/
session_start();
// Sanatize funcion from the text
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
    } else {
        if ($field == "email") {
            $data = filter_var($_POST[$field], FILTER_SANITIZE_EMAIL);
        } else {
            $data = trim($_POST[$field]);
            $data = htmlspecialchars($data);
        }

    }
    return $data;
}

function getGData($field){
    if (!isset($_GET[$field])){
        $data="";
    } else {
        $data = trim($_GET[$field]);
        $data = htmlspecialchars($data);
    }
    return $data;
}




print  PHP_EOL . '<!-- SECTION: 1a. debugging setup -->' . PHP_EOL;
//print '<p>POST Array:</p><pre>';
//print_r($_POST);
//print '</pre>';
print PHP_EOL . '<!-- SECTION: 1b security -->' . PHP_EOL;
$thisURL = DOMAIN . PHP_SELF;
print PHP_EOL . '<!-- SECTION: 1c form variables -->' . PHP_EOL;
/**Expiration of Username**/
$expireU=time()+60*60*24*30;//30days
/**Expiration of Password**/
$expireP=time()+60*60;//1hour

$username="";
$password="";
$confirmpwd="";
$email = "youremail@uvm.edu";
$hashedpwd="";

$firstname="";
$middlename="";
$lastname="";
$gender="";
$telephone="";
$ssn="";
$address="";

$usernameInfos ="<li> Start with a letter;</li>".PHP_EOL;
$usernameInfos .="<li> 5-16 bytes;</li>".PHP_EOL;
$usernameInfos .="<li> Only allow letters, numbers and underscores</li>".PHP_EOL;

$passwordInfos = "<li> Contain at least 1 upper case letter</li>".PHP_EOL;
$passwordInfos .= "<li> Contain at least 1 lower case letter</li>".PHP_EOL;
$passwordInfos .= "<li> Contain at least 1 number or special character</li>".PHP_EOL;
$passwordInfos .= "<li> Contain at least 8 characters in length</li>".PHP_EOL;


print PHP_EOL . '<!-- SECTION: 1d form error flags -->' . PHP_EOL;
$usernameERROR =false;
$passwordERROR =false;
$emailERROR =false;
$nameERROR =false;
$genderERROR=false;
$telephoneERROR=false;
$ssnERROR=false;
$addressERROR=false;

print PHP_EOL . '<!-- SECTION: 1e misc variables -->' . PHP_EOL;
$errorMsg = array();
$dataRecord = array();
$dataEntered = false;
$mailed = false;
$messageA = "";
$messageB = "";
$messageC = "";

print PHP_EOL . '<!-- SECTION: 2 Process for when the form is submitted -->' . PHP_EOL;
$btnSubmit = "";

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $btnSubmit = getPData("btnSubmit");
    if(isset($btnSubmit)&&(!empty($btnSubmit)))
    {
        print PHP_EOL . '<!-- SECTION: 2a Security -->' . PHP_EOL;
        if (!securityCheck($thisURL))
        {
            $msg = "<p>Sorry you cannot access this page. ";
            $msg.= "Security breach detected and reported.</p>";
            die($msg);
        }
        if($btnSubmit=="Register")
        {
            print PHP_EOL . '<!-- SECTION: 2b Sanitize (clean) data  -->' . PHP_EOL;
            //Server side Sanitization
            $username=getPData("username");
            $password=getPData("first-password");
            $confirmpwd=getPData("confirm-password");
            $email = getPData("email");
            $firstname=getPData("firstname");
            $middlename=getPData("middlename");
            $lastname=getPData("lastname");
            $gender=getPData("gender");
            $telephone=getPData("telephone");
            $ssn=getPData("SSN");
            $address=getPData("address");
            print PHP_EOL . '<!-- SECTION: 2c Validation -->' . PHP_EOL;
            if ($username == ""){
                $errorMsg[] = "Please enter your Username";
                $usernameERROR = true;
            }elseif (!verifyUsername($username)){
                $errorMsg[] = "Your Username is invalid. Please follow the Tips.";
                $usernameERROR = true;
            }else{
                if ($thisDatabaseReader->querySecurityOk($chkRepQuery, 1,0,0,0,0)) {
                    $checkArr=array($username);
                    $checkRepeat = $thisDatabaseReader->select($chkRepQuery, $checkArr);
                    if($checkRepeat){
                        $errorMsg[] = "This username has already been registered, please choose another available username.";
                        $usernameERROR= true ;
                    }
                }
            }

            if ($password == ""){
                $errorMsg[] = "Please enter your Password";
                $passwordERROR = true;
            }elseif (!checkStrongPassword($password)){
                $errorMsg[] = "Your Password is invalid. Please follow the Tips.";
                $passwordERROR = true;
            }elseif ($password != $confirmpwd){
                $errorMsg[] = "The two passwords you entered are inconsistent.Please Check and Re-enter.";
                $passwordERROR = true;
            }else{
                $hashedpwd = password_hash($password,PASSWORD_DEFAULT);
            }

            if ($email == ""){
                $errorMsg[] = "Please enter your Email Address.";
                $emailERROR = true;
            }elseif (!verifyEmail($email)){
                $errorMsg[] = "Your email address appears to be incorrect.Please Check and Re-enter.";
                $emailERROR = true;
            }

            if(!(($firstname=="")&&($lastname==""))) {
                if (!($middlename == "")) {
                    if (!verifyEnglishName($middlename)) {
                        $errorMsg[] = "Incorrect Name";
                        $nameERROR = true;
                    }
                }
                if (!(verifyEnglishName($firstname) && verifyEnglishName($lastname))) {
                    if(!$nameERROR){
                        $errorMsg[] = "Incorrect Name";
                        $nameERROR = true;
                    }
                }
            }

            if ($gender==""){
                $gender="prefer";
            }

            if (!($telephone=="")){
                if(!verifyPhone($telephone)){
                    $errorMsg[] = "Invalid Telephone Number.";
                    $telephoneERROR = true;
                }
            }

            if (!($ssn=="")){
                if(!verifySSN($ssn)){
                    $errorMsg[] = "Invalid SSN.";
                    $ssnERROR = true;
                }
            }

            print PHP_EOL . '<!-- SECTION: 2d Process Form - Passed Validation -->' . PHP_EOL;
            if(!$errorMsg)
            {
                if(DEBUG) {
                    print "<p>Form is valid</p>";
                }
                $premaryKey="";
                $dataEntered=false;
                $infosresults = false;
                try{
                    $thisDatabaseWriter->db->beginTransaction();
                    $data = array($username,$hashedpwd,$email);
                    if(DEBUG){
                        print "<p>sql " . $insertUsersQuery;
                        print"<p><pre>";
                        print_r($data);
                        print"</pre></p>";
                    }
                    if ($thisDatabaseWriter->querySecurityOk($insertUsersQuery, 0,0,0,0,0))
                    {
                        $inCusSQL = $thisDatabaseWriter->sanitizeQuery($insertUsersQuery);
                        $results = $thisDatabaseWriter->insert($insertUsersQuery, $data);
                        $primaryKey = $thisDatabaseWriter->lastInsert();
                        setcookie("Username",$username,$expireU);
                        setcookie("Password",$password,$expireP);
                    }

                    $dataEntered = $thisDatabaseWriter->db->commit();

                } catch (PDOExecption $e) {
                    $thisDatabaseWriter->db->rollback();
                    if (DEBUG) print "Error!: " . $e->getMessage() . "</br>";
                    $errorMsg[] = "There was a problem with accpeting your data please contact us directly.";
                }
                /**********insert user's information*********/

                $userInfos = array($primaryKey,$firstname,$middlename,$lastname,$gender,$telephone,$ssn,$address);
                if ($thisDatabaseWriter->querySecurityOk($insertUsersInfosQuery,0)){
                    $insertUsersInfosQuery = $thisDatabaseWriter->sanitizeQuery($insertUsersInfosQuery);
                    $infosresults = $thisDatabaseWriter->insert($insertUsersInfosQuery,$userInfos);
                }
                // If the transaction was successful, we need to get date joined
                if ($dataEntered) {
                    if (DEBUG)
                        print "<p>Data entered now prepare keys ";
                    //#################################################################
                    // create a key value for confirmation
                    $data2 = array($primaryKey);
                    if ($thisDatabaseReader->querySecurityOk($confirmEmailQuery, 1)) {
                        $confirmEmailQuery = $thisDatabaseReader->sanitizeQuery($confirmEmailQuery);
                        $results = $thisDatabaseReader->select($confirmEmailQuery, $data2);
                    }

                    $timeSubmitted = $results[0]["regtime"];
                    $key1 = password_hash($timeSubmitted, PASSWORD_DEFAULT);
                    $key2 = $primaryKey;
                    if (DEBUG) {
                        print "<p>Date: " . $timeSubmitted;
                        print "<p>key 1: " . $key1;

                        print "<p>key 2: " . $key2;
                    }

//                     SECTION: 2f Create message
//
//                     build a message to display on the screen in section 3a and to mail
//                     to the person filling out the form (section 2g).
//                    #################################################################
//
//                    Put forms information into a variable to print on the screen
//

                    $messageA = '<h2>Thank you for registering.</h2>';

                    $messageA .= '<p>Please check your mail for instructions.</p>';

                    $messageB = "<p>Click this link to confirm your registration: ";
                    $messageB .= '<a href="http:' . DOMAIN . PATH_PARTS["dirname"] . '/confirmation.php?Token=' . $key1 . '&amp;Id=' . $key2 . '">Confirm Registration</a></p>';
                    $messageB .= "<p>or copy and paste this url into a web browser: ";
                    $messageB .= 'http:' . DOMAIN . PATH_PARTS["dirname"] . '/confirmation.php?Token=' . $key1 . '&amp;Id=' . $key2 . "</p>";


                    $messageC .= "<p><b>Email Address:</b><i>   " . $email . "</i></p>";

                    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
                    //
                    // SECTION: 2g Mail to user
                    //
                    // Process for mailing a message which contains the forms data
                    // the message was built in section 2f.

                    $to = $email; // the person who filled out the form
                    $cc = "";
                    $bcc = "";
                    $from = 'KATHARSIS <xzhang@uvm.edu>';
                    $subject = "CS 148 Final Project Group-16 Registration";
                    $message = $messageA . $messageB . $messageC;
                    $mailed = sendMail($to, $cc, $bcc, $from, $subject, $message);
                    // remove click to confirm
                    $message = $messageA . $messageC;
                } // end data was entered
            }//end if form is valid
        }//end if form is registration
    }//end if any form is submitted
}//end if receive any POST
include 'top.php';
?>

<main class="body<?php
    if($dataEntered) echo ' confirm';
    ?>">
        <?php
        if (isset($_POST["btnSubmit"]) AND empty($errorMsg) AND ($dataEntered)) {
            print '<article class="email-window pop-up-window">';
            print '<section class="confirm-content">';
            print '<h2>Thank you for providing your information.</h2>';
            print '<p>For your records a copy of this data has ';
            if (!$mailed) {
                print "not ";
            }
            print 'been sent to:</p>';
            print '<i>' . $email . '</i>';
            print "<p>Please check your email for confirmation.</p>";
            print "</section>";
            print "</article>";
            $btnSubmit = "Login";
        } else {
            if ($errorMsg) {
                print "<article class='mistakes-window pop-up-window'>" . PHP_EOL;
                print "<section class='mistakes-content'>" . PHP_EOL;
                print "<section class='mistakes-header'>" . PHP_EOL;
                print '<h2>Warning!</h2>' . PHP_EOL;
                print "</section>" . PHP_EOL;
                print "<section class='mistakes-body'>" . PHP_EOL;
                print '<p>Your form has the following mistakes that need to be fixed.</p>' . PHP_EOL;
                print "<ol class='mistakes'>" . PHP_EOL;
                foreach ($errorMsg as $err) {
                    print "<li class='mistake'>" . $err . "</li>\n";
                }
                print "</ol>" . PHP_EOL;
                print "</section>" . PHP_EOL . "<section class='mistakes-footer'>";
                print "<p>|--Please click on the outer shadow to close this window--|</p>";
                print "</section>" . PHP_EOL . "</section>" . PHP_EOL;
                print "</article>" . PHP_EOL;
            }
        }
            print PHP_EOL . '<!-- SECTION 3c html Form -->' . PHP_EOL;
            ?>
    <article class="mainbody middle <?php
        if((isset($_POST["btnSubmit"]) AND (!empty($errorMsg)))){
            echo 'mistakes'." ".$btnSubmit;
        }?>">
        <!--- Login in form --->
        <form class="form-box front" action="login.php" method="POST">
            <h1>Login</h1>
            <input type="text"
                   name="username"
                   value="<?php
                   if (isset($_COOKIE["Username"])){
                       echo $_COOKIE["Username"];
                   }?>"
                   placeholder="Username"
                   required
            />

            <input type="password"
                   name="password"
                   value="<?php
                   if (isset($_COOKIE["Password"])){
                       echo $_COOKIE["Password"];
                   }?>"
                   placeholder="Password"
                   required
            />

            <input type="submit"
                   name="btnSubmit"
                   value="Login"
            />

            <p style="margin-top: 60px">If you don't have account.Please</p>
            <p>Click here to <a class="signup" onclick="signup()">Sign Up</a></p>
        </form>

        <!--    - Sign up form --->
        <form class="form-box back" action="form.php" method="POST">
            <h1>Register</h1>
            <fieldset class="regfieldset">
                <fieldset class="required">
                    <legend>Required</legend>
                    <section>
                        <input
                            <?php if($usernameERROR) print "class='input-mistake' onchange='revise(this)'";?>
                            onfocus="showtips(1)"
                            onblur="hiddentips(1)"
                            type="text"
                            name="username"
                            value="<?php if(!$usernameERROR) echo $username;?>"
                            placeholder="Set Username"
                        required/>
                        <section class="tooltip tip1">
                            <ul class="tooltiptext tiptext1">Tips<?php echo $usernameInfos; ?></ul>
                        </section>
                    </section>

                    <section>
                        <input
                            <?php if($passwordERROR) print "class='input-mistake' onchange='revise(this)'";?>
                            onfocus="showtips(2)"
                            onblur="hiddentips(2)"
                            type="password"
                            name="first-password"
                            value="<?php if(!$passwordERROR) echo $password;?>"
                            placeholder="Set Password"
                        required/>
                        <section class="tooltip tip2">
                            <ul class="tooltiptext tiptext2">Tips<?php echo $passwordInfos; ?></ul>
                        </section>
                    </section>

                    <input
                        <?php if($passwordERROR) print "class='input-mistake' onchange='revise(this)'";?>
                        type="password"
                        name="confirm-password"
                        value="<?php if(!$passwordERROR) echo $confirmpwd;?>"
                        placeholder="Confirm Password"
                    required/>

                    <input
                        <?php if($emailERROR) print "class='input-mistake' onchange='revise(this)'";?>
                        type="email"
                        name="email"
                        value="<?php
                            if(!$emailERROR) {
                                echo $email;
                            }
                        ?>"
                        placeholder="Set Email"
                    required />
                </fieldset>

                <fieldset class="optional">
                    <legend>Optional</legend>
                    <fieldset class="name">
                        <legend>Name:</legend>
                        <input
                            <?php if($nameERROR) print "class='input-mistake' onchange='revise(this)'";?>
                            type="text"
                            name="firstname"
                            value="<?php if(!$nameERROR) echo $firstname;?>"
                            placeholder="First"
                        />

                        <input
                            <?php if($nameERROR) print "class='input-mistake' onchange='revise(this)'";?>
                            type="text"
                            name="middlename"
                            value="<?php if(!$nameERROR) echo $middlename;?>"
                            placeholder="Middle"
                        />

                        <input
                            <?php if($nameERROR) print "class='input-mistake' onchange='revise(this)'"?>
                            type="text"
                            name="lastname"
                            value="<?php if(!$nameERROR) echo $lastname;?>"
                            placeholder="Last"
                        />

                    </fieldset>

                    <p class="gender">Gender:</p>
                    <input id="female" type="radio" name="gender" value="female"
                        <?php if($gender=="female") echo 'checked'?> />
                    <label for="female">Female</label>
                    <input id="male" type="radio" name="gender" value="male"
                        <?php if($gender=="male") echo 'checked'?> />
                    <label for="male">Male</label>
                    <input id="prefer" type="radio" name="gender" value="prefer"
                        <?php if($gender=="prefer") echo 'checked'?> />
                    <label for="prefer">Prefer Not to Say</label>

                    <input
                        <?php if($telephoneERROR) print "class='input-mistake' onchange='revise(this)'"?>
                        type="tel"
                        name="telephone"
                        value="<?php if(!$telephoneERROR) echo $telephone;?>"
                        placeholder="Set Telephone Number"
                    />

                    <input
                        <?php if($ssnERROR) print "class='input-mistake' onchange='revise(this)'"?>
                        type="text"
                        name="SSN"
                        value="<?php if(!$ssnERROR) echo $ssn ?>"
                        placeholder="Set SSN"
                    />

                    <textarea name="Address" rows="2" cols="30" placeholder="Set Address"><?php print $address;?></textarea>
                </fieldset>
            </fieldset>
            <input type="submit" name="btnSubmit" value="Register" />
            <p class="comment">Have a account ? You can Click here to <a class="login" onclick="login()">Log in</a></p>
        </form>
    </article>
</main>
<?php
include_once "footer.php";
print "</body>".PHP_EOL."</html>";
?>