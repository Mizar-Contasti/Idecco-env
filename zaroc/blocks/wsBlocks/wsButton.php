<?php

function wsButton(
  array $wsHeaderType,
  array $wsHeaderContent,
  array $wsBodyContent,
  array $wsFooterContent,
  array $wsButtonTitleArray,
  array $wsButtonCustomArray
) {
  $raw = '{
        "type": "interactive",
        "interactive": {
          "type": "button",';

  if ($wsHeaderType[0] == "image") {
    $raw .= '"header": {
              "type": "image",
              "image": {
                "link": "' . $wsHeaderContent[0] . '"
              }
            },';
  } else if ($wsHeaderType[0] == "text") {
    $raw .= '"header": {
      "type": "text",
      "text": "' . $wsHeaderContent[0] . '"
    },';
  } else if ($wsHeaderType[0] == "document") {

    if ($wsHeaderContent[1]) {

      $raw .= '"header": {
        "type": "document",
        "document": {
          "link": "' . $wsHeaderContent[0] . '",
          "filename": "' . $wsHeaderContent[1] . '"
        }},';

    } else {
      $raw .= '"header": {
        "type": "document",
        "document": {
          "link": "' . $wsHeaderContent[0] . '",
          "filename": "file.pdf"
        }},';
    }
  } else if ($wsHeaderType[0] == "video") {

    $raw .= '"header": {
      "type": "video",
      "video": {
        "link": "' . $wsHeaderContent[0] . '"
      }
    },';

  } else {
    $raw .= '"header": {
      "type": "text",
      "text": "No Header type defined at wsButton"
    },';
  }


  $raw .= '
          "body": {
            "text": "' . $wsBodyContent[0] . '"
          },
          "footer": {
            "text": "' . $wsFooterContent[0] . '"
          },
          "action": {
            "buttons": [';


  for ($i = 0; $i < count($wsButtonTitleArray); $i++) {
    $raw .= '{
                              "type": "reply",
                              "reply": {
                                "id": "' . $wsButtonCustomArray[$i] . '",
                                "title": "' . $wsButtonTitleArray[$i] . '"
                              }
                            },';
  }
  $result = rtrim($raw, ",");


  $result .= ']
          }
        }
      }';

  echo ($result);
}



?>