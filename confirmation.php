<?php
session_start();
require_once 'lib/security.php';
include_once 'lib/constants.php';
include_once LIB_PATH . '/validation-functions.php';
include_once LIB_PATH . '/mail-message.php';
include_once LIB_PATH . '/sql.php';
include_once LIB_PATH . '/sanatize.php';
include_once LIB_PATH . '/Connect-With-Database.php';
/* As the setcookie function must requires that you place calls to
 * this function prior to any output, including <html> and <head> tags
 * as well as any whitespace. Therefore, I put the top.php at the end
 * of these php codes. But the codes below still need the php files above,
 * so I INCLUDE them here one by one.
 */
/**Expiration of Username**/
$expireU=time()+60*60*24*1;//30days

if($_SERVER["REQUEST_METHOD"] == 'GET'){
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
            $username = $confirmresults[0]["Username"];
        }
        if (password_verify($regtime, $keyTime)) {
            if ($thisDatabaseWriter->querySecurityOk($confirmedQuery,1)) {
                $confirmedQuery = $thisDatabaseWriter->sanitizeQuery($confirmedQuery);
                $statusresults = $thisDatabaseWriter->update($confirmedQuery, $IdArr);
                setcookie("Username",$username,$expireU);
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
                $Login_username = $username;
                $to = $sentemail;
                $cc = "";
                $bcc = "";
                $from = 'KATHARSIS <xzhang@uvm.edu>';
                $subject = "Katharsis E-shop Account Registration Confirmed";
                $ConfirmCheckMessage = "<p>Thank you for taking the time to confirm your registration!</p>";
                $ConfirmCheckMessage .= "<p>You can log into your account now!</p>";
                $ConfirmCheckMessage .= "<p class='link'><a href='".BASE_PATH."form.php'>Click Here to Log In</a></p>";
                $mailed = sendMail($to, $cc, $bcc, $from, $subject, $ConfirmCheckMessage);
            } else {
                // update failed
                $ConfirmCheckMessage .= $failedMessage;
            }
        }else{
            $ConfirmCheckMessage .= $failedMessage;
        }
        include "top.php";
        print "<main>".PHP_EOL;
        print "<article class='confirmed-content'>".PHP_EOL;
        print "<h1>Congratulation! The account has been successfully activated</h1>".PHP_EOL;
        print "<p>Thank you for taking the time to confirm your registration!</p>";
        print "<p>You can log into your account now!</p>";
        print "Will jump to the login page in 5 seconds...";
        header('Refresh: 5; url='.BASE_PATH.'form.php');
        print "</article>";
        print "</main>";
        include "footer.php";
        print "</body>".PHP_EOL."</html>";
    }//end if receive for confirmation
}//end if receive any Get
?>