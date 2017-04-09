<?php
/**
 * Created by OOO 1C-SOFT.
 * User: teacher
 * Date: 23.11.16
 */

namespace Online1c\Iblock;

use Bitrix\Iblock;
use Bitrix\Main;
use Online1c\Iblock\Property\Property;

class ElementBuilder
{
	private $iblockId = null;

	/** @var  Main\Entity\Base */
	private $entity;
	/**
	 * ElementBuilder constructor.
	 *
	 * @param null $iblockId
	 */
	public function __construct($iblockId = null)
	{
		$this->iblockId = $iblockId;
	}

	/**
	 * @method builder
	 * @return $this
	 */
	public function builder()
	{
		if(intval($this->iblockId) > 0){
			$this->entity = $this->compileEntity();
			$Property = new Property($this->iblockId);
			$propEntity = $Property->compileProps()->getEntity();
			$this->entity->addField(new Main\Entity\ReferenceField(
				'PROPERTY',
				$propEntity,
				['ref.IBLOCK_ELEMENT_ID' => 'this.ID'],
				['join_type' => 'LEFT']
			));
		} else {
			$this->entity = Iblock\ElementTable::getEntity();
		}

		return $this;
	}

	private function compileEntity()
	{
		$entity = Main\Entity\Base::compileEntity(
			'Element'.$this->iblockId,
			Iblock\ElementTable::getMap(),
			['table_name' => Iblock\ElementTable::getTableName()]
		);

		return $entity;
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