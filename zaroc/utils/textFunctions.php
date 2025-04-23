<?php
//------------------------------------TEXTS---------------------------------//

$br = '</br>';
$n = '\n';

function id(){
$c = uniqid();
// $id = md5($c);
return $c;
}

function id2(){
  $c = uniqid();
  $id = md5($c);
  return $id;
}

function createSpan($text,$values){

  $span = "<span $values >$text</span>";
  return $span;
}




#Check last caracter of a word
function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if (!$length)
    {
        return true;
    }
    return substr($haystack, -strlen($needle)) === $needle;
}


function startsWith ($string, $startString)
{
    $len = strlen($startString);
    return (substr($string, 0, $len) === $startString);
}


#Remove blank
function reduce($text)
{
    $text = str_replace(' ', '', $text);
    return $text;
}
#Remove Gaps
function lower($text)
{
    $text = strtolower($text);
    return $text;
}

#Remove blank and Gaps
function reduceLower($text)
{
    $text = lower($text);
    $text = reduce( $text);
    return $text;
}

#To Capital and remove gaps
function titleAdjust($text)
{
    $text = str_replace('-', ' ', $text);
    $text = ucwords($text);
    return $text;
}

function adaptJson($text)
{
  $text = str_replace('"','\'',$text);
  return $text;
}

#Prepares a WhatsApp Link
function whatsAppLink($texto,$telefono){
  $telefono = reduce($telefono);
  $tlf = "52$telefono";
  $texto = rawurlencode($texto);
  $final = "https://api.whatsapp.com/send/?phone=$tlf=&text=$texto";
  return $final;

}

function textToUrlEncode($text){
  $raw = '';
  if(is_array($text)){
    for($i1 = 0; $i < count($text); $i++):
      $raw .= $text." ";
    endfor;
  $raw = rawurlencode($raw);
  return $raw;
  } else {
  $text = rawurlencode($text);
  return $text;
}
}

function createBankNumber($type){

$num = crypt(uniqid(rand()**2));
$num = hexdec($num);


  if($type== 'card'){

  $num .= '-';
  $num .= rand(1000,9999);
  $num .= '-';
  $num .= rand(1000,9999);
  $num .= '-';
  $num .= rand(1000,9999);

  return $num;

  }

  if($type=='account'){

  $num = rand(000000000000000000,999999999999999999);

  return $num;
  }


}


function pluralToSingular($name)
{

  if ($name == 'Italianas') {
    $nombre = 'Italiana';
  } else if ($name == 'Hawaianas') {
    $nombre = 'Hawaiana';
  } else {
    $nombre = $name;
  }
  return $nombre;
}

function getRandomCode($longitud)
{
  $key = '';
  $pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
  $max = strlen($pattern) - 1;
  for ($i = 0; $i < $longitud; $i++) {
    $key .= $pattern[mt_rand(0, $max)];
  }
  return $key;
}





