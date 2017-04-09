<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
$arComponentDescription = array(
	"NAME" => Loc::getMessage("O1C_REVIEW_PARAMS.COMPONENT_NAME"),
	"CACHE_PATH" => "Y",
	"SORT" => 40,
	"PATH" => array(
		"ID" => "online1c",
		"NAME" => Loc::getMessage("O1C_REVIEW_PARAMS.COMPONENT_SECTION"),
	),
);