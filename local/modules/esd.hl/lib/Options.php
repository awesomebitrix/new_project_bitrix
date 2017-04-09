<?php namespace Esd\HL;

use Bitrix\Main\Config\Option;
use PW\Tools\Debug;

class Options
{
	protected static $userTypes = array();
	protected static $fieldsParam = array();

	/**
	 * Options constructor.
	 */
	public function __construct()
	{

	}

	/**
	 * @method changeMenu
	 * @param array $data
	 * @return array
	 * @throws \Bitrix\Main\ArgumentNullException
	 */
	public static function changeMenu($data)
	{
		$arMenu = unserialize(Option::get(PW_HL_MODULE, 'menu_items'));
		if(array_key_exists($data['ID'], $arMenu)){
			$arMenu[$data['ID']]['TITLE'] = $data['item'];
		} elseif(strlen($data['item']) > 0) {
			$arMenu[$data['ID']]['TITLE'] = $data['item'];
		}

		Option::set(PW_HL_MODULE, 'menu_items', serialize($arMenu));
		return $arMenu;
	}

	public static function changeUrl($data)
	{
		$arMenu = unserialize(Option::get(PW_HL_MODULE, 'menu_items'));
		if(array_key_exists($data['ID'], $arMenu)){
			$arMenu[$data['ID']]['URL'] = $data['item'];
		} elseif(strlen($data['item']) > 0) {
			$arMenu[$data['ID']]['URL'] = $data['item'];
		}
		Option::set(PW_HL_MODULE, 'menu_items', serialize($arMenu));
		return $arMenu;
	}

	/**
	 * @method getModuleMenu
	 * @return array
	 * @throws \Bitrix\Main\ArgumentNullException
	 */
	public static function getModuleMenu()
	{
		/** @var \Bitrix\Main\HttpRequest $request */
		$request = \Bitrix\Main\Context::getCurrent()->getRequest();

		$items = array();
		$arMenu = unserialize(Option::get(PW_HL_MODULE, 'menu_items'));

		foreach ($arMenu as $id => $menu) {
			$url = [
				'list'=>'pw_hl_row_list.php?ENTITY_ID='.$id.'&lang='.LANGUAGE_ID,
				'edit'=>'pw_hl_row_edit.php?ENTITY_ID='.$id.'&lang='.LANGUAGE_ID
			];

			if(isset($menu['URL'])){
				$url = [
					'list'=>$menu['URL'].'_list.php?ENTITY_ID='.$id.'&lang='.LANGUAGE_ID,
					'edit'=>$menu['URL'].'_edit.php?ENTITY_ID='.$id.'&lang='.LANGUAGE_ID,
				];
			}

			$items[] = array(
				"text" => $menu['TITLE'],
				"url" => $url['list'],
				"module_id" => PW_HL_MODULE,
				"icon"=> "iblock_menu_icon_iblocks",
				"more_url" => $url
			);
		}

		return $items;
	}

	/**
	 * @method getBlocks
	 * @return array
	 * @throws \Bitrix\Main\ArgumentException
	 * @throws \Bitrix\Main\ArgumentNullException
	 */
	public static function getBlocks()
	{
		$arHBlocks = array();
		$arMenu = unserialize(Option::get(PW_HL_MODULE, 'menu_items'));
		$obBlock = MainTable::getList();
		while($block = $obBlock->fetch()){
			$title = strlen($arMenu[$block['ID']]['TITLE']) > 0 ? $arMenu[$block['ID']]['TITLE'] : $block['NAME'];
			$arHBlocks[] = array(
				'ID'=>$block['ID'],
				'ENTITY'=>$block['NAME'],
				'TITLE'=>$title
			);
		}
		return $arHBlocks;
	}

	/**
	 * @method getFields
	 * @param array $data
	 * @return array
	 */
	public static function getFields($data)
	{
		global $USER_FIELD_MANAGER;
		$ID = $data['ID'];

		$arFields = $USER_FIELD_MANAGER->GetUserFields('HLBLOCK_'.$ID, null, LANGUAGE_ID);
		$param = self::getFieldsParam();
		foreach ($arFields as $code => $field) {
			if(array_key_exists($code, $param[$ID])){
				$arFields[$code]['fieldEdit'] = array(
					'type'=> $param[$ID][$code]['type'],
					'title'=> $param[$ID][$code]['title']
				);
			} else {
				$arFields[$code]['fieldEdit'] = array('type'=>'none', 'title'=> ' --- ');
			}

			if(strlen($arFields[$code]['EDIT_FORM_LABEL']) == 0){
				$arFields[$code]['EDIT_FORM_LABEL'] = $field['FIELD_NAME'];
			}
		}

		return $arFields;
	}

