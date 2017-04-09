<?php
use Bitrix\Main\Entity;
use Bitrix\Highloadblock as HL;
use Esd\HL\MainTable;
use Bitrix\Main\Localization\Loc;

// admin initialization
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
$moduleId = 'esd.hl';
IncludeModuleLangFile(__FILE__);

global $APPLICATION, $USER, $USER_FIELD_MANAGER;
$modulePermissions = $APPLICATION->GetGroupRight($moduleId);

if ($modulePermissions == "D"){
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}
if (!\Bitrix\Main\Loader::IncludeModule($moduleId)) {
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

/** @var \Bitrix\Main\HttpRequest $request */
$request = \Bitrix\Main\Context::getCurrent()->getRequest();
$entityId = intval($request['ENTITY_ID']);
// get entity settings
$editUrl = 'esd_key_edit.php?ENTITY_ID='.$entityId.'&lang='.LANGUAGE_ID;
$listUrl = 'esd_key_list.php?ENTITY_ID='.$entityId.'&lang='.LANGUAGE_ID;
$importUrl = 'pw_hl_csv.php?ENTITY_ID='.$entityId.'&lang='.LANGUAGE_ID;

if ($entityId == 0)
{
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

	echo GetMessage('HLBLOCK_ADMIN_ROWS_LIST_NOT_FOUND');

	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");

	exit;
}

$MainTable = new MainTable($entityId);
$entity = $MainTable->getHLEntity();

$AdminList = new Esd\HL\Admin\AdminList($MainTable);
$AdminList->setHeaders();
$hlblock = $MainTable->getHBlock();


$arOptions = $AdminList->getOptions($hlblock['ID']);
$titlePage = strlen($arOptions['menu_items']['TITLE']) > 0 ? $arOptions['menu_items']['TITLE'] :  $hlblock['NAME'];

$APPLICATION->SetTitle($titlePage);

/** @var HL\DataManager $entity_data_class */
$entity_data_class = $entity->getDataClass();
$entity_table_name = $hlblock['TABLE_NAME'];

$sTableID = 'tbl_'.$entity_table_name;
$oSort = new CAdminSorting($sTableID, "ID", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);
$arHeaders = $AdminList->getHeaders();

$ufEntityId = 'HLBLOCK_'.$hlblock['ID'];

$arBlockTypes = array(
	'2'=>'GAMES_IB',
	'3'=>'PROGRAMMS_IB'
);

foreach ($arHeaders as &$arHeader) {
//	if($head['id'] == 'UF_PRODUCT' || $head['id'] == 'UF_SERVICE' || $head['id'] == 'UF_USER_X' || $head['id'] == 'UF_USER_ADD'){
//		$arHeader['default'] = false;
//	}
	$arHeader['default'] = true;
}

unset($arHeader);



$lAdmin->AddHeaders($arHeaders);

if (!in_array($by, $lAdmin->GetVisibleHeaderColumns(), true))
{
	$by = 'ID';
}

// add filter
$filter = null;

$filterFields = array('find_id');
$filterValues = array();
$filterTitles = array('ID');

$AdminList->addFilterFields($filterFields);
$filter = $lAdmin->InitFilter($filterFields);

if (!empty($find_id))
{
	$filterValues['ID'] = $find_id;
}

$AdminList->addAdminListFilter($filterValues);
$AdminList->addFindFields($filterTitles);

$filter = new CAdminFilter(
	$sTableID."_filter_id",
	$filterTitles
);

// group actions
if($lAdmin->EditAction()) {
	foreach($FIELDS as $ID=>$arFields)
	{
		$ID = (int)$ID;
		if ($ID <= 0)
			continue;

		if(!$lAdmin->IsUpdated($ID))
			continue;

		$entity_data_class::update($ID, $arFields);
	}
}
if($arID = $lAdmin->GroupAction()) {
	if($_REQUEST['action_target']=='selected') {
		$arID = array();

		$rsData = $entity_data_class::getList(array(
			"select" => array('ID'),
			"filter" => $filterValues
		));

		while($arRes = $rsData->Fetch())
			$arID[] = $arRes['ID'];
	}

	foreach ($arID as $ID) {
		$ID = (int)$ID;

		if (!$ID) {
			continue;
		}

		switch($_REQUEST['action']) {
			case "delete":
				$entity_data_class::delete($ID);
				break;
			case 'activate':
				if($entity->getField('UF_ACTIVE') && $entity->getField('UF_ACTIVE') instanceof Entity\IntegerField)
					$resultUpdate = $entity_data_class::update($ID, array('UF_ACTIVE'=>1));
				break;
			case 'deactivate':
				if($entity->getField('UF_ACTIVE') && $entity->getField('UF_ACTIVE') instanceof Entity\IntegerField)
					$resultUpdate = $entity_data_class::update($ID, array('UF_ACTIVE'=>0));
				break;
		}
	}
}
$lAdmin->AddGroupActionTable(array(
	'delete' => true,
	'activate'=>Loc::getMessage('PW_HL_EL_ACTION_ACTIVE'),
	'deactivate'=>Loc::getMessage('PW_HL_EL_ACTION_DEACTIVE'),
));

// select data
/** @var string $order */
$order = strtoupper($order);

$usePageNavigation = true;
if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'excel') {
	$usePageNavigation = false;
}else {
	$navyParams = CDBResult::GetNavParams(CAdminResult::GetNavSize(
		$sTableID,
		array('nPageSize' => 20, 'sNavID' => $APPLICATION->GetCurPage().'?ENTITY_ID='.$entityId)
	));
	if ($navyParams['SHOW_ALL']) {
		$usePageNavigation = false;
	} else {
		$navyParams['PAGEN'] = (int)$navyParams['PAGEN'];
		$navyParams['SIZEN'] = (int)$navyParams['SIZEN'];
	}
}

