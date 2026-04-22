<?php
set_time_limit(0);
chdir(dirname(__FILE__) . '/../');
include_once("./config.php");
include_once("./lib/loader.php");
include_once("./lib/threads.php");
include_once("./load_settings.php");
include_once(DIR_MODULES . "control_modules/control_modules.class.php");
$ctl = new control_modules();
include_once(DIR_MODULES . 'vkmessenger/vkmessenger.class.php');
$vkmessenger_module = new vkmessenger();
$vkmessenger_module->getConfig();
if (empty($vkmessenger_module->config['API_KEY']))
   exit; // no devices added -- no need to run this cycle
echo date("H:i:s") . " running " . basename(__FILE__) . PHP_EOL;
$latest_check=0;
$checkEvery=20; // poll every 20 seconds
$token = $vkmessenger_module->config['API_KEY'];
$groupid = $vkmessenger_module->config['GROUP_ID'];
$version = V_API;
if(!$vkmessenger_module->config['VK_WEBHOOK']) $connect = $vkmessenger_module->vkApi_call('groups.getLongPollServer');
$vkmessenger_module->usersUpdate(true);
echo 'Start vkmessenger cycle' . PHP_EOL;

while (1){
	if ((time()-$latest_check)>$checkEvery){
		setGlobal((str_replace('.php', '', basename(__FILE__))) . 'Run', time(), 1);
		$latest_check=time();
	}
	if (!$vkmessenger_module->config['VK_WEBHOOK']){ //если вебхук не активен
		if(isset($connect['server'])){
			$url = $connect['server'].'?act=a_check&key='.$connect['key'].'&ts='.$connect['ts'].'&wait=15&version='.$version;
			$response = json_decode(@file_get_contents($url), true);
			if (isset($response['failed'])){
				if($response['failed'] == 1){
					$connect['ts'] = $response['ts'];
				} else if($response['failed'] == 2 or $response['failed'] == 3){
					$connect = $vkmessenger_module->vkApi_call('groups.getLongPollServer');
				}   
			} else {
				if(!empty($response['updates'])){
					//print_r($response);
					foreach ($response['updates'] as $event){
						//$vkmessenger_module->processMessage($event);
						$url = BASE_URL . '/webhook_vkmessenger.php';
						$data_string = json_encode($event);
						$ch=curl_init($url);
						curl_setopt_array($ch, array(
							CURLOPT_RETURNTRANSFER => true,
							CURLOPT_POST => true,
							CURLOPT_POSTFIELDS => $data_string,
							CURLOPT_HEADER => true,
							CURLOPT_HTTPHEADER => array('Content-Type:application/json', 'Content-Length: ' . strlen($data_string)))
						);
						curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
						curl_setopt($ch, CURLOPT_TIMEOUT_MS, 100);
						$result = curl_exec($ch);
						curl_close($ch);
					}
				}
				$connect['ts'] = $response['ts'] ?? $connect['ts'];
			}
		} else { //если данные для подключения отсутствуют (нет доступа в интернет), пробуем получить их каждые 5 секунд
			sleep(5);
			$connect = $vkmessenger_module->vkApi_call('groups.getLongPollServer');
		}
	} else sleep(1);
	if (file_exists('./reboot') || isset($_GET['onetime'])) exit;
}
$vkmessenger_module->writeLog("Unexpected close of cycle: " . basename(__FILE__));