<?php

namespace DigitalWand\AdminHelper\Widget;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Р’РёРґР¶РµС‚ СЃ С‡РёСЃР»РѕРІС‹РјРё Р·РЅР°С‡РµРЅРёСЏРјРё. РўРѕС‡РЅР°СЏ РєРѕРїРёСЏ StringWidget, С‚РѕР»СЊРєРѕ СЂР°Р±РѕС‚Р°РµС‚ СЃ С‡РёСЃР»Р°РјРё Рё РЅРµ РёС‰РµС‚ РїРѕ РїРѕРґСЃС‚СЂРѕРєРµ.
 */
class NumberWidget extends StringWidget
{
    static protected $defaults = array(
        'FILTER' => '=',
        'EDIT_IN_LIST' => true
    );

    public function checkFilter($operationType, $value)
    {
        return $this->isNumber($value);
    }

    public function checkRequired()
    {
        if ($this->getSettings('REQUIRED') == true) {
            $value = $this->getValue();
            return !is_null($value);
        } else {
            return true;
        }
    }

    public function processEditAction()
    {
        if (!$this->checkRequired()) {
            $this->addError('DIGITALWAND_AH_REQUIRED_FIELD_ERROR');

        } else if (!$this->isNumber($this->getValue())) {
            $this->addError('VALUE_IS_NOT_NUMERIC');
        }
    }

    protected function isNumber($value)
    {
        return intval($value) OR floatval($value) OR doubleval($value) OR is_null($value) OR empty($value);
    }
}