<?php
require 'connectDB.php';


$nume_fisier = '/home/vladlen/Desktop/XXX/HackatoonX/rfidattendance/data.json';

$json_data = file_get_contents($nume_fisier);

$data = json_decode($json_data, true);

$uid =  $data['card_uid'];



$sql = "UPDATE users_logs
SET timeout = CURRENT_TIMESTAMP, card_out = 1
WHERE card_uid = '{$uid}' AND (timeout IS NULL OR timeout = '00:00:00')
LIMIT 1;
";

$conn->query($sql);





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