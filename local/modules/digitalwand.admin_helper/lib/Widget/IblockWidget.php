<?php
/**
 * Created by OOO 1C-SOFT.
 * User: GrandMaster
 * Date: 07.03.17
 */

namespace DigitalWand\AdminHelper\Widget;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Iblock;

class IblockWidget extends NumberWidget
{
	static protected $defaults = array(
		'FILTER' => '=',
		'INPUT_SIZE' => 5,
		'WINDOW_WIDTH' => 1024,
		'WINDOW_HEIGHT' => 860,
	);

	public function __construct(array $settings = array())
	{
		Loc::loadMessages(__FILE__);
		Loader::includeModule('iblock');

		parent::__construct($settings);
	}

	protected function getEditHtml()
	{
		$inputSize = (int)$this->getSettings('INPUT_SIZE');
		$windowWidth = (int)$this->getSettings('WINDOW_WIDTH');
		$windowHeight = (int)$this->getSettings('WINDOW_HEIGHT');
		$iblockType = $this->getSettings('IBLOCK_TYPE');

		$iblockId = $this->getValue();
		$key = $this->getCode();

		$name = 'FIELDS';

		$result = [];
		$obIblocks = Iblock\IblockTable::getList([
			'select' => ['ID', 'NAME', 'TYPE_ID' => 'IBLOCK_TYPE_ID','TYPE_NAME' => 'TYPE.LANG_MESSAGE.NAME'],
			'filter' => ['ACTIVE' => 'Y'],
			'order' => ['SORT' => 'ASC', 'NAME' => 'ASC'],
		]);
		while ($rs = $obIblocks->fetch()){
			$result[$rs['TYPE_ID']]['NAME'] = $rs['TYPE_NAME'];
			$result[$rs['TYPE_ID']]['ITEMS'][$rs['ID']] = $rs;
		}

		$select = '<select name="'.$name.'['.$key.']">';
		foreach ($result as $type => $arItem) {
			$select .= '<optgroup label="'.$arItem['NAME'].'">';
			foreach ($arItem['ITEMS'] as $value) {
				$selected = false;
				if($value['ID'] == $iblockId)
					$selected = 'selected';

				$select .= '<option '.$selected.' value="'.$value['ID'].'">'.$value['NAME'].'</option>';
			}
			$select .= '</optgroup>';
		}
		$select.='</select>';

		return $select;
	}


}