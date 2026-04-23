<?php
/**
* Default language file for VKmessenger module
*
*/
$dictionary=array(
/* general */
'ABOUT' => 'About',
'VK_HELP' => 'Help',
'VK_TOKEN'=>'The community\'s API KEY',
'VK_STORAGE_PATH'=>'Path download storage',
'VK_ADMIN'=>'Administrator',
'VK_SILENT'=>'Silent mode',
'VK_HISTORY'=>'History',
'VK_HISTORY_LEVEL'=>'History level',
'VK_HISTORY_SILENT'=>'History level not silent',
'VK_COMMANDS'=>'Burrons',
'VK_COMMAND'=>'Button',
'VK_PATTERNS'=>'Patterns',
'VK_DOWNLOAD'=>'Download',
'VK_PLAY_VOICE'=>'Play',
'VK_DISABLE'=>'Disable',
'VK_ONLY_ADMIN'=>'Only administrators',
'VK_ALL'=>'All',
'VK_ALL_NO_LIMIT' => 'All (no limit)',
'VK_SHOW_COMMAND'=>'Show command',
'VK_SHOW'=>'Show',
'VK_HIDE'=>'Hide',
'VK_CONDITION'=>'Condition',
'VK_EVENTS'=>'Events',
'VK_EVENT'=>'Event',
'VK_ENABLE'=>'Enable',
'VK_EVENT_TEXT'=>'Text message',
'VK_EVENT_IMAGE'=>'Image',
'VK_EVENT_VOICE'=>'Voice',
'VK_EVENT_AUDIO'=>'Audio',
'VK_EVENT_VIDEO'=>'Video',
'VK_EVENT_DOCUMENT'=>'Document',
'VK_EVENT_GRAFFITI'=>'Graffity',
'VK_EVENT_LOCATION'=>'Location',
'VK_COUNT_ROW'=>'Count commands on row',
'VK_PLAYER'=>'Player for voice',
'VK_TIMEOUT'=>'Timeout long polling (sec)',
'VK_UPDATE_USER_INFO'=>'Update user info',
'VK_UPDATE_USER_KEYB'=>'Update user keyboards',
'VK_UPDATE_KEYBOARD'=>'Keyboard Update',
'VK_USE_WEBHOOK'=>'Use webhook',
'VK_WEBHOOK_URL'=>'Webhook URL',
'VK_PATH_CERT'=>'Path to certificate',
'VK_WEBHOOK_SET'=>'Set webhook',
'VK_WEBHOOK_CLEAN'=>'Clean webhook',
'VK_WEBHOOK_INFO'=>'Status webhook',
'VK_USE_PROXY'=>'Use proxy',
'VK_PROXY_TYPE'=>'Type proxy',
'VK_PROXY_URL'=>'Server proxy',
'VK_PROXY_LOGIN'=>'Login proxy',
'VK_PROXY_PASSWORD'=>'Password proxy',
'VK_REG_USER'=>'Auto registration users',
'VK_LAST_NAME'=>'Last name',
'VK_CMD_TEXT'=>'text. Sends a message with the name of the button',
'VK_CMD_LOCATION'=>'location. Opens a dialog box with location information',
'VK_CMD_VKPAY'=>'vkpay. Opens the VKPay payment window with predefined parameters',
'VK_CMD_LINK'=>'open_link. Opens the link',
'VK_CMD_APP'=>'open_app. Opens a mini-app or game,',
'VK_CMD_CALLBACK'=>'callback. Does not send a message, executes the code from the "Code" field',
/* about */

/* help */
'HELP_TOKEN'=>'Community access key with maximum rights',
'HELP_STORAGE'=>'Path storage to save files from user',
'HELP_TIMEOUT'=>'Timeout cycle in ms',
'HELP_REG_USER'=>'Enable registration user (anti spam)',
'HELP_USE_PROXY'=>'Enable https proxy (torsocks)',
'HELP_LOG_LEVEL'=>'Debug = all, info = information level, warning = only warning level',
'HELP_USERID'=>'VK User ID',
'HELP_NAME'=>'Name user',
'HELP_MEMBER'=>'Link to system user',
'HELP_ADMIN'=>'Administrator',
'HELP_SILENT'=>'Send silent messages',
'HELP_HISTORY'=>'Send system messages to user',
'HELP_HISTORY_LEVEL'=>'Level messages to send(0 - send all messages)',
'HELP_HISTORY_SILENT'=>'Level messages to send not silent(0 - send all messages not silent)',
'HELP_COMMANDS'=>'Process command from user',
'HELP_PATTERNS'=>'Process patterns from user',
'HELP_DOWNLOAD'=>'Download files to storage from user',
'HELP_PLAY_VOICE'=>'Play voice from user',
'HELP_TITLE'=>'Title игеещт (view in keyboard vk client)',
'HELP_DESCRIPTION'=>'Description игеещт',
'HELP_ACCESS_CONTROL'=>'Access control игеещт',
'HELP_COUNTROW'=>'Count игеещты on row'

/* end module names */
);

foreach ($dictionary as $k=>$v) {
 if (!defined('LANG_'.$k)) {
  define('LANG_'.$k, $v);
 }
}

?>