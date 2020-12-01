<?php
print PHP_EOL . '<!--  BEGIN include mail-message       -->' . PHP_EOL;
function sendMail($to, $cc, $bcc, $from, $subject, $message){ 
    $MIN_MESSAGE_LENGTH=40;
    $blnMail=false;
    $to = filter_var($to, FILTER_SANITIZE_EMAIL);
    $cc = filter_var($cc, FILTER_SANITIZE_EMAIL);
    $bcc = filter_var($bcc, FILTER_SANITIZE_EMAIL); 
    $subject = htmlentities($subject,ENT_QUOTES,"UTF-8");
    if(empty($to)) return false;
    if(!filter_var($to, FILTER_VALIDATE_EMAIL)) return false;
    if($to == "youremail@uvm.edu") return false;
    if($cc!="") if(!filter_var($cc, FILTER_VALIDATE_EMAIL)) return false;  
    if($bcc!="") if(!filter_var($bcc, FILTER_VALIDATE_EMAIL)) return false;   
    if(empty($from)) return false;    
    if(empty($subject)) return false;    
    if(empty($message)) return false;
    if (strlen($message)<$MIN_MESSAGE_LENGTH) return false;
    $messageTop  = '<html><head><title>' . $subject . '</title></head><body>';
    $mailMessage = $messageTop . $message;
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
    $headers .= "From: " . $from . "\r\n";
    if ($cc!="") $headers .= "CC: " . $cc . "\r\n";
    if ($bcc!="") $headers .= "Bcc: " . $bcc . "\r\n";
    $blnMail=mail($to, $subject, $mailMessage, $headers);
    return $blnMail;
}
print PHP_EOL . '<!--  END include mail-message -->' . PHP_EOL;
?>