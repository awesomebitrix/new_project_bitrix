<?php

namespace DigitalWand\AdminHelper\Widget;

use Bitrix\Iblock\ElementTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

/**
 * Р’РёРґР¶РµС‚ РґР»СЏ РІС‹Р±РѕСЂР° СЌР»РµРјРµРЅС‚Р° РёРЅС„РѕР±Р»РѕРєР°.
 *
 * Р”РѕСЃС‚СѓРїРЅС‹Рµ РѕРїС†РёРё:
 * <ul>
 * <li> <b>IBLOCK_ID</b> - (int) ID РёРЅС„РѕР±Р»РѕРєР°
 * <li> <b>INPUT_SIZE</b> - (int) Р·РЅР°С‡РµРЅРёРµ Р°С‚СЂРёР±СѓС‚Р° size РґР»СЏ input </li>
 * <li> <b>WINDOW_WIDTH</b> - (int) Р·РЅР°С‡РµРЅРёРµ width РґР»СЏ РІСЃРїР»С‹РІР°СЋС‰РµРіРѕ РѕРєРЅР° РІС‹Р±РѕСЂР° СЌР»РµРјРµРЅС‚Р° </li>
 * <li> <b>WINDOW_HEIGHT</b> - (int) Р·РЅР°С‡РµРЅРёРµ height РґР»СЏ РІСЃРїР»С‹РІР°СЋС‰РµРіРѕ РѕРєРЅР° РІС‹Р±РѕСЂР° СЌР»РµРјРµРЅС‚Р° </li>
 * </ul>
 *
 * @author Nik Samokhvalov <nik@samokhvalov.info>
 */
class IblockElementWidget extends NumberWidget
{
    static protected $defaults = array(
        'FILTER' => '=',
        'INPUT_SIZE' => 5,
        'WINDOW_WIDTH' => 600,
        'WINDOW_HEIGHT' => 500,
    );
    
    public function __construct(array $settings = array())
    {
        Loc::loadMessages(__FILE__);
        Loader::includeModule('iblock');
        
        parent::__construct($settings);
    }

    /**
     * {@inheritdoc}
     */
    public function getEditHtml()
    {
        $iblockId = (int) $this->getSettings('IBLOCK_ID');
        $inputSize = (int) $this->getSettings('INPUT_SIZE');
        $windowWidth = (int) $this->getSettings('WINDOW_WIDTH');
        $windowHeight = (int) $this->getSettings('WINDOW_HEIGHT');

        $name = 'FIELDS';
        $key = $this->getCode();

        $elementId = $this->getValue();

        if (!empty($elementId)) {
            $rsElement = ElementTable::getById($elementId);

            if (!$element = $rsElement->fetchAll()) {
                $element['NAME'] = Loc::getMessage('IBLOCK_ELEMENT_NOT_FOUND');
            }
        } else {
            $elementId = '';
        }

        return '<input name="' . $this->getEditInputName() . '"
                     id="' . $name . '[' . $key . ']"
                     value="' . $elementId . '"
                     size="' . $inputSize . '"
                     type="text">' .
        '<input type="button"
                    value="..."
                    onClick="jsUtils.OpenWindow(\'/bitrix/admin/iblock_element_search.php?lang=' . LANGUAGE_ID
        . '&amp;IBLOCK_ID=' . $iblockId . '&amp;n=' . $name . '&amp;k=' . $key . '\', ' . $windowWidth . ', '
        . $windowHeight . ');">' . '&nbsp;<span id="sp_' . md5($name) . '_' . $key . '" >'
        . static::prepareToOutput($element['NAME'])
        . '</span>';
    }

    /**
     * {@inheritdoc}
     */
    public function getValueReadonly()
    {
        $elementId = $this->getValue();

        if (!empty($elementId)) {
            $rsElement = ElementTable::getList([
                'filter' => [
                    'ID' => $elementId
                ],
                'select' => [
                    'ID',
                    'NAME',
                    'IBLOCK_ID',
                    'IBLOCK.IBLOCK_TYPE_ID',
                ]
            ]);

            $element = $rsElement->fetch();
            
            return '<a href="/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=' . $element['IBLOCK_ID']
            . '&type=' . $element['IBLOCK_ELEMENT_IBLOCK_IBLOCK_TYPE_ID'] . '&ID='
            . $elementId . '&lang=ru">[' . $elementId . '] ' . static::prepareToOutput($element['NAME']) . '</a>';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function generateRow(&$row, $data)
    {
        $elementId = $this->getValue();

        if (!empty($elementId)) {
            $rsElement = ElementTable::getList([
                'filter' => [
                    'ID' => $elementId
                ],
                'select' => [
                    'ID',
                    'NAME',
                    'IBLOCK_ID',
                    'IBLOCK.IBLOCK_TYPE_ID',
                ]
            ]);
            
            $element = $rsElement->fetch();
            
            $html = '<a href="/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=' . $element['IBLOCK_ID']
                . '&type=' . $element['IBLOCK_ELEMENT_IBLOCK_IBLOCK_TYPE_ID'] . '&ID='
                . $elementId . '&lang=ru">[' . $elementId . '] ' . static::prepareToOutput($element['NAME']) . '</a>';
        } else {
            $html = '';
        }

        $row->AddViewField($this->getCode(), $html);
    }
}
