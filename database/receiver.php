<?php
require("config.php");

date_default_timezone_set('US/Central');
$now = new DateTime();
parse_str( html_entity_decode( $_SERVER['QUERY_STRING']) , $data);

if ( array_key_exists( 'windspeed' , $data ) ) {

  try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $datenow = $now->format('Y-m-d H:i:s');
  $windspeed = ($data['windspeed'] <= 1 ? 0 : $data['windspeed'] * .68);
  $temperature = $data['temperature'] - 4.7;
    
  // These next two values are from the DHT11 Sensor
  $humidity = $data['humidity'];
  $temperature2 = $data['temperature2'] - 4.7;
  
  $sql = "INSERT INTO DataTable (logdate, windspeed, temperature, humidity, temperature2) VALUES ('$datenow', '$windspeed', '$temperature', '$humidity', '$temperature2')";

  // Prepare statement
  $stmt = $conn->prepare($sql);

  // execute the query
  $stmt->execute();

  // echo a message to say the UPDATE succeeded
  echo $stmt->rowCount() . " records INSERTED successfully";
  }
  catch(PDOException $e)
  {
  echo $sql . "<br>" . $e->getMessage();
  }

$conn = null;
}
?>