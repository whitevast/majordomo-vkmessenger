<?php
/**
* Russian language file for VKmessenger module
*
*/
$dictionary=array(
/* general */
'ABOUT' => 'О модуле',
'VK_HELP' => 'Помощь',
'VK_TOKEN'=>'API KEY сообщества',
'VK_STORAGE_PATH'=>'Путь к хранилищу',
'VK_ADMIN'=>'Администратор',
'VK_SILENT'=>'Режим без звука',
'VK_HISTORY'=>'История',
'VK_HISTORY_LEVEL'=>'Приоритет истории',
'VK_HISTORY_SILENT'=>'Приоритет истории сo звуком',
'VK_COMMANDS'=>'Команды',
'VK_BUTTONS'=>'Кнопки',
'VK_COMMAND'=>'Команда',
'VK_PATTERNS'=>'Шаблоны',
'VK_DOWNLOAD'=>'Загрузка',
'VK_PLAY_VOICE'=>'Играть голос',
'VK_DISABLE'=>'Запретить',
'VK_ONLY_ADMIN'=>'Только для администраторов',
'VK_ALL'=>'Для всех',
'VK_ALL_NO_LIMIT' => 'Для всех (без ограничений)',
'VK_SHOW_COMMAND'=>'Отображение кнопки',
'VK_SHOW'=>'Показать',
'VK_HIDE'=>'Скрыть',
'VK_CONDITION'=>'Условие',
'VK_EVENTS'=>'События',
'VK_EVENT'=>'Событие',
'VK_ENABLE'=>'Включить',
'VK_EVENT_TEXT'=>'Текстовое сообщение',
'VK_EVENT_IMAGE'=>'Изображение',
'VK_EVENT_VOICE'=>'Голосовое сообщение',
'VK_EVENT_AUDIO'=>'Аудио',
'VK_EVENT_VIDEO'=>'Видео',
'VK_EVENT_DOCUMENT'=>'Документ',
'VK_EVENT_GRAFFITI'=>'Граффити',
'VK_EVENT_LOCATION'=>'Местоположение',
'VK_COUNT_ROW'=>'Кнопок в строке',
'VK_PLAYER'=>'Проигрыватель голоса',
'VK_TIMEOUT'=>'Период long polling (сек)',
'VK_UPDATE_USER_INFO'=>'Обновить информацию пользователей',
'VK_UPDATE_USER_KEYB'=>'Обновить клавиатуры пользователей',
'VK_UPDATE_KEYBOARD'=>'Обновление клавиатуры',
'VK_USE_WEBHOOK'=>'Использовать webhook',
'VK_WEBHOOK_URL'=>'Webhook URL',
'VK_PATH_CERT'=>'Путь к сертификату',
'VK_WEBHOOK_SET'=>'Установить webhook',
'VK_WEBHOOK_CLEAN'=>'Удалить webhook',
'VK_WEBHOOK_INFO'=>'Статус webhook',
'VK_USE_PROXY'=>'Использовать прокси',
'VK_PROXY_TYPE'=>'Тип прокси',
'VK_PROXY_URL'=>'Адрес сервера прокси',
'VK_PROXY_LOGIN'=>'Логин прокси',
'VK_PROXY_PASSWORD'=>'Пароль прокси',
'VK_REG_USER'=>'Регистрация пользователей',
'VK_LAST_NAME'=>'Фамилия',
'VK_CMD_TEXT'=>'text. Отправляет сообщение с именем кнопки',
'VK_CMD_LOCATION'=>'location. Открывает диалоговое окно с информацией о местоположении',
'VK_CMD_VKPAY'=>'vkpay. Открывает окно оплаты VK Pay с предопределёнными параметрами',
'VK_CMD_LINK'=>'open_link. Открывает ссылку',
'VK_CMD_APP'=>'open_app. Открывает мини-приложение или игру',
'VK_CMD_CALLBACK'=>'callback. Не отправляет сообщение, выполняет код из поля "Код"',
'VK_CMD_PAYLOAD'=>'Полезные данные',
/* about */

/* help */
'HELP_TOKEN'=>'Ключ доступа сообщества с максимальнывми правами',
'HELP_STORAGE'=>'Путь для сохранения файлов полученных от пользователя',
'HELP_TIMEOUT'=>'Период ожидания новых сообщений в секундах',
'HELP_REG_USER'=>'Опция позволяет отключить автоматическую регистрацию пользователей (antispam)',
'HELP_USE_PROXY'=>'Настройки прокси для обхода блокировок Роскомнадзора',
'HELP_LOG_LEVEL'=>'Уровень логирования: Debug = писать все, info = основную информацию, warning = только важное',
'HELP_USERID'=>'VK User ID',
'HELP_NAME'=>'Имя пользователя',
'HELP_MEMBER'=>'Связь с пользователем системы',
'HELP_ADMIN'=>'Администратор',
'HELP_SILENT'=>'Сообщения приходят в VK клиент без звука',
'HELP_HISTORY'=>'Отправка системных сообщений пользователю',
'HELP_HISTORY_LEVEL'=>'Уровень важности для отправки (0 - отправка всех сообщений)',
'HELP_HISTORY_SILENT'=>'Уроверь важности при котором сообщения приходят со звуком(0 - все со звуком)',
'HELP_COMMANDS'=>'Обработка команд полученных от пользователя',
'HELP_PATTERNS'=>'Обработка сообщения пользователя как шаблона поведения',
'HELP_DOWNLOAD'=>'Сохранение файлов отправляемых пользователем',
'HELP_PLAY_VOICE'=>'Проигрывать голосовые сообщения от пользователя',
'HELP_TITLE'=>'Имя кнопки (отображается на клавиатуре в VK клиенте)',
'HELP_DESCRIPTION'=>'Описание кнопки',
'HELP_ACCESS_CONTROL'=>'Ограничение доступа к кнопке',
'HELP_COUNTROW'=>'Количество кнопок в одной строке на клавиатуре в VK клиенте',
'HELP_PAYLOAD'=>'Если в данном поле есть данные, код из поля Код выполнятся не будет. Необходимо обрабатывать кнопку в событиях callback.',

/* end module names */
);

foreach ($dictionary as $k=>$v) {
 if (!defined('LANG_'.$k)) {
  define('LANG_'.$k, $v);
 }
}

?>