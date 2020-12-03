<?php
session_start();
include_once "lib/constants.php";
if(isset($_SESSION["UserId"])){
    unset($_SESSION["UserId"]);
}
if(isset($_SESSION["Username"])){
    unset($_SESSION["Username"]);
}
setcookie("Username", "", time()-3600);
setcookie("Password", "", time()-3600);
session_destroy();
header("Location:".BASE_PATH."form.php");
?>