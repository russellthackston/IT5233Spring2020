<p>
The list of tokens below was generated using this code.
</p>
<pre>
# Characters to use in token
$characters = ['a','b','c','d','e','f','0','1','2','3','4','5','6','7','8','9'];

# Generate the same tokens every time
srand(1);

for ($j = 0; $j < 10000; $j++) {
	# Initialize our PRN string
	$str = "";

	# Randomly select 4 characters and append them to the string
	for ($i = 0; $i < 4; $i++) {
		$index = rand(0, count($characters)-1);
		$str .= $characters[$index];
	}
	echo $str;
	echo PHP_EOL;
}
</pre>
<p>
If you know (1) the algorithm for generating a session token and (2) a token that was just issued,
then you can guess the next token that will be generated simply by generating a list of tokens
and finding your token in the list.
</p>

<pre>
<?php
# Characters to use in token
$characters = ['a','b','c','d','e','f','0','1','2','3','4','5','6','7','8','9'];

# Uncomment this line to generate the same string over and over again
srand(1);

for ($j = 0; $j < 10000; $j++) {
	# Initialize our PRN string
	$str = "";

	# Randomly select 4 characters and append them to the string
	for ($i = 0; $i < 4; $i++) {
		$index = rand(0, count($characters)-1);
		$str .= $characters[$index];
	}
	echo $str;
	echo PHP_EOL;
}

?>
</pre>