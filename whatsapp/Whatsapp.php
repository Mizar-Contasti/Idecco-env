<?php

class Whatsapp
{
    private $APIurl;
    private $token;
    private $source;
    private $data;

    /**
     * Constructor: inicializa token, fuente y carga datos del webhook.
     */
    public function __construct(string $source, string $bot_token)
    {
        $this->source = $source;
        $this->token  = $bot_token;
        $this->APIurl = 'https://graph.facebook.com/v22.0/'
            . $source
            . '/messages?access_token=' . $bot_token;

        // Leer JSON de entrada solo una vez
        $raw         = file_get_contents('php://input');
        $this->data  = json_decode($raw, true) ?: [];
    }

    /**
     * Envía un mensaje de texto (o array de textos) con pausa opcional.
     */
    public function sendMessage(string $chatId, array $texts, int $pause = 3): array
    {
        $texts   = array_filter($texts, fn($t) => $t !== '' && $t !== null);
        $results = [];

        foreach ($texts as $i => $message) {
            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type'    => 'individual',
                'to'                => $chatId,
                'type'              => 'text',
                'text'              => ['preview_url' => false, 'body' => $message]
            ];
            $json     = json_encode($payload);
            $results[] = $this->sendRequest($json);

            if (count($texts) > 1 && $i < count($texts) - 1) {
                sleep($pause);
            }
        }

        return $results;
    }

    /**
     * Envía contenido "puro": maneja mensajes individuales o listas.
     */
    public function rawPure(string $chatId, array $content): array
    {
        $results = [];

        // Determinar lista de elementos a enviar
        if (isset($content['element']) && is_array($content['element'])) {
            $items = $content['element'];
        } elseif (array_is_list($content)) {
            $items = $content;
        } else {
            $items = [$content];
        }

        foreach ($items as $msg) {
            // Delay opcional
            if (isset($msg['time']) && $msg['time'] > 0) {
                sleep(intval($msg['time']));
            }

            // Corregir payload de texto simple
            if (isset($msg['text']) && is_string($msg['text'])) {
                $msg['text'] = ['preview_url' => false, 'body' => $msg['text']];
            }

            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type'    => 'individual',
                'to'                => $chatId
            ] + $msg;

            $json = json_encode($payload);
            // Log payload para depuración
            file_put_contents(__DIR__ . '/whatsapp_payload.log', $json . PHP_EOL, FILE_APPEND);

            $results[] = $this->sendRequest($json);
        }

        return $results;
    }

    /**
     * Envía una lista interactiva.
     */
    public function sendList(string $chatId, array $content, array $buttons): array
    {
        $template = [
            'type'   => 'list',
            'body'   => [],
            'action' => ['button' => 'Menu', 'sections' => [['title' => 'Menu', 'rows' => $buttons]]]
        ];

        if (isset($content['header'])) {
            $template['header'] = is_array($content['header'])
                ? $content['header']
                : ['type' => 'text', 'text' => $content['header']];
        }
        if (isset($content['body'])) {
            $template['body'] = is_array($content['body'])
                ? $content['body']
                : ['type' => 'text', 'text' => $content['body']];
        }
        if (isset($content['footer'])) {
            $template['footer'] = ['text' => $content['footer']];
        }
        if (isset($content['button'])) {
            $template['action']['button'] = $content['button'];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => $chatId,
            'type'              => 'interactive',
            'interactive'       => $template
        ];

        return [$this->sendRequest(json_encode($payload))];
    }

    /**
     * Obtiene la URL de un archivo multimedia.
     */
    public function getFileUrl(): ?string
    {
        $msg = $this->data['entry'][0]['changes'][0]['value']['messages'][0] ?? [];
        if (!isset($msg['image']['id'])) {
            return null;
        }

        $url = 'https://graph.facebook.com/v22.0/'
             . $msg['image']['id']
             . '?access_token=' . $this->token;

        $ch   = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($ch);
        curl_close($ch);

        $j = json_decode($resp, true);
        return $j['url'] ?? null;
    }

    /**
     * Indica si el mensaje NO es propio.
     */
    public function fromMe(): bool
    {
        return !isset($this->data['entry'][0]['changes'][0]['value']['messages'][0]['type']);
    }

    /**
     * ID de chat del usuario.
     */
    public function chatId(): ?string
    {
        return $this->data['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'] ?? null;
    }

    /**
     * Texto del mensaje o reply id.
     */
    public function Text(): ?string
    {
        $msg = $this->data['entry'][0]['changes'][0]['value']['messages'][0] ?? [];
        return $msg['text']['body']
            ?? $msg['interactive']['button_reply']['id']
            ?? $msg['interactive']['list_reply']['title']
            ?? null;
    }

    /**
     * Envía la petición cURL a la API.
     */
    private function sendRequest(string $json): array
    {
        $ch = curl_init($this->APIurl);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => $json,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $resp = curl_exec($ch);
        curl_close($ch);

        return json_decode($resp, true) ?: [];
    }
}
