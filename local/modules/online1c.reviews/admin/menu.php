<?php
use Bitrix\Main\Localization\Loc;
use Online1c\Reviews\Admin;

Loc::loadLanguageFile(__FILE__);
Bitrix\Main\Loader::includeModule('online1c.reviews');

$menu = array(
	"parent_menu" => "global_menu_services",
	"section" => "online_1c_reviews",
	"sort" => 200,
	"text" => 'Отзывы',
	"url" => '',
	"icon" => "fileman_sticker_icon",
	"page_icon" => "fileman_sticker_icon",
	"more_url" => [
		'admin_helper_route.php'
	],
	"items_id" => "online_1c_reviews",
	"module_id" => 'online1c.reviews',
	"items" => [
		array(
			"sort" => 10,
			"url" => Admin\Type\TypeListHelper::getUrl(),
			"more_url" => array(
				Admin\Type\TypeListHelper::getUrl(),
				Admin\Type\TypeListEditHelper::getUrl()
			),
			"text" => 'Список типов',
			"icon" => "iblock_menu_icon_iblocks", //highloadblock_menu_icon
			"page_icon" => "iblock_page_icon_iblocks",
		),
		array(
			"sort" => 10,
			"url" => Admin\Reviews\ReviewListHelper::getUrl(),
			"more_url" => array(
				Admin\Reviews\ReviewListHelper::getUrl(),
				Admin\Reviews\ReviewEditHelper::getUrl()
			),
			"text" => 'Список отзывов',
			"icon" => "iblock_menu_icon_iblocks", //highloadblock_menu_icon
			"page_icon" => "iblock_page_icon_iblocks",
		),
	]
);

return $menu;