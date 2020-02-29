<?php
# Check for reset request
if (array_key_exists('reset', $_GET)) {

	# You have to start a session to destroy a session
	session_start();

	# remove all session variables
	session_unset();

	# destroy the session
	session_destroy(); 

	header("Location: session.php");
	exit();

}

# Start a new session
session_start();

# Characters to use in token
$characters = ['a','b','c','d','e','f','0','1','2','3','4','5','6','7','8','9'];

# Randomly select the n-th token
$tindex = rand(0, 9999);

# Check for first access
if (!array_key_exists('sessionid', $_SESSION)) {

	# Initialize our PRN string
	$str = "";

	# Generate the same series of tokens every time
	srand(1);

	# Randomly select the n-th token
	for ($j = 0; $j < $tindex; $j++) {

		# Reinitialize our PRN string
		$str = "";

		# Randomly select 4 characters and append them to the string
		for ($i = 0; $i < 4; $i++) {
			$index = rand(0, count($characters)-1);
			$str .= $characters[$index];
		}

	}

	# Store this token in session and return to the browser as a cookie (expires in one day)
	$_SESSION["sessionid"] = $str;
	setcookie("sessionid", $str, time() + (86400 * 30), "/");

	# Reinitialize our PRN string for the next token
	$str = "";

	# Randomly select 4 characters and append them to the string
	for ($i = 0; $i < 4; $i++) {
		$index = rand(0, count($characters)-1);
		$str .= $characters[$index];
	}

	# Store the other user's token in the session for the student to guess
	$_SESSION["guessme"] = $str;

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
	<h1>Attacking Sessions Lab</h1>
	
	<h2>Instructions</h2>
	<p>
		A session ID token has been created and stored on the server for you. 
		The same token has been sent to you as a cookie ("sessionid").
	</p>
	<p>
		A second session ID token has been created and stored on the server for another user.
		They have been provided the token as a cookie in their browser.
		You obviously do not know what this token value is.
	</p>
	<p style="font-weight: bold;">
		Your challenge is to figure out what token they were sent and modify your "sessionid" cookie to be that value.
	</p>
	<p>
		Suggestion: Use the session_tokens.php script to generate a list of tokens.
		Find the token you were issued and modify your cookie to have the next value in the list.
	</p>
	<p>
		You will be notified below when you've successfully hijacked their session.
	</p>
	<pre>
		# Characters to use in token
		$characters = ['a','b','c','d','e','f','0','1','2','3','4','5','6','7','8','9'];

		# Initialize our PRN string
		$str = "";

		# Uncomment this line to generate the same string over and over again
		srand(1);

		# Randomly select 4 characters and append them to the string
		for ($i = 0; $i < 4; $i++) {
			$index = rand(0, count($characters)-1);
			$str .= $characters[$index];
		}
		
		# Add code here to generate the next 4 character token
		???
	</pre>
	
	<?php
	# Check if the browser sent back the cookie they were given
	if ($_COOKIE['sessionid'] == $_SESSION["sessionid"]) {
	?>
		<h1>
			You have sent the server the session ID token that YOU were issued.
		</h1>

	<?php
	}
	# Check if the browser sent back the cookie they were given
	else if ($_COOKIE['sessionid'] == $_SESSION["guessme"]) {
	?>
		<h1>
			You have successfully hijacked someone else's session/token!
		</h1>

	<?php
	}
	# Check if the browser sent back the cookie they were given
	else {
	?>
		<h1>
			That is not a valid session ID token for you or anyone else.
		</h1>

	<?php
	}
	?>

	<p>
		<a href="session.php?reset=reset">Click here to start over.</a>
	</p>

</body>
</html>

