<?php

/*
  Written by Yafim Landa for my MIT 6.470, IAP '11 PHP Lecture given on
  1/5/2010. Please feel free to improve on it. Please keep in mind that
  this file was written for pedagogical reasons.

  This script authenticates a user using MIT certificates. It writes user
  data to a database, and so it requires that a database.php file exist
  that establishes a connection to a database.

  I've included links to CSS, JavaScript, and favicon files so that 6.470
  students wouldn't have to look up how to include these files. If you plan
  to use this script for authentication, you probably want to take those out
  of the <head> or replace them with your own.
*/

	session_start(); // Must include this call every time we work with sessions
	
	// Uncomment the following two lines to turn on debugging.
	// ini_set('display_errors', 1);
	// error_reporting(E_ALL);
	
	define('PATH_TO_APP', '/'); // Where the web application is located on the server.
	
	include_once 'database.php'; // Include our database code
	
	// If the user is here, we want to log them out and then prompt them
	// to log back in, perhaps with a different account.

	$_SESSION = array(); // Reset the $_SESSION array to a blank variable

	// If they're using HTTPS, then it must mean that they're trying to log in
	// using their MIT certificates.
	if (array_key_exists('HTTPS', $_SERVER)) {
		$fullname = explode(" ", $_SERVER['SSL_CLIENT_S_DN_CN']);
		$first_name = $fullname[0];
		$last_name = $fullname[count($fullname)-1];
		$email = $_SERVER['SSL_CLIENT_S_DN_Email'];
		
		// Remember everything we need in the $_SESSION variable
		$_SESSION['first_name'] = $first_name;
		$_SESSION['last_name'] = $last_name;
		$_SESSION['email'] = $email;
		
		// Okay, now that we've saved this info in the session, let's make a record
		// in the database.
		$thequery = "INSERT INTO users
					 (first_name, last_name, email)
					 VALUES ('$first_name', '$last_name', '$email')
					 ON DUPLICATE KEY UPDATE logins=logins+1";
		mysql_query($thequery) or die(mysql_error()); // Make the query and short circuit out or die with the error
		$_SESSION['id'] = mysql_insert_id(); // Get the id from the row we just inserted or updated
		
		// Now we just have to redirect the user back to wherever they came from.
		// We are going to do this a simple way and just send them back to index.php,
		// but you could pass a return address as a GET parameter.
		$url = 'http://' . $_SERVER['SERVER_NAME'] . PATH_TO_APP . '/index.php';
		header("Location: $url");
	}

?>

<!DOCTYPE html>

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Authentication</title>
	<link href="css/main.css" rel="stylesheet" type="text/css" />
	<script src="javascript/main.js" type="text/javascript"></script>
	<link rel="shortcut icon" href="favicon.ico" />
</head>
<body>
	<a href="https://<?php echo $_SERVER['SERVER_NAME'] . ':444' . $_SERVER['PHP_SELF']?>">Click here</a> to login with your MIT certificates.
</body>
</html>
