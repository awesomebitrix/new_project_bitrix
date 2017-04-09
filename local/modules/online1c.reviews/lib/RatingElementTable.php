<?php
/**
 * Created by OOO 1C-SOFT.
 * User: dremin_s
 * Date: 10.03.2017
 */

namespace Online1c\Reviews;

use Bitrix\Main\Entity;
use AB\Tools\Helpers;

class RatingElementTable extends Helpers\MainDataManager
{
	/** @var string  */
	protected static $loc_prefix = 'O1C_REVIEW_RATING.';

	protected static function getIndexes()
	{
		return [
			'ix_rating_el_CODE' => ['ELEMENT_CODE']
		];
	}

	public static function getTableName()
	{
		return 'online_review_rating_el';
	}

	public static function getMap()
	{
		return [
			new Entity\IntegerField(
				'ID',
				[
					'primary' => true,
					'autocomplete' => true,
					'title' => 'ID',
				]
			),
			new Entity\StringField(
				'ELEMENT_CODE',
				['title' => self::getTitleField('ELEMENT_CODE'), 'required' => true]
			),
			new Entity\FloatField(
				'RATING_ELEMENT',
				['title' => self::getTitleField('RATING_ELEMENT'), 'required' => true]
			),
			new Entity\IntegerField(
				'REVIEW_COUNT',
				['title' => self::getTitleField('ELEMENT_CODE'), 'default_value' => 0]
			)
		];
	}

	/**
	 * @method getByElement
	 * @param $el
	 *
	 * @return array|null
	 */
	public static function getByElement($el)
	{
		return self::getRow([
			'filter' => ['=ELEMENT_CODE' => $el]
		]);
	}

}