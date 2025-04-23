<?php
// -----------------------------------------------------------------------------
// Configuración
// -----------------------------------------------------------------------------
$apiVersion  = 'v22.0';
$tokenWord   = 'Perianza';
$tokenId     = 'EAAJeBk2tNIEBO1C6hxdbTjT4jWxMGLF8T092LaPxhg6oehxm8uL5B2O8U3UmOSjHtxJi2DdoHybXbpfQVrfehvdthCcnQFWBkdLNpWd09AWNKillgjp9y2gvd7dACKf6NW2b7q3IlJjWZBLId2U8cZBorld0ZBfJpkmUrKi0ZCHKx7hHAejTr00zq3bbAMiidQ6U1mpziqlWICgoPLLsjPvV7IgRnoQqaHoZD';
$phoneId     = '598265826711939';
$graphUrl    = "https://graph.facebook.com/{$apiVersion}/{$phoneId}/messages";
$defaultMenu = [
    "📌 Hola, visita mi web botsmexico para más información.",
    "📋 Ingresa un número para recibir información:",
    "1️⃣ Información del Curso",
    "2️⃣ Ubicación del local",
    "3️⃣ Temario en PDF",
    "4️⃣ Audio explicativo",
    "5️⃣ Video de Introducción",
    "6️⃣ Hablar con Perianza",
    "7️⃣ Horario de Atención"
];

// -----------------------------------------------------------------------------
// Helpers
// -----------------------------------------------------------------------------

/**
 * Hace un POST a la Graph API de WhatsApp.
 */
function callGraphApi(array $payload): array
{
    global $graphUrl, $tokenId;

    $options = [
        'http' => [
            'method'        => 'POST',
            'header'        => "Content-Type: application/json\r\nAuthorization: Bearer {$tokenId}\r\n",
            'content'       => json_encode($payload),
            'ignore_errors' => true,
        ]
    ];
    $ctx      = stream_context_create($options);
    $response = file_get_contents($graphUrl, false, $ctx);

    return $response
        ? json_decode($response, true)
        : ['error' => ['message' => 'No se pudo conectar a la API']];
}

/**
 * Envía un mensaje por WhatsApp según tipo y contenido.
 */
function sendWhatsAppMessage(string $to, string $type, array $content): bool
{
    $payload = array_merge([
        'messaging_product' => 'whatsapp',
        'recipient_type'    => 'individual',
        'to'                => $to,
        'type'              => $type,
    ], [$type => $content]);

    $resp = callGraphApi($payload);
    if (isset($resp['error'])) {
        error_log("WhatsApp API error: " . $resp['error']['message']);
        return false;
    }
    return true;
}

/**
 * Procesa la verificación de webhook (GET).
 */
function handleVerification(): void
{
    global $tokenWord;
    if (
        ($_GET['hub_mode'] ?? '') === 'subscribe' &&
        ($_GET['hub_verify_token'] ?? '') === $tokenWord &&
        isset($_GET['hub_challenge'])
    ) {
        echo $_GET['hub_challenge'];
    } else {
        http_response_code(403);
    }
    exit;
}

/**
 * Procesa un webhook entrante (POST).
 */
function handleWebhook(): void
{
    $input = json_decode(file_get_contents('php://input'), true);
    $entry = $input['entry'][0]['changes'][0]['value'] ?? null;
    if (! $entry || empty($entry['messages'] ?? [])) {
        // nada que hacer
        http_response_code(200);
        exit;
    }

    $msg    = $entry['messages'][0];
    $from   = $msg['from'];
    $body   = mb_strtolower($msg['text']['body'] ?? '');

    // Logging básico
    file_put_contents('log.txt', json_encode(['from'=>$from,'body'=>$body]) . PHP_EOL, FILE_APPEND);

    // Lógica de respuestas
    switch (true) {
        case strpos($body, 'hola') !== false:
            $text = 'Hola, visita mi web botsmexico';
            sendWhatsAppMessage($from, 'text', ['preview_url'=>false,'body'=>$text]);
            break;

        case $body === '1':
            sendWhatsAppMessage($from, 'text', ['preview_url'=>false,'body'=>str_repeat("Lorem ipsum ", 20)]);
            break;

        case $body === '2':
            sendWhatsAppMessage($from, 'location', [
                'latitude'  => '-12.067158831865067',
                'longitude' => '-77.03377940839486',
                'name'      => 'Estadio Nacional del Perú',
                'address'   => 'Cercado de Lima'
            ]);
            break;

        case in_array($body, ['3','4','5','6','7'], true):
            // aquí podrías mapear rápidamente el resto de opciones...
            // ejemplo para '3' (document)
            if ($body === '3') {
                sendWhatsAppMessage($from, 'document', [
                    'link'    => 'http://s29.q4cdn.com/175625835/files/doc_downloads/test.pdf',
                    'caption' => 'Temario del Curso #001'
                ]);
            }
            break;

        case strpos($body, 'gchatgpt:') === 0:
            $prompt = trim(str_replace('gchatgpt:', '', $body));
            // Lógica para llamar a OpenAI y luego enviar la respuesta...
            break;

        case preg_match('/gracias|ad[ií]os|bye|chao/', $body):
            sendWhatsAppMessage($from, 'text', ['preview_url'=>false,'body'=>'¡Gracias a ti! 😊']);
            break;

        default:
            // Menú por defecto
            sendWhatsAppMessage($from, 'text', [
                'preview_url' => false,
                'body'        => implode("\n", $GLOBALS['defaultMenu'])
            ]);
    }

    echo 'EVENT_RECEIVED';
    exit;
}

// -----------------------------------------------------------------------------
// Router principal
// -----------------------------------------------------------------------------
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        handleVerification();
        break;
    case 'POST':
        handleWebhook();
        break;
    default:
        http_response_code(405);
        echo 'Método no permitido';
        break;
}
