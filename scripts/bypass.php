<?php
session_start();

$blankuname = FALSE;
$blankpword = FALSE;
$nonemail = FALSE;
$longuname = FALSE;
$longpword = FALSE;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	
	if (!array_key_exists('uname', $_POST) || empty($_POST['uname'])) {
		$_SESSION['BLANK_UNAME'] = TRUE;
	} 
	if (array_key_exists('uname', $_POST) && !empty($_POST['uname']) && strpos($_POST['uname'], "@") === FALSE) {
		$_SESSION['NON_EMAIL'] = TRUE;
	}
	if (array_key_exists('uname', $_POST) && strlen($_POST['uname']) > 5) {
		$_SESSION['LONG_UNAME'] = TRUE;
	}

	if (!array_key_exists('pword', $_POST) || empty($_POST['pword'])) {
		$_SESSION['BLANK_PWORD'] = TRUE;
	}

	if (array_key_exists('pword', $_POST) && strlen($_POST['pword']) > 5) {
		$_SESSION['LONG_PWORD'] = TRUE;
	}


	// Kill switch for session
	if (array_key_exists('uname', $_POST) && $_POST['uname'] == "k@ll") {
		session_destroy();
	}

}

if (array_key_exists('BLANK_UNAME',$_SESSION)) {
	$blankuname = TRUE;
}

if (array_key_exists('BLANK_PWORD',$_SESSION)) {
	$blankpword = TRUE;
}

if (array_key_exists('NON_EMAIL',$_SESSION)) {
	$nonemail = TRUE;
}

if (array_key_exists('LONG_UNAME',$_SESSION)) {
	$longuname = TRUE;
}

if (array_key_exists('LONG_PWORD',$_SESSION)) {
	$longpword = TRUE;
}

//var_dump($_POST);

?>
<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>Bypass Lab</title>
  <meta name="description" content="Bypass Lab">
  <meta name="author" content="Russell Thackston">
  <style>
	li.done {
		font-weight: bold;
	}
  </style>

</head>
<body>
  <h1>Bypass Lab</h1>
  <form action="bypass.php" method="post">
	<label for="uname">Email address:</label>
	<input type="email" name="uname" id="uname" required maxlength="5">
	<br>
	<label for="uname">Password:</label>
	<input type="password" name="pword" id="pword" required maxlength="5">
	<br>
	<input type="submit">
  </form>

  <h1>Instructions</h1>
  <p>Perform the following actions. They will be marked as bold when completed.</p>
  <ul>
	<li class="foo <?php if ($blankuname) { echo 'done'; } ?>">Submit a blank username.</li>
	<li class="foo <?php if ($blankpword) { echo 'done'; } ?>">Submit a blank password.</li>
	<li class="foo <?php if ($nonemail) { echo 'done'; } ?>">Submit a non-blank username that is not an email address.</li>
	<li class="foo <?php if ($longuname) { echo 'done'; } ?>">Submit a username longer than five characters.</li>
	<li class="foo <?php if ($longpword) { echo 'done'; } ?>">Submit a password longer than five characters.</li>
  </ul>
</body>
</html>

