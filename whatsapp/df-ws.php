<?php
require_once "Whatsapp.php";
require_once "../vendor/autoload.php";

use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryInput;

date_default_timezone_set('America/Mexico_City');

// -----------------------------------------------------------------------------
// CONFIGURACIÓN
// -----------------------------------------------------------------------------
define('VERIFY_TOKEN',     'Perianza');
$source                 = "598265826711939";
$bot_token              = "EAAJeBk2tNIEBO1C6hxdbTjT4jWxMGLF8T092LaPxhg6oehxm8uL5B2O8U3UmOSjHtxJi2DdoHybXbpfQVrfehvdthCcnQFWBkdLNpWd09AWNKillgjp9y2gvd7dACKf6NW2b7q3IlJjWZBLId2U8cZBorld0ZBfJpkmUrKi0ZCHKx7hHAejTr00zq3bbAMiidQ6U1mpziqlWICgoPLLsjPvV7IgRnoQqaHoZD";
$jsonCredentialsPath    = "credenciales/creds.json";
$projectId              = 'uveg-bot-pybt';  // <-- reemplaza con tu Project ID

// -----------------------------------------------------------------------------
// FUNCIÓN DE LOG
// -----------------------------------------------------------------------------
function writeLog(string $msg): void {
    $ts = date('Y-m-d H:i:s');
    file_put_contents(__DIR__ . '/app.log', "[$ts] $msg" . PHP_EOL, FILE_APPEND);
}

// -----------------------------------------------------------------------------
// VERIFICACIÓN DE WEBHOOK (GET)
// -----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    writeLog("GET recibido para verificación: " . json_encode($_GET));
    if (
        isset($_GET['hub_mode'], $_GET['hub_verify_token'], $_GET['hub_challenge'])
        && $_GET['hub_mode'] === 'subscribe'
        && $_GET['hub_verify_token'] === VERIFY_TOKEN
    ) {
        writeLog("Token verificado correctamente, enviando challenge.");
        echo $_GET['hub_challenge'];
    } else {
        writeLog("Falló la verificación del token.");
        http_response_code(403);
    }
    exit;
}

// -----------------------------------------------------------------------------
// PROCESAMIENTO DE MENSAJES (POST)
// -----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawBody = file_get_contents('php://input');
    writeLog("Raw POST body: $rawBody");

    $whatsappResponse = json_decode($rawBody, true);
    if (! $whatsappResponse) {
        writeLog("JSON inválido en POST.");
        http_response_code(400);
        exit;
    }

    // Ignorar status updates
    if (isset($whatsappResponse['entry'][0]['changes'][0]['value']['statuses'][0]['status'])) {
        writeLog("Status update recibido, sin acción.");
        http_response_code(200);
        exit;
    }

    writeLog("Procesando mensaje entrante...");

    $whatsapp = new Whatsapp($source, $bot_token);
    if ($whatsapp->fromMe()) {
        writeLog("Mensaje propio detectado, se ignora.");
        http_response_code(200);
        exit;
    }

    // Extracción de datos
    $chat    = $whatsapp->chatId();
    $waText  = $whatsapp->Text();
    writeLog("De: chatId=$chat, texto='$waText'");

    // Inicializar Dialogflow
    try {
        $sessionsClient = new SessionsClient([
            'credentials' => $jsonCredentialsPath
        ]);
        $session = $sessionsClient->sessionName($projectId, $chat ?: uniqid());
        writeLog("Sesión Dialogflow iniciada: $session");

        // Construir consulta
        $textInput  = (new TextInput())->setText($waText)->setLanguageCode('es');
        $queryInput = (new QueryInput())->setText($textInput);
        writeLog("Enviando a Dialogflow: " . json_encode([
            'session' => $session,
            'text'    => $waText
        ]));

        // Detectar intención
        $response    = $sessionsClient->detectIntent($session, $queryInput);
        $queryResult = $response->getQueryResult();
        writeLog("Respuesta de Dialogflow recibida: " . $queryResult->getFulfillmentText());

        // Payload de webhook (si lo usas)
        $payload = $queryResult->getWebhookPayload()
            ? json_decode($queryResult->getWebhookPayload()->serializeToJsonString(), true)
            : ['text' => $queryResult->getFulfillmentText()];
        writeLog("Payload para WhatsApp: " . json_encode($payload));

        // Envío a WhatsApp
        $sendResult = $whatsapp->rawPure($chat, $payload);
        writeLog("rawPure() llamado, resultado: " . var_export($sendResult, true));

        $sessionsClient->close();
    } catch (Exception $e) {
        writeLog("Error en Dialogflow o envío: " . $e->getMessage());
    }

    // Aceptamos el webhook
    http_response_code(200);
    echo 'EVENT_RECEIVED';
    exit;
}

// -----------------------------------------------------------------------------
// MÉTODOS NO SOPORTADOS
// -----------------------------------------------------------------------------
writeLog("Método HTTP no soportado: " . $_SERVER['REQUEST_METHOD']);
http_response_code(405);
echo 'Método no permitido';
