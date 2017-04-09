<?php
/**
 * Created by PhpStorm.
 * User: dremin_s
 * Date: 15.07.2016
 * Time: 13:55
 */

namespace DigitalWand\AdminHelper\Widget;

use Bitrix\Main\Loader;
use PW\Tools\Debug;
use UL\Main\Map\Model;


class MultiShopWidget extends IblockElementWidget
{
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
			$rsElement = ElementTable::getById($elementId);

			if (!$element = $rsElement->fetchAll()){
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
                    onClick="jsUtils.OpenWindow(\'/bitrix/admin/iblock_element_search.php?lang='.LANGUAGE_ID
		.'&amp;IBLOCK_ID='.$iblockId.'&amp;n='.$name.'&amp;k='.$key.'\', '.$windowWidth.', '
		.$windowHeight.');">'.'&nbsp;<span id="sp_'.md5($name).'_'.$key.'" >'
		.static::prepareToOutput($element['NAME'])
		.'</span>';
	}

	protected function getMultipleEditHtml()
	{
		Loader::includeModule('ul.main');

		$style = $this->getSettings('STYLE');
		$size = $this->getSettings('SIZE');
		$uniqueId = $this->getEditInputHtmlId();

		$rsEntityData = null;

		if (!empty($this->data['ID'])){
			$rsEntityData = Model\MultiShopTable::getList([
				'select'=>['*','NAME'=>'ELEMENT.NAME'],
				'filter'=>['=ID'=>$this->data[$this->getCode()]],
			]);
		}
		ob_start();
		?>

		<div id="<?=$uniqueId?>-field-container" class="<?=$uniqueId?>">
		</div>

		<script>
			var multiple = new MultipleWidgetHelper(
				'#<?= $uniqueId ?>-field-container',
				'{{field_original_id}}<input type="text" name="<?= $this->getCode()?>[{{field_id}}][<?=$this->getMultipleField('VALUE')?>]" style="<?=$style?>" size="<?=$size?>" value="{{value}}">'
			);
			<?
			if ($rsEntityData)
			{
				while($referenceData = $rsEntityData->fetch()){
					if (empty($referenceData['NAME'])){
						continue;
					}

					?>
					multiple.addField({
						value: '<?= static::prepareToJs($referenceData['VALUE']) ?>',
						field_original_id: '<input type="hidden" name="<?= $this->getCode()?>[{{field_id}}][<?= $this->getMultipleField('ID') ?>]"' +
						' value="<?= $referenceData['VALUE'] ?>">',
						field_id: <?= $referenceData['VALUE'] ?>
					});
					<?
					}
				}
			?>

			// TODO Р”РѕР±Р°РІР»РµРЅРёРµ СЃРѕР·РґР°РЅРЅС‹С… РїРѕР»РµР№
			multiple.addField();
		</script>
		<?
		return ob_get_clean();
	}

	protected function getMultipleEditHtmlss()
	{
		$iblockId = (int)$this->getSettings('IBLOCK_ID');
		$inputSize = (int)$this->getSettings('INPUT_SIZE');
		$windowWidth = (int)$this->getSettings('WINDOW_WIDTH');
		$windowHeight = (int)$this->getSettings('WINDOW_HEIGHT');


		$style = $this->getSettings('STYLE');
		$size = $this->getSettings('SIZE');
		$uniqueId = $this->getEditInputHtmlId();
		$name = 'FIELDS';
		$key = $this->getCode();

		$rsEntityData = null;

		if (!empty($this->data['ID'])){
			$entityName = $this->entityName;
			$rsEntityData = Model\MultiShopTable::getList([
				'select'=>['*','NAME'=>'ELEMENT.NAME'],
				'filter'=>['=ID'=>$this->data[$this->getCode()]],
			]);
		}

		ob_start();
		?>

		<div id="<?=$uniqueId?>-field-container" class="<?=$uniqueId?>">
		</div>

		<script>

			var urlIblockSearch = '/bitrix/admin/iblock_element_search.php?lang=<?=LANGUAGE_ID?>';
			var UrlIblockParams = {
				IBLOCK_ID: '<?=$iblockId?>',
				n: '<?= $this->getCode()?>[{{field_id}}][<?=$this->getMultipleField('VALUE')?>]',
				k: '<?=$this->getCode()?>'
			};
			urlIblockSearch = BX.util.add_url_param(urlIblockSearch, UrlIblockParams);

			var htmlInput = '';
			htmlInput += '{{field_original_id}}';
			htmlInput += '<input type="text" name="<?= $this->getCode()?>[{{field_id}}][<?=$this->getMultipleField('VALUE')?>]"';
			htmlInput += 'style="<?=$style?>" size="<?=$size?>" value="{{value}}" id="<?=$name?>[{{field_id}}][<?=$this->getMultipleField('VALUE')?>]" />';
			htmlInput += '<input type="button"  value="..." ';
			htmlInput += 'onClick="jsUtils.OpenWindow(urlIblockSearch, <?=$windowWidth?>,<?=$windowHeight?>);" />';
			htmlInput += '&nbsp;<span id="sp_{{spHash}}">{{elementName}}</span>';

			<?/*var multiple = new MultipleWidgetHelper(
				'#<?= $uniqueId ?>-field-container',
				'{{field_original_id}}<input type="text" ' +
				'name="<?= $this->getCode()?>[{{field_id}}][<?=$this->getMultipleField('VALUE')?>]" style="<?=$style?>" size="<?=$size?>" value="{{value}}">'
			);*/?>

			var multiple = new MultipleWidgetHelper(
				'#<?= $uniqueId ?>-field-container',
				htmlInput
			);

			<?
				while($referenceData = $rsEntityData->fetch()){

					if (empty($referenceData['NAME'])){
						continue;
					}

				/*
				 * '<input name="'.$this->getEditInputName().'"
				 id="'.$name.'['.$key.']"
				 value="'.$elementId.'"
				 size="'.$inputSize.'"
				 type="text">'.




	'<input type="button"
				value="..."
				onClick="jsUtils.OpenWindow(\'/bitrix/admin/iblock_element_search.php?lang='.LANGUAGE_ID
	.'&amp;IBLOCK_ID='.$iblockId.'&amp;n='.$name.'&amp;k='.$key.'\', '.$windowWidth.', '
	.$windowHeight.');">'.'&nbsp;<span id="sp_'.md5($name).'_'.$key.'" >'
	.static::prepareToOutput($element['NAME'])
	.'</span>';
				 *
				 * */

					?>
					multiple.addField({
						value: '<?= static::prepareToJs($referenceData['VALUE']) ?>',
						field_original_id: '<input type="hidden" name="<?= $this->getCode()?>[{{field_id}}][<?= $this->getMultipleField('ID') ?>]"' +
						' value="<?= $referenceData['VALUE'] ?>">',
						field_id: '<?= $referenceData['VALUE'] ?>',
						elementName: '<?$referenceData['NAME']?>',
						spHash: '<?=md5($referenceData['ID'])?>_<?=$this->getCode()?>'
					});
					<?
				}
			?>

			// TODO Р”РѕР±Р°РІР»РµРЅРёРµ СЃРѕР·РґР°РЅРЅС‹С… РїРѕР»РµР№
			multiple.addField();
		</script>
		<?
		return ob_get_clean();
	}
}