<?php
/**
 * Created by PhpStorm.
 * User: dremin_s
 * Date: 29.07.2016
 * Time: 11:09
 */

namespace DigitalWand\AdminHelper\Widget;

use Bitrix\Iblock\SectionTable;
use Bitrix\Main\Localization\Loc;

class SectionWidget extends IblockElementWidget
{
	/**
	 * {@inheritdoc}
	 */
	public function getEditHtml()
	{
		$iblockId = (int)$this->getSettings('IBLOCK_ID');
		$inputSize = (int)$this->getSettings('INPUT_SIZE');
		$windowWidth = (int)$this->getSettings('WINDOW_WIDTH');
		$windowHeight = (int)$this->getSettings('WINDOW_HEIGHT');

		$name = 'FIELDS';
		$key = $this->getCode();

		$elementId = $this->getValue();

		if (!empty($elementId)){

			$element = SectionTable::getRowById($elementId);
			if (!$element){
				$element['NAME'] = Loc::getMessage('IBLOCK_ELEMENT_NOT_FOUND');
			}
		} else {
			$elementId = '';
		}

		return '<input name="'.$this->getEditInputName().'"
                     id="'.$name.'['.$key.']"
                     value="'.$elementId.'"
                     size="'.$inputSize.'"
                     type="text">'.
		'<input type="button"
                    value="..."
                    onClick="jsUtils.OpenWindow(\'/bitrix/admin/cat_section_search.php?lang=ru&discount=Y&n=tree__0__0_value&lang='.LANGUAGE_ID
		.'&amp;IBLOCK_ID='.$iblockId.'&amp;n='.$name.'&amp;k='.$key.'\', '.$windowWidth.', '
		.$windowHeight.');">'.'&nbsp;<span id="sp_'.md5($name).'_'.$key.'" >'
		.static::prepareToOutput($element['NAME'])
		.'</span>';
	}

	/**
	 * {@inheritdoc}
	 */
	public function generateRow(&$row, $data)
	{
		$elementId = $this->getValue();

		if (!empty($elementId)){
			$rsElement = SectionTable::getList([
				'filter' => [
					'ID' => $elementId,
				],
				'select' => [
					'ID',
					'NAME',
					'IBLOCK_ID',
					'IBLOCK_TYPE'=>'IBLOCK.IBLOCK_TYPE_ID',
				],
			]);

			$element = $rsElement->fetch();

			$html = '<a href="/bitrix/admin/iblock_section_edit.php?IBLOCK_ID='.$element['IBLOCK_ID']
				.'&type='.$element['IBLOCK_TYPE'].'&ID='
				.$elementId.'&lang=ru">['.$elementId.'] '.static::prepareToOutput($element['NAME']).'</a>';
		} else {
			$html = '';
		}

		$row->AddViewField($this->getCode(), $html);
	}
}