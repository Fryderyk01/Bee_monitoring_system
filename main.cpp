#include <WiFi.h>
#include <HTTPClient.h>
#include "DHT.h"

#define DHTPIN 4      // Pin, do którego podłączony jest DHT22
#define DHTTYPE DHT22 // Typ czujnika

const char *ssid = "";
const char *password = "";
const char *serverUrl = "http://192.168.0.234/stacja_pogodowa/save_data.php"; // IP serwera XAMPP
const char *deviceName = "ESP32_Weather_Station";

// Ustawienie unikalnego ID ręcznie
String deviceID = "BEE_KEEPER_01"; // Tutaj możesz ustawić swoje unikalne ID

DHT dht(DHTPIN, DHTTYPE);

void setup()
{
  Serial.begin(115200);
  WiFi.begin(ssid, password);
  WiFi.setHostname(deviceName);
  Serial.print("Laczenie z WiFi");

  while (WiFi.status() != WL_CONNECTED)
  {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nPolaczono z WiFi");
  dht.begin();
}

void loop()
{
  float temperature = dht.readTemperature();
  float humidity = dht.readHumidity();

  if (isnan(temperature) || isnan(humidity))
  {
    Serial.println("Nie mozna odczytac danych z czujnika!");
    return;
  }

  Serial.printf("Temperatura: %.2f C, Wilgotnosc: %.2f%%\n", temperature, humidity);

  if (WiFi.status() == WL_CONNECTED)
  {
    HTTPClient http;
    String url = serverUrl;

    // Dodajemy do URL ID urządzenia
    url += "?device_id=" + deviceID + "&temperature=" + String(temperature) + "&humidity=" + String(humidity);

    Serial.println("Wysylanie danych do serwera: " + url);

    http.begin(url);
    int httpResponseCode = http.GET();

    if (httpResponseCode > 0)
    {
      String response = http.getString();
      Serial.printf("Odpowiedz serwera: %d, tresc: %s\n", httpResponseCode, response.c_str());
    }
    else
    {
      Serial.printf("Blad wysylania: %s\n", http.errorToString(httpResponseCode).c_str());
    }

    http.end();
  }
  else
  {
    Serial.println("Brak polaczenia z WiFi!");
  }

  delay(60000); // Wysyłanie danych co 60 sekund
}