$arSelect = $lAdmin->GetVisibleHeaderColumns();

$runTime = array(
	new Entity\ReferenceField(
		'PRODUCT',
		\Bitrix\Iblock\ElementTable::getEntity(),
		array('=this.UF_PRODUCT'=>'ref.ID')
	),
	new Entity\ReferenceField(
		'SERVICE',
		\Bitrix\Iblock\ElementTable::getEntity(),
		array('=this.UF_SERVICE'=>'ref.ID')
	),
);
$dopSelect = [
	'PRODUCT_NAME'=>'PRODUCT.NAME',
	'PRODUCT_IBLOCK'=>'PRODUCT.IBLOCK_ID',
	'SERVICE_NAME'=>'SERVICE.NAME',
 	/*'USER_X_LOGIN'=>'USER_X.LOGIN',
	'USER_ADD_LOGIN'=>'USER_ADD.LOGIN'*/
];
$arSelect = array_merge($arSelect, $dopSelect);
//PR($arSelect);

$getListParams = array(
	'select' => $arSelect,
	'filter' => $filterValues,
	'order' => array($by => $order),
	'runtime'=>$runTime
);
unset($filterValues);
if ($usePageNavigation) {
	$getListParams['limit'] = $navyParams['SIZEN'];
	$getListParams['offset'] = $navyParams['SIZEN']*($navyParams['PAGEN']-1);
}

if ($usePageNavigation) {
	$countQuery = new Entity\Query($entity_data_class::getEntity());
	$countQuery->addSelect(new Entity\ExpressionField('CNT', 'COUNT(1)'));
	$countQuery->setFilter($getListParams['filter']);
	$totalCount = $countQuery->setLimit(null)->setOffset(null)->exec()->fetch();
	unset($countQuery);
	$totalCount = (int)$totalCount['CNT'];
	if ($totalCount > 0)
	{
		$totalPages = ceil($totalCount/$navyParams['SIZEN']);
		if ($navyParams['PAGEN'] > $totalPages)
			$navyParams['PAGEN'] = $totalPages;
		$getListParams['limit'] = $navyParams['SIZEN'];
		$getListParams['offset'] = $navyParams['SIZEN']*($navyParams['PAGEN']-1);
	}else {
		$navyParams['PAGEN'] = 1;
		$getListParams['limit'] = $navyParams['SIZEN'];
		$getListParams['offset'] = 0;
	}
}
$rsData = new CAdminResult($entity_data_class::getList($getListParams), $sTableID);
if ($usePageNavigation) {
	$rsData->NavStart($getListParams['limit'], $navyParams['SHOW_ALL'], $navyParams['PAGEN']);
	$rsData->NavRecordCount = $totalCount;
	$rsData->NavPageCount = $totalPages;
	$rsData->NavPageNomer = $navyParams['PAGEN'];
}else {
	$rsData->NavStart();
}

