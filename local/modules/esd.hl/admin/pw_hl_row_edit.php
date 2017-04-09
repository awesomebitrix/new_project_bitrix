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

$is_create_form = true;
$is_update_form = false;

$isEditMode = true;

$errors = array();

// get entity
$entity = HL\HighloadBlockTable::compileEntity($hlblock);

/** @var HL\DataManager $entity_data_class */
$entity_data_class = $entity->getDataClass();

// get row
$row = null;

if (isset($_REQUEST['ID']) && $_REQUEST['ID'] > 0)
{
	$row = $entity_data_class::getById($_REQUEST['ID'])->fetch();

	if (!empty($row))
	{
		$is_update_form = true;
		$is_create_form = false;
	}
	else
	{
		$row = null;
	}
}

$AdminForm = new Esd\HL\Admin\AdminForm($MainTable);
$arOptions = $AdminForm->getOptions($hlblock['ID']);
$titlePage = strlen($arOptions['menu_items']['TITLE']) > 0 ? $arOptions['menu_items']['TITLE'] :  $hlblock['NAME'];

if ($is_create_form)
{
	$APPLICATION->SetTitle(GetMessage('HLBLOCK_ADMIN_ENTITY_ROW_EDIT_PAGE_TITLE_NEW', array('#NAME#' => $titlePage)));
}
else
{
	$APPLICATION->SetTitle(GetMessage('HLBLOCK_ADMIN_ENTITY_ROW_EDIT_PAGE_TITLE_EDIT',
		array('#NAME#' => $titlePage, '#NUM#' => $row['ID']))
	);
}

$sTableLog = 'tbl_history';
$CAdminSorting= new CAdminSorting($sTableLog, "ID", "desc");
$CAdminList = new CAdminList($sTableLog, $CAdminSorting);

// form
$aTabs = array(
	array("DIV" => "edit1", "TAB" => $hlblock['NAME'], "ICON"=>"ad_contract_edit", "TITLE"=> $hlblock['NAME'])
);
$arOptionsLog = unserialize(Bitrix\Main\Config\Option::get($moduleId, 'hl_history'));

$showHistory = false;
if($arOptionsLog[$hlblock['NAME']] && $is_update_form)
	$showHistory = true;

/** ================================================== логирование ================================================== */
if($showHistory){
	$aTabs[] = [
		"DIV" => "edit2",
		"TAB" => Loc::getMessage('PW_HL_EDIT_LOG_TAB'),
		"ICON"=>"ad_contract_edit",
		"TITLE"=> Loc::getMessage('PW_HL_EDIT_LOG_TAB')
	];

	$arLogHead = LogTable::getAdminHead();
	$CAdminList->AddHeaders($arLogHead);

	$filter = ['ELEMENT_ID'=>$row['ID'],'ENTITY_NAME'=>$MainTable->getHLEntity()->getName()];
	$order = ['ID'=>'DESC'];
	$limit = 30;

	$navyParams = CDBResult::GetNavParams(CAdminResult::GetNavSize(
		$sTableLog,
		array('nPageSize' => $limit, 'sNavID' => $APPLICATION->GetCurPage().'?ENTITY_ID='.$entityId.'&ID='.$row['ID'])
	));

	$QueryCnt = new Entity\Query(LogTable::getEntity());
	$QueryCnt->addSelect(new Entity\ExpressionField('CNT', 'COUNT(1)'));
	$totalCount = $QueryCnt->setLimit(null)->setOffset(null)->exec()->fetch();
	unset($QueryCnt);

	$navyParams['PAGEN'] = (int)$navyParams['PAGEN'];
	$navyParams['SIZEN'] = (int)$navyParams['SIZEN'];
	$navyParams['PAGEN'] = 1;

	$QueryLog = new Entity\Query(LogTable::getEntity());
	$obLogList = $QueryLog->setFilter($filter)
		->setSelect(array('*','USER_LOGIN'=>'USER.LOGIN'))
		->setOrder($order);

	$totalCount = (int)$totalCount['CNT'];
	if($totalCount > 0){

		$totalPages = ceil($totalCount/$navyParams['SIZEN']);
		if ($navyParams['PAGEN'] > $totalPages)
			$navyParams['PAGEN'] = $totalPages;
		$obLogList->setLimit($navyParams['SIZEN']);
		$obLogList->setOffset($navyParams['SIZEN']*($navyParams['PAGEN']-1));
	}else {
		$navyParams['PAGEN'] = 1;
		$obLogList->setLimit($navyParams['SIZEN']);
		$obLogList->setOffset(null);
	}



	$getListParams['limit'] = $navyParams['SIZEN'];
	$getListParams['offset'] = 0;

	$rsData = new CAdminResult($obLogList->exec(), $sTableLog);
	$rsData->NavStart($QueryLog->getLimit(), false, $navyParams['PAGEN']);
	$rsData->NavRecordCount = $totalCount;
	$rsData->NavPageCount = $totalPages;
	$rsData->NavPageNomer = $navyParams['PAGEN'];

//	$CAdminList->NavText($rsData->GetNavPrint(GetMessage("PAGES")));

	$arUFields = $AdminForm->getUserFields();

	while($arRes = $rsData->NavNext(true, "f_")) {
		$history = $CAdminList->AddRow($f_ID, $arRes);

		$history->AddViewField('USER_X', sprintf('[%d] %s',  $arRes['USER_X'], $arRes['USER_LOGIN']));
		$changeFields = LogTable::getChangeFields($arUFields, $arRes);
		$history->AddViewField('FIELDS', $changeFields);
	}
}
/** ================================================================================================================= */