function generateFallbackQuestion($language) {
    $phrases_en = [
      "Could you please rephrase that?",
      "I'm having trouble understanding. Can you try again?",
      "Perhaps you could word it differently?",
      "I didn't quite catch that. Can you reword it?",
      "Let's try another way to say that.",
      "Could you elaborate on that?",
      "I'm not sure what you mean. Please clarify.",
      "Can you reword your question?",
      "Could you be more specific?",
      "I'm still confused. Can you explain it differently?",
      "Please rephrase your question.",
      "Try asking it another way.",
      "Can you put it differently?",
      "I need more information. Please reword.",
      "Let's try a different approach.",
      "Please rephrase for clarity.",
      "Could you explain that in another way?",
      "I'm not following. Please reword.",
      "Can you say that again, but differently?",
      "Please provide more details.",
      "How else could you say that?",
      "Can you put a different spin on it?",
      "Let's try a new angle.",
      "I'm curious, can you rephrase?",
      "Tell me another way to think about it.",
      "Can you reword it in simpler terms?",
      "Let's try a fresh perspective.",
      "I'm listening, but I need more clarity.",
      "Can you say that differently for me?",
      "Help me understand. Rephrase, please.",
      "I'm eager to understand. Please rephrase.",
      "Your input is valuable. Please reword.",
      "I'm open to different perspectives. Please rephrase.",
      "Let's find the right words together. Please rephrase.",
      "Your unique viewpoint matters. Please rephrase.",
      "I'm all ears. Please reword your question.",
      "Let's explore this further. Please rephrase.",
      "I'm interested in learning more. Please reword.",
      "Your thoughts are important. Please rephrase.",
      "Let's work together to clarify. Please rephrase.",
      "Please rephrase for better understanding.",
      "A different phrasing would be helpful.",
      "I need a clearer explanation. Please rephrase.",
      "Your request is unclear. Please reword.",
      "Please try asking again, differently.",
      "I'm unable to assist without clarification. Please rephrase.",
      "Your query is confusing. Please reword.",
      "Please refine your question.",
      "I'm not understanding. Please rephrase.",
      "Please provide a clearer explanation."
    ];
    
    $phrases_es = [
       "¿Podrías reformular eso, por favor?",
    "Estoy teniendo dificultades para entender. ¿Puedes intentarlo de nuevo?",
    "Quizás podrías expresarlo de otra manera.",
    "No capté bien eso. ¿Podrías reformularlo?",
    "Intentemos decirlo de otra manera.",
    "¿Podrías profundizar en eso?",
    "No estoy seguro de lo que quieres decir. Por favor, acláralo.",
    "¿Podrías reformular tu pregunta?",
    "¿Podrías ser más específico?",
    "Todavía estoy confundido. ¿Puedes explicarlo de otra manera?",
    "Por favor, reformula tu pregunta.",
    "Intenta preguntarlo de otra manera.",
    "¿Podrías decirlo de otra forma?",
    "Necesito más información. Por favor, reformula.",
    "Intentemos un enfoque diferente.",
    "Por favor, reformula para mayor claridad.",
    "¿Podrías explicar eso de otra manera?",
    "No te sigo. Por favor, reformula.",
    "¿Puedes decirlo de nuevo, pero de forma diferente?",
    "Por favor, proporciona más detalles.",
    "¿Cómo podrías decirlo de otra manera?",
    "¿Puedes darle un giro diferente?",
    "Intentemos desde un nuevo ángulo.",
    "Tengo curiosidad, ¿puedes reformular?",
    "Dime otra forma de pensar en eso.",
    "¿Puedes reformularlo en términos más simples?",
    "Intentemos con una nueva perspectiva.",
    "Estoy escuchando, pero necesito más claridad.",
    "¿Podrías decirlo de otra manera para mí?",
    "Ayúdame a entender. Reformula, por favor.",
    "Quiero entender. Por favor, reformula.",
    "Tu opinión es valiosa. Por favor, reformula.",
    "Estoy abierto a diferentes perspectivas. Por favor, reformula.",
    "Busquemos las palabras correctas juntos. Por favor, reformula.",
    "Tu punto de vista único importa. Por favor, reformula.",
    "Estoy atento. Por favor, reformula tu pregunta.",
    "Explorémoslo más a fondo. Por favor, reformula.",
    "Estoy interesado en aprender más. Por favor, reformula.",
    "Tus pensamientos son importantes. Por favor, reformula.",
    "Trabajemos juntos para aclararlo. Por favor, reformula.",
    "Por favor, reformula para una mejor comprensión.",
    "Una redacción diferente sería útil.",
    "Necesito una explicación más clara. Por favor, reformula.",
    "Tu solicitud no está clara. Por favor, reformula.",
    "Intenta preguntar de nuevo, de forma diferente.",
    "No puedo ayudarte sin una aclaración. Por favor, reformula.",
    "Tu consulta es confusa. Por favor, reformula.",
    "Por favor, afina tu pregunta.",
    "No estoy entendiendo. Por favor, reformula.",
    "Por favor, proporciona una explicación más clara."
    ];

    if ($language === 'en') {
        return $phrases_en[array_rand($phrases_en)];
    } elseif ($language === 'es') {
        return $phrases_es[array_rand($phrases_es)];
    } else {
        return "Invalid language parameter. Please use 'en' or 'es'.";
    }
}

