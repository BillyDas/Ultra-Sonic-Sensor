import mysql.connector as mariadb
from mysql.connector import Error
from mysql.connector import errorcode
import sys
import serial
import json
import random
import time

vacancy = None
OutPut = None
parkedduration = None
parked = None
parkedprevious = 0
ArrayLength = 0

#DB connection variables
sys.stdout.write("-----Starting Database Connection-----\n" )
connection = mariadb.connect(user="pi", password="root", database="individual")
cursor = connection.cursor(buffered=True)

#Check that all our tables exist, and set them up if they dont
try:
    query = "CREATE TABLE IF NOT EXISTS CurrentlyParked (Parked int(1) NOT NULL, Primary KEY(Parked));"
    cursor.execute(query)
    query = "CREATE TABLE IF NOT EXISTS ParkedDuration (ID int(10) AUTO_INCREMENT, ParkTime DOUBLE(6,2) NOT NULL, NumberPlate varchar(6) NOT NULL, Primary KEY(ID));"
    cursor.execute(query)
    query = "CREATE TABLE IF NOT EXISTS ParkingUsers (NumberPlate varchar(6) NOT NULL, Email varchar(30) NOT NULL, Name varchar(15) NOT NULL, Primary KEY(NumberPlate));"
    cursor.execute(query)
    query ="INSERT IGNORE INTO ParkingUsers (NumberPlate, Email, Name)VALUES" \
            "('1CD2SD','wardude202@hotmail.com', 'BillyDas')," \
            "('1LS2MD','billy.das@hotmail.com', 'BillyDasWork')," \
            "('0LD9KD','10115315@student.swin.edu.au', 'Student Email')," \
            "('6DF4HJ','SpamBam1234@hotmail.com', 'SpamEmail')," \
            "('8KS5LK','ShopAndShipIt@hotmail.com', 'ShopAndShipIt');";
    print(query)
    cursor.execute(query)
    connection.commit()
except:
    print("Tables Created And Data Inserted")
# Serial commmunication
try:
    ser = serial.Serial('/dev/ttyACM0', 115200)
    ser.flushInput()
    ser.flushOutput()
    print("Serial Uplink Established")
except:
    print("Yo we couldnt connect to the arduino")
    
#Checks if there is a car parked
def get_parked_status():
    global OutPut
    try:
        Temp = ser.readline()
        Temp = Temp.decode('utf-8')
        OutPut = json.loads(Temp)
    except (json.decoder.JSONDecodeError, UnicodeDecodeError):
        print("Error With Input")
    global vacancy
    vacancy = OutPut["Vacancy"]
    time.sleep(1)
    return vacancy
    
    
#If car parked == 1 then increment time
while True:
    get_parked_status()
    if vacancy == 1:
        query = "UPDATE CurrentlyParked set parked = 1 where parked = 0"
        cursor.execute(query)
        connection.commit()
        print("Car Is Parked")
        if parkedprevious == 0:
            global parkedprevious
            global starttime
            starttime = time.time()
            parkedprevious = 1
    elif vacancy == 0:
        query = "UPDATE CurrentlyParked set parked = 0 where parked = 1"
        cursor.execute(query)
        connection.commit()
        print ("Car Is Not Parked")
        if parkedprevious == 1:
            global parkedduration
            endtime = time.time()
            parkedduration = endtime - starttime
            parkedduration = parkedduration / 60
            parkedduration = str(parkedduration)
            print("Parked For " + parkedduration + " Minutes")
            parkedprevious = 0
            
    if parkedduration != None:
        global parkedduration
        comparison = float(parkedduration)
        if not(comparison < .1):
            global ArrayLength
            query = "SELECT NumberPlate FROM ParkingUsers"
            cursor.execute(query)
            NumberPlates = cursor.fetchall()
            ArrayLength = len(NumberPlates)
            NumberChosen = random.randint(0,ArrayLength)
            NumberChosen = NumberChosen - 1
            NumPlateChosen = NumberPlates[NumberChosen]
            NumPlateChosen = "".join(NumPlateChosen)

            try:
                query = "INSERT INTO ParkedDuration (ParkTime, NumberPlate) VALUES" \
                "('" + parkedduration + "','" + NumPlateChosen + "')"
                cursor.execute(query)
                connection.commit()
            except mysql.connector.Error as error :
                print("Failed to work". format(error))
            print("Placed Park Time In Database")
            parkedduration = None
cursor.close()