<?php
/**
 * Created by OOO 1C-SOFT.
 * User: dremin_s
 * Date: 03.03.2017
 */

namespace Online1c\Reviews;

use Bitrix\Main\Entity;
use AB\Tools\Helpers;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class TypesTable extends Helpers\MainDataManager
{
	protected static $loc_prefix = 'O1C_REVIEW_TYPE_ENTITY.';

	protected static function getIndexes()
	{
		return [
			'ix_review_type_TYPE' => ['TYPE_ID'],
			'ix_review_type_CODE' => ['CODE'],
			'ix_review_type_XML_ID' => ['XML_ID'],
		];
	}

	public static function getTableName()
	{
		return 'online_review_type';
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
				'TITLE',
				['title' => self::getTitleField('TITLE')]
			),
			new Entity\StringField(
				'CODE',
				['title' => self::getTitleField('CODE')]
			),
			new Entity\StringField(
				'XML_ID',
				['title' => self::getTitleField('XML_ID')]
			),
			new Entity\StringField(
				'TYPE_ID',
				['title' => self::getTitleField('TYPE_ID')]
			),
			new Entity\IntegerField(
				'IBLOCK_ID',
				['title' => self::getTitleField('IBLOCK_ID')]
			)
		];
	}
}