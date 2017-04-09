<?php
/**
 * Created by OOO 1C-SOFT.
 * User: dremin_s
 * Date: 22.11.2016
 */

namespace Online1c\Iblock;

use Bitrix\Iblock;
use Bitrix\Main;

Main\Loader::includeModule('iblock');

class ElementTable extends Iblock\ElementTable
{
	/**
	 * Returns entity object
	 *
	 * @return Main\Entity\Base
	 */
	public static function getEntity()
	{
		return parent::getEntity();
	}

	/**
	 * Creates and returns the Query object for the entity
	 *
	 * @return Main\Entity\Query
	 */
	public static function query()
	{
		$q = static::query();

		return $q;
	}


}