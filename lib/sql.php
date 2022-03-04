<?php
/*************** CS148 Final Project ******************
 * This is a php file for recording all codes of SQL
 *
 *
 *
 */
/***************** tblUsers ********************/
/*************** form.php **************/
//Insert
$insertUsersQuery = "INSERT INTO `tblUsers` SET `Username` = ? , `Password` = ? , `Email` = ?";
//Check Username Repeat
$chkRepQuery = "SELECT * FROM `tblUsers` WHERE `Username`=?";
//Confirm the registration by sending Email
$confirmEmailQuery = "SELECT `regtime`,`Email`,`Username` FROM `tblUsers` WHERE `tblUsers`.`UserId` = ? ";
/************ confirmation.php *************/
$confirmedQuery = "UPDATE `tblUsers` SET `status` = 1 WHERE `tblUsers`.`UserId` = ?";
/************ login.php ****************/
$checkAccountQuery = "SELECT * FROM `tblUsers` WHERE `Username`=?";




/******************** tblUsersInfos ***********************/
$insertUsersInfosQuery = "INSERT INTO `tblUsersInfos` SET `tblUsersInfos`.`UserId`=?, `FirstName`=?, `MiddleName`=?, `LastName`=?, `Gender`=?, `Telephone`=?, `SSN`=?, `Address`=?";



/********************* tblProducts and tblProductsInfos *********************/
/*                                                                         */
//****************************display the table
//(BussinessId)
$displayAllQuery = "SELECT tblProducts.TimeOfAdding AS `Time`,tblProducts.ProductId AS `Id`, tblProductsInfos.ProductName AS `Name`,tblProductsInfos.Category AS `Category`,tblProductsInfos.Price AS `Price`,tblProductsInfos.Quantity AS `Quantity`,tblProductsInfos.Description AS `Description` FROM tblProducts JOIN tblProductsInfos ON tblProducts.ProductId=tblProductsInfos.ProductId WHERE tblProducts.BussinessId = ?";
//(BussinessId, Category)
$displayProOfCatQuery= "SELECT tblProducts.TimeOfAdding AS `Time`,tblProducts.ProductId AS `Id`, tblProductsInfos.ProductName AS `Name`,tblProductsInfos.Category AS `Category`,tblProductsInfos.Price AS `Price`,tblProductsInfos.Quantity AS `Quantity`,tblProductsInfos.Description AS `Description` FROM tblProducts JOIN tblProductsInfos ON tblProducts.ProductId=tblProductsInfos.ProductId WHERE tblProducts.BussinessId = ? AND tblProductsInfos.Category = ?";
//(BussinessId)
$displayCateQuery= "SELECT DISTINCT tblProductsInfos.Category AS `Category` FROM tblProducts JOIN tblProductsInfos ON tblProducts.ProductId=tblProductsInfos.ProductId WHERE tblProducts.BussinessId = ?";

/* shop.php */  ##displays all other users' products
//(BussinessId)
$displayAllOtrQuery = "SELECT tblProducts.TimeOfAdding AS `Time`,tblProducts.ProductId AS `Id`, tblProductsInfos.ProductName AS `Name`,tblProductsInfos.Category AS `Category`,tblProductsInfos.Price AS `Price`,tblProductsInfos.Quantity AS `Quantity`,tblProductsInfos.Description AS `Description` FROM tblProducts JOIN tblProductsInfos ON tblProducts.ProductId=tblProductsInfos.ProductId WHERE tblProducts.BussinessId != ?";
//(BussinessId, Category)
$displayOtrProOfCatQuery= "SELECT tblProducts.TimeOfAdding AS `Time`,tblProducts.ProductId AS `Id`, tblProductsInfos.ProductName AS `Name`,tblProductsInfos.Category AS `Category`,tblProductsInfos.Price AS `Price`,tblProductsInfos.Quantity AS `Quantity`,tblProductsInfos.Description AS `Description` FROM tblProducts JOIN tblProductsInfos ON tblProducts.ProductId=tblProductsInfos.ProductId WHERE tblProducts.BussinessId != ? AND tblProductsInfos.Category = ?";
//(BussinessId)
$displayOtrCateQuery= "SELECT DISTINCT tblProductsInfos.Category AS `Category` FROM tblProducts JOIN tblProductsInfos ON tblProducts.ProductId=tblProductsInfos.ProductId WHERE tblProducts.BussinessId != ?";


//############################delete the product
//(ProductId)
$deleteProQuery ="DELETE FROM `tblProducts` WHERE `ProductId`= ? ";
//############################Count the total number of products
//(BussinessId)=>the total number of rows
$countProQuery = "SELECT COUNT(*) FROM tblProducts WHERE tblProducts.BussinessId = ?";

//************************* add products
//(BussinessId)
$addProQuery = "INSERT INTO `tblProducts` SET BussinessId = ?";
$addProInfosQuery = "INSERT INTO `tblProductsInfos` SET `ProductId`= ?, `ProductName`= ?, `Category`= ?, `Price`= ?,`Quantity`= ?, `Description`= ?";

?>

