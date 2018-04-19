<?php 
require("config.php");

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

//get current data
$sql_current = "SELECT ID, logdate, ROUND(windspeed,2) AS windspeed, ROUND(temperature * 9/5 + 32, 2) AS temperature, charging, charged
FROM DataTable
ORDER BY ID DESC
LIMIT 1";

$result_current = mysqli_query( $conn , $sql_current );

$output = array();

while ($row = mysqli_fetch_assoc($result_current)) {
    array_push($output, $row);
}

print (json_encode($output));
$conn->close();
?>