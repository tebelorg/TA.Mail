#!/usr/local/bin/php -q
<?php

/* MAILBOT SCRIPT FOR AUTO-RESPONDING TO EMAILS ~ TEBEL.SG */

$iostream = fopen("php://stdin", 'r'); $email = "";
while (!feof($iostream)) $email .= fread($iostream, 1024); fclose($iostream);

$logentry = get_from($email) . ", " . get_to($email) . ", " . get_subject($email) . "\n";
$logentry .= get_message($email) . "\n" . process_email($email) . "\n\n";
$logfile = fopen('/fullpath/mailbot.log', 'a'); fwrite($logfile, $logentry); fclose($logfile);

/* PROCESS EMAIL */
function process_email($email_content) {
	$email_subject = strtoupper(str_replace(" ","",get_subject($email_content)));
	$email_subject .= strtoupper(str_replace(" ","",get_message($email_content)));

    if (strpos($email_subject, "FOODNEARBY") !== false)
    {
        if (ctype_digit(substr(strtoupper(str_replace(" ","",get_subject($email_content))),-6)))
        $_GET['POSTAL']=substr(strtoupper(str_replace(" ","",get_subject($email_content))),-6);
        $_GET['SERVICE']="FOODNEARBY"; return call_service();
    }	
	else if (strpos($email_subject, "DELIVEROO") !== false)
	{
		$_GET['MESSAGE']="Ordering food from Deliveroo is not yet enabled.";
		$_GET['SERVICE']="SENDMAIL"; return call_service();
	}
	else 
	{	
        $_GET['MESSAGE']="Your email does not have actionable instructions.";
        $_GET['SERVICE']="SENDMAIL"; return call_service();
	}
}

/* CALL SERVICE */
function call_service() {
        ob_start(); include('/fullpath/run.php');
	$php_result = ob_get_contents(); ob_end_clean(); return $php_result;
}

/* GET EMAIL FROM */
function get_from($email_content) {
	$from1 = explode ("\nFrom: ", $email_content);
	$from2 = explode ("\n", $from1[1]);
	if(strpos ($from2[0], '<') !== false)
	{
    		$from3 = explode ('<', $from2[0]);
    		$from4 = explode ('>', $from3[1]);
    		$from = $from4[0];
	}
	else
	{
    		$from = $from2[0];
	}
        return $from;
}

/* GET EMAIL TO */
function get_to($email_content) {
	$to1 = explode ("\nTo: ", $email_content);
	$to2 = explode ("\n", $to1[1]);
	$to = str_replace ('>', '', str_replace('<', '', $to2[0]));
	return $to;
}

/* GET EMAIL SUBJECT */
function get_subject($email_content) {
        $subject1 = explode ("\nSubject: ", $email_content);
        $subject2 = explode ("\n", $subject1[1]);
        $subject = $subject2[0];
        return $subject;
}

/* GET EMAIL MESSAGE */
function get_message($email_content) {
	$message1 = explode ("\n\n", $email_content);
	$start = count ($message1) - 3;
	if ($start < 1) $start = 1;
	$message2 = explode ("\n\n", $message1[$start]);
	$message = $message2[0];
	return $message;
}

?>
