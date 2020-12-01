<?php
include 'top.php';

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

print  PHP_EOL . '<!-- SECTION: 1a. debugging setup -->' . PHP_EOL;
//print '<p>POST Array:</p><pre>';
//print_r($_POST);
//print '</pre>';
print PHP_EOL . '<!-- SECTION: 1b security -->' . PHP_EOL;
$thisURL = $domain . $phpSelf;
print PHP_EOL . '<!-- SECTION: 1c form variables -->' . PHP_EOL;
$Login_username = "";
$Login_password = "";

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
                $errorMsg[] = "Your Username is invalid. Please Re-Enter a username that follows the Valid Username Rules.";
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
                $errorMsg[] = "Your Password is invalid. Please Re-Enter a password that follows the Strong Password Rules.";
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
                        $errorMsg[] = "Incorrect Middle Name";
                        $nameERROR = true;
                    }
                }
                if (!(verifyEnglishName($firstname) && verifyEnglishName($lastname))) {
                    $errorMsg[] = "Incorrect Name";
                    $nameERROR = true;
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
                    $messageB .= '<a href="http:' . DOMAIN . PATH_PARTS["dirname"] . '/LoginAndRegistration.php?Token=' . $key1 . '&amp;Id=' . $key2 . '">Confirm Registration</a></p>';
                    $messageB .= "<p>or copy and paste this url into a web browser: ";
                    $messageB .= 'http:' . DOMAIN . PATH_PARTS["dirname"] . '/LoginAndRegistration.php?Token=' . $key1 . '&amp;Id=' . $key2 . "</p>";


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
elseif($_SERVER["REQUEST_METHOD"] == 'GET'){
    if((isset($_GET["Token"]))&&(isset($_GET["Id"]))){
        $keyTime = $_GET["Token"];  // I did not sanitize in case of special characters were in the hased value
        $keyId = (int) htmlentities($_GET["Id"], ENT_QUOTES, "UTF-8");

        $IdArr = array($keyId);

        $confirmresults = "";
        $regtime = "";

        $statusresults = "";
        $sentemail = "";
        $statusUpdated = false;

        $AdminMessage = "";
        $ConfirmCheckMessage="";
        $failedMessage = "<p>I am sorry but this project cannot be confirmed at this time. Please email to ". ADMIN_EMAIL ." for help in resolving this matter.</p>";

        if ($thisDatabaseReader->querySecurityOk($confirmEmailQuery)) {
            $confirmEmailQuery = $thisDatabaseReader->sanitizeQuery($confirmEmailQuery);
            $confirmresults = $thisDatabaseReader->select($confirmEmailQuery, $IdArr);
        }
        if ($confirmresults) {
            $regtime = $confirmresults[0]["regtime"];
            $sentemail = $confirmresults[0]["Email"];
        }
        if (password_verify($regtime, $keyTime)) {
            if ($thisDatabaseWriter->querySecurityOk($confirmedQuery,1)) {
                $confirmedQuery = $thisDatabaseWriter->sanitizeQuery($confirmedQuery);
                $statusresults = $thisDatabaseWriter->update($confirmedQuery, $IdArr);
            }
            //##############################################################
            // notify admin

            if (!$statusresults) {
                $AdminMessage .= '<h1>Confirmed failed: Look at Registration Id: ' . $keyId . '</h1>';
                $to = ADMIN_EMAIL;
                $cc = "";
                $bcc = "";
                $from = 'KATHARSIS <xzhang@uvm.edu>';
                $subject = "Registration Confirmation Failed";
                $mailed = sendMail($to, $cc, $bcc, $from, $subject, $AdminMessage);
            }

            //##############################################################
            // notify user
            if ($statusresults) {
                $Login_username=$username;
                $Login_password=$password;
                $to = $sentemail;
                $cc = "";
                $bcc = "";
                $from = 'KATHARSIS <xzhang@uvm.edu>';
                $subject = "Katharsis E-shop Account Registration Confirmed";
                $ConfirmCheckMessage = "<p>Thank you for taking the time to confirm your registration!</p>";
                $ConfirmCheckMessage .= "<p>You can log into your account now!</p>";
                $mailed = sendMail($to, $cc, $bcc, $from, $subject, $ConfirmCheckMessage);
            } else {
                // update failed
                $ConfirmCheckMessage .= $failedMessage;
            }
        }else{
            $ConfirmCheckMessage .= $failedMessage;
        }
        print "<h2 class='confirmed-content'>".$ConfirmCheckMessage."</h2>";
    }//end if receive for confirmation
}//end if receive any Get
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
        } else {
            if($errorMsg){
                print "<article class='mistakes-window pop-up-window'>".PHP_EOL;
                print "<section class='mistakes-content'>".PHP_EOL;
                print "<section class='mistakes-header'>".PHP_EOL;
                print '<h2>Warning!</h2>'.PHP_EOL;
                print "</section>".PHP_EOL;
                print "<section class='mistakes-body'>".PHP_EOL;
                print '<p>Your form has the following mistakes that need to be fixed.</p>'.PHP_EOL;
                print "<ol id='mistakes'>".PHP_EOL;
                foreach ($errorMsg as $err) {
                    print "<li id='mistake'>" . $err . "</li>\n";
                }
                print "</ol>".PHP_EOL;
                print "</section>".PHP_EOL."<section class='mistakes-footer'>";
                print "<p>|--Please click on the outer shadow to close this window--|</p>";
                print "</section>".PHP_EOL."</section>".PHP_EOL;
                print "</article>".PHP_EOL;
            }

            print PHP_EOL . '<!-- SECTION 3c html Form -->' . PHP_EOL;
            ?>
    <article class="mainbody middle <?php
        if((isset($_POST["btnSubmit"]) AND (!empty($errorMsg)))){
            echo 'mistakes'." ".$btnSubmit;
        }?>">
        <!--- Login in form --->
        <form class="form-box front" action="LoginAndRegistration.php" method="POST">
            <h1>Login</h1>
            <input type="text" name="username" value="<?php echo $Login_username ?>" placeholder="Username" required />
            <input type="password" name="password"  placeholder="Password" required />
            <input type="submit" name="btnSubmit" value="Login" />
            <p style="margin-top: 60px">If you don't have account.Please</p>
            <p>Click here to <a class="signup" onclick="signup()">Sign Up</a></p>
        </form>

        <!--    - Sign up form --->
        <form class="form-box back" action="LoginAndRegistration.php" method="POST">
            <h1>Register</h1>
            <fieldset class="regfieldset">
                <fieldset class="required">
                    <legend>Required</legend>
                    <input
                        <?php if($usernameERROR) print "id='mistake' onchange='revise(this)'";?>
                        type="text"
                        name="username"
                        value="<?php if(!$usernameERROR) echo $username;?>"
                        placeholder="Set Username"
                    required/>


                    <input
                        <?php if($passwordERROR) print "id='mistake' onchange='revise(this)'";?>
                        type="password"
                        name="first-password"
                        value="<?php if(!$passwordERROR) echo $password;?>"
                        placeholder="Set Password"
                    required/>

                    <input
                        <?php if($passwordERROR) print "id='mistake' onchange='revise(this)'";?>
                        type="password"
                        name="confirm-password"
                        value="<?php if(!$passwordERROR) echo $confirmpwd;?>"
                        placeholder="Confirm Password"
                    required/>

                    <input
                        <?php if($emailERROR) print "id='mistake' onchange='revise(this)'";?>
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
                            <?php if($nameERROR) print "id='mistake' onchange='revise(this)'";?>
                            type="text"
                            name="firstname"
                            value="<?php if(!$nameERROR) echo $firstname;?>"
                            placeholder="First"
                        />

                        <input
                            <?php if($nameERROR) print "id='mistake' onchange='revise(this)'";?>
                            type="text"
                            name="middlename"
                            value="<?php if(!$nameERROR) echo $middlename;?>"
                            placeholder="Middle"
                        />

                        <input
                            <?php if($nameERROR) print "id='mistake' onchange='revise(this)'"?>
                            type="text"
                            name="lastname"
                            value="<?php if(!$nameERROR) echo $lastname;?>"
                            placeholder="Last"
                        />

                    </fieldset>

                    <p class="gender">Gender:</p>
                    <input id=female type="radio" name="gender" value="female"
                        <?php if($gender=="female") echo 'checked'?> />
                    <label for="female">Female</label>
                    <input id="male" type="radio" name="gender" value="male"
                        <?php if($gender=="male") echo 'checked'?> />
                    <label for="male">Male</label>
                    <input id="prefer" type="radio" name="gender" value="prefer"
                        <?php if($gender=="prefer") echo 'checked'?> />
                    <label for="prefer">Prefer Not to Say</label>

                    <input
                        <?php if($telephoneERROR) print "id='mistake' onchange='revise(this)'"?>
                        type="tel"
                        name="telephone"
                        value="<?php if(!$telephoneERROR) echo $telephone;?>"
                        placeholder="Set Telephone Number"
                    />

                    <input
                        <?php if($ssnERROR) print "id='mistake' onchange='revise(this)'"?>
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
    <?php };?>
</main>
<?php
include "footer.php";
print "</body>".PHP_EOL."</html>";
?>