<?php
// Database config in seperate file
require("config.php");

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Set Time Zone and make current date object
date_default_timezone_set('America/Chicago');
$startrange = date('Y-m-d H:i:s');
$endrange = new DateTime(); //current date/time

// Check what scope to use
if ($_POST["scope"] == 'Hour') {
  $endrange->sub(new DateInterval("PT1H"));
  $endrange = $endrange->format('Y-m-d H:i:s');
  $granularity = 2;
}
if ($_POST["scope"] == 'Day'){
  $endrange->sub(new DateInterval("P1D"));
  $endrange = $endrange->format('Y-m-d H:i:s');
  $granularity = 40;
}
if ($_POST["scope"] == 'Week'){
  $endrange->sub(new DateInterval("P1W"));
  $endrange = $endrange->format('Y-m-d H:i:s');
  $granularity = 160;
}
if ($_POST["scope"] == 'Month'){
  $endrange->sub(new DateInterval("P1M"));
  $endrange = $endrange->format('Y-m-d H:i:s');
  $granularity = 600;
}
if ($_POST["scope"] == 'Year'){
  $endrange->sub(new DateInterval("P1Y"));
  $endrange = $endrange->format('Y-m-d H:i:s');
  $granularity = 10600;
}

// Query Database
$result = mysqli_query($conn, "SELECT ID, logdate, windspeed, ROUND(temperature * 9/5 + 29, 2) AS temperature
FROM DataTable
WHERE logdate BETWEEN '$endrange' AND '$startrange'
AND ID % '$granularity' = 0
ORDER BY ID ASC");

// Output array to Json Object
$output = array();
$i = 0;
while ($row = mysqli_fetch_assoc($result)) {
      array_push($output, $row);
}

print (json_encode($output));
$conn->close();
?>