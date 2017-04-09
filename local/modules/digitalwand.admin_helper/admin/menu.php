<?php
use Bitrix\Main\Localization\Loc;
use DigitalWand\AdminHelper\Page;

Bitrix\Main\Loader::includeModule('digitalwand.admin_helper');
Loc::loadLanguageFile(__FILE__);
global $USER;

$menu = array(
	"parent_menu" => "global_menu_settings",
	"section" => "digitalwand_admin_helper",
	"sort" => 200,
	"text" => 'Утилиты',
	"url" => '',
	"icon" => "fileman_sticker_icon",
	"page_icon" => "fileman_sticker_icon",
	"more_url" => [
		Page\Components\CreateComponentHelper::getUrl(),
		'admin_helper_route.php'
	],
	"items_id" => "dw_utils",
	'items' => [
		array(
			"sort" => 200,
			"url" => Page\Components\CreateComponentHelper::getUrl(),
			"more_url" => array(
				Page\Components\CreateComponentHelper::getUrl(),
				'admin_helper_route.php'
			),
			"text" => 'Создание компонента',
			"icon" => "iblock_menu_icon_iblocks", //highloadblock_menu_icon
			"page_icon" => "iblock_page_icon_iblocks",
		),
	]
);
//PR($menu);
return $menu;