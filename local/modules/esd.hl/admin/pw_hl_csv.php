<?php
define("ADMIN_MODULE_NAME", "esd.hl");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
global $USER, $APPLICATION, $USER_FIELD_MANAGER;
IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile(__DIR__.'/highloadblock_rows_list.php');
IncludeModuleLangFile(__DIR__.'/pw_hl_row_edit.php');

use Bitrix\Main\Localization\Loc;
use Bitrix\Highloadblock as HL;
use Esd\HL\MainTable;
use Esd\HL\LogTable;
use Bitrix\Main\Entity;

$moduleId = 'esd.hl';
Bitrix\Main\Loader::IncludeModule($moduleId);

$modulePermissions = $APPLICATION->GetGroupRight($moduleId);
if ($modulePermissions == "D"){
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}
if (!\Bitrix\Main\Loader::IncludeModule($moduleId)) {
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

/** @var \Bitrix\Main\HttpRequest $request */
$request = \Bitrix\Main\Context::getCurrent()->getRequest();


$hlblock = null;

$entityId = intval($request['ENTITY_ID']);
$MainTable = new MainTable($entityId);

$entity = $MainTable->getHLEntity();

// get entity info
if (isset($_REQUEST['ENTITY_ID']))
{
	$hlblock = $MainTable->getHBlock();
}

if (intval($entityId) == 0)
{
	// 404
	if ($_REQUEST["mode"] == "list")
	{
		require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");
	}
	else
	{
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	}

	echo GetMessage('HLBLOCK_ADMIN_ROW_EDIT_NOT_FOUND');

	if ($_REQUEST["mode"] == "list")
	{
		require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");
	}
	else
	{
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	}

	die();
}

$APPLICATION->SetAdditionalCSS('/bitrix/themes/.default/esd.hl/css/bootstrap.min.css');
$APPLICATION->SetAdditionalCSS('/bitrix/themes/.default/esd.hl/css/font-awesome.min.css');
$APPLICATION->SetAdditionalCSS('/bitrix/themes/.default/esd.hl/css/esd.hl.css');


$editUrl = 'pw_hl_row_edit.php?ENTITY_ID='.$entityId.'&lang='.LANGUAGE_ID;
$listUrl = 'pw_hl_row_list.php?ENTITY_ID='.$entityId.'&lang='.LANGUAGE_ID;


$errors = array();

$aTabs = array(
	array("DIV" => "edit1", "TAB" => $hlblock['NAME'], "ICON"=>"ad_contract_edit", "TITLE"=> Loc::getMessage('PW_HL_ADMIN_CSV_TITLE'))
);

$tabControl = new CAdminForm("hlrow_csv_".$hlblock['ID'], $aTabs);

//view

if ($_REQUEST["mode"] == "list"){
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");
}else{
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
}

if (!empty($errors)){
	CAdminMessage::ShowMessage(join("\n", $errors));
}

$tabControl->BeginPrologContent();

echo $USER_FIELD_MANAGER->ShowScript();

echo \CAdminCalendar::ShowScript();

$tabControl->EndPrologContent();
$tabControl->BeginEpilogContent();
?>
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="ID" value="<?=htmlspecialcharsbx(!empty($row)?$row['ID']:'')?>">
	<input type="hidden" name="ENTITY_ID" value="<?=htmlspecialcharsbx($hlblock['ID'])?>">
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">

	<?$tabControl->EndEpilogContent();?>

	<? $tabControl->Begin(array(
		"FORM_ACTION" => $APPLICATION->GetCurPage()."?ENTITY_ID=".$hlblock['ID']."&lang=".LANG
	));?>

	<? $tabControl->BeginNextFormTab();
	$tabControl->BeginCustomField('CSV', false)?>
	<tr>
		<td colspan="2">
			<?$APPLICATION->IncludeComponent('hl:csv.admin', '', array(
				'URL'=>'/local/components/hl/csv.admin/file.php?sessid='.bitrix_sessid().'&ENTITY_ID='.$request['ENTITY_ID'].'&upload=Y&ajaxAction=Y'
			), false)?>
		</td>
	</tr>
	<?$tabControl->EndCustomField('CSV');
	$tabControl->Show();?>
<?
if ($_REQUEST["mode"] == "list")
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");
else
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");