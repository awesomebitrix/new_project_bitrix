<?php namespace Esd\HL;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main;
use Esd\Debug;

Loc::loadMessages(__FILE__);

/**
 * Class LogKeys
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> ELEMENT_ID int mandatory
 * <li> USER_X int optional
 * <li> FIELDS string optional
 * <li> DATE_X datetime mandatory
 * <li> VERSION int mandatory
 * </ul>
 *
 * @package Esd\HL
 **/

class LogTable extends Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'hl_log';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		global $USER;
		if (!is_object($USER))
			$USER = new \CUser();

		$helper = Main\Application::getConnection()->getSqlHelper();

		return array(
			'ID' => new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('LOG_ENTITY_ID_FIELD'),
			)),
			'ELEMENT_ID' => new Entity\IntegerField('ELEMENT_ID', array(
				'primary' => true,
				'title' => Loc::getMessage('LOG_ENTITY_ELEMENT_ID_FIELD'),
			)),
			'USER_X' => new Entity\IntegerField('USER_X', array(
				'primary' => true,
				'title' => Loc::getMessage('LOG_ENTITY_USER_X_FIELD'),
				'default_value' => $USER->GetID()
			)),
			'FIELDS' => new Entity\TextField('FIELDS', array(
				'title' => Loc::getMessage('LOG_ENTITY_FIELDS_FIELD'),
				'serialized' => true
			)),
			'DATE_X' => new Entity\DatetimeField('DATE_X', array(
				'title' => Loc::getMessage('LOG_ENTITY_DATE_X_FIELD'),
				'default_value' => new Main\Type\DateTime()
			)),
			'VERSION' => new Entity\IntegerField('VERSION', array(
				'primary' => true,
				'title' => Loc::getMessage('LOG_ENTITY_VERSION_FIELD'),
			)),
			'ENTITY_NAME' => new Entity\StringField('ENTITY_NAME', array(
				'title' => Loc::getMessage('LOG_ENTITY_ENTITY_ID_FIELD'),
				'primary' => true,
			)),
			'USER' => new Entity\ReferenceField(
				'USER',
				\Bitrix\Main\UserTable::getEntity(),
				array('=this.USER_X'=>'ref.ID')
			),
		);
	}

	/**
	 * @method getAdminHead
	 * @return array
	 */
	public static function getAdminHead()
	{
		$result = array();
		$fields = self::getEntity()->getFields();
		foreach ($fields as $obField) {
			if(!$obField instanceof Entity\ReferenceField){
				$title = $obField->getTitle() ? $obField->getTitle() : $obField->getName();
				$default = true;

				if($obField->getName() == 'ID')
					$default = false;

				$result[] = [
					'id'=>$obField->getName(),
					'content'=>$title,
					'sort'=>false,
					'default'=>$default
				];
			}
		}
		return $result;
	}

	/**
	 * @method add
	 * @param array $data
	 * @return Entity\AddResult
	 * @throws \Bitrix\Main\ArgumentException
	 * @throws \Exception
	 */
	public static function add(array $data)
	{
		if (!isset($data['VERSION'])) {
			$arElement = parent::getList(array(
				'select' => array(
					new Entity\ExpressionField('CNT', 'COUNT(1)')
				),
				'filter' => array('ELEMENT_ID'=>$data['ELEMENT_ID'], 'ENTITY_NAME'=>$data['ENTITY_NAME'])
			))->fetch();
			$nextCnt = $arElement['CNT']++;
			$data['VERSION'] = $nextCnt;
		}

		return parent::add($data);
	}

	/**
	 * @method getChangeFields
	 * @param array $arUFields
	 * @param array $fields
	 * @return bool|string
	 */
	public static function getChangeFields(array $arUFields, array $fields)
	{
		if(intval($fields['ELEMENT_ID']) == 0)
			return false;

		$res = array();
		$result = '';
		$nowFields = $fields['FIELDS'];

		$prevFields = self::getVersionPrev($fields['ELEMENT_ID'], $fields['VERSION']);
		if(is_null($prevFields)){
			$res[] = Loc::getMessage('PW_HL_NEW_LOG_ELEMENT');
		} else {
			foreach ($prevFields['FIELDS'] as $code => $val) {
				if(!$val instanceof Main\Type\DateTime && !$val instanceof Main\Type\Date && !$val instanceof \DateTime){
					if($val != $nowFields[$code]){
						$title = strlen($arUFields[$code]['EDIT_FORM_LABEL']) > 0 ? $arUFields[$code]['EDIT_FORM_LABEL'] : $code;
						$res[$code] = $title;
					}
				}
			}
		}

		if(count($res) > 0){
			$result = implode(', ', $res);
		}

		return $result;
	}

	/**
	 * @method getVersionPrev
	 * @param null $id
	 * @param $version
	 * @return array|bool|null
	 */
	public static function getVersionPrev($id = null, $version)
	{
		if(intval($id) == 0)
			return false;

		return parent::getRow(array(
			'select'=>array('FIELDS','VERSION'),
			'filter'=>array('ELEMENT_ID'=>$id,'<VERSION'=>$version),
			'order'=>array('ID'=>'DESC')
		));
	}
}