//PR($USER_FIELD_MANAGER->arFieldsCache);

// build list
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("PAGES")));
while($arRes = $rsData->NavNext(true, "f_")) {

	$row = $lAdmin->AddRow($f_ID, $arRes);
	$AdminList->addViewField('HLBLOCK_'.$hlblock['ID'], $arRes, $row);

	$row->AddViewField('UF_SERIAL',sprintf('<a href="%s&ID=%d">%s</a>', $editUrl, $arRes['ID'], $arRes['UF_SERIAL']));
	$row->AddViewField('UF_PRODUCT', sprintf(
		'%s <a target="_blank" href="/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=%d&type=%s&ID=%d&lang=%s">[%d]</a>',
		$arRes['PRODUCT_NAME'],
		$arRes['PRODUCT_IBLOCK'],
		$arBlockTypes[$hlblock['ID']],
		$arRes['UF_PRODUCT'],
		LANGUAGE_ID,
		$arRes['UF_PRODUCT']
	));
	$row->AddViewField('UF_SERVICE', sprintf(
		'%s <a target="_blank" href="/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=51&type=billing&ID=%d&lang=%s">[%d]</a>',
		$arRes['SERVICE_NAME'],
		$arRes['UF_SERVICE'],
		LANGUAGE_ID,
		$arRes['UF_SERVICE']
	));


	$can_edit = true;

	$arActions = array();

	$arActions[] = array(
		"ICON" => "edit",
		"TEXT" => GetMessage($can_edit ? "MAIN_ADMIN_MENU_EDIT" : "MAIN_ADMIN_MENU_VIEW"),
		"ACTION" => $lAdmin->ActionRedirect($editUrl.'&ID='.$f_ID),
		"DEFAULT" => true
	);

	$arActions[] = array(
		"ICON"=>"delete",
		"TEXT" => GetMessage("MAIN_ADMIN_MENU_DELETE"),
		"ACTION" => "if(confirm('".GetMessageJS('HLBLOCK_ADMIN_DELETE_ROW_CONFIRM')."')) ".
			$lAdmin->ActionRedirect($editUrl.'&'.bitrix_sessid_get().'&action=delete&ID='.$arRes['ID'])
	);

	$row->AddActions($arActions);
}


// view
$lAdmin->AddAdminContextMenu(array(
	array(
		"TEXT"	=> GetMessage('HLBLOCK_ADMIN_ROWS_ADD_NEW_BUTTON'),
		"TITLE"	=> GetMessage('HLBLOCK_ADMIN_ROWS_ADD_NEW_BUTTON'),
		"LINK"	=> $editUrl,
		"ICON"	=> "btn_new"
	),
	array(
		"TEXT"	=> GetMessage('PW_HL_ADMIN_LIST_IMPORT'),
		"TITLE"	=> GetMessage('PW_HL_ADMIN_LIST_IMPORT'),
		"LINK"	=> $importUrl,
		"ICON"	=> "btn_new"
	)
));
$lAdmin->CheckListMode();

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

?>
	<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?ENTITY_ID=<?=$hlblock['ID']?>">
		<?
		$filter->Begin();
		?>
		<tr>
			<td>ID</td>
			<td><input type="text" name="find_id" size="47" value="<?echo htmlspecialcharsbx($find_id)?>"><?=ShowFilterLogicHelp()?></td>
		</tr>
		<?
//		$USER_FIELD_MANAGER->AdminListShowFilter($ufEntityId);
		$arUserFields = $AdminList->getUserFields();
		foreach($arUserFields as $FIELD_NAME=>$arUserField)
		{
			if($arUserField["SHOW_FILTER"]!="N" && $arUserField["USER_TYPE"]["BASE_TYPE"]!="file"){
				echo $AdminList->getFilterHTML($arUserField, "find_".$FIELD_NAME, $GLOBALS["find_".$FIELD_NAME]);
			}
		}

		$filter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage().'?ENTITY_ID='.$hlblock['ID'], "form"=>"find_form"));
		$filter->End();
		?>
	</form>
<?

$lAdmin->DisplayList();
echo BeginNote();?>
	<span><a target="_blank"
			 href="/bitrix/admin/userfield_admin.php?lang=ru&set_filter=Y&find=HLBLOCK_<?=$request['ENTITY_ID']?>&find_type=ENTITY_ID">
			Настройка полей блока
		</a></span>
<?echo EndNote();
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");