<?php namespace Esd\HL;

use Bitrix\Main\Type;
use Bitrix\Main\Entity;
use Bitrix\Main\Application;
use Esd\Debug;

/**
 * Class EventHandlers
 * @package Esd\HL
 */
class EventHandlers
{
	public function buildAdminMenu(&$aGlobalMenu, &$aModuleMenu)
	{
		$param = Tools::getModule();
		$arMenu = self::getAdminMenuTree($aGlobalMenu, $aModuleMenu);
	}

	protected static function getAdminMenuTree($aGlobalMenu, $aModuleMenu)
	{
		$result = array();
		foreach ($aGlobalMenu as $code => $val) {
			foreach($aModuleMenu as $arMenu){
				if($arMenu['parent_menu'] == $code){
					$val['items'][] = $arMenu;
				}
			}

			$result[$code] = $val;
		}

//		$aGlobalMenu['global_menu_content']['items'] = array();
//
//		PR($aGlobalMenu);
		return $result;
	}

	/**
	 * @method getCacheMenuId
	 * @param array $arParam
	 * @return string
	 */
	public static function getCacheMenuId($arParam = array())
	{
		$param = $arParam;
		if(count($param) == 0){
			$param = Tools::getModule();;
		}
		return $param['id'].'_build_menu';
	}

	/**
	 * @method getCacheMenuTime - get param cacheMenuTime
	 * @return mixed
	 */
	public static function getCacheMenuTime()
	{
		return 86400 * 365;
	}

	/**
	 * @method getCacheMenuDir
	 * @param array $arParam
	 * @return string
	 */
	public static function getCacheMenuDir($arParam = array())
	{
		$param = $arParam;
		if(count($param) == 0){
			$param = Tools::getModule();
		}
		return '/'.$param['id'];
	}

	/**
	 * @method addToHistory
	 * @param Entity\Event $event
	 * @return Entity\EventResult
	 */
	public static function addToHistory(Entity\Event $event)
	{
		$result = new Entity\EventResult();
		$params = $event->getParameters();
		$entity = $event->getEntity()->getName();

		$ID = intval($params['id']['ID']) > 0 ? $params['id']['ID'] : $params['id'];

		$arHistory = LogTable::getRow(array(
			'select'=>array('FIELDS'),
			'filter'=>array('ENTITY_NAME'=>$entity, 'ELEMENT_ID'=>$ID),
			'order'=>array('ID'=>'DESC')
		));
		$change = [];
		foreach ($arHistory['FIELDS'] as $code => $val) {
			if(!$val instanceof Type\Date && !$val instanceof Type\DateTime && !$val instanceof \DateTime){
				if($val != $params['fields'][$code])
					$change[$code] = $val;
			}
		}

		if((count($change) > 0 && !is_null($arHistory)) || is_null($arHistory)){
			$arLog = array(
				'ELEMENT_ID'=>$ID,
				'FIELDS'=>$params['fields'],
				'ENTITY_NAME'=>$entity
			);
			$resLog = LogTable::add($arLog);
			$result->addError(new Entity\EntityError(
				implode(', ', $resLog->getErrorMessages())
			));
		}

		return $result;
	}

	/**
	 * @method delHistory
	 * @param Entity\Event $event
	 * @return Entity\EventResult
	 */
	public static function delHistory(Entity\Event $event)
	{
		$result = new Entity\EventResult();
		$params = $event->getParameter('id');
		$entity = $event->getEntity()->getName();

		$sql = "DELETE FROM ".LogTable::getTableName()." WHERE ELEMENT_ID='".$params['ID']."' AND ENTITY_NAME='".$entity."';";
		Application::getConnection()->query($sql);

		return $result;
	}

	/**
	 * @method onAddChangeInfo
	 * @param Entity\Event $event
	 * @return Entity\EventResult
	 */
	public static function onAddChangeInfo(Entity\Event $event)
	{
		global $USER;
		$result = new Entity\EventResult();
		$params = $event->getParameter('fields');
		$changedFields = [];
		$date = new \Bitrix\Main\Type\DateTime();
		if(empty($params['UF_DATE'])){
			$changedFields['UF_DATE'] = $date;
		}
		$changedFields['UF_USER_X'] = $USER->GetID();
		$changedFields['UF_USER_ADD'] = $USER->GetID();
		$changedFields['UF_DATE_X'] = $date;
		$changedFields['UF_DATE_ADD'] = $date;

		$result->modifyFields($changedFields);

		return $result;
	}

	/**
	 * @method onUpdateChangeInfo
	 * @param Entity\Event $event
	 * @return Entity\EventResult
	 */
	public static function onUpdateChangeInfo(Entity\Event $event)
	{
		global $USER;
		$result = new Entity\EventResult();
		$changedFields = [];
		$date = new \Bitrix\Main\Type\DateTime();

		$changedFields['UF_USER_X'] = $USER->GetID();
		$changedFields['UF_DATE_X'] = $date;

		$result->modifyFields($changedFields);

		return $result;
	}
}