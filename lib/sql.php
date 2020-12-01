<?php
/*************** CS148 Final Project ******************
 * This is a php file for recording all codes of SQL
 *
 *
 *
 */
/***************** tblUsers ********************/
//Insert
$insertUsersQuery = "INSERT INTO `tblUsers` SET `Username` = ? , `Password` = ? , `Email` = ?";
//Check Username Repeat
$chkRepQuery = "SELECT * FROM `tblUsers` WHERE `Username`=?";
//Confirm the registration by sending Email
$confirmEmailQuery = "SELECT `regtime`,`Email` FROM `tblUsers` WHERE `tblUsers`.`UserId` = ? ";
$confirmedQuery = "UPDATE `tblUsers` SET `status` = 1 WHERE `tblUsers`.`UserId` = ?";

/******************** tblUsersInfos ***********************/
$insertUsersInfosQuery = "INSERT INTO `tblUsersInfos` SET `tblUsersInfos`.`UserId`=?, `FirstName`=?, `MiddleName`=?, `LastName`=?, `Gender`=?, `Telephone`=?, `SSN`=?, `Address`=?";

?>

