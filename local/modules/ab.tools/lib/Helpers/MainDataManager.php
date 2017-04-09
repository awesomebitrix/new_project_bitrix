<?php
/**
 * Created by OOO 1C-SOFT.
 * User: dremin_s
 * Date: 23.11.2016
 */

namespace AB\Tools\Helpers;

use Bitrix\Main\Entity;
use Bitrix\Main\Entity\Event;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;

class MainDataManager extends Entity\DataManager
{
	protected static $loc_prefix = '';

	/**
	 * @method getTitleField
	 * @param $fieldName
	 *
	 * @return string
	 */
	public static function getTitleField($fieldName)
	{
		if(strlen(static::$loc_prefix) == 0){
			$entityName = static::getEntity()->getFullName();
			$entityName = str_replace('\\', '_', $entityName);
			static::$loc_prefix = strtoupper('ENTITY_'.$entityName.'_FIELD_');
		}
		return Loc::getMessage(static::$loc_prefix.$fieldName);
	}

	/**
	 * @method getUser
	 * @return array|bool|\CDBResult|\CUser|mixed
	 */
	public static function getUser()
	{
		global $USER;
		if(!$USER instanceof \CUser){
			$USER = new \CUser();
		}

		return $USER;
	}

	/**
	 * @method getIndexes
	 * @return array
	 */
	protected static function getIndexes()
	{
		return array();
	}

	/**
	 * @method createTable
	 */
	public static function createTable()
	{
		$tableName = static::getTableName();
		$connect = static::getEntity()->getConnection();
		if(!is_null($tableName) && !$connect->isTableExists($tableName)){
			TableHelper::createTable(static::getEntity(), static::getIndexes());
		}
	}

	/**
	 * @method clearTable
	 */
	public static function clearTable()
	{
		$tableName = static::getTableName();
		$connect = static::getEntity()->getConnection();
		if(!is_null($tableName) && $connect->isTableExists($tableName)){
			$connect->truncateTable($tableName);
		}
	}

	/**
	 * @method dropTable
	 */
	public static function dropTable()
	{
		$tableName = static::getTableName();
		$connect = static::getEntity()->getConnection();
		if(!is_null($tableName) && $connect->isTableExists($tableName)){
			$connect->dropTable($tableName);
		}
	}

	/**
	 * @method getDbTableStructureDump
	 * @return array of string
	 */
	public static function getDbTableStructureDump()
	{
		return TableHelper::getDump(static::getEntity());
	}

	/**
	 * @method onBeforeUpdate
	 * @param Event $event
	 *
	 * @return Entity\EventResult
	 */
	public static function onBeforeUpdate(Event $event)
	{
		$result = new Entity\EventResult();
		$modifier = [];

		$entity = $event->getEntity();

		if($entity->hasField('USER_X')){
			$modifier['USER_X'] = self::getUser()->GetID();
		}
		if($entity->hasField('USER_UPDATE')){
			$modifier['USER_UPDATE'] = self::getUser()->GetID();
		}

		if($entity->hasField('DATE_X')){
			$modifier['DATE_X'] = new DateTime();
		}
		if($entity->hasField('DATE_UPDATE')){
			$modifier['DATE_UPDATE'] = new DateTime();
		}

		$result->modifyFields($modifier);

		return $result;
	}
}