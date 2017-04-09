<?php
$module_id = "esd.hl";
/** @global CMain $APPLICATION */

global $USER_FIELD_MANAGER, $APPLICATION, $USER;

use Bitrix\Main;
use Bitrix\Main\Loader;
use Bitrix\Main\Entity;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Localization\Loc;
use Esd\HL\EventHandlers;
use Bitrix\Main\Config\Option;

$SALE_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($SALE_RIGHT>="R"):
	Loc::loadMessages($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/options.php');
	Loc::loadMessages(__FILE__);

//	Main\Page\Asset::getInstance()->addJs('/bitrix/js/sale/options.js');
//	$APPLICATION->SetAdditionalCSS("/bitrix/themes/.default/sale.css");

	Loader::includeModule($module_id);
	/** @var \Bitrix\Main\HttpRequest $request */
	$request = Bitrix\Main\Context::getCurrent()->getRequest();

	$Asset = Bitrix\Main\Page\Asset::getInstance();

	$APPLICATION->SetAdditionalCSS('/bitrix/themes/.default/esd.hl/css/bootstrap.min.css');
	$APPLICATION->SetAdditionalCSS('/bitrix/themes/.default/esd.hl/css/font-awesome.min.css');
	$APPLICATION->SetAdditionalCSS('/bitrix/themes/.default/esd.hl/css/esd.hl.css');

	$Asset->addJs('/bitrix/js/esd.hl/jquery/jquery-2.1.4.min.js');
	$Asset->addJs('/bitrix/js/esd.hl/ang/angular.min.js');
	$Asset->addJs('/bitrix/js/esd.hl/ang/angular-resource.min.js');
	$Asset->addJs('/bitrix/js/esd.hl/ang/ui-bootstrap-tpls-0.13.4.min.js');
	$Asset->addJs('/bitrix/js/esd.hl/jquery/bootstrap-growl.min.js');
	$Asset->addJs('/bitrix/js/esd.hl/jquery/jquery-toggles.min.js');
	$Asset->addJs('/bitrix/js/esd.hl/ajax_service.js');

	$Asset->addJs('/bitrix/js/esd.hl/admin_options.js');

	$arAllOptions = array(
		'MENU'=>array('title'=>Loc::getMessage('EHL_OPT_MENU')),
		'FIELDS'=>array('title'=>Loc::getMessage('EHL_OPT_FIELDS')),
		'LOG'=>array('title'=>Loc::getMessage('EHL_OPT_LOG')),
		'RIGHT'=>array('title'=>Loc::getMessage('EHL_OPT_RIGHT'))
	);

	$aTabs = array();

	foreach ($arAllOptions as $code => $val) {
		$aTabs[] = array(
			"DIV" => "edit_".$code,
			"TAB" => $val['title'],
			"ICON" => "esd_hl_settings",
			"TITLE" => $val['title'],
		);
	}


	$sTableID = 'tbl_menu_hl';
	$arBlocks =  Esd\HL\MainTable::getList()->fetchAll();
	$arMenuMain = array();
	$contentItemMenu = 'global_menu_content';
	$arOptionMenu = unserialize(Option::get($module_id, 'menu_items'));
	$arEventOption = unserialize(Option::get($module_id, 'hl_history'));

	$EventManager = Bitrix\Main\EventManager::getInstance();
//	$EventManager->registerEventHandler('main','OnBuildGlobalMenu',$module_id,'\Esd\HL\EventHandlers','buildAdminMenu');

//	$EventManager->registerEventHandler('','ProgramKeysOnBeforeAdd',$module_id,'\Esd\HL\EventHandlers','onAddChangeInfo');
//	$EventManager->registerEventHandler('','ProgramKeysOnBeforeUpdate',$module_id,'\Esd\HL\EventHandlers','onUpdateChangeInfo');
//	$EventManager->unRegisterEventHandler('','ProgramKeysOnBeforeUpdate',$module_id,'\Esd\HL\EventHandlers','onAddChangeInfo');
//	$EventManager->unRegisterEventHandler('','ProgramKeysOnBeforeAdd',$module_id,'\Esd\HL\EventHandlers','onUpdateChangeInfo');

//	$EventManager->addEventHandler('main','OnBuildGlobalMenu',$module_id,'\Esd\HL\EventHandlers','buildAdminMenu');

//	$EventManager->registerEventHandler('main','OnUserTypeBuildList',$module_id,'\Esd\HL\Type\FileAjax','GetUserTypeDescription');
//	$EventManager->unRegisterEventHandler('main','OnUserTypeBuildList',$module_id,'\Esd\HL\Type\FileAjax','GetUserTypeDescription');

//	PR($EventManager->findEventHandlers('','ProgramKeysOnBeforeAdd'));

	$tabControl = new CAdminTabControl("tabControl", $aTabs);
	$tabControl->Begin();
	?>
	<div ng-app="OptionApp">
		<?$tabControl->BeginNextTab();?>
		<tr>
			<td>
				<table class="table table-striped table-hover table-bordered table_esd">
					<thead>
					<tr class="warning">
						<th>ID</th>
						<th>Сущность</th>
						<th>Название в меню</th>
						<th>url</th>
					</tr>
					</thead>
					<tbody>
					<?foreach ($arBlocks as $k => $block):?>
						<tr>
							<td><?=$block['ID']?></td>
							<td><?=$block['NAME']?></td>
							<td>
								<div hl-menu="<?=$arOptionMenu[$block['ID']]['TITLE']?>" block="<?=$block['ID']?>"></div>
							</td>
							<td>
								<div hl-menu-url="<?=$arOptionMenu[$block['ID']]['URL']?>" block="<?=$block['ID']?>"></div>
							</td>
						</tr>
					<?endforeach;?>
					</tbody>
				</table>
			</td>
		</tr>


		<?$tabControl->BeginNextTab();?>
		<tr>
			<td>
				<hl-fields></hl-fields>
			</td>
		</tr>

		<?$tabControl->BeginNextTab();?>
		<tr>
			<td>
				<table class="table table-striped table-hover table-bordered table_esd">
					<thead>
					<tr class="warning">
						<th>ID</th>
						<th>Сущность</th>
						<th>Лоигровать</th>
					</tr>
					</thead>
					<tbody>
					<?foreach ($arBlocks as $k => $block):
						$active = $arEventOption[$block['NAME']];
						?>
						<tr>
							<td><?=$block['ID']?></td>
							<td><?=$block['NAME']?></td>
							<td>
								<div class="hl_log_switcher light" switcher="<?=$block['NAME']?>" check="<?=$active?>">
									<div class="toggle" data-checkbox="checkme"></div>
									<input type="checkbox" style="display: none;" class="checkme" value="" />
								</div>
							</td>
						</tr>
					<?endforeach;?>
					</tbody>
				</table>
			</td>
		</tr>


		<?$tabControl->BeginNextTab();?>
		<form method="POST" action="<?= $APPLICATION->GetCurPage()?>?mid=<?=$module_id?>&lang=<?=LANGUAGE_ID?>" name="opt_form">
			<tr>
				<td>
					<?=bitrix_sessid_post();?>
					<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
					<input type="hidden" name="Update" value="Y">
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					<div class="hl_btn_row_group bx_btn">
						<input type="submit" name="save" value="Сохранить" title="Сохранить" class="adm-btn-save">
					</div>
				</td>
				<td></td>
				<td></td>
			</tr>
		</form>
	</div>
	<?$tabControl->End();?>

<?else:?>
	<?$APPLICATION->AuthForm('');?>
<?endif;?>

