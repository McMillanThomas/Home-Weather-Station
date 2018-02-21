<?php 
require("config.php");

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

//get current data
$sql_current = "SELECT dt2.ID, dt2.logdate, ROUND(AVG(dt1.windspeed),2) AS windspeed, ROUND(dt2.temperature * 9/5 + 29, 2) AS temperature
FROM (SELECT `windspeed`, ID FROM DataTable ORDER BY ID DESC LIMIT 2) dt1
LEFT JOIN DataTable dt2 ON dt1.ID = dt2.ID";

$result_current = mysqli_query( $conn , $sql_current );

$output = array();

while ($row = mysqli_fetch_assoc($result_current)) {
    array_push($output, $row);
}

print (json_encode($output));
$conn->close();
?>