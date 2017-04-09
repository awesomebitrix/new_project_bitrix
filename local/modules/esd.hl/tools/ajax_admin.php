<?php require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/main/include/prolog_before.php');

Bitrix\Main\Loader::includeModule('esd.hl');

if(check_bitrix_sessid()){
	echo Esd\HL\Ajax\Ajax::instance()->init()->getResult();
}