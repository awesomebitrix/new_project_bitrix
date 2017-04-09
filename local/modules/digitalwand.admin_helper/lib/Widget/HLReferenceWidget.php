<?php
/**
 * Created by PhpStorm.
 * User: dremin_s
 * Date: 04.08.2016
 * Time: 12:44
 */

namespace DigitalWand\AdminHelper\Widget;

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;

Loader::includeModule('highloadblock');

class HLReferenceWidget extends StringWidget
{
	static protected $defaults = array(
		'FILTER' => '=',
		'INPUT_SIZE' => 5,
		'WINDOW_WIDTH' => 1024,
		'WINDOW_HEIGHT' => 600,
	);

	/**
	 * @inheritdoc
	 */
	protected function getEditHtml()
	{
		$style = $this->getSettings('INPUT_STYLE');
		$size = $this->getSettings('INPUT_SIZE');
		$model = $this->getSettings('MODEL');
		$nameCode = $this->getSettings('HL_FIELD_SHOW');
		$windowWidth = (int) $this->getSettings('WINDOW_WIDTH');
		$windowHeight = (int) $this->getSettings('WINDOW_HEIGHT');

		$link = false;

		$arBlock = HL\HighloadBlockTable::getRow([
			'filter' => ['=NAME'=>$model]
		]);
		$entity = HL\HighloadBlockTable::compileEntity($arBlock);
		$class = $entity->getDataClass();

		$arSelect = ['ID'];
		if(strlen($nameCode) > 0){
			$arSelect[] = $nameCode;
		}
		$arItem = $class::getRow([
			'select' => $arSelect,
			'filter' => ['=ID'=>(int)$this->getValue()]
		]);
		if(!is_null($arItem)){
			$title = $arItem['ID'];
			if(isset($arItem[$nameCode])){
				$title = $arItem[$nameCode];
			}

			$href = '/bitrix/admin/pw_hl_row_edit.php?ENTITY_ID='.$arBlock['ID'].'&lang=ru&ID='.$arItem['ID'];
			$link = '<a href="'.$href.'" target="_blank">'.$title.'</a>';
		}


		return '<input name="' . $this->getEditInputName() . '"
                     id="' . $nameCode . '[' . $arItem['ID'] . ']"
                     value="' . $arItem['ID'] . '"
                     size="' . $size . '"
                     style="' . $style . '"
                     type="text">' .
		'<input type="button"
                    value="..."
                    onClick="jsUtils.OpenWindow(\'/bitrix/admin/hl_search_list.php?lang=' . LANGUAGE_ID
		. '&amp;ENTITY_ID=' . $arBlock['ID'] . '&amp;n=' . $nameCode . '&amp;k=' . $arItem['ID'] . '\', ' . $windowWidth . ', '
		. $windowHeight . ');">' . '&nbsp;<span id="sp_' . md5($nameCode) . '_' . $arItem['ID'] . '" >'
		. static::prepareToOutput($title)
		. '</span>';
	}

}