#include <ArduinoJson.h>
// Ultrasonic HC-SR04 unit interface
// Uses serial port at 115200 baud for communication
// use trig pin for output, echo pin for input
// pulse out (10us) on trig initiates a cycle
// pulse width returned on echo is proportional to distance
// specs say 38ms = no return (beyond limit), but 90ms and more have been seen
// set utimeout when calling usonic (routine will take longer for longer returns)
// higher timeout measures further, but can take longer if no echo
// if return >= utimeout, no valid pulse received
// if return < ~100 unit is faulty/disconnected (routine is timing out waiting for start of return)
// if return == 0 then unit is still sending return from last ping (or is faulty)
// maximum nominal range is 5m => utimeout = 29000 us
// call usonicsetup() during setup
// call usonic(timeout) to get return time in microseconds
// divide result of usonic by 58 to get range in cm
//define pins here
#define TRIG 12
#define ECHO 13
#define USMAX 3000
int timeset = 0;
#define GREEN 11
#define RED 10
const int capacity = JSON_OBJECT_SIZE(2);
StaticJsonDocument<capacity> doc;


void setup() {
 Serial.begin(115200); //open serial port
 PortSetup(); //set up ultrasonic sensor
}
void loop() {
 int distance; //variable to store distance
 distance=usonic(11600)/58; //distance in cm, time out at 11600us or 2m maximum range
 sizeof(distance);
 if (distance < 30) {
  timeset += 250;
  doc["Vacancy"].set(1);
  doc["Duration"].set(timeset);
  String CurrentParkingStatusTaken = "1"; //If the parking system is currently taken it will output a 1 into the bit length
  digitalWrite(RED, HIGH);
  digitalWrite(GREEN, LOW);
  Serial.println(CurrentParkingStatusTaken);
 } else {
  doc["Vacancy"].set(0);
  doc["Duration"].set(timeset);
  String CurrentParkingStatusVacant = "0"; //If the parking system is currently Vacant it will output a 0 into the bit length
  digitalWrite(RED, LOW);
  digitalWrite(GREEN, HIGH);
  Serial.println(CurrentParkingStatusVacant);
 }
 delay(250); //wait a bit so we don't overload the serial port
 serializeJson(doc, Serial);
}

//PIN SETUP
void PortSetup(void){
 pinMode(ECHO, INPUT);
 pinMode(TRIG, OUTPUT);
 pinMode(GREEN, OUTPUT);
 pinMode(RED, OUTPUT);
 digitalWrite(TRIG, LOW);

 
}
//ULTRA SONIC SENSOR READING DATA
long usonic(long utimeout) { //utimeout is maximum time to wait for return in us
 long b;
 if(digitalRead(ECHO)==HIGH){return 0;} //if echo line is still low from last result, return 0;
 digitalWrite(TRIG, HIGH); //send trigger pulse
 delay(1);
 digitalWrite(TRIG, LOW);
 long utimer=micros();
 while((digitalRead(ECHO)==LOW)&&((micros()-utimer)<1000)){} //wait for pin state to changereturn starts after 460us typically or timeout (eg if not connected)
 utimer=micros();
 while((digitalRead(ECHO)==HIGH)&&((micros()-utimer)<utimeout)){} //wait for pin state to change
 b=micros()-utimer;
 if(b==0){b=utimeout;}
 return b;
}
