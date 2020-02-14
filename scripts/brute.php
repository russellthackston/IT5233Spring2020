<?php
session_start();

// Constants
$CREDFILE = 'credentials.txt';

// Variables
$username = "";
$password = "";
$success = FALSE;
$failure = FALSE;

function makeCredentials() {
	
	global $CREDFILE;

	if (file_exists($CREDFILE)) {
		unlink($CREDFILE);
	}

	// Read the lists of usernames and passwords
	$usernames = file('usernames.txt');
	$passwords = file('passwords.txt');

	// Pick a random username and password from the lists
	$username = trim($usernames[rand(0, sizeof($usernames)-1)]);
	$password = trim($passwords[rand(0, sizeof($passwords)-1)]);
	
	// write the values to 'credentials.txt'
	$f = fopen("credentials.txt", "w");
	fwrite($f, $username);
	fwrite($f, PHP_EOL);
	fwrite($f, $password);
	fwrite($f, PHP_EOL);
	fclose($f);

}


/* 
This script authenticates against a username and password combination 
from the usernames.txt and passwords.txt files, stored in credentials.txt.

If credentials.txt does not exist, it creates it with a 
random username/password combination.

If the correct username/password combination is entered, 
a new set of credentials is randomly selected and stored in credentials.txt.

This way, you can repreatedly use brute force attacks against this page 
without ever knowing the right credentials (unless you peek ;^).

*/


if (!file_exists($CREDFILE)) {
	makeCredentials();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	// Get the username and password submitted by the client
	$uname = trim($_POST['uname']);
	$pword = trim($_POST['pword']);

	// Read the "valid" credentials from the credentials file
	$credentials = file($CREDFILE);
	$username = trim($credentials[0]);
	$password = trim($credentials[1]);

	// Compare the submitted values to the ones from the credentials file
	if ($uname == $username && $pword == $password) {
		
		// Successful login!!!!
		$success = TRUE;
		makeCredentials();

	} else {
		
		// Failed login
		$failure = TRUE;

	}

}

?>
<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>Brute Force Lab</title>
  <meta name="description" content="Brute Force Lab">
  <meta name="author" content="Russell Thackston">
  <style>
    p.success { color: green; }
	p.failure { color: red; }
  </style>

</head>
<body>
  <h1>Brute Force Lab</h1>
  <form action="brute.php" method="post">
	<label for="uname">Username:</label>
	<input type="text" name="uname" id="uname" required>
	<br>
	<label for="uname">Password:</label>
	<input type="password" name="pword" id="pword" required>
	<br>
	<input type="submit">
  </form>

  <?php if ($success) { ?>
    <p class="success">You have logged in successfully. The credentials have been reset to new values.</p>
  <?php } ?>
  <p class="failure">
	  <?php if ($failure && $username != $uname) { ?>
		That is not a valid username.
	  <?php } else if ($failure && $password != $pword) { ?>
		Wrong password.
	  <?php } ?>
  </p>

</body>
</html>

