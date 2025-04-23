<?php


function fbExternalCard( array $fbTitleArray, array $bTitleArray,array $bCustomArray){
  $raw = '{"facebook":{
      "attachment":{
        "type":"template",
        "payload":{
          "template_type":"button",
          "text": "'.addslashes($fbTitleArray[0]).'",';
            $raw .= fbExternalButton($bTitleArray,$bCustomArray);
            $raw .='}}}}';
            echo($raw);
}




?>
