<?php
/**
 * Created by OOO 1C-SOFT.
 * User: dremin_s
 * Date: 03.03.2017
 */

namespace Online1c\Reviews;

use Bitrix\Main\Entity;
use AB\Tools\Helpers;
use Bitrix\Main\Entity\Event;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Data;
use Bitrix\Main\Result;
use Bitrix\Main\Type;
use Bitrix\Main\UserTable;

Loc::loadMessages(__FILE__);

class ReviewsTable extends Helpers\MainDataManager
{
	const REVIEW_ACTIVE = 'Y'; // Коммент опубликован
	const REVIEW_NO_ACTIVE = 'N'; // Коммент на модерации
	const TYPE_TEXT = 'text'; // типы текста
	const TYPE_HTML = 'html';
	const CACHE_DIR = '/online1c/cmp/reviews'; // папка для кеша отзывов
	const CACHE_ID = 'reviews_list_';

	/** @var string */
	protected static $loc_prefix = 'O1C_REVIEW_ENTITY.';

	/**
	 * @method getIndexes
	 * @return array
	 */
	protected static function getIndexes()
	{
		return [
			'ix_review_ELEMENT_CODE' => ['ELEMENT_CODE', 'TYPE_ID'],
			'ix_review_LEFT_MARGIN' => ['LEFT_MARGIN'],
			'ix_review_RIGHT_MARGIN' => ['RIGHT_MARGIN'],
			'ix_review_PARENT_ID' => ['PARENT_ID'],
			'ix_review_TYPE_ID' => ['TYPE_ID'],
		];
	}

	/**
	 * @method getTableName
	 * @return string
	 */
	public static function getTableName()
	{
		return 'online_review_items';
	}

	/**
	 * @method getMap
	 * @return array
	 */
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
			new Entity\IntegerField(
				'USER_ID',
				[
					'title' => self::getTitleField('USER_ID'),
					'default_value' => self::getUser()->GetID()
				]
			),
			new Entity\StringField(
				'ELEMENT_CODE',
				['title' => self::getTitleField('ELEMENT_CODE'), 'required' => true]
			),
			new Entity\StringField(
				'PAGE',
				['title' => self::getTitleField('PAGE')]
			),
			/*new Entity\StringField(
				'XML_ID',
				['title' => self::getTitleField('XML_ID')]
			),*/
			new Entity\TextField(
				'TEXT',
				['title' => self::getTitleField('TEXT')]
			),
			new Entity\EnumField(
				'TEXT_TEXT_TYPE',
				[
					'values' => array(self::TYPE_TEXT, self::TYPE_HTML),
					'default_value' => self::TYPE_TEXT,
					'title' => self::getTitleField('TEXT_TEXT_TYPE'),
				]),
			new Entity\TextField(
				'ADVANTAGE',
				['title' => self::getTitleField('ADVANTAGE')]
			),
			new Entity\EnumField(
				'ADVANTAGE_TEXT_TYPE',
				[
					'values' => array(self::TYPE_TEXT, self::TYPE_HTML),
					'default_value' => self::TYPE_TEXT,
					'title' => self::getTitleField('ADVANTAGE_TEXT_TYPE'),
				]
			),
			new Entity\TextField(
				'DISADVANTAGE',
				['title' => self::getTitleField('DISADVANTAGE')]
			),
			new Entity\EnumField(
				'DISADVANTAGE_TEXT_TYPE',
				[
					'values' => array(self::TYPE_TEXT, self::TYPE_HTML),
					'default_value' => self::TYPE_TEXT,
					'title' => self::getTitleField('DISADVANTAGE_TEXT_TYPE'),
				]
			),
			new Entity\IntegerField(
				'LIKE',
				['title' => self::getTitleField('LIKE'), 'default_value' => 0]
			),
			new Entity\IntegerField(
				'DISLIKE',
				['title' => self::getTitleField('DISLIKE'), 'default_value' => 0]
			),
			new Entity\IntegerField(
				'LEVEL',
				['title' => self::getTitleField('LEVEL')]
			),
			new Entity\IntegerField(
				'LEFT_MARGIN',
				['title' => self::getTitleField('LEFT_MARGIN')]
			),
			new Entity\IntegerField(
				'RIGHT_MARGIN',
				['title' => self::getTitleField('RIGHT_MARGIN')]
			),
			new Entity\IntegerField(
				'PARENT_ID',
				['title' => self::getTitleField('PARENT_ID')]
			),
			new Entity\StringField(
				'FILES',
				['title' => self::getTitleField('FILES'), 'serialized' => true]
			),
			new Entity\EnumField(
				'ACTIVE',
				[
					'values' => [self::REVIEW_NO_ACTIVE, self::REVIEW_ACTIVE],
					'title' => self::getTitleField('ACTIVE'),
				]
			),
			new Entity\IntegerField(
				'TYPE_ID',
				['title' => self::getTitleField('TYPE_ID'), 'required' => true]
			),
			new Entity\ReferenceField(
				'TYPE_REVIEW',
				TypesTable::getEntity(),
				['=this.TYPE_ID' => 'ref.ID']
			),
			new Entity\DatetimeField(
				'DATE_CREATE',
				[
					'title' => self::getTitleField('DATE_CREATE'),
					'default_value' => new Type\DateTime(),
				]
			),
			new Entity\StringField(
				'EMAIL',
				['title' => self::getTitleField('EMAIL')]
			),
			new Entity\StringField(
				'FIO',
				['title' => self::getTitleField('FIO')]
			),
			new Entity\StringField(
				'PHONE',
				['title' => self::getTitleField('PHONE')]
			),
			new Entity\StringField(
				'IP',
				['title' => self::getTitleField('IP')]
			),
			new Entity\ReferenceField(
				'USER',
				UserTable::getEntity(),
				['=this.USER_ID' => 'ref.ID']
			),
			new Entity\FloatField(
				'RATING_VAL',
				['title' => self::getTitleField('RATING_VAL'), 'default_value' => 0]
			),
		];
	}

	/**
	 * @method clearCacheTag
	 * @param string $elementId
	 */
	public static function clearCacheTag($elementId = '')
	{
		if (strlen($elementId) > 0){
			$tagId = self::CACHE_ID.$elementId;
			$TaggedCache = new Data\TaggedCache();
			$TaggedCache->clearByTag($tagId);
		}
	}

	/**
	 * @method onAfterAdd
	 * @param Event $event
	 *
	 * @return Entity\EventResult
	 */
	public static function onAfterAdd(Event $event)
	{
		$result = new Entity\EventResult();
		$fields = $event->getParameter('fields');
		self::clearCacheTag($fields['ELEMENT_CODE']);
		if ($fields['RATING_VAL'])
			self::calcRating($fields['ELEMENT_CODE'], intval($fields['RATING_VAL']));

		return $result;
	}

	/**
	 * @method onAfterUpdate
	 * @param Event $event
	 *
	 * @return Entity\EventResult
	 */
	public static function onAfterUpdate(Event $event)
	{
		$result = new Entity\EventResult();
		$fields = $event->getParameter('fields');
		self::clearCacheTag($fields['ELEMENT_CODE']);
		if ($fields['RATING_VAL'])
			self::calcRating($fields['ELEMENT_CODE'], intval($fields['RATING_VAL']));

		return $result;
	}

	/**
	 * @method onBeforeDelete
	 * @param Event $event
	 *
	 * @return Entity\EventResult
	 */
	public static function onBeforeDelete(Event $event)
	{
		$result = new Entity\EventResult();
		$id = $event->getParameter('id')['ID'];
		$row = self::getRowById($id);
		self::clearCacheTag($row['ELEMENT_CODE']);

		return $result;
	}

	public static function calcRating($id, $val = null)
	{
		if (intval($val) > 0 && strlen($id) > 0){
			$comments = self::getCommentsByElement($id, ['ID', 'RATING_VAL']);
			$rating = 0;
			if (!is_null($comments)){
				foreach ($comments as $comment) {
					if (intval($comment['RATING_VAL']) == 0){
						$comment['RATING_VAL'] = 0;
					}
					$rating += $comment['RATING_VAL'];
				}
				$rating = ($rating + $val) / count($comments);
				$ratingElement = RatingElementTable::getByElement($id);
				if (is_null($ratingElement)){
					return RatingElementTable::add(['ELEMENT_CODE' => $id, 'REVIEW_COUNT' => count($comments), 'RATING_ELEMENT' => $rating]);
				} else {
					return RatingElementTable::update($ratingElement['ID'], ['ELEMENT_CODE' => $id, 'REVIEW_COUNT' => count($comments), 'RATING_ELEMENT' => $rating]);
				}
			}
		}
	}

	public static function getCommentsByElement($id, $select = [])
	{
		$id = (int)$id;
		$comments = null;
		if ($id > 0){
			if (count($select) == 0){
				$select = ['*'];
			}
			$oComment = self::getList([
				'select' => $select,
				'filter' => ['=ELEMENT_CODE' => $id],
				'limit' => null,
			]);
			$comments = $oComment->fetchAll();
		}

		return $comments;
	}
}