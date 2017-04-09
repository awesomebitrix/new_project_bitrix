<?php

namespace DigitalWand\AdminHelper\Widget;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Р’С‹РІРѕРґРёС‚ textarea РґР»СЏ СЂРµРґР°РєС‚РёСЂРѕРІР°РЅРёСЏ РґР»РёРЅРЅС‹С… СЃС‚СЂРѕРє.
 * РЈСЂРµР·Р°РµС‚ РґР»РёРЅРЅС‹Рµ СЃС‚СЂРѕРєРё РїСЂРё РѕС‚РѕР±СЂР°Р¶РµРЅРёРё РІ СЃРїРёСЃРєРµ
 *
 * Р”РѕСЃС‚СѓРїРЅС‹Рµ РѕРїС†РёРё:
 * <ul>
 * <li><b>COLS</b> - С€РёСЂРёРЅР°</li>
 * <li><b>ROWS</b> - РІС‹СЃРѕС‚Р°</li>
 * </ul>
 */
class TextAreaWidget extends StringWidget
{
    /**
     * РєРѕР»РёС‡РµСЃС‚РІРѕ РѕС‚РѕР±СЂР°Р¶Р°РµРјС‹С… СЃРёРјРІРѕР»РѕРІ РІ СЂРµР¶РёРјРµ СЃРїРёСЃРєР°.
     */
    const LIST_TEXT_SIZE = 150;

    static protected $defaults = array(
        'COLS' => 65,
        'ROWS' => 5,
        'EDIT_IN_LIST' => false
    );

    /**
     * @inheritdoc
     */
    protected function getEditHtml()
    {
        $cols = $this->getSettings('COLS');
        $rows = $this->getSettings('ROWS');

        return '<textarea cols="' . $cols . '" rows="' . $rows . '" name="' . $this->getEditInputName() . '">'
        . static::prepareToOutput($this->getValue(), false) . '</textarea>';
    }

    /**
     * @inheritdoc
     */
    public function generateRow(&$row, $data)
    {
        $text = $this->getValue();

        if ($this->getSettings('EDIT_IN_LIST') AND !$this->getSettings('READONLY')) {
            $row->AddInputField($this->getCode(), array('style' => 'width:90%'));
        } else {
            if (strlen($text) > self::LIST_TEXT_SIZE && !$this->isExcelView()) {
                $pos = false;
                $pos = $pos === false ? stripos($text, " ", self::LIST_TEXT_SIZE) : $pos;
                $pos = $pos === false ? stripos($text, "\n", self::LIST_TEXT_SIZE) : $pos;
                $pos = $pos === false ? stripos($text, "</", self::LIST_TEXT_SIZE) : $pos;
                $pos = $pos === false ? 300 : $pos;
                $text = substr($text, 0, $pos) . " ...";
            }

            $text = static::prepareToOutput($text);

            $row->AddViewField($this->code, $text);
        }
    }
}