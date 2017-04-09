<?php
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
\Bitrix\Main\Loader::includeModule('esd.hl');

$arItems = Esd\HL\Options::getModuleMenu();
$arMoreUrl = ['pw_hl_row_list.php', 'pw_hl_row_edit.php','pw_hl_csv.php'];
foreach ($arItems as $item) {
	foreach ($item['more_url'] as $url) {
		$arMoreUrl[] = $url;
	}
}
$arMoreUrl = array_unique($arMoreUrl);

return array(
	"parent_menu" => "global_menu_content",
	"section" => "pw_custom_hl",
	"sort" => 200,
	"text" => Loc::getMessage('PW_HL_MENU_MAIN'),
	//"url" => "highloadblock_index.php?lang=".LANGUAGE_ID,
	"url" => '',
	"icon" => "fileman_sticker_icon", //highloadblock_menu_icon
	"page_icon" => "highloadblock_page_icon",
	"more_url" => $arMoreUrl,
	"items_id" => "pw_custom_hl",
	"items" => $arItems
);