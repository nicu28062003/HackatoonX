#include <SPI.h>
#include <MFRC522.h>
#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>

#define SS_PIN  D2  //D2
#define RST_PIN D1  //D1

MFRC522 mfrc522(SS_PIN, RST_PIN); // Crează instanța MFRC522.

const char *ssid = "nicu";
const char *password = "11111111";
const char* device_token  = "3263636165346439";

String baseURL = "http://192.168.204.12/";
String getData, Link;
String CurrentCardID = "";
boolean loggedIn = false;
unsigned long previousMillis = 0;
unsigned long logoutDelay = 3000;  // Timpul în milisecunde pentru a declanșa logout-ul

void setup() {
  delay(1000);
  Serial.begin(115200);
  SPI.begin();
  mfrc522.PCD_Init();
  connectToWiFi();
}

void loop() {
  if(!WiFi.isConnected()){
    connectToWiFi();
  }
  
  if ( mfrc522.PICC_IsNewCardPresent()) {
    if ( mfrc522.PICC_ReadCardSerial()) {
      String CardID ="";
      for (byte i = 0; i < mfrc522.uid.size; i++) {
        CardID += mfrc522.uid.uidByte[i];
      }
      
      if( CardID != CurrentCardID ){
        CurrentCardID = CardID;
        login(CurrentCardID);
        loggedIn = true;
        // Reset the timer when a card is detected
        previousMillis = millis();
      }
    }
  } else {
    if (loggedIn && (millis() - previousMillis >= logoutDelay)) {
      logout();
      loggedIn = false;
    }
    CurrentCardID = ""; // Resetează CurrentCardID când cardul este scos
  }
}

void login(String Card_uid) {
  String loginURL = baseURL + "getdata.php";
  SendCardID(Card_uid, loginURL, "login");
}

void logout() {
  String logoutURL = baseURL + "logout.php";
  SendCardID(CurrentCardID, logoutURL, "logout");

  // Afișează mesajul de logout pe Serial Monitor
  Serial.println("Logout efectuat");

  // Trimite un mesaj de logout către server
  if (WiFi.isConnected()) {
    HTTPClient http;
    String logoutMessage = "Dispositivul cu ID " + String(device_token) + " a fost delogat.";
    getData = "?message=" + logoutMessage;
    Link = baseURL + "logout_message.php" + getData; // Schimbă "logout_message.php" cu scriptul real pentru mesajul de logout
    WiFiClient client;
    http.begin(client, Link);
    int httpCode = http.GET();
    String payload = http.getString();

    // Afișează răspunsul serverului pe Serial Monitor
    Serial.println("Cod HTTP pentru mesajul de logout: " + String(httpCode));
    Serial.println("Răspuns de la server: " + payload);

    // Închide conexiunea HTTP
    http.end();
  }
}


void SendCardID(String Card_uid, String url, String action){
  Serial.println("Trimitere ID card");
  if(WiFi.isConnected()){
    HTTPClient http;
    getData = "?card_uid=" + String(Card_uid) + "&device_token=" + String(device_token) + "&action=" + action ;
    Link = url + getData;
    WiFiClient client;
    http.begin(client, Link);
    int httpCode = http.GET();
    String payload = http.getString();

    Serial.println(httpCode);
    Serial.println(Card_uid);
    Serial.println(payload);

    if (httpCode == 200) {
      if (payload.substring(0, 5) == "login") {
        String user_name = payload.substring(5);
      }
      else if (payload.substring(0, 6) == "logout") {
        String user_name = payload.substring(6);
      }
      else if (payload == "succesful") {
      }
      else if (payload == "available") {
      }
      delay(100);
      http.end();
    }
  }
}

void connectToWiFi(){
  WiFi.mode(WIFI_OFF);
  delay(1000);
  WiFi.mode(WIFI_STA);
  Serial.print("Conectare la ");
  Serial.println(ssid);
  WiFi.begin(ssid, password);
  
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.println("Conectat");
  Serial.print("Adresa IP: ");
  Serial.println(WiFi.localIP());
  delay(1000);
}
