/*
  Weather Station
  Gets data from
    Ultrasonic Sensor (to measure windspeed from passing blades)
    Temperature sensor
  Sends data to web server over wifi
*/

// Libraries
#include <ESP8266WiFi.h>
#include <Wire.h>
#include <Adafruit_MCP9808.h>

// Constants
#define ECHOPIN 12 // Pin to receive echo pulse
#define TRIGPIN 15 // Pin to send trigger pulse
/*
  Wifi Variables (included in credentials.h)
  const char* ssid     = "";
  const char* password = "";
 */
#include "credentials.h"
int sleeptime = 30; // seconds to sleep
const char* host = "weather.mcmillanthomas.com";

// Variables
int bladePassCounter = 0;   // counter for the number of button presses
int lastButtonState = 0;     // previous state of the button
bool bladePassing = false;
float windspeed = 0.00;
float temp = 22.00;

// Create the MCP9808 temperature sensor object
Adafruit_MCP9808 tempsensor = Adafruit_MCP9808();

void setup() {
  // Serial.begin(115200);
  pinMode(ECHOPIN, INPUT);
  pinMode(TRIGPIN, OUTPUT);

  // Connect to Wifi Network
  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    //Serial.print(".");
  }
  //Serial.println("");
  //Serial.println("WiFi connected");

  // Get Windspeed
  int i;
  int n = 300;
  for (i = 0; i < n; i++) {   
    digitalWrite(TRIGPIN, LOW); // Set the trigger pin to low for 2uS
    delayMicroseconds(2);
    digitalWrite(TRIGPIN, HIGH); // Send a 10uS high to trigger ranging
    delayMicroseconds(10);
    digitalWrite(TRIGPIN, LOW); // Send pin low again
    int distance = pulseIn(ECHOPIN, HIGH); // Read in times pulse
    distance = distance / 58; // Calculate distance from time of pulse
    
    if (bladePassing == true) {
      if (distance > 10) {
        bladePassing = false;
      }
    } else {
      if (distance < 10) {
        bladePassing = true;
        bladePassCounter++;
      }
    }
    //delay before next ranging
    delay(10);
  } 

  // Get Temperature
  // Make sure the sensor is found, you can also pass in a different i2c
  // address with tempsensor.begin(0x19) for example
  if (!tempsensor.begin()) {
    //Serial.println("Couldn't find MCP9808!");
    while (1);
  }
  tempsensor.wake();   // wake up, ready to read!
  temp = tempsensor.readTempC();
  tempsensor.shutdown(); // shutdown MSP9808 - power consumption ~0.1 mikro Ampere

  // Send Data over Wifi
  // Use WiFiClient class to create TCP connections
  WiFiClient client;
  const int httpPort = 80;
  if (!client.connect(host, httpPort)) {
    return;
  }
  
  String url = "/database/receiver.php";

  windspeed = (bladePassCounter * .60); //calculates the wind speed (in mi/hr)
  // This will send the request to the server
  client.print(String("GET ") + url + "?windspeed=" + windspeed + "&temperature=" + temp + " HTTP/1.1\r\n" +
               "Host: " + host + "\r\n" +
               "Connection: close\r\n\r\n");
               
  bladePassCounter = 0;

  // Sleep for sleeptime seconds
  ESP.deepSleep(sleeptime * 1000000);
}

void loop() {
}