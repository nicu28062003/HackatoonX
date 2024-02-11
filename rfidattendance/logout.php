<?php

$jsonFilePath = '/home/vladlen/Desktop/XXX/HackatoonX/rfidattendance/data.json';

// Create an empty JSON object
$emptyJsonData = '{}';

// Write the empty JSON data to the file
file_put_contents($jsonFilePath, $emptyJsonData);

// Optionally, you can also unset any existing variables that hold the JSON data
unset($emptyJsonData);


	session_start();
	session_unset();
	session_destroy();
	header("location: login.php");
?>