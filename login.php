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

$thisURL = DOMAIN.PHP_SELF;
$username = "";
$password = "";
$errormsg = "";
$accountCorrect =false;
/**Expiration of Username**/
$expireU=time()+60*60*24*3;//3days
/**Expiration of Password**/
$expireP=time()+60*60*1*1;//1hour


if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $btnSubmit = getPData("btnSubmit");
    if (isset($btnSubmit) && (!empty($btnSubmit))) {
        if ($btnSubmit == "Login") {
            $username = getPData("username");
            $password = getPData("password");
            //check the login information
            try {
                if ($thisDatabaseReader->querySecurityOk($checkAccountQuery, 1)) {
                    $checkAccountQuery = $thisDatabaseReader->sanitizeQuery($checkAccountQuery);
                    $results = $thisDatabaseReader->select($checkAccountQuery, array($username));
                }
                if(!$results){
                    $errormsg = "Sorry, there is not such a Username called ".$username;
                }else{
                    if($results[0]["status"]==0){
                        $errormsg ="The account is not activated.<br>";
                        $errormsg .="Please log in after activating the account in your email.";
                    }else{
                        if(!password_verify($password,$results[0]["Password"])){
                            $errormsg  = "Your password is NOT correct.<br>";
                            $errormsg .= "Please Check and Re-Enter";

                        }else{
                            $accountCorrect =true;
                            setcookie("Username",$username,$expireU);
                            setcookie("Password",$password,$expireP);
                        }
                    }
                }
            } catch (PDOExecption $e){
                if (DEBUG) print "Error!: " . $e->getMessage() . "</br>";
                $errorMsg[] = "There was a problem with accpeting your data please contact us directly.";
            }
        }//end if btnSubmit is login
    }//end if there is a btnSubmit
}//end if receive a POST
else{
    $errormsg = "Something Wrong.";
}

if($accountCorrect){
    $_SESSION["UserId"] = $results[0]["UserId"];
    $_SESSION["Username"] = $results[0]["Username"];
    header("Location:".BASE_PATH."homepage.php");
}else{
    include_once "top.php";
    header('Refresh: 5; url='.BASE_PATH.'form.php');
    print "<h2>".$errormsg."</h2>";
    print "<p>Loading, please wait...</p>";
    print "<p>You will be back to the login page after 5 seconds</p>";
    include_once "footer.php";
    print "</body>".PHP_EOL."</html>";
}
?>
