<?php

include_once("./config.php");
include_once("./lib/loader.php");
include_once("./load_settings.php");
include_once("./modules/vkmessenger/vkmessenger.class.php");

$vkmessenger_module = new vkmessenger();
$vkmessenger_module->getConfig();

if (!isset($_REQUEST)) {
  exit;
}

$event = json_decode(file_get_contents('php://input'), true);

switch ($event['type']) {
  //Подтверждение сервера
  case 'confirmation':
    _callback_response($vkmessenger_module->config['WEBHOOK_CODE']);
    break;

  //Получение нового сообщения
  case 'message_new':
    $vkmessenger_module->processMessage($event);
    break;      
  case 'message_reply':
    break;

  default:
    $vkmessenger_module->processMessage($event);
    break;
}

_callback_response('ok');

function _callback_response($data) {
  echo $data;
  exit();
}