<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Online1c\Reviews;
use Bitrix\Main\Config\Option;

if(!Loader::includeModule('online1c.reviews'))
	return;

Loc::loadMessages(__FILE__);

$arTypes = [];
$oTypes = Reviews\TypesTable::getList([
	'select' => ['ID','TITLE'],
]);
while ($type = $oTypes->fetch()){
	$arTypes[$type['ID']] = $type['TITLE'];
}

$fields = Reviews\MainHelper::getParamsComponentFields();

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"TYPE_ID" => array(
			"PARENT" => "BASE",
			"NAME" => Reviews\MainHelper::getLangParams('TYPE_ID'),
			"TYPE" => "LIST",
			"VALUES" => $arTypes,
			"DEFAULT" => "news",
			"REFRESH" => "Y",
		),
		"ELEMENT_CODE" => array(
			"PARENT" => "BASE",
			"NAME" => Reviews\MainHelper::getLangParams('ELEMENT_CODE'),
			"TYPE" => "STRING",
			"DEFAULT" => '',
		),
		"CACHE_TIME"  =>  array("DEFAULT"=>3600),
		"SHOW_AVATARS" => array(
			"NAME" => Reviews\MainHelper::getLangParams('SHOW_AVATARS'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'Y',
		),
		'LIST_ONLY' => array(
			'NAME' =>  Reviews\MainHelper::getLangParams('LIST_ONLY'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
			"REFRESH" => "Y",
		),
		'SHOW_LIKES' => array(
			'NAME' =>  Reviews\MainHelper::getLangParams('SHOW_LIKES'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'Y',
			"REFRESH" => "Y",
		),
	),
);
if($arCurrentValues['LIST_ONLY'] != 'Y'){
	$arComponentParameters['PARAMETERS']['PREMODERATE'] = array(
		'NAME' => Reviews\MainHelper::getLangParams('PREMODERATE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	);
	$arComponentParameters['PARAMETERS']['FIELDS'] = array(
		'NAME' => Reviews\MainHelper::getLangParams('FIELDS'),
		'TYPE' => 'LIST',
		'VALUES' =>$fields,
		'MULTIPLE' => 'Y',
		'SIZE' => 7
	);
	$arComponentParameters['PARAMETERS']['REQUIRED_FIELDS'] = array(
		'NAME' => Reviews\MainHelper::getLangParams('REQUIRED_FIELDS'),
		'TYPE' => 'LIST',
		'VALUES' =>$fields,
		'MULTIPLE' => 'Y',
		'SIZE' => 7
	);
	$arComponentParameters['PARAMETERS']['ONLY_AUTH'] = array(
		'NAME' => Reviews\MainHelper::getLangParams('ONLY_AUTH'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
		"REFRESH" => "Y",
	);
}
if($arCurrentValues['SHOW_LIKES'] === 'Y'){
	$arComponentParameters['PARAMETERS']['DISLIKE_START'] = array(
		'NAME' => Reviews\MainHelper::getLangParams('DISLIKE_START'),
		'DEFAULT' => 0
	);
}
if($arCurrentValues['ONLY_AUTH'] === 'Y'){
	$templeAuth = Option::get('main', 'auth_components_template');
	$arComponentParameters['PARAMETERS']['SYSTEM_AUTH_TEMPLE'] = array(
		'NAME' => Reviews\MainHelper::getLangParams('SYSTEM_AUTH_TEMPLE'),
		'DEFAULT' => $templeAuth
	);
}