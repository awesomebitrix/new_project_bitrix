#!/usr/bin/php
<?php
$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/www";
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
set_time_limit(0);
define("LANG", "ru");
require($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_before.php");

@ignore_user_abort(true);

CEvent::CheckEvents();

if(CModule::IncludeModule('sender'))
{
	\Bitrix\Sender\MailingManager::checkPeriod(false);
	\Bitrix\Sender\MailingManager::checkSend();
}

require($DOCUMENT_ROOT."/bitrix/modules/main/include/epilog_after.php");
?>