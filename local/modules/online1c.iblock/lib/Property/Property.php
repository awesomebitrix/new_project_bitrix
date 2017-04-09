<?php
/**
 * Created by OOO 1C-SOFT.
 * User: dremin_s
 * Date: 22.11.2016
 */

namespace Online1c\Iblock\Property;

use Bitrix\Main;
use Online1c\Iblock\CollectionEntity;
use Online1c\Iblock\ElementBuilder;
use Online1c\Iblock\Tools\IblockCache;
use Online1c\Iblock\HelperIblock;
use Bitrix\Iblock;
use Bitrix\Iblock\PropertyTable;

Main\Loader::includeModule('iblock');

class Property
{
	private $iblockId = null;

	/** @var  Main\Entity\Base */
	private $entity;

	/** @var  string */
	private $entityName;

	/** @var  array of properties из b_iblock_property */
	private $meta;

	/** @var bool on|off кеш для $this->metaProperty() */
	private $useCache = false;

	protected $propCodes = [];

	public function __construct($iblockId, $arCodes = [])
	{
		if (intval($iblockId) == 0){
			throw new PropertyException('IBLOCK_ID in null');
		}

		$this->iblockId = $iblockId;
		if (count($arCodes) > 0){
			$this->propCodes = $arCodes;
		}

	}

	/**
	 * @method metaProperty
	 * @return null
	 */
	public function metaProperty()
	{
		$result = null;

		$Cache = new IblockCache(
			HelperIblock::CACHE_META_PROP_TIME,
			HelperIblock::CACHE_META_PROP_FOLDER,
			HelperIblock::CACHE_META_PROP_ID.$this->iblockId
		);

		if ($Cache->isValid() && $this->useCache){
			$result = $Cache->getData();
		} else {
			$select = [
				'ID', 'NAME', 'CODE', 'IBLOCK_ID', 'PROPERTY_TYPE', 'LIST_TYPE',
				'MULTIPLE', 'XML_ID', 'IS_REQUIRED', 'VERSION', 'USER_TYPE','LINK_IBLOCK_ID'
			];

			$obProps = PropertyTable::getList([
				'select' => $select,
				'filter' => ['=IBLOCK_ID' => $this->iblockId],
			]);
			while ($prop = $obProps->fetch()) {
				if (strlen($prop['CODE']) == 0){
					$prop['CODE'] = 'PROPERTY_'.$prop['ID'];
				}
				$result[$prop['CODE']] = $prop;
			}

			$Cache->addCache($result);
		}

		$this->meta = $result;

		return $result;
	}

	public function compileBaseEntity()
	{
		$this->entity = Main\Entity\Base::compileEntity(
			'Property'.$this->iblockId,
			[new Main\Entity\IntegerField('IBLOCK_ELEMENT_ID', ['required' => true, 'primary' => true])],
			['table_name' => 'b_iblock_element_prop_s'.$this->iblockId]
		);

		$this->entityName = $this->entity->getName();

		return $this;
	}

	public function compileProps()
	{
		if (count($this->meta) == 0){
			$this->metaProperty();
		}

		if (!$this->entity instanceof Main\Entity\Base){
			$this->compileBaseEntity();
		}

		foreach ($this->meta as $code => $arProp) {
			if (intval($arProp['ID']) == 0 || strlen($arProp['CODE']) == 0){
				unset($this->meta[$code]);
			}
			if (count($this->propCodes) > 0 && !array_key_exists($code, $this->propCodes)){
				unset($this->meta[$code]);
			}

			$this->setPropField($arProp);
		}

//		PR($this->meta);
//		PR($this->entity);

		return $this;
	}

	public function setPropField($arProp)
	{
		$field = null;
		switch ($arProp['PROPERTY_TYPE']) {
			case PropertyTable::TYPE_LIST:
				$field = $this->addEnumField($arProp);
				break;
			case PropertyTable::TYPE_ELEMENT:
				$field = $this->addIblockField($arProp);
				break;
			case PropertyTable::TYPE_NUMBER:
			case PropertyTable::TYPE_STRING:
			default:
				$field = $this->addScalarField($arProp);
				break;
		}

		$this->entity->addField($field);
	}