	/**
	 * @method getOptionFields
	 * @param array $data
	 * @return array
	 */
	public static function getOptionFields($data)
	{
		return array(
			'FIELDS'=>self::getFields($data),
			'TYPES'=>self::getUserTypes()
		);
	}

	/**
	 * @method saveFieldsParam
	 * @param array $data
	 * @return array
	 */
	public static function saveFieldsParam($data = array())
	{
		$param = self::getFieldsParam();
		foreach ($data['ITEMS'] as $code => $item) {
			if($item['type'] != 'none'){
				$param[$data['HL']][$code] = $item;
			}
		}
		Option::set(PW_HL_MODULE, 'fields_type', serialize($param));
		return array('type'=>'success','msg'=>'Все настройки сохранены');
	}

	/**
	 * @method getFieldsParam
	 * @return array
	 * @throws \Bitrix\Main\ArgumentNullException
	 */
	public static function getFieldsParam()
	{
		if(count(self::$fieldsParam) == 0){
			self::$fieldsParam = unserialize(Option::get(PW_HL_MODULE,'fields_type'));
		}
		return self::$fieldsParam;
	}

	/**
	 * @method getUserTypes
	 * @return array
	 */
	public static function getUserTypes()
	{
		if(count(self::$userTypes) > 0)
			return self::$userTypes;

		$dir = dirname(__FILE__).'/Type';
		$arFilesPath = glob($dir.'/*.php');
		$nameSpace = __NAMESPACE__.'\\Type';
		self::$userTypes[] = array('type'=>'none','title'=>' --- ');
		foreach ($arFilesPath as $file) {
			preg_match('#.*\/(.*).php$#i', $file, $matchName);
			$Ref = new \ReflectionClass($nameSpace.'\\'. $matchName[1]);
			if(!$Ref->isInterface()){
				$obType = $Ref->newInstance();
				/** @var \Esd\HL\Type\IType $obType */
				$desc = $obType->GetUserTypeDescription();
				self::$userTypes[] = array(
					'type'=>$desc['USER_TYPE_ID'], 'title'=>$desc['DESCRIPTION']
				);
			}
			unset($obType);
		}
		return self::$userTypes;
	}

	/**
	 * @method getOptionLog
	 * @param array $data
	 * @return array
	 * @throws \Bitrix\Main\ArgumentNullException
	 */
	public static function setOptionLog($data)
	{
		$arOption = unserialize(Option::get(PW_HL_MODULE, 'hl_history'));

		$arOption[$data['ID']] = self::setRegisterEvents($data['ID'], $data['CHECK']);

		Option::set(PW_HL_MODULE, 'hl_history', serialize($arOption));
		return $arOption;
	}

	/**
	 * @method setRegisterEvents
	 * @param string $entityName
	 * @param string $status
	 * @return bool|null
	 */
	public static function setRegisterEvents($entityName, $status)
	{
		$result = null;
		$EventManager = \Bitrix\Main\EventManager::getInstance();
		if($status){
			$EventManager->registerEventHandler('', $entityName.'OnAfterUpdate', PW_HL_MODULE,'\Esd\HL\EventHandlers', 'addToHistory');
			$EventManager->registerEventHandler('', $entityName.'OnAfterAdd', PW_HL_MODULE,'\Esd\HL\EventHandlers', 'addToHistory');
			$EventManager->registerEventHandler('', $entityName.'OnAfterDelete', PW_HL_MODULE,'\Esd\HL\EventHandlers', 'delHistory');
//			$EventManager->registerEventHandler('', $entityName.'OnBeforeDelete', PW_HL_MODULE,'\Esd\HL\EventHandlers', 'OnBeforeDelete');

			$result = true;
		} else {
			$EventManager->unRegisterEventHandler('', $entityName.'OnAfterUpdate', PW_HL_MODULE,'\Esd\HL\EventHandlers', 'addToHistory');
			$EventManager->unRegisterEventHandler('', $entityName.'OnAfterAdd', PW_HL_MODULE,'\Esd\HL\EventHandlers', 'addToHistory');
			$EventManager->unRegisterEventHandler('', $entityName.'OnAfterDelete', PW_HL_MODULE,'\Esd\HL\EventHandlers', 'delHistory');
//			$EventManager->unRegisterEventHandler('', $entityName.'OnBeforeDelete', PW_HL_MODULE,'\Esd\HL\EventHandlers', 'OnBeforeDelete');
		}
		return $result;
	}
}