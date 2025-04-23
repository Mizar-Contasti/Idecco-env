<?php

// '{
//     "fulfillmentMessages": [
//       {
//         "payload":';

function externalRequiringNumber(array $text, array $buttonText)
{

    $raw = '{
    "telegram": {
      "text": "' . $text[0] . '",
      "reply_markup": {
        "one_time_keyboard": true,
        "resize_keyboard": true,
        "keyboard": [
          [
            {
              "request_contact": true,
              "text": "' . $buttonText[0] . '",
              "callback_data": "phone"
            }
          ]
        ]
      }
    }
  }';

    echo $raw;
}







// '}]}' . PHP_EOL;



?>