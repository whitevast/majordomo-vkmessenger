<?php
/*
Bulgarian language file for VKmessenger module
*
*/
$dictionary=array(
/* general */
'ABOUT' => 'За модула',
'VK_HELP' => 'Помощ',
'VK_TOKEN'=>'API KEY общността',
'VK_STORAGE_PATH'=>'Път до хранилището',
'VK_ADMIN'=>'Администратор',
'VK_SILENT'=>'Безшумен режим',
'VK_HISTORY'=>'История',
'VK_HISTORY_LEVEL'=>'Ниво на лога',
'VK_HISTORY_SILENT'=>'Приоритизирайте историята със звук',
'VK_COMMANDS'=>'Команди',
'VK_BUTTONS'=>'Бутон',
'VK_COMMAND'=>'Команда',
'VK_PATTERNS'=>'Шаблони',
'VK_DOWNLOAD'=>'Зареждане',
'VK_PLAY_VOICE'=>'Възпроизвеждане на глас',
'VK_DISABLE'=>'Забраняване',
'VK_ONLY_ADMIN'=>'Само за администратори',
'VK_ALL'=>'За всички',
'VK_ALL_NO_LIMIT' => 'За всички (без ограничения)',
'VK_SHOW_COMMAND'=>'Показване на команди',
'VK_SHOW'=>'Показване',
'VK_HIDE'=>'Скриване',
'VK_CONDITION'=>'Условие',
'VK_EVENTS'=>'Събития',
'VK_EVENT'=>'Събитие',
'VK_ENABLE'=>'Включване',
'VK_EVENT_TEXT'=>'Текстово съобщение',
'VK_EVENT_IMAGE'=>'Изображение',
'VK_EVENT_VOICE'=>'Голосово съобщение',
'VK_EVENT_AUDIO'=>'Аудио',
'VK_EVENT_VIDEO'=>'Видео',
'VK_EVENT_DOCUMENT'=>'Документ',
'VK_EVENT_GRAFFITI'=>'Графит',
'VK_EVENT_LOCATION'=>'Местоположение',
'VK_COUNT_ROW'=>'Бутон на ред',
'VK_PLAYER'=>'Гласов плейър',
'VK_TIMEOUT'=>'Период long polling (сек)',
'VK_UPDATE_USER_INFO'=>'Актуализация на информацията за потребителя',
'VK_UPDATE_USER_KEYB'=>'Актуализирайте клавиатурите на потребителите',
'VK_UPDATE_KEYBOARD'=>'Актуализация на клавиатурата',
'VK_USE_WEBHOOK'=>'Използване на webhook',
'VK_WEBHOOK_URL'=>'Webhook URL',
'VK_PATH_CERT'=>'Път към сертификат',
'VK_WEBHOOK_SET'=>'Задаване на webhook',
'VK_WEBHOOK_CLEAN'=>'Изтриване на webhook',
'VK_WEBHOOK_INFO'=>'Статус на webhook',
'VK_USE_PROXY'=>'Исползване на прокси',
'VK_PROXY_TYPE'=>'Тип прокси',
'VK_PROXY_URL'=>'Адрес на прокси сервъра ',
'VK_PROXY_LOGIN'=>'Потребителско име',
'VK_PROXY_PASSWORD'=>'Парола',
'VK_REG_USER'=>'Регистрация на потребител',
'VK_LAST_NAME'=>'Фамилия',
'VK_CMD_TEXT'=>'text. Отправляет сообщение с именем кнопки',
'VK_CMD_LOCATION'=>'location. Отваря диалогов прозорец с информация за местоположението',
'VK_CMD_VKPAY'=>'vkpay. Отваря прозореца за плащане с предварително зададени параметри',
'VK_CMD_LINK'=>'open_link. Отваря връзката',
'VK_CMD_APP'=>'open_app. Отваря мини приложение или игра',
'VK_CMD_CALLBACK'=>'callback. Не изпраща съобщение, изпълнява код от полето "Код',
/* about */

/* help */
'HELP_TOKEN'=>'Ключ за достъп на Общността с максимални права',
'HELP_STORAGE'=>'Път за запазване на файлове, получени от потребителя',
'HELP_TIMEOUT'=>'Периодът на чакане за нови съобщения в секунди',
'HELP_REG_USER'=>'Опцията Ви позволява да деактивирате автоматичната регистрация на потребителите (антиспам)',
'HELP_USE_PROXY'=>'Активиране на https прокси (torsocks)',
'HELP_LOG_LEVEL'=>'Ниво на запис на логовете: Debug = записва всичко, info = основна информация, warning = само важното',
'HELP_USERID'=>'VK User ID',
'HELP_NAME'=>'Потребителско име',
'HELP_MEMBER'=>'Комуникация с потребителя на системата',
'HELP_ADMIN'=>'Администратор',
'HELP_HISTORY'=>'Изпращане на системни логове на потребителя',
'HELP_HISTORY_LEVEL'=>'Ниво на важност за изпращане (0 - изпращане на свички логове)',
'HELP_COMMANDS'=>'Обработка на команди, получени от потребителя',
'HELP_PATTERNS'=>'Обработка на потребителско съобщение като шаблон за поведение',
'HELP_DOWNLOAD'=>'Запазване на файлове, изпратени от потребителя',
'HELP_PLAY_VOICE'=>'Възпроизвеждане на гласови съобщения от потребителя',
'HELP_TITLE'=>'Име на бутона (показва се на клавиатурата на клиента на VK)',
'HELP_DESCRIPTION'=>'Описание на бутона',
'HELP_ACCESS_CONTROL'=>'Ограничаване на достъпа до бутона',
'HELP_COUNTROW'=>'Броя на командните бутони в един ред на клавиатурата на клиента на VK'

/* end module names */
);

foreach ($dictionary as $k=>$v) {
if (!defined('LANG'.$k)) {
define('LANG'.$k, $v);
}
}

?>