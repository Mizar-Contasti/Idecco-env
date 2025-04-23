<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


if(intent("saludar")){

$webTitle = ["test"];
$imageArray = ["https://picsum.photos/200/150"];
$aTitleArray = ["Card Title"];
$aSubtitleArray = ["Card Subtitle"];
$aTextArray = ["Card Accordeon Text"];

$bTitleArray = ["Button Title 1","ButtonTitle2"];
$bCustomArray = ["button Custom 1","button Custom 2"];
$bIconArray = ["home","home"];
$bColorArray = ["#00efff","#000fff"];


$structure = [
                'image',
                'comma',
                'card',
                'superDivider',
                'button'
            ];

$components = [
                [$imageArray,$webTitle],
                [],
                [$aTitleArray,$aSubtitleArray,$aTextArray],
                [],
                [$bTitleArray,$bCustomArray,$bIconArray,$bColorArray]
            ];

webStructureTemplate($session, $structure, $components);


}

if (intent("test")) {

    $session = [];

    $wsHeaderType = ["image"];
    $wsHeaderContent = ["https://picsum.photos/500"];
    $wsBodyContent = ["body text"];
    $wsFooterContent = ["footer text"];
    $wsButtonTitleArray = ["button1", "button2"];
    $wsButtonCustomArray = ["button1", "button2"];

    $structure = [
        'button',
    ];

    $components = [
        [$wsHeaderType, $wsHeaderContent, $wsBodyContent, $wsFooterContent, $wsButtonTitleArray, $wsButtonCustomArray],
    ];

    wsStructureTemplate($session, $structure, $components);

}


if (intent('api')) {

    $client = new Client();

    $url = '';
    $body = "";

    try {
        // Realizar la solicitud POST
        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey,
            ],
            'json' => $body, // El cuerpo se envía como JSON
        ]);

        // Extraer y retornar la respuesta
        $responseBody = json_decode($response->getBody()->getContents(), true);

        // print_r($responseBody);

    } catch (RequestException $e) {
        // Capturar errores de Guzzle
        if ($e->hasResponse()) {
            $errorResponse = $e->getResponse();
            $errorMessage = $errorResponse->getBody()->getContents();
        } else {
            $errorMessage = $e->getMessage();
        }
        $prompt = ["Error:  $errorMessage"];
    } catch (Exception $e) {
        // Capturar cualquier otro error
        $prompt = ['General Error: ' . $e->getMessage()];
    }

    triggerPropmt($session, $prompt);

}


