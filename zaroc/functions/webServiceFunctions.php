<?php
header('Content-Type: application/json;charset=utf-8');
$retrievedUser = $_SERVER['PHP_AUTH_USER'];
$retrievedPassword = $_SERVER['PHP_AUTH_PW'];
$time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

#Auth
function auth($user, $password)
{
  global $retrievedUser;
  global $retrievedPassword;
  if (($user != $retrievedUser) or ($password != $retrievedPassword)) {
    echo "Put your Credentials Correctly";
    die();
  }
}

function auth2($user)
{
  global $retrievedUser;
  if (($user != $retrievedUser)) {
    echo "Put your username Credential Correctly";
    die();
  }
}

// INTENTS and FILTERS

function intent($intentName)
{
  global $input;
  if ($input["queryResult"]["intent"]["displayName"] == $intentName) {
    return true;
  } else {
    return false;
  }
}

function action($actionName)
{
  global $input;
  if ($input["queryResult"]["action"] == $actionName) {
    return true;
  } else {
    return false;
  }
}

// PARAMETERS

function getIntentParameter()
{
  global $input;
  if (isset($input["queryResult"]["parameters"])) {
    return $input["queryResult"]["parameters"];
  } else {
    return false;
  }
}

function getContextParameter()
{
  global $input;

  $arrays = $input["queryResult"]["outputContexts"];
  rsort($arrays);
  return $arrays[0]["parameters"];
}


// TRIGGERS

function triggerEvent(array $eventName, array $params)
{
  $raw = '{
    "followupEventInput": {
      "name": "' . $eventName[0] . '",
      "languageCode": "en-US",
      "parameters": {';

  foreach ($params as $param => $value):
    $raw .= ' "' . $param . '":"' . $value . '",';
  endforeach;
  $raw = rtrim($raw, ",");
  $raw .= '}}}' . PHP_EOL;
  echo $raw;
}

function triggerError(array $session, array $errorMessage)
{
  $intent = getIntent();
  if ($session[0]) {
    echo ('{"fulfillmentMessages": [{"text": {"text": ["' . $intent[0] . "💥: " . $errorMessage[0] . '"],"outputContexts":[' . $session[0] . ']}}]}' . PHP_EOL);
  } else {
    echo ('{"fulfillmentMessages": [{"text": {"text": ["' . $intent[0] . "💥: " . $errorMessage[0] . '"]}}]}' . PHP_EOL);
  }
}


function triggerPropmt(array $session, array $prompt)
{
  if ($session[0]) {
    $raw = '{"fulfillmentMessages": [{"text": {"text": ["' . $prompt[0] . '"]}}],"outputContexts":[';
    $raw .= $session[0];
    $raw .= ']}' . PHP_EOL;
    echo ($raw);
  } else {
    echo ('{"fulfillmentMessages": [{"text": {"text": ["' . $prompt[0] . '"]}}]}' . PHP_EOL);
  }
}


// CONTEXT

function setContextParameters(array $parameters)
{
  global $input;

  $arrays = $input["queryResult"]["outputContexts"];
  rsort($arrays);
  $context = $arrays[0];

  foreach ($parameters as $variable => $value):
    $context["parameters"]["$variable"] = $value;
  endforeach;

  $context = json_encode($context);
  return [$context];

}


// TROUBLESHOOT

function getInput()
{
  global $input;

  return $input;
}

function getIntent()
{
  global $input;

  $intent = $input["queryResult"]["intent"]["displayName"];
  return [$intent];
}

// set a the name that your file with the request will have
function createInput($fileName)
{
  if (is_array($fileName)) {
    $fileName = $fileName[0];
  }
  global $input;

  $raw = json_encode($input);
  $file = fopen("$fileName.txt", "w");
  fwrite($file, $raw . PHP_EOL);
  fclose($file);

}

function createOutput($fileName, $raw)
{
  global $input;

  if (is_array($fileName)) {
    $fileName = $fileName[0];
  }
  if (is_array($raw)) {
    $raw = $raw[0];
  }
  $file = fopen("$fileName.txt", "w");
  fwrite($file, $raw . PHP_EOL);
  fclose($file);
}


// retrieves data from a different source (mostly integrations)
function getSourceData($fileName)
{
  global $input;

  if (is_array($fileName)) {
    $fileName = $fileName[0];
  }
  $raw = $input['originalDetectIntentRequest'];
  $file = fopen("$fileName.txt", "w");
  fwrite($file, $raw . PHP_EOL);
  fclose($file);
}


function getPlatform()
{
  global $input;

  @$platform = $input["originalDetectIntentRequest"]["source"];
  if ($platform) {
    return $platform;
  } else {
    return false;
  }
}

function getTimeOut()
{
  global $time;

  $time *= 1000;
  $time = round($time, 2);
  return $time;
}

// retrieves user input
function getUserInput()
{
  global $input;

  $userInput = $input["queryResult"]['queryText'];
  return $userInput;
}


// retrieves bot input, fullfilment text

function getBotInput()
{
  global $input;

  $userInput = $input["queryResult"]['fulfillmentText'];
  return $userInput;
}


// MISCELANEOUS

// retrieves dialogflow projectname
function getProjectName()
{
  global $input;

  if (isset($input["session"])) {
    $inputs = explode("/", $input["session"]);
    $projectName = 1;
    return $inputs[$projectName];
  } else {
    return false;
  }
}

// retrieves dialogflow session id
function getSessionId()
{
  global $input;

  if (isset($input["session"])) {
    $inputs = explode("/", $input["session"]);
    $session = 4;
    return $inputs[$session];
  } else {
    return false;
  }
}

// retrieves whatsapp phone number
function getWhatsAppPhoneNumber()
{
  global $input;

  $userPhone = substr(intval(preg_replace('/[^0-9]+/', '', $input["session"]), 10), -10);
  return $userPhone;
}


//  retrieves facebook contact id
function getContactId()
{
  global $input;

  if ($input['originalDetectIntentRequest']['payload']['contact']['cId']) {
    $contactId = $input['originalDetectIntentRequest']['payload']['contact']['cId'];
  } else {
    $contactId = "contact Id";
  }
  return $contactId;
}

//  retrieves telegram chat id
function getTelegramChatId()
{
  global $input;

  $chatId = $input["originalDetectIntentRequest"]["payload"]["data"]["chat"]["id"];
  return $chatId;
}


function getTelegramCallbackId()
{
  global $input;

  if ($input['originalDetectIntentRequest']['payload']['data']['callback_query']['id']) {
    return $input['originalDetectIntentRequest']['payload']['data']['callback_query']['id'];
  } else {
    return false;
  }

}

