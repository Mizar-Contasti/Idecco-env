<?php

function sendEmail($toEmail, $subject, $emailType, $variables) {
    // 1. Cargar el contenido HTML desde el archivo

    if($emailType == "terms_es") {
        $htmlFilePath = __DIR__."/terms_es.html";
    } else  if ($emailType == "terms_en") {
        $htmlFilePath =  __DIR__."/terms_en.html";
    } else if ($emailType == "cookie_en"){

        // $values = [
        //     'updateDate' => '2024-11-13',
        //     'userPreferences' => 'language settings and dark mode',
        //     'languagePreferences' => 'English',
        //     'cookieManagementLink' => 'https://www.allaboutcookies.org',
        //     'partnerName' => 'Google',
        //     'notificationMethod' => 'email',
        //     'contactEmail' => 'support@example.com'
        // ];
        

        $htmlFilePath = __DIR__."/cookie_en.html";
    } else if ($emailType == "cookie_es"){
        $htmlFilePath = __DIR__."/cookie_es.html";
    }


    if (!file_exists($htmlFilePath)) {
        throw new Exception("HTML file not found.");
    }
    $htmlContent = file_get_contents($htmlFilePath);

    // 2. Reemplazar las variables dentro del contenido HTML
    foreach ($variables as $key => $value) {
        // Realiza el reemplazo, donde la variable en el HTML es algo como {{variable}}
        $htmlContent = str_replace("{{{$key}}}", $value, $htmlContent);
    }

    // 3. Configuración de encabezados de correo
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: admin@botsmexico.com" . "\r\n";

    // 4. Enviar el correo
    if (mail($toEmail, $subject, $htmlContent, $headers)) {
        return "Email sent successfully to $toEmail.";
    } else {
        throw new Exception("Failed to send email.");
    }
}

