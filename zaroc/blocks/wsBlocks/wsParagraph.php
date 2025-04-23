<?php

function wsParagraph(array $wsTextArray)
{
    echo ('{
        "type":
        "text",
        "text":{
            "preview_url":"false",
            "body":"' . $wsTextArray[0] . '"
        }
    }');
}


?>