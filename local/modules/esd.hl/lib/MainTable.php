<?php namespace Esd\HL;

use Esd\Debug;
use Bitrix\Main;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Highloadblock\HighloadBlockTable;

Loc::loadMessages(__FILE__);

/**
 * Class KeysTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> UF_SERIAL string optional
 * <li> UF_ACTIVE int optional
 * <li> UF_PRODUCT int optional
 * <li> UF_USER int optional
 * <li> UF_DATE datetime optional
 * <li> UF_SERVICE int optional
 * <li> UF_DISTRIBUTOR int optional
 * <li> UF_DESC string optional
 * <li> UF_KEY_TYPE string optional
 * <li> UF_FILE string optional
 * <li> UF_XML_ID string optional
 * </ul>
 *
 * @package Bitrix\Game
 **/
class MainTable extends HighloadBlockTable
{
	/** @var Entity\AddResult */
	protected static $resultAdd;
	/** @var Entity\UpdateResult */
	protected static $resultUpdate;
	protected static $HBlock = array();
	protected static $entityBlock = null;

	/**
	 * MainTable constructor.
	 * @param array|int|string $block
	 */
	public function __construct($block)
	{
		$hlblock = array();
		if(intval($block) > 0){
			$hlblock = parent::getRowById($block);
		} elseif(is_array($block)){
			$hlblock = $block;
		} elseif(is_string($block)) {
			$hlblock = parent::getRow(array(
				'select'=>array('*'),
				'filter'=>array('NAME'=>$block)
			));
		}
		self::$HBlock = $hlblock;
		self::$entityBlock = $this->compileEntity($hlblock);
	}

	/**
	 * @param array $data
	 *
	 * @return Entity\AddResult
	 * @throws \Bitrix\Main\SystemException
	 */
	public static function add($data)
	{
		static::$resultAdd = parent::add($data);
		return static::$resultAdd;
	}

	/**
	 * @param mixed $primary
	 * @param array $data
	 *
	 * @return Entity\UpdateResult
	 */
	public static function update($primary, $data)
	{
		static::$resultUpdate = parent::update($primary, $data);
		return static::$resultUpdate;
	}

	/**
	 * @method init
	 * @param \Bitrix\Main\Entity\Base|bool $entity
	 * @return \Bitrix\Main\Entity\DataManager
	 */
	public function init($entity = false)
	{
		if($entity){
			$class = $entity->getDataClass();
		} else {
			$class = self::$entityBlock->getDataClass();
		}

		return new $class;
	}

	/**
	 * @method getHBlock
	 * @return array
	 */
	public static function getHBlock()
	{
		return self::$HBlock;
	}

	/**
	 * @method getHLEntity
	 * @return null|Entity\Base
	 */
	public static function getHLEntity()
	{
		return self::$entityBlock;
	}

}
