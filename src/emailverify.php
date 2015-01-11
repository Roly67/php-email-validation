<?php
/**
*
* PHP EMAIL VALIDATION
* This script performs real time email verification.
*
* @author Rowland O'Connor <https://plus.google.com/+RowlandOConnor>
* @version 1.0.0
*
* IMPORTANT DISCLAIMER
* This script is provided AS IS for educational purposes only.
*
* Script is NOT designed for industrial application and has 
* several known key deficiencies in error tolerance 
* and accuracy / coverage of email address queries.
*
* No warranty or support is provided. Use of this script 
* is at your own risk.
*
*
* USAGE WARNING
* Many hosting companies do not allow SMTP send 
* operations. Please get permission from your hosting provider 
* before deploying this script.
*
*
* LICENSE
* This script is licensed under Apache 2.0 (http://www.apache.org/licenses/).
*
*
* For further details, please see accompanying readme.txt.
*
*
* Source code at
* https://github.com/Roly67/php-email-validation
*/
?>

<?php
/* CONFIGURATION */

/*
$FROM Appears in the MAIL FROM:<> part of the SMTP conversation
It is VITAL to set this to your valid domain otherwise email 
verification might not work.
*/
$FROM = "postmaster@yourdomain.com"; // <-- !VERY, VERY, IMPORTANT. DON'T FORGET TO SET.

/*
$EMAIL_REGEX is used for Regex validation of the email address.
You can use your own, but the default one below is pretty comprehensive 
and should be good enough for most purposes.
*/
$EMAIL_REGEX="^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$";

/*
$TCP_BUFFER_SIZE defines the in memory buffer size used for SMTP conversations.
Default of 1024 is fine in most cases.
*/
$TCP_BUFFER_SIZE = 1024;

?>

<?php
/*
Presentation HTML
*/
?>
<p><strong><a href="https://github.com/Roly67/php-email-validation" target="_blank">PHP Email Validation</a></strong> script.</p>
<p>Example script to verify emails in real time using <abbr title="Personal Home Page">PHP</abbr>.</p>
<p><strong>Notes:</strong></p>
<ul>
<li>Provided &quot;as is&quot; for educational purposes only.</li>
<li>
	Performs two layers of validation:
	<ol>
		<li><strong>Syntax</strong> using server side regular expression.</li>
		<li><strong><abbr title="Simple Mail Transfer Protocol">SMTP</abbr></strong> connects to remote mail servers.</li>
	</ol>
</li>
<li>Source code and documentation at <a href="https://github.com/Roly67/php-email-validation" target="_blank" rel="nofollow">Github</a></li>
</ul>
<form action="" method="post">
	<input type="text" id="emailtoverify" name="emailtoverify" value="<?php if(isset($_POST["emailtoverify"]))
echo($_POST["emailtoverify"])?>" />
	<input type="submit" value="verify" />
</form>

<?php

/*POST back handler*/
if(isset($_POST["emailtoverify"]))
{
	$result = VerifyMail(trim($_POST["emailtoverify"]));
	
	// SMTP code 250 shows email is valid.
	if (substr($result[0],0,3) == "250")
		  echo("<strong>Result</strong>: Email is OK"); 
		else 
		{
		  echo("<strong>Result</strong>: Email is bad"); 
		  
		  // The reason why it's bad.
		  echo("<br/><br/> <strong>Description</strong>: ".$result[0]);
		}  
		
	echo("<p><strong>Server log:</strong></p>");
	$log = $result[2];
	$log = str_replace("<","&lt;", $log);
	$log = str_replace(">","&gt;", $log);
	$log = str_replace("\r","<br/>", $log);
	echo($log);
}


/*
Description:
Verifies email address

Parameters:
$Email - Email address to verify

Returns:
Array containing email verification result.
*/
function VerifyMail($Email) 
{
	global $FROM; // FROM address. See settings section above
	global $EMAIL_REGEX; // Email syntax verification Regex
	global $TCP_BUFFER_SIZE; //TCP buffer size for mail server conversation.

	// $HTTP_HOST gets the host name of the server running the PHP script.
	$HTTP_HOST = $_SERVER["HTTP_HOST"];
	
	// Prep up the function return.
	$Return = array();  

	// Do the syntax validation using simple regex expression.
	// Eliminates basic syntax faults.
	if (!eregi($EMAIL_REGEX, $Email)) 
	{ 
		$Return[0] = "Bad Syntax";         
		return $Return; 
	}
   
	// load the user and domain name into a local list from email address using string split function.
	list ( $Username, $Domain ) = split ("@",$Email); 

	// check if domain has MX record(s)
	if ( checkdnsrr ( $Domain, "MX" ) )  
	{ 
		$log .= "MX record for {$Domain} exists.\r"; 
	
		// Get DNS MX records from domain
		if ( getmxrr ($Domain, $MXHost))  
		{              
		} 

		// Get the IP address of first MX record
		$ConnectAddress = $MXHost[0]; 

		// Open TCP connection to IP address on port 25 (default SMTP port)
		$Connect = fsockopen ( $ConnectAddress, 25 );
		
		// Rerun array element index 1 contains the IP address of the target mail server
		$Return[1] = $ConnectAddress;
		  
		// Successful connection to mail server.
		if ($Connect)   
		{
			$log .= "Connection to {$ConnectAddress} SMTP succeeded.\r"; 
			
			// look for a response code of 220 using Regex
			if ( ereg ( "^220", $reply = fgets ( $Connect, $TCP_BUFFER_SIZE ) ) ) 
			{ 
				$log .= $reply."\r";
				
				// Start SMTP conversation with HELO
				fputs ( $Connect, "HELO ". $HTTP_HOST ."\r\n" ); 
				$log .=  "> HELO ". $HTTP_HOST ."\r"; 
				$reply = fgets ( $Connect, $TCP_BUFFER_SIZE );
				$log .= $reply."\r";                  

				// Next, do MAIL FROM:
				fputs ( $Connect, "MAIL FROM: <". $FROM .">\r\n" ); 
				$log .=  "> MAIL FROM: <". $FROM .">\r"; 
				$reply = fgets ( $Connect, $TCP_BUFFER_SIZE );
				$log .= $reply."\r";    
				
				// Next, do RCPT TO:
				fputs ( $Connect, "RCPT TO: <{$Email}>\r\n" ); 
				$log .= "> RCPT TO: <{$Email}>\r"; 
				$to_reply = fgets ( $Connect, $TCP_BUFFER_SIZE );
				$log .= $to_reply."\r";  
				
				// Quit the SMTP conversation.
				fputs ( $Connect, "QUIT\r\n"); 

				// Close TCP connection
				fclose($Connect); 
			} 
		} 
		else 
		{ 
			// Return array element 0 contains a message.
			$Return[0]="500 Can't connect mail server ({$ConnectAddress}).";         
			return $Return; 
		} 
	} 
	else 
	{
		$to_reply = "Domain '{$Domain}' doesn't exist.\r";    
		$log .= "MX record for '{$Domain}' doesn't exist.\r"; 
	}
        
	$Return[0]=$to_reply;
	$Return[2]=$log;

	return $Return;
}
?>