$tabControl = new CAdminForm("hlrow_edit_".$hlblock['ID'], $aTabs);

// delete action
if ($is_update_form && isset($_REQUEST['action']) && $_REQUEST['action'] === 'delete' && check_bitrix_sessid())
{
	$entity_data_class::delete($row['ID']);

	LocalRedirect($listUrl);
}

// save action
if ((strlen($save)>0 || strlen($apply)>0) && $REQUEST_METHOD=="POST" && check_bitrix_sessid())
{
	$data = array();

	$USER_FIELD_MANAGER->EditFormAddFields('HLBLOCK_'.$hlblock['ID'], $data);

	/** @param Bitrix\Main\Entity\AddResult $result */
	if ($is_update_form)
	{
		$ID = intval($_REQUEST['ID']);
		$result = $entity_data_class::update($ID, $data);
	}
	else
	{
		$result = $entity_data_class::add($data);
		$ID = $result->getId();
	}

	if($result->isSuccess())
	{
		if (strlen($save)>0)
		{
			LocalRedirect($listUrl);
		}
		else
		{
			LocalRedirect($editUrl."&ID=".intval($ID)."&".$tabControl->ActiveTabParam());
		}
	}
	else
	{
		$errors = $result->getErrorMessages();
	}
}

// menu
$aMenu = array(
	array(
		"TEXT"	=> GetMessage('HLBLOCK_ADMIN_ROWS_RETURN_TO_LIST_BUTTON'),
		"TITLE"	=> GetMessage('HLBLOCK_ADMIN_ROWS_RETURN_TO_LIST_BUTTON'),
		"LINK"	=> $listUrl,
		"ICON"	=> "btn_list",
	)
);

$context = new CAdminContextMenu($aMenu);


//view

if ($_REQUEST["mode"] == "list"){
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");
}else{
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
}

$context->Show();

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
		"FORM_ACTION" => $APPLICATION->GetCurPage()."?ENTITY_ID=".$hlblock['ID']."&ID=".IntVal($ID)."&lang=".LANG
	));?>

	<? $tabControl->BeginNextFormTab(); ?>

	<? $tabControl->AddViewField("ID", "ID", !empty($row)?$row['ID']:''); ?>

	<?$AdminForm->showUserFieldsWithReadyData($tabControl, $hlblock['ID'], $row, false, 'ID');?>

	<?
		$ufields = $USER_FIELD_MANAGER->GetUserFields('HLBLOCK_'.$hlblock['ID']);
		$hasSomeFields = !empty($ufields);
	?>

	<?
	if($showHistory):
		$tabControl->BeginNextFormTab();
		$tabControl->BeginCustomField('LOG', '');
		$CAdminList->CheckListMode();
		?>
		<tr>
			<td colspan="2" valign="top">
				<?$CAdminList->DisplayList()?>
			</td>
		</tr>
		<?$tabControl->EndCustomField('LOG');
	endif; // $showHistory

	$disable = true;
	if($isEditMode)
		$disable = false;

	if ($hasSomeFields)
	{
		$tabControl->Buttons(array(
			"disabled" => $disable,
			"back_url"=>$listUrl
		));
	}
	else
	{
		$tabControl->Buttons(false);
	}?>

	<?$tabControl->Show();?>
</form>
<?
if ($_REQUEST["mode"] == "list")
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");
else
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");