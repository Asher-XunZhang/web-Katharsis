<?php
session_start();
include "top.php";
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
                $_SESSION["Confirm"] = true;
                $ConfirmCheckMessage .= "<p><a href='./login.php'>Click Here to Log In</a></p>";
                $mailed = sendMail($to, $cc, $bcc, $from, $subject, $ConfirmCheckMessage);
            } else {
                // update failed
                $ConfirmCheckMessage .= $failedMessage;
            }
        }else{
            $ConfirmCheckMessage .= $failedMessage;
        }
        print "<main>".PHP_EOL;
        print "<article class='confirmed-content'>".PHP_EOL;
        print "<h1>Congratulation! The account has been successfully activated</h1>".PHP_EOL;
        print $ConfirmCheckMessage."</article>";
        print "</main>";
    }//end if receive for confirmation
}//end if receive any Get
include "footer.php";
print "</body>".PHP_EOL."</html>";
?>