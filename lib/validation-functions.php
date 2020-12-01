<?php
print PHP_EOL . '<!--  BEGIN include validation-functions -->' . PHP_EOL;
function verifyAlphaNum($testString) {
    return (preg_match ("/^([[:alnum:]]|-|\.| |\'|&|;|#)+$/", $testString));
}
function verifyEmail($testString) {
    return filter_var($testString, FILTER_VALIDATE_EMAIL);
}
function verifyNumeric($testString) {
    return (is_numeric($testString));
}
function verifyPhone($testString) {
    $regex = '/^(?:1(?:[. -])?)?(?:\((?=\d{3}\)))?([2-9]\d{2})(?:(?<=\(\d{3})\))? ?(?:(?<=\d{3})[.-])?([2-9]\d{2})[. -]?(\d{4})(?: (?i:ext)\.? ?(\d{1,5}))?$/';
    return (preg_match($regex, $testString));
}
function verifySSN($testString){
    $regex = "/^(?!666|000|9\\d{2})\\d{3}-(?!00)\\d{2}-(?!0{4})\\d{4}$/";
    return (preg_match($regex, $testString));
}
/* Start with a letter;
 * allow 5-16 bytes;
 * allow letters, numbers and underscores
 */
function verifyUsername($testString){
    $regex = "/^[a-zA-Z][a-zA-Z0-9_]{4,15}$/";
    return (preg_match($regex, $testString));
}
function verifyEnglishName($testString){
    $regex = "/^[A-Za-z]+$/";
    return (preg_match($regex, $testString));
}
/* * contain at least (1) upper case letter
   * contain at least (1) lower case letter
   * contain at least (1) number or special character
   * contain at least (8) characters in length
   */
function checkStrongPassword($testString){
    $regex = "/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/";
    return (preg_match($regex, $testString));
}
print PHP_EOL . '<!--  END include validation-functions -->' . PHP_EOL;
?>


