#include <Arduino.h>
#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>

/*
En el ESP8266, el pin D3 es RST_PIN y
el pin D4 es SS_PIN
*/
#define RST_PIN D3
#define SS_PIN D4

// Crea una instancia del lector RFID MFRC522 y una clave para la autenticación
MFRC522 reader(SS_PIN, RST_PIN);
MFRC522::MIFARE_Key key;

// Define las credenciales de WiFi (reemplaza con tu propio SSID y contraseña)
const char *ssid = "SSID";
const char *password = "CONTRASEÑA";

/*
La dirección IP o del servidor. Si estás en localhost, coloca la IP de tu computadora (por ejemplo http://192.168.1.65)
Si el servidor está en línea, coloca el dominio del servidor, por ejemplo, https://parzibyte.me
*/
const String SERVER_ADDRESS = "http://192.168.1.77/asistencia-php-rfid";

void setup()
{
  // Conéctate a la red WiFi
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED)
  {
    delay(1000);
  }

  SPI.begin();

  // Inicializa el lector RFID
  reader.PCD_Init();
  // Espera algunos segundos
  delay(4);

  // Prepara la clave de seguridad para las funciones de lectura y escritura
  // Normalmente es 0xFFFFFFFFFFFF
  // Nota: 6 proviene de MF_KEY_SIZE en MFRC522.h
  for (byte i = 0; i < 6; i++)
  {
    key.keyByte[i] = 0xFF; // keyByte está definido en la estructura "MIFARE_Key" en el archivo .h de la librería
  }
}

void loop()
{
  // Si no está conectado a WiFi, no es necesario leer nada
  if (WiFi.status() != WL_CONNECTED)
  {
    return;
  }

  // Pero si hay una conexión, comprobamos si hay una nueva tarjeta para leer

  // Reinicia el bucle si no hay una nueva tarjeta presente en el lector
  if (!reader.PICC_IsNewCardPresent())
  {
    return;
  }

  // Selecciona una de las tarjetas. Esto devuelve false si la lectura no es exitosa y, si eso sucede, detenemos el código
  if (!reader.PICC_ReadCardSerial())
  {
    return;
  }

  /*
    En este punto estamos seguros de que hay una tarjeta que se puede leer y que hay
    una conexión estable. Así que leemos el ID y lo enviamos al servidor
  */

  String serial = "";
  for (int x = 0; x < reader.uid.size; x++)
  {
    // Si es menor que 10, agregamos un cero
    if (reader.uid.uidByte[x] < 0x10)
    {
      serial += "0";
    }
    // Transforma el byte a hexadecimal
    serial += String(reader.uid.uidByte[x], HEX);
    // Agrega un guión
    if (x + 1 != reader.uid.size)
    {
      serial += "-";
    }
  }
  // Transforma a mayúsculas
  serial.toUpperCase();

  // Detiene la tarjeta PICC
  reader.PICC_HaltA();
  // Detiene la encriptación en PCD
  reader.PCD_StopCrypto1();

  HTTPClient http;

  // Envía el ID de la etiqueta en un parámetro GET
  const String full_url = SERVER_ADDRESS + "/rfid_register.php?serial=" + serial;
  http.begin(full_url);

// puede que esté mal lo de arriba, probar con la linea de abajo
  // const String SERVER_ADDRESS = "http://localhost/asistencia-php-rfid-main";


  // Realiza la solicitud
  int httpCode = http.GET();
  if (httpCode > 0)
  {
    if (httpCode == HTTP_CODE_OK)
    {
      // const String &payload = http.getString().c_str(); // Obtiene la carga de respuesta de la solicitud
    }
    else
    {
    }
  }
  else
  {
  }

  http.end(); // Cierra la conexión
}
