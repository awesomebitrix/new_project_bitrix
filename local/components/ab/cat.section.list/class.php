<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Iblock;

Loader::includeModule('iblock');

class SectionList extends \CBitrixComponent
{
	protected $USER;
	protected $CIBlockSection;

	/**
	 * @param \CBitrixComponent|null $component
	 */
	public function __construct($component)
	{
		global $USER;
		parent::__construct($component);
		$this->USER = $USER;
		$this->CIBlockSection = new \CIBlockSection();
	}

	/**
	 * @method onPrepareComponentParams
	 * @param $arParams
	 *
	 * @return mixed
	 */
	public function onPrepareComponentParams($arParams)
	{
		if (intval($arParams['CACHE_TIME']) == 0)
			$arParams['CACHE_TIME'] = 86400;

		return $arParams;
	}

	public function getSections($level = 1)
	{
		$filter = array('IBLOCK_ID' => $this->arParams['IBLOCK_ID'], 'ACTIVE' => 'Y', 'GLOBAL_ACTIVE' => 'Y');

		$filter['DEPTH_LEVEL'] = $level;

		$obSection = $this->CIBlockSection->GetList(
			array('SORT' => 'ASC'),
			$filter,
			false,
			array(
				"*"
			)
		);
		$obSection->SetUrlTemplates("", $this->arParams["SECTION_URL"]);
		$arSection = $first = $second = $last = [];
		while ($section = $obSection->GetNext(true, false)) {
			$arSection[$section['ID']] = $section;
		}

		return $arSection;
	}

	/**
	 * @method executeComponent
	 */
	public function executeComponent()
	{
		if ($this->startResultCache($this->arParams['CACHE_TIME'], $this->arParams)) {
			$arSection = $this->getSections();
//		    $first = array_keys($arSection);

			$arSection2 = $this->getSections(2);
//		    $second = array_keys($arSection2);

//			$arSection3 = $this->getSections(3);
//		    $last = array_keys($arSection3);

//			foreach ($arSection3 as $id3 => $section3) {
//				$arSection2[$section3['IBLOCK_SECTION_ID']]['CHILD'][$id3] = $section3;
//			}

			foreach ($arSection2 as $id2 => $section2) {
				$arSection[$section2['IBLOCK_SECTION_ID']]['CHILD'][$id2] = $section2;
			}

			$this->arResult['SECTIONS'] = $arSection;

			$this->includeComponentTemplate();
		}
	}

}