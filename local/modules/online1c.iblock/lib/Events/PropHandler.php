<?php
/**
 * Created by OOO 1C-SOFT.
 * User: dremin_s
 * Date: 22.11.2016
 */

namespace Online1c\Iblock\Events;
use Online1c\Iblock\HelperIblock;


class PropHandler
{
	public static function onClearMetaPropCache(&$arFields)
	{
		$CacheTag = new \Bitrix\Main\Data\TaggedCache();
		$CacheTag->clearByTag(HelperIblock::CACHE_META_PROP_ID.$arFields['IBLOCK_ID']);
	}
}