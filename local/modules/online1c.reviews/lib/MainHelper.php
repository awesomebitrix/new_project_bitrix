<?php
/**
 * Created by OOO 1C-SOFT.
 * User: dremin_s
 * Date: 09.03.2017
 */

namespace Online1c\Reviews;

use Bitrix\Main\Localization\Loc;

class MainHelper
{
	const LANG_PARAMS = 'O1C_REVIEW_PARAMS.';

	public static function clearAjaxComponentParams($arParams = [])
	{
		$res = [];
		foreach ($arParams as $code => $param) {
			if(substr($code, 0, 1) !== '~'){
				$res[$code] = $param;
			}
		}

		return \CUtil::PhpToJSObject($res);
	}

	public static function encodeComponentParams($arParams = [])
	{
		return base64_encode(serialize($arParams));
	}

	public static function decodeComponentParams($params = '')
	{
		return unserialize(base64_decode($params));
	}

	public static function getCountLang($number = 0)
	{
		$titles = ['отзыв','отзыва','отзывов'];
		$cases = array (2, 0, 1, 1, 1, 2);
		return $number." ".$titles[ ($number%100 > 4 && $number %100 < 20) ? 2 : $cases[min($number%10, 5)] ];
	}

	public static function getLangParams($code)
	{
		return Loc::getMessage(self::LANG_PARAMS.$code);
	}

	/**
	 * @method getParamsComponentFields
	 * @return array
	 */
	public static function getParamsComponentFields()
	{
		$entity = ReviewsTable::getEntity();

		$result = [];
		$fields = ['TEXT', 'ADVANTAGE', 'DISADVANTAGE', 'FIO', 'EMAIL', 'PHONE', 'RATING_VAL'];
		foreach ($fields as $field) {
			if($entity->hasField($field)){
				$result[$field] = strlen($entity->getField($field)->getTitle()) > 0 ? $entity->getField($field)->getTitle() : $entity->getField($field)->getName();
			}
		}

		return $result;
	}
}