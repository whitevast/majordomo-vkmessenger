<?php
/**
* vkmessenger 
* @package project
* @author Wizard <sergejey@gmail.com>
* @copyright http://majordomo.smartliving.ru/ (c)
*/
//
const DEBUG = 0;
const V_API = "5.199";
const CASH_PATH = ROOT . "cms/cached" . DIRECTORY_SEPARATOR . "vkmessenger" . DIRECTORY_SEPARATOR;

class vkmessenger extends module {
	
/**
* vkmessenger
*
* Module class constructor
*
* @access private
*/
function __construct() {
  $this->name="vkmessenger";
  $this->title="VK мессенджер";
  $this->module_category="<#LANG_SECTION_APPLICATIONS#>";
  $this->checkInstalled();
  $this->getConfig();
  $this->debug=$this->config['LOG_DEBMES'] == 1 ? true : false;
  if(!is_dir(CASH_PATH)) mkdir(CASH_PATH, 0777, true);
}
/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
function saveParams($data=1) {
 $p=array();
 if (isset($this->id)) {
  $p["id"]=$this->id;
 }
 if (isset($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (isset($this->edit_mode)) {
  $p["edit_mode"]=$this->edit_mode;
 }
 if (isset($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}
/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;
  global $mode;
  global $view_mode;
  global $edit_mode;
  global $tab;
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }
}
/**
* Run
*
* Description
*
* @access public
*/
function run() {
 global $session;
  $out=array();
  if ($this->action=='admin') {
   $this->admin($out);
  } else {
   $this->usual($out);
  }
  if (isset($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (isset($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  $out['TAB']=$this->tab;
  $this->data=$out;
  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;
}
/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {
 $this->getConfig();
 $out['API_KEY']=$this->config['API_KEY'] ?? '';
 $out['GROUP_ID']=$this->config['GROUP_ID'] ?? '';
 $out['GROUP_NAME']=$this->config['GROUP_NAME'] ?? '';
 $out['VK_STORAGE']=$this->config['VK_STORAGE'] ?? '';
 $out['VK_COUNT_ROW'] = $this->config['VK_COUNT_ROW'] ?? 3;
 $out['VK_PLAYER'] = $this->config['VK_PLAYER'] ?? 1;
 $out['VK_WEBHOOK']=$this->config['VK_WEBHOOK'] ?? 0;
 $out['WEBHOOK_URL']=$this->config['WEBHOOK_URL'] ?? '';
 $out['LOG_DEBMES']=$this->config['LOG_DEBMES'] ?? 0;
 $out['CLUB']=$this->config['GROUP_ID'] ? "club" . $this->config['GROUP_ID'] : '';
 
 $getlog = gr('getlog');
 $filter = gr('filter');
 $limit = gr('limit');
 if($getlog) {
     header("HTTP/1.0: 200 OK\n");
     header('Content-Type: text/html; charset=utf-8');
     //$limit = 50;
     if (defined('SETTINGS_SYSTEM_DEBMES_PATH') && SETTINGS_SYSTEM_DEBMES_PATH!='') {
         $path = SETTINGS_SYSTEM_DEBMES_PATH;
     } elseif (defined('LOG_DIRECTORY') && LOG_DIRECTORY!='') {
         $path = LOG_DIRECTORY;
     } else {
         $path = ROOT . 'cms/debmes';
     }
     $filename=$path.'/'.date('Y-m-d').'/vkmessenger.log';
     if (!file_exists($filename))
     {
         echo "Empty log...";
         exit;
     }
     // Open file
     $data = LoadFile($filename);
     $lines = explode("\n", $data);
     $lines = array_reverse($lines);
     $res_lines = array();
     $total = count($lines);
     $added = 0;
     for($i = 0; $i < $total; $i++) {
         if(trim($lines[$i]) == '') {
             continue;
         }
         if($filter && preg_match('/' . preg_quote($filter) . '/is', $lines[$i])) {
             $res_lines[] = $lines[$i];
             $added++;
         } elseif(!$filter) {
             $res_lines[] = $lines[$i];
             $added++;
         }
         if($added >= $limit) {
             break;
         }
     }
     echo implode("<br/>", $res_lines);
     exit;
 }
 
 $webhookinfo = gr('webhookinfo');
  if ($webhookinfo){
      $data = $this->vkApi_call('groups.getCallbackServers');
	  foreach($data['items'] as $server){
		  if($server['title'] == "MajorDoMo"){
			  if($server['url'] == $this->config['WEBHOOK_URL']."/webhook_vkmessenger.php"){
				echo '<font color="green">УСТАНОВЛЕН</font>';
				if($server['status'] == 'ok') echo '<font color="green">, АКТИВЕН</font>';
				exit;
			  }
		  }
	  }
	  echo '<font color="red">НЕ УСТАНОВЛЕН</font>';
      exit;
  }
  $setwebhook = gr('setwebhook');
  if ($setwebhook){
  	  $vk_webhook_url = gr('vk_webhook_url');
      $this->config['WEBHOOK_URL'] = $vk_webhook_url;
	  $this->config['VK_WEBHOOK'] = 1;
      $this->saveConfig();
	  $id = 0;
	  $data = $this->vkApi_call('groups.getCallbackServers');
	  foreach($data['items'] as $server){
		if($server['url'] == $this->config['WEBHOOK_URL']."/webhook_vkmessenger.php"){
			if($server['status'] == 'ok'){
				echo '<font color="red">Сервер уже установлен!</font>';
				exit;
			}
			$id = $server['id'];
		}
	   }
	  if($id == 0){
		$code = $this->vkApi_call('groups.getCallbackConfirmationCode');
		$this->config['WEBHOOK_CODE'] = $code['code'];
		$this->saveConfig();
		$data = $this->vkApi_call('groups.addCallbackServer', array(
			'url' => $this->config['WEBHOOK_URL']."/webhook_vkmessenger.php",
			'title' => "MajorDoMo",
		));
		$id = $data['server_id'];
	  }
	  $this->vkApi_call('groups.setCallbackSettings', array(
	  		'server_id' => $id,
			'api_version' => V_API,
			'message_new' => 1,
			'message_event' => 1,
			'message_typing_state' => 1,
			'message_read' => 1,
			'message_edit' => 1,
			'photo_new' => 1,
	  ));
	  $this->vkApi_call('groups.setLongPollSettings', array( //отключаем LongPoll
			'enabled' => 0,
	  ));
	  setGlobal('cycle_vkmessengerControl','restart');
      echo '<font color="green">OK</font>';
      exit;
  }
  $cleanwebhook = gr('cleanwebhook');
  if ($cleanwebhook){
	echo $this->whClean();
    exit;
  }
 
 if ($this->view_mode=='update_settings') {
   $new = false;
   if(empty($this->config['API_KEY'])) $new = true;
   $this->config['API_KEY'] = gr('api_key');
   $this->config['GROUP_ID'] = '';
   $group = $this->vkApi_call('groups.getById');
   $group = $group['groups'][0];
   $this->config['GROUP_ID'] = $group['id'];
   $this->config['GROUP_NAME'] = $group['name'];
   $this->downloadFile($group['photo_200'], CASH_PATH . $group['id'] . ".jpg");
   $vk_storage = gr('vk_storage');
   if(substr($vk_storage, -1) != DIRECTORY_SEPARATOR) $vk_storage = $vk_storage . DIRECTORY_SEPARATOR;
   $this->config['VK_STORAGE'] = $vk_storage;
   $this->config['VK_COUNT_ROW'] = gr('vk_count_row');
   if($this->config['VK_COUNT_ROW'] > 5) $this->config['VK_COUNT_ROW'] = 5;
   $this->config['VK_PLAYER'] = gr('vk_player');
   $this->config['LOG_DEBMES'] = gr('log_debmes');
   
   $this->usersUpdate();
   if($new){
		$this->vkApi_call('groups.setLongPollSettings', array(
			'enabled' => 1,
			'api_version' => V_API,
			'message_new' => 1,
			'message_event' => 1,
			'message_typing_state' => 1,
			'message_read' => 1,
			'message_edit' => 1,
			'photo_new' => 1,
		));
		$managers = $this->vkApi_call('groups.getMembers', array(
			'filter' => 'managers',
		));
		foreach($managers['items'] as $manager){
			if($manager['role'] = 'creator'){
				$rec=SQLSelectOne("SELECT * FROM vk_user WHERE USER_ID='".$manager['id']."'");
				$rec['ADMIN'] = 1;
				$rec['HISTORY'] = 1;
				$rec['HISTORY_LEVEL'] = 0;
				SQLUpdate('vk_user', $rec); // update
			}
		}
   }
   $this->saveConfig();
   $vk_webhook = gr('vk_webhook');
   if($vk_webhook != $this->config['VK_WEBHOOK']){
	  if(!$vk_webhook) $this->whClean();
   }
   setGlobal('cycle_vkmessengerControl','restart');
   $this->redirect("?");
  }
  if (isset($this->data_source) && !isset($_GET['data_source']) && !isset($_POST['data_source'])) {
   $out['SET_DATASOURCE']=1;
  }
  $update_user_info = gr('update_user_info');
  if ($update_user_info) {
  	$this->usersUpdate(true);
  	$this->redirect("?");
  }
  $update_keyboard = gr('update_keyboard');
  if ($update_keyboard) {
  	$this->sendMessageToAll("Обновление клавиатуры", '', true);
  	$this->redirect("?tab=cmd");
  }
  if ($this->data_source=='vkmessenger' || $this->data_source=='') {
	if ($this->view_mode=='delete_vkmessenger') {
	$this->delete_vkmessenger($this->id);
	$this->redirect("?");
	}
	
	$sendMessage = gr('sendMessage');
	if ($sendMessage){
		header("HTTP/1.0: 200 OK\n");
		header('Content-Type: text/html; charset=utf-8');
		$user = gr('user');
		$text = gr('text') ?? '';
		$image = gr('image') ?? '';
		$silent = gr('silent') ?? '';
		if ($image != '' && file_exists($image)) {
			$this->sendImageToUser($user, $image, $text);
		}
		else if ($text != '') {
			$this->sendMessageToUser($user, $text);
		}
		echo "Ok";
		exit;
	}
	if($this->view_mode == 'user_edit') {
		$id = $this->id;
		require(DIR_MODULES . $this->name . '/user_edit.inc.php');
	}
	if($this->view_mode == 'cmd_edit') {
		$id = $this->id;
		require(DIR_MODULES . $this->name . '/cmd_edit.inc.php');
	}
	if($this->view_mode == 'event_edit') {
		$id = $this->id;
		require(DIR_MODULES . $this->name . '/event_edit.inc.php');
	}
	if($this->view_mode == 'cmd_delete') {
		$this->delete_cmd($this->id);
		$this->redirect("?tab=cmd");
	}
	if($this->view_mode == 'event_delete') {
		$this->delete_event($this->id);
		$this->redirect("?tab=events");
	}
	if($this->view_mode == 'history_delete') {
		$this->delete_history($this->id);
		$this->redirect("?tab=history");
	}
	if ($this->view_mode=='export_command') {
		$this->export_command($out, $this->id);
	}
	if ($this->view_mode=='import_command') {
		$this->import_command($out);
		$this->redirect("?tab=cmd");
	}
	if ($this->view_mode=='export_event') {
		$this->export_event($out, $this->id);
	}
	if ($this->view_mode=='import_event') {
		$this->import_event($out);
		$this->redirect("?tab=events");
	}
	
	
	
	if($this->view_mode == '' || $this->view_mode == 'search_ms') {
		if($this->tab == 'cmd') {
			require(DIR_MODULES . $this->name . '/vk_cmd.inc.php');
		} else if($this->tab == 'events') {
			require(DIR_MODULES . $this->name . '/vk_events.inc.php');
		} else if($this->tab == 'history') {
			require(DIR_MODULES . $this->name . '/vk_history.inc.php');
		} else if($this->tab == 'log') {
			require(DIR_MODULES . $this->name . '/vk_log.inc.php');
		} else {
			require(DIR_MODULES . $this->name . '/vk_users.inc.php');
		}
	}
 }
}

 function export_command(&$out, $id) {
     $command=SQLSelectOne("SELECT * FROM vk_cmd WHERE ID='".(int)$id."'");
     unset($command['ID']);
     $data=json_encode($command);
     $filename="Command_VKmessenger_".urlencode($command['TITLE']).".txt";
     $this->export_file($filename,$data);
 }
 function import_command(&$out) {
     global $file;
	 $overwrite = gr('overwrite');
     if(!empty($file)){
		$data=LoadFile($file);
		$this->export_rec("vk_cmd",$data,$overwrite);
		$this->redirect("?tab=cmd");
	 } else {
		 $out['ERR']=1;
	 }
 }
 function export_event(&$out, $id) {
     $event=SQLSelectOne("SELECT * FROM vk_event WHERE ID='".(int)$id."'");
     unset($event['ID']);
     $data=json_encode($event);
     $filename="Event_VKmessenger_".urlencode($event['TITLE']).".txt";
     $this->export_file($filename,$data);
 }
 function import_event(&$out) {
     global $file;
	 $overwrite = gr('overwrite');
     if(!empty($file)){
		$data=LoadFile($file);
		$this->export_rec("vk_event",$data,$overwrite);
		$this->redirect("?tab=events");
	 } else {
		 $out['ERR']=1;
	 }
 }
 
  function export_file($filename,$data){
     $ie = false;
     $ua = htmlentities($_SERVER['HTTP_USER_AGENT'], ENT_QUOTES, 'UTF-8');
     if (preg_match('~MSIE|Internet Explorer~i', $ua) || (strpos($ua, 'Trident/7.0') !== false && strpos($ua, 'rv:11.0') !== false))
         $ie = true;
     if(!$ie)
         $mime_type = 'application/octetstream';
     else
         $mime_type = 'application/octet-stream';
     header('Content-Type: ' . $mime_type);
     if(!$ie)
     {
         header('Content-Disposition: inline; filename="' . $filename . '"');
         header("Content-Transfer-Encoding: binary");
         header('Expires: 0');
         header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
         header('Pragma: public');
         print $data;
     } else {
         header('Content-Disposition: attachment; filename="' . $filename . '"');
         header("Content-Transfer-Encoding: binary");
         header('Expires: 0');
         header('Pragma: no-cache');
         print $data;
     }
     exit;
 }
 
 function export_rec($table,$data,$overwrite){
     $data=json_decode(preg_replace("/^\xEF\xBB\xBF/", '', $data),true);
     if (is_array($data)) 
     {
         $rec=SQLSelectOne("SELECT * FROM ".$table." WHERE TITLE='". DBSafe($data["TITLE"]) . "'");
         if ($rec['ID'])
         {
             if ($overwrite)
             {
                 $data['ID'] = $rec['ID'];
                 SQLUpdate($table, $data); // update
             }
             else
             {
                 $data["TITLE"] .= "_copy";
                 SQLInsert($table, $data); // adding new record
             }
         }
         else
             SQLInsert($table, $data); // adding new record
     }
 }
 
 function delete_cmd($id) {
     $rec = SQLSelectOne("SELECT * FROM vk_cmd WHERE ID='$id'");
     // some action for related tables
     SQLExec("DELETE FROM vk_cmd WHERE ID='" . $rec['ID'] . "'");
     SQLExec("DELETE FROM vk_user_cmd WHERE CMD_ID='" . $rec['ID'] . "'");
 }
 function delete_event($id) {
     $rec = SQLSelectOne("SELECT * FROM vk_event WHERE ID='$id'");
     // some action for related tables
     SQLExec("DELETE FROM vk_event WHERE ID='" . $rec['ID'] . "'");
 }


/**
* FrontEnd
*
* Module frontend
*
* @access public
*/
function usual(&$out) {
 $this->admin($out);
}

 function processMessage($data) {
	$skip = false;
	$this->getConfig();
	if(DEBUG) print_r($data);
	if($data['type'] == 'message_new'){ //обрабатываем входящее сообщение
	$message = $data['object']['message'];
		$user = SQLSelectOne("SELECT * FROM vk_user WHERE USER_ID LIKE '" . DBSafe($message['from_id']) . "'");
		$user_id = $message['from_id'];
		$chat_id = $user_id;
		$text = $message['text'];
		if(!empty($message['payload'])){
			$payload = $message['payload'];
			$payload_data = json_decode($payload, true);
			if(is_array($payload_data)){
				//если это кнопка, то выполним её код
				if(isset($payload_data['type']) and $payload_data['type'] == 'button'){
					$but_id = $payload_data['id'];
					$cmd = SQLSelectOne("SELECT CODE FROM vk_cmd WHERE TITLE='$but_id'");
					if(!empty($cmd['CODE'])){
						try {
							$success = eval($cmd['CODE']);
							if(!empty($success)){
								if(!isset($keyboard)) $keyboard = '';
								$this->sendMessageTo($user, $success, $keyboard);
							}
						}
						catch(Exception $e) {
							registerError('vkmessenger', sprintf('Exception in "%s" method ' . $e->getMessage(), $text));
						}
						if($skip) {
							$this->writeLog("Skip next processing events type = ".$type);
							return;
						}
					}
				}
			}
		}
		if(!empty($text)){
			if ($user['PATTERNS'] == 1) say(htmlspecialchars($text), 0, $user['MEMBER_ID'], 'vkmessenger' . $user['ID']);
		}
		if(!empty($message['attachments'])){
			if($user['DOWNLOAD']){
				foreach($message['attachments'] as $attachment){
					if($attachment['type'] == 'photo'){
						$name = empty($message['text']) ? $attachment['photo']['id'] : $message['text'];
						$name = $name . ".jpg";
						$url = $attachment['photo']['orig_photo']['url'];
						$type = 2;
					} else if($attachment['type'] == 'audio_message'){
						$name = $attachment['audio_message']['id'];
						$name = $name . ".mp3";
						$url = $attachment['audio_message']['link_mp3'];
						$type = 3;
					} else if($attachment['type'] == 'audio'){
						//Получение URL видео доступно только при подписке на VK Music
						$file_path = $attachment['audio']['url'];
						$type = 4;
					} else if($attachment['type'] == 'video'){
						//Получение URL видео доступно только доверенным приложениям
						$file_path = $attachment['video']['first_frame'][1]['url'];
						$type = 5;
					} else if($attachment['type'] == 'doc'){
						$name = $attachment['doc']['title'];
						$url = $this->getFileUrl($attachment['doc']['url']);
						$type = 6;
					} else if($attachment['type'] == 'graffiti'){
						$url = $this->getFileUrl($attachment['graffiti']['url']);
						$name = empty($message['text']) ? $attachment['graffiti']['id'] : $message['text'];
						$name = $name . ".png";
						$type = 7;
					}
					if(!empty($name)){
						if(!empty($this->config['VK_STORAGE'])) $file_path = $this->config['VK_STORAGE'] . $name;
						else $file_path = CASH_PATH . $name;
						$this->downloadFile($url, $file_path);
						if($type == 3 && $user['PLAY'] == 1) {
							//проиграть голосовое сообщение
							$this->writeLog("Play voice from " . $chat_id . " - " . $file_path);
							@touch($file_path);
							if ($this->config['VK_PLAYER'] == 2)
								playMedia($file_path, 'localhost', true);
							else
								playSound($file_path, 1, $level);
						}
					}
					if(!empty($file_path)) {
						// get events
						$events = SQLSelect("SELECT * FROM vk_event WHERE TYPE_EVENT=" . $type . " and ENABLE=1;");
						foreach($events as $event) {
							if($event['CODE']) {
								$this->writeLog("Execute code event " . $event['TITLE']);
								try {
									eval($event['CODE']);
								}
								catch(Exception $e) {
									registerError('telegram', sprintf('Exception in "%s" method ' . $e->getMessage(), $text));
								}
							}
							if($skip) {
								$this->writeLog("Skip next processing events type = ".$type);
								break;
							}
						}
					}
					$file_path = "";
				}
			}
		}
		
		if(!empty($message['geo'])){
			$location = $message['geo'];
			if($location) {
                $latitude = $location['coordinates']["latitude"];
                $longitude = $location['coordinates']["longitude"];
				$city = $location['place']["city"];
                $this->writeLog("Get location from " . $chat_id . " - " . $latitude . "," . $longitude);
                if($user['MEMBER_ID']) {
                    $sqlQuery = "SELECT * FROM users WHERE ID = '" . $user['MEMBER_ID'] . "'";
                    $userObj = SQLSelectOne($sqlQuery);
                    if($userObj['LINKED_OBJECT']) {
                        $this->writeLog("Update location to user '" . $userObj['LINKED_OBJECT']."'");
                        setGlobal($userObj['LINKED_OBJECT'] . '.Coordinates', $latitude . ',' . $longitude);
                        setGlobal($userObj['LINKED_OBJECT'] . '.CoordinatesUpdated', date('H:i'));
                        setGlobal($userObj['LINKED_OBJECT'] . '.CoordinatesUpdatedTimestamp', time());
                    }
                    if (isModuleInstalled('app_gpstrack')) {
                        getURLBackground('http://localhost/gps.php?latitude='.urlencode($latitude).'&longitude='.urlencode($longitude) .'&deviceid='.urlencode('vkmessenger'.$chat_id));
                    }
                }
                // get events for location
                $events = SQLSelect("SELECT * FROM vk_event WHERE TYPE_EVENT=8 and ENABLE=1;");
                foreach($events as $event) {
                    if($event['CODE']) {
                        $this->writeLog("Execute code event " . $event['TITLE']);
                        try {
                            eval($event['CODE']);
                        }
                        catch(Exception $e) {
                            registerError('telegram', sprintf('Exception in "%s" method ' . $e->getMessage(), $text));
                        }
						if($skip) {
							$this->writeLog("Skip next processing events location");
							break;
						}
                    }
                }
                return; //если прислана геопозиция, то дальнейшая обработка не нужна
			}
		}
		
		if($user['CMD'] == 1) {
        // Выполним события при получении текстового сообщения
            $events = SQLSelect("SELECT * FROM vk_event WHERE TYPE_EVENT=1 and ENABLE=1;");
            foreach($events as $event) {
                if($event['CODE']) {
                    $this->writeLog("Выполнение кода события " . $event['TITLE']);
                    try {
                        eval($event['CODE']);
                    }
                    catch(Exception $e) {
                        registerError('vkmessenger', sprintf('Exception in "%s" method ' . $e->getMessage(), $text));
                    }
                }
                if($skip) {
                    $this->writeLog("Skip next processing events message");
                    break;
                }
            }
        }
		//callback кнопка
	} else if($data['type'] == 'message_event'){
		$user = SQLSelectOne("SELECT * FROM vk_user WHERE USER_ID LIKE '" . DBSafe($data['object']['user_id']) . "'");
		$user_id = $data['object']['user_id'];
		$chat_id = $user_id;
		$payload = $data['object']['payload']['id'];
		$callback = $payload;
		$event_id = $data['object']['event_id'];
		$callback_id = $event_id;
		// Выполним код из кнопки
		$cmd = SQLSelectOne("SELECT CODE FROM vk_cmd WHERE TITLE='$payload'");
		if(!empty($cmd['CODE'])){
			try {
				$success = eval($cmd['CODE']);
				if(!empty($success)){
					if(!isset($keyboard)) $keyboard = '';
					$this->sendMessageTo($user, $success, $keyboard);
				}
			}
			catch(Exception $e) {
				registerError('vkmessenger', sprintf('Exception in "%s" method ' . $e->getMessage(), $text));
			}
			// пропуск дальнейшей обработки если с обработчике событий установили $skip (события обрабатываться не будут)
            if($skip) {
                $this->writeLog("Skip next processing message");
                return;
            }
		}
		// Выполним события при получении callback
		$events = SQLSelect("SELECT * FROM vk_event WHERE TYPE_EVENT=9 and ENABLE=1;");
		foreach($events as $event) {
			if($event['CODE']) {
				$this->writeLog("Выполнение кода события " . $event['TITLE']);
				try {
					eval($event['CODE']);
				}
				catch(Exception $e) {
					registerError('vkmessenger', sprintf('Exception in "%s" method ' . $e->getMessage(), $text));
				}
			}
			if($skip) {
				$this->writeLog("Skip next processing events message");
				break;
			}
		}
		//чтобы кнопка не крутилась, отправляем пустой event_data
		$this->vkApi_call('messages.sendMessageEventAnswer', array(
				'user_id' => $data['object']['user_id'],
				'peer_id' => $data['object']['peer_id'],
				'event_id'=> $data['object']['event_id'],
				'event_data'=> '',
		));
	}
 }
 
 function processSubscription($event, $details='') {
	//$this->getConfig();
	if ($event=='SAY') {
		$level=$details['level'];
		$message=$details['message'];
		$image = $details['image'];
		$users = SQLSelect("SELECT * FROM vk_user WHERE HISTORY=1");
		if($users){
			foreach($users as $user){
				if($level >= $user['HISTORY_LEVEL']){
					if ($level >= $user['HISTORY_SILENT']) $silent = false;
					else $silent = true;
					$url=BASE_URL."/ajax/vkmessenger.html?sendMessage=1&user=".$user['USER_ID']."&text=".urlencode($message)."&image=".urlencode($image)."&silent=".$silent;
					getURLBackground($url,0);
					//$this->sendMessageTo($user, $message, '', $silent);
				}
			}
		}
	}
 }
/**
* Install
*
* Module installation routine
*
* @access private
*/
 function install($data='') {
  subscribeToEvent($this->name, 'SAY');
  parent::install();
 }
/**
* Uninstall
*
* Module uninstall routine
*
* @access public
*/
 function uninstall() {
  unsubscribeFromEvent($this->name, 'SAY');
  SQLExec('DROP TABLE IF EXISTS vk_user');
  SQLExec('DROP TABLE IF EXISTS vk_cmd');
  SQLExec('DROP TABLE IF EXISTS vk_user_cmd');
  SQLExec('DROP TABLE IF EXISTS vk_event');
  SQLExec('DROP TABLE IF EXISTS vk_history');
  unlink('..//../webhook_vkmessenger.php');
  parent::uninstall();
 }
/**
* dbInstall
*
* Database installation routine
*
* @access private
*/
 function dbInstall($data) {
  $data = <<<EOD
 
 vk_user: ID int(10) unsigned NOT NULL auto_increment
 vk_user: FIRST_NAME varchar(255) NOT NULL DEFAULT ''
 vk_user: LAST_NAME varchar(255) NOT NULL DEFAULT ''
 vk_user: USER_ID varchar(25) NOT NULL DEFAULT '0'
 vk_user: MEMBER_ID int(10) NOT NULL DEFAULT '0'
 vk_user: ADMIN int(3) unsigned NOT NULL DEFAULT '0'
 vk_user: SILENT int(3) unsigned NOT NULL DEFAULT '0'
 vk_user: HISTORY int(3) unsigned NOT NULL DEFAULT '0'
 vk_user: HISTORY_LEVEL int(3) unsigned NOT NULL DEFAULT '0'
 vk_user: HISTORY_SILENT int(3) unsigned NOT NULL DEFAULT '0'
 vk_user: CMD int(3) unsigned NOT NULL DEFAULT '0'
 vk_user: PATTERNS int(3) unsigned NOT NULL DEFAULT '0'
 vk_user: DOWNLOAD int(3) unsigned NOT NULL DEFAULT '0'
 vk_user: PLAY int(3) unsigned NOT NULL DEFAULT '0'
 vk_user: UPDATED datetime

 vk_cmd: ID int(10) unsigned NOT NULL auto_increment
 vk_cmd: TITLE varchar(255) NOT NULL DEFAULT ''
 vk_cmd: DESCRIPTION text
 vk_cmd: CODE text
 vk_cmd: ACCESS int(10) NOT NULL DEFAULT '0'
 vk_cmd: SHOW_MODE int(10) NOT NULL DEFAULT '1'
 vk_cmd: TYPE int(10) NOT NULL DEFAULT '0'
 vk_cmd: DATA varchar(255) NOT NULL DEFAULT ''
 vk_cmd: COLOR int(10) NOT NULL DEFAULT '0'
 vk_cmd: LINKED_OBJECT varchar(255) NOT NULL DEFAULT ''
 vk_cmd: LINKED_PROPERTY varchar(255) NOT NULL DEFAULT ''
 vk_cmd: CONDITION int(10) NOT NULL DEFAULT '1'
 vk_cmd: CONDITION_VALUE varchar(255) NOT NULL DEFAULT ''
 vk_cmd: PRIORITY int(10) NOT NULL DEFAULT '1'

 vk_user_cmd: ID int(10) unsigned NOT NULL auto_increment
 vk_user_cmd: USER_ID int(10) NOT NULL
 vk_user_cmd: CMD_ID int(10) NOT NULL

 vk_event: ID int(10) unsigned NOT NULL auto_increment
 vk_event: TITLE varchar(255) NOT NULL DEFAULT ''
 vk_event: DESCRIPTION text
 vk_event: TYPE_EVENT int(3) unsigned NOT NULL DEFAULT '1'
 vk_event: ENABLE int(3) unsigned NOT NULL DEFAULT '0'
 vk_event: CODE text
EOD;
  parent::dbInstall($data);
 }
// --------------------------------------------------------------------

//MY FUNCTIONS
function getKeyb($user) {
	$visible = true;
	if($user['CMD'] == 0) {
		return '{"buttons": []}';
	} else {
		$button = array();
		$sql = "SELECT * FROM vk_cmd where ACCESS=3 or ((select count(*) from vk_user_cmd where vk_cmd.ID=vk_user_cmd.CMD_ID and vk_user_cmd.USER_ID=" . $user['ID'] . ")>0 and ACCESS>0) order by vk_cmd.PRIORITY asc, vk_cmd.TITLE;";
		$rec = SQLSelect($sql);
		$total = count($rec);
		if($total) {
			for($i = 0; $i < $total; $i++) {
				$view = false;
				if($rec[$i]["SHOW_MODE"] == 1)
					$view = true;
				elseif($rec[$i]["SHOW_MODE"] == 3) {
					if ($rec[$i]["LINKED_OBJECT"] && $rec[$i]["LINKED_PROPERTY"])
					{
						$val = gg($rec[$i]["LINKED_OBJECT"].".".$rec[$i]["LINKED_PROPERTY"]);
						if($val!='')
						{
							if($rec[$i]["CONDITION"] == 1 && $val == $rec[$i]["CONDITION_VALUE"])
								$view = true;
							if($rec[$i]["CONDITION"] == 2 && $val > $rec[$i]["CONDITION_VALUE"])
								$view = true;
							if($rec[$i]["CONDITION"] == 3 && $val < $rec[$i]["CONDITION_VALUE"])
								$view = true;
							if($rec[$i]["CONDITION"] == 4 && $val <> $rec[$i]["CONDITION_VALUE"])
								$view = true;
						}
					}
				}
				if($view)
					$button[] = $rec[$i];
			}
		}
	}
	$keyb = $this->buildUserKeyBoard($button);
	return $keyb;
}
/*type:
0 - text
1 - location
2 - vkpay
3 - open_link
4 - open_app
5 - callback
*/
function buildKeyBoardButton($name, $type, $payload = "", $color = "1", $data = ""){
	if(is_numeric($type)){
		switch($type){
			case 0:
				$type = "text";
				break;
			case 1:
				$type = "location";
				break;
			case 2:
				$type = "vkpay";
				break;
			case 3:
				$type = "open_link";
				break;
			case 4:
				$type = "open_app";
				break;
			case 5:
				$type = "callback";
				break;
		}
	}
	if(is_numeric($color)){
		switch($color){
			case 0:
				$color = "primary";
				break;
			case 1:
				$color = "secondary";
				break;
			case 2:
				$color = "negative";
				break;
			case 3:
				$color = "positive";
				break;
		}
	}
	$arr['type'] = $type;
	if($payload == "") $payload = $name;
	$arr['payload'] = json_encode(array('id' => $payload, 'type' => 'button')); 
	if($type != "location" and $type != "vkpay") $arr['label'] = $name;
	if($type == "vkpay") $arr['hash'] = $data;
	else if($type == "open_link") $arr['link'] = $data == '' ? $payload : $data;
	else if($type == "open_app") $arr['app_id'] = $data == '' ? $payload : $data;
	$but['action'] = $arr;
	if($type == "text" or $type == "callback") $but['color'] = $color;
	return $but;
}

function buildUserKeyBoard($buttons){
	$this->getConfig();
	$line = 0;
	$but = 0;
	$keyb['one_time'] = false;
	$keyb['inline'] = false;
	foreach($buttons as $button){
		if($button['TYPE'] == 1 or $button['TYPE'] == 3 or $button['TYPE'] == 4){
			$twobuttons[] = $button;
			continue;
		} else if($button['TYPE'] == 2){
			$vkpay = $button;
			continue;
		}
		$keyb['buttons'][$line][] = $this->buildKeyBoardButton($button['TITLE'], $button['TYPE'], '', $button['COLOR'], $button['DATA'], '');
		$but++;
		if($but == $this->config['VK_COUNT_ROW']){
			$but = 0;
			$line++;
		}
	}
	if(isset($twobuttons)){
		if($but > 1){
			$line++;
			$but = 0;
		}
		foreach($twobuttons as $twobutton){
			$keyb['buttons'][$line][$but] = $this->buildKeyBoardButton($twobutton['TITLE'], $twobutton['TYPE'],'', '', $twobutton['DATA']);
			$but++;
			if($but > 1){
				$line++;
				$but = 0;
			}
		}
	}
	if(isset($vkpay)){
		$line++;
		$keyb['buttons'][$line][0] = $this->buildKeyBoardButton($vkpay['TITLE'], $vkpay['TYPE'], '', '', $vkpay['DATA']);
	}
	return json_encode($keyb);
}
/*type:
text
location
vkpay
open_link
open_app
callback
*/
function buildKeyBoard($buttons, $inline = true, $one_time = false, $lines = ''){
	$this->getConfig();
	$buts = false;
	if(!is_bool($one_time)){
		$lines = $one_time;
		$one_time = false;
	}
	if($lines != ''){
		$count = iconv_strlen($lines);
		for($i=0;$i<$count;$i++){
			$buts[$i] = substr($lines, $i, 1);
		}
	}
	$line = 0;
	$but = 0;
	$keyb['inline'] = $inline;
	if(!$inline) $keyb['one_time'] = $one_time;
	foreach($buttons as $button){
		$type = $button['action']['type'];
		if($type == 'location' or $type == 'open_link' or $type == 'open_app'){
			$twobuttons[] = $button;
			continue;
		} else if($type == 'vkpay'){
			$vkpay = $button;
			continue;
		}
		$keyb['buttons'][$line][] = $button;
		$but++;
		if((!$buts and $but == $this->config['VK_COUNT_ROW']) or $but == $buts[$line]){
			$but = 0;
			$line++;
			if(!isset($buts[$line])) $buts = false;
		}
	}
	if(isset($twobuttons)){
		if($but > 1){
			$line++;
			$but = 0;
		}
		foreach($twobuttons as $twobutton){
			$keyb['buttons'][$line][$but] = $twobutton;
			$but++;
			if($but > 1){
				$line++;
				$but = 0;
			}
		}
	}
	if(isset($vkpay)){
		$line++;
		$keyb['buttons'][$line][0] = $vkpay;
	}
	return json_encode($keyb);
}

function buildInlineKeyBoard($buttons, $lines = ''){
	return $this->buildKeyBoard($buttons, true, false, $lines);
}

function getFileUrl($url){
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, TRUE);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
	$response = curl_exec($ch);
	preg_match_all('/^Location:(.*)$/mi', $response, $matches);
	curl_close($ch);
	return !empty($matches[1]) ? trim($matches[1][0]) : false;
}

function whClean(){
	$this->getConfig();
	$this->config['VK_WEBHOOK'] = 0;
	$this->saveConfig();
	$this->vkApi_call('groups.setLongPollSettings', array( //включаем LongPoll
			'enabled' => 1,
			));
	$data = $this->vkApi_call('groups.getCallbackServers');
	foreach($data['items'] as $server){
		if($server['title'] == "MajorDoMo" or $server['url'] == $this->config['WEBHOOK_URL']."/webhook_vkmessenger.php"){
			$this->vkApi_call('groups.deleteCallbackServer', array(
				'server_id' => $server['id'],
			));
			setGlobal('cycle_vkmessengerControl','restart');
			return '<font color="red">СЕРВЕР УДАЛЁН!</font>';
			exit;
		}
	}
	return '<font color="red">НЕ ИСПОЛЬЗУЕТСЯ!</font>';
}

function usersUpdate($forced = false){
	$dbusers = SQLSelect("SELECT * FROM vk_user");
	$vkusers = $this->vkApi_call('groups.getMembers', array(
		'fields' => 'photo_max'
	));
	foreach($vkusers['items'] as $vkuser){
		$indb = false;
		foreach($dbusers as $dbuser){
			if($vkuser['id'] == $dbuser['USER_ID']){
				$indb = true;
				if($forced or $vkuser['first_name'] != $dbuser['FIRST_NAME'] or $vkuser['last_name'] != $dbuser['LAST_NAME']){
					$dbuser['FIRST_NAME'] = $vkuser['first_name'];
					$dbuser['LAST_NAME'] = $vkuser['last_name'];
					$dbuser['UPDATED'] = date('Y-m-d H:i:s');
					SQLUpdate('vk_user', $dbuser);
					$file_path = CASH_PATH . $vkuser["id"] . ".jpg";
					$this->downloadFile($vkuser['photo_max'], $file_path);
				}
			}
		}
		if(!$indb){
			$user['USER_ID'] = $vkuser['id'];
			$user['FIRST_NAME'] = $vkuser['first_name'];
			$user['LAST_NAME'] = $vkuser['last_name'];
			$user['UPDATED'] = date('Y-m-d H:i:s');
			SQLInsert('vk_user', $user);
			$file_path = CASH_PATH . $vkuser["id"] . ".jpg";
			$this->downloadFile($vkuser['photo_max'], $file_path);
		}
	}
}

function downloadFile($url, $path) {
    $options = array(
        CURLOPT_FILE    => fopen($path, 'wb'),
        CURLOPT_TIMEOUT =>  28800,
        CURLOPT_URL     => $url
    );
    $ch = curl_init();
    curl_setopt_array($ch, $options);
    curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $httpcode;
}


function sendMessage($user_id, $message = '',$keyboard='', $silent = false, $attachments = array()){
	$format_data = '';
	$comma = substr_count($user_id, ",");
	if($comma > 0){
		$user = 'peer_ids';
		if($comma > 100){
			$positions = $this->splitAtNthOccurrence($user_id, ",", 100, false);
			$user_id = $positions['before'];
			$this->sendMessage($positions['after'], $message, $keyboard, $silent, $attachments);
		}
	} else $user = 'user_id';
	$format = $this->extractFormattedTags($message);
	if($format){
		$message = $format['message'];
		$format_data = array( 'version' => 1,
					'items' => $format['items'],
					);
		$format_data = json_encode($format_data);
		$this->writeLog($format_data);
	}
	return $this->vkApi_call('messages.send', array(
		$user		=> $user_id,
		'message'	=> $message,
		'keyboard'	=> $keyboard,
		'silent'	=> $silent,
		'format_data'=>$format_data,
		'attachment'=> implode(',', $attachments),
		'random_id'	=> 0,
	));
}

function sendMessageTo($users, $message = '',$keyboard='', $silent = false, $attachments = array()) {
	if(isset($users['ID'])) $users = [$users];
	foreach($users as $user) {
		$user_id = $user['USER_ID'];
		if($keyboard != '') {
			if(is_array($keyboard)) $keyboard = json_encode($keyboard);
		} else $keyboard = $this->getKeyb($user);
		if (!$silent) $silent = $user['SILENT'];
		$res = $this->sendMessage($user_id, $message, $keyboard, $silent, $attachments);
	}
	return $res;
}

function sendMessageToUser($user_id, $message = '',$keyboard='', $silent = false, $attachments = array()) {
	$user = SQLSelect("SELECT * FROM vk_user WHERE USER_ID='".$user_id."'");
	return $this->sendMessageTo($user, $message, $keyboard,  $silent, $attachments);
}
function sendMessageToAdmin($message, $keyboard = '', $silent = false, $attachments=array()) {
	$users = SQLSelect("SELECT * FROM vk_user WHERE ADMIN='1'");
	return $this->sendMessageTo($users, $message, $keyboard, $silent, $attachments);
}
function sendMessageToAll($message, $keyboard = '', $silent = false, $attachments=array()) {
    $users = SQLSelect("SELECT * FROM vk_user");
	return $this->sendMessageTo($users, $message, $keyboard, $silent, $attachments);
} 

function sendImageTo($image, $users, $message = '',$keyboard='', $silent = false, $attachments = array()) {
	$this->getConfig();
	if(isset($users['ID'])) $users = [$users];
	 if($image) {
	  $photo = $this->uploadPhoto($this->config['GROUP_ID'], $image);
	  $attachments = array(
		'photo'.$photo['owner_id'].'_'.$photo['id'],
	  );
  }
	foreach($users as $user) {
		$user_id = $user['USER_ID'];
		if($keyboard != '') {
			if(is_array($keyboard)) $keyboard = json_encode($keyboard);
		} else $keyboard = $this->getKeyb($user);
		if (!$silent) $silent = $user['SILENT'];
		$res = $this->sendMessage($user_id, $message, $keyboard, $silent, $attachments);
	}
	return $res;
}

function sendImageToUser($user_id, $image, $message = '',$keyboard='', $silent = false, $attachments = array()) {
	$user = SQLSelect("SELECT * FROM vk_user WHERE USER_ID='".$user_id."'");
	return $this->sendImageTo($image, $user, $message, $keyboard, $silent, $attachments);
}
function sendImageToAdmin($image, $message, $keyboard = '', $silent = false, $attachments=array()) {
	$users = SQLSelect("SELECT * FROM vk_user WHERE ADMIN='1'");
	return $this->sendImageTo($image, $users, $message, $keyboard, $silent, $attachments);
}
function sendImageToAll($image, $message, $keyboard = '', $silent = false, $attachments=array()) {
    $users = SQLSelect("SELECT * FROM vk_user");
	return $this->sendImageTo($image, users, $message, $keyboard, $silent, $attachments);
} 

//Функции, созданные ИИ
function splitAtNthOccurrence($string, $substring, $n, $substron = true) {
    $position = -1;
    for ($i = 0; $i < $n; $i++) {
        $position = strpos($string, $substring, $position + 1);
        if ($position === false) {
            return null; // Подстрока не найдена нужное количество раз
        }
    }
    // Разделяем строку: до и после найденной позиции
    $before = substr($string, 0, $position);
	if($substrin) $after = substr($string, $position);
    else $after = substr($string, $position + strlen($substring)); 
    return [
        'position' => $position,
        'before' => $before,
        'after' => $after
    ];
}

function extractFormattedTags($inputString) {
    // Соответствие тегов типам форматирования
    $tagTypes = [
        'b' => 'bold',
        'strong' => 'bold',
        'i' => 'italic',
        'em' => 'italic',
        'u' => 'underline',
        'a' => 'url'
    ];

    // Создаём DOM-документ
    $dom = new DOMDocument();

    // Подавляем предупреждения о некорректном HTML
    libxml_use_internal_errors(true);

    // Добавляем обёртку, чтобы гарантировать наличие body
    $html = '<div>' . mb_convert_encoding($inputString, 'HTML-ENTITIES', 'UTF-8') . '</div>';

    if (!$dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD)) {
        libxml_clear_errors();
        return false;
    }

    libxml_clear_errors();

    // Находим корневой элемент (наш div)
    $root = $dom->getElementsByTagName('div')->item(0);
    if (!$root) {
        return false;
    }

    $result = [
        'message' => '',
        'items' => []
    ];
    $cleanText = '';

    // Рекурсивно обрабатываем узлы
    $this->processNode($root, $cleanText, $result['items'], $tagTypes);

    // Если не найдено ни одного тега с форматированием, возвращаем false
    if (empty($result['items'])) {
        return false;
    }

    $result['message'] = $cleanText;
    return $result;
}

function processNode($node, &$cleanText, &$items, $tagTypes, $parentOffset = 0) {
    // Проверяем, что узел существует и имеет дочерние узлы
    if (!$node || !$node->hasChildNodes()) {
        return;
    }

    foreach ($node->childNodes as $child) {
        if ($child->nodeType === XML_TEXT_NODE) {
            // Текстовый узел — добавляем в итоговую строку
            $text = $child->textContent;
            $cleanText .= $text;
        } elseif ($child->nodeType === XML_ELEMENT_NODE) {
            $tagName = strtolower($child->nodeName);

            if (isset($tagTypes[$tagName])) {
                // Узел с форматированием
                $type = $tagTypes[$tagName];
                $url = null;

                if ($type === 'url') {
                    $url = $child->getAttribute('href');
                }

                // Запоминаем текущую длину cleanText как offset
                $offset = mb_strlen($cleanText, 'UTF-8');

                // Рекурсивно обрабатываем дочерние узлы этого тега
                $this->processNode($child, $cleanText, $items, $tagTypes, $offset);

                // После обработки дочерних узлов рассчитываем длину текста внутри этого тега
                $length = mb_strlen($cleanText, 'UTF-8') - $offset;

                // Добавляем элемент форматирования
                $item = [
                    'offset' => $offset,
                    'length' => $length,
                    'type' => $type
                ];
                if ($type === 'url') {
                    $item['url'] = $url;
                }
                $items[] = $item;
            } else {
                // Другие теги — просто рекурсивно обрабатываем их содержимое
                $this->processNode($child, $cleanText, $items, $tagTypes, $parentOffset);
            }
        }
    }
}




//API//

function vkApi_call($method, $params = array()) {
  $this->getParams();
  $params['access_token'] = $this->config['API_KEY'];
  $params['group_id'] = $this->config['GROUP_ID'];
  $params['v'] = V_API;
  $query = http_build_query($params);
  $url = 'https://api.vk.com/method/'.$method.'?'.$query;
  //$this->writeLog($url);
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $json = curl_exec($curl);
  $error = curl_error($curl);
  if ($error) {
    $this->log_error($error);
  }
  curl_close($curl);
  $response = json_decode($json, true);
  $this->writeLog($response);
  if (!$response || !isset($response['response'])) {
    $this->log_error($json);
  } else return $response['response'];
}

function uploadPhoto($user_id, $file_name) {
  $upload_server_response = $this->vkApi_photosGetMessagesUploadServer($user_id);
  $upload_response = $this->vkApi_upload($upload_server_response['upload_url'], $file_name);
  $save_response = $this->vkApi_photosSaveMessagesPhoto($upload_response['photo'], $upload_response['server'], $upload_response['hash']);
  return array_pop($save_response);
}

function vkApi_photosGetMessagesUploadServer($peer_id) {
  return $this->vkApi_call('photos.getMessagesUploadServer', array(
    //'peer_id' => $peer_id,
    'group_id' => $peer_id,
  ));
}

function vkApi_upload($url, $file_name) {
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, array('file' => new CURLfile($file_name)));
  $json = curl_exec($curl);
  $error = curl_error($curl);
  if ($error) {
    $this->log_error($error);
  }
  curl_close($curl);
  return json_decode($json, true);
}

function vkApi_photosSaveMessagesPhoto($photo, $server, $hash) {
  return $this->vkApi_call('photos.saveMessagesPhoto', array(
    'photo'  => $photo,
    'server' => $server,
    'hash'   => $hash,
  ));
}

// Загрузка голосового сообщения от бота (OGG)
function uploadVoiceMessage($user_id, $file_name) {
  $upload_server_response = $this->vkApi_docsGetMessagesUploadServer($user_id, 'audio_message');
  $upload_response = $this->vkApi_upload($upload_server_response['upload_url'], $file_name);
  $save_response = $this->vkApi_docsSave($upload_response['file'], 'Voice message');
  return array_pop($save_response);
}
function vkApi_docsGetMessagesUploadServer($peer_id, $type) {
  return $this->vkApi_call('docs.getMessagesUploadServer', array(
    'peer_id' => $peer_id,
    'type'    => $type,
  ));
}

function vkApi_docsSave($file, $title) {
  return $this->vkApi_call('docs.save', array(
    'file'  => $file,
    'title' => $title,
  ));
}


//логи
function log_msg($message) {
  if (is_array($message)) {
    $message = json_encode($message);
  }
  $trace = debug_backtrace();
  $function_name = isset($trace[1]) ? $trace[1]['function'] : '-';
  $mark = '[' . $function_name . ']';
  $this->writeLog('[ERROR] ' . '$mark' . $message);
}

function log_error($message) {
  if (is_array($message)) {
    $message = json_encode($message);
  }
  $trace = debug_backtrace();
  $function_name = isset($trace[1]) ? $trace[1]['function'] : '-';
  $mark = '[' . $function_name . ']';
  $this->writeLog('[ERROR] ' . '$mark' . $message);
}

function writeLog($message) {
	if ($this->debug) {
		DebMes($message, $this->name);
	}
}

//Функции "на всякай случай"
//отправка сообщения (без индивидуальной клавиатуры) большому количеству пользователей
function sendMessageTos($users, $message = '',$keyboard='', $silent = false, $attachments = array()) {
	if(is_array($keyboard)) $keyboard = json_encode($keyboard);
	$sids = '';
	$ids = '';
  foreach($users as $user){
	  if(!silent and $user['SILENT'] == 1) $sids = $sids.$user['USER_ID'].",";
	  else $ids = $ids.$user['USER_ID'].",";
  }
  if($ids != ''){
	  $ids = rtrim($ids, ",");
	  $this->sendMessage($ids, $message, $keyboard, $silent, $attachments);
  }
  if($sids != ''){
	  $sids = rtrim($sids, ",");
	  $this->sendMessage($sids, $message, $keyboard, true, $attachments);
  }
}

}