	/**
	 * @method addScalarField
	 * @param $arProp
	 *
	 * @return Main\Entity\IntegerField|Main\Entity\StringField
	 */
	protected function addScalarField($arProp)
	{
		if ($arProp['PROPERTY_TYPE'] == 'N'){
			$field = new Main\Entity\IntegerField($arProp['CODE'], [
				'title' => $arProp['NAME'],
				'required' => $arProp['IS_REQUIRED'] == 'Y' || false,
				'column_name' => 'PROPERTY_'.$arProp['ID'],
			]);
			$field->addFetchDataModifier(function ($value, $field, $data, $alias) use ($arProp) {
				$result = 0;
				$val = explode('.', $value);

				if (count($val) > 1){
					intval($val[1]) > 0 ? $result = floatval($value) : $result = intval($value);
				} else {
					$result = intval($value);
				}

				return $result;
			});
		} else {
			$field = new Main\Entity\StringField($arProp['CODE'], [
				'title' => $arProp['NAME'],
				'column_name' => 'PROPERTY_'.$arProp['ID'],
				'required' => $arProp['IS_REQUIRED'] == 'Y' || false,
			]);
			switch (strtoupper($arProp['USER_TYPE'])) {
				case 'HTML':
					$field->addFetchDataModifier(function ($value, $field, $data, $alias) use ($arProp) {
						$res = unserialize($value);
						$res['~TEXT'] = htmlspecialcharsbx($res['TEXT']);

						return $res;
					});
					break;
			}
		}

		return $field;
	}

	/**
	 * @method addEnumField
	 * @param $arProp
	 *
	 * @return Main\Entity\ReferenceField
	 */
	protected function addEnumField($arProp)
	{
		$singleField = $arProp['CODE'].'_SINGLE';
		$this->entity->addField(new Main\Entity\IntegerField($singleField, [
			'title'=>$arProp['NAME'],
			'column_name'=>'PROPERTY_'.$arProp['ID'],
			'required'=>$arProp['IS_REQUIRED'] == 'Y' ? true : false
		]));
		$field = new Main\Entity\ReferenceField(
			$arProp['CODE'],
			Iblock\PropertyEnumerationTable::getEntity(),
			['=this.'.$singleField => 'ref.ID']
		);

		return $field;
	}

	protected function addIblockField($arProp)
	{
		if(intval($arProp['LINK_IBLOCK_ID']) > 0){
			$entityLink = new ElementBuilder($arProp['LINK_IBLOCK_ID']);
			$entityLink->builder()->getEntity();
//			$field = new Main\Entity\ReferenceField(
//				''
//			);
		}

		PR($arProp);
	}

	/**
	 * @method getIblockId - get param iblockId
	 * @return null
	 */
	public function getIblockId()
	{
		return $this->iblockId;
	}

	/**
	 * @param null $iblockId
	 *
	 * @return Property
	 */
	public function setIblockId($iblockId)
	{
		$this->iblockId = $iblockId;

		return $this;
	}

	/**
	 * @method getPropCodes - get param propCodes
	 * @return array
	 */
	public function getPropCodes()
	{
		return $this->propCodes;
	}

	/**
	 * @param array $propCodes
	 *
	 * @return Property
	 */
	public function setPropCodes($propCodes)
	{
		$this->propCodes = $propCodes;

		return $this;
	}

	/**
	 * @method getEntityName - get param entityName
	 * @return mixed
	 */
	public function getEntityName()
	{
		return $this->entityName;
	}

	/**
	 * @method getEntity - get param entity
	 * @return Main\Entity\Base
	 */
	public function getEntity()
	{
		return $this->entity;
	}

}