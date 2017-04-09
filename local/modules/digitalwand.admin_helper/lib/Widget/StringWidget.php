<?php

namespace DigitalWand\AdminHelper\Widget;

use Bitrix\Main\Localization\Loc;
use DigitalWand\AdminHelper\Helper\AdminEditHelper;
use DigitalWand\AdminHelper\Helper\AdminListHelper;
use DigitalWand\AdminHelper\Helper\AdminSectionListHelper;

Loc::loadMessages(__FILE__);

/**
 * Р’РёРґР¶РµС‚ СЃС‚СЂРѕРєРё СЃ С‚РµРєСЃС‚РѕРј.
 *
 * Р”РѕСЃС‚СѓРїРЅС‹Рµ РѕРїС†РёРё:
 * <ul>
 * <li> <b>EDIT_LINK</b> - РѕС‚РѕР±СЂР°Р¶Р°С‚СЊ РІ РІРёРґРµ СЃСЃС‹Р»РєРё РЅР° СЂРµРґР°РєС‚РёСЂРѕРІР°РЅРёРµ СЌР»РµРјРµРЅС‚Р° </li>
 * <li> <b>STYLE</b> - inline-СЃС‚РёР»Рё РґР»СЏ input </li>
 * <li> <b>SIZE</b> - Р·РЅР°С‡РµРЅРёРµ Р°С‚СЂРёР±СѓС‚Р° size РґР»СЏ input </li>
 * <li> <b>TRANSLIT</b> - true, РµСЃР»Рё РїРѕР»Рµ Р±СѓРґРµС‚ С‚СЂР°РЅСЃР»РёС‚РµСЂРёСЂРѕРІР°С‚СЊСЃСЏ РІ СЃРёРјРІРѕР»СЊРЅС‹Р№ РєРѕРґ</li>
 * <li> <b>MULTIPLE</b> - РїРѕРґРґРµСЂР¶РёРІР°РµС‚СЃСЏ РјРЅРѕР¶РµСЃС‚РІРµРЅРЅС‹Р№ РІРІРѕРґ. Р’ С‚Р°Р±Р»РёС†Рµ С‚СЂРµР±СѓРµС‚СЃСЏ РЅР°Р»РёС‡РёРµ РїРѕР»СЏ VALUE</li>
 * </ul>
 */
class StringWidget extends HelperWidget
{
    static protected $defaults = array(
        'FILTER' => '%', //Р¤РёР»СЊС‚СЂР°С†РёСЏ РїРѕ РїРѕРґСЃС‚СЂРѕРєРµ, Р° РЅРµ РїРѕ С‚РѕС‡РЅРѕРјСѓ СЃРѕРѕС‚РІРµС‚СЃС‚РІРёСЋ.
        'EDIT_IN_LIST' => true
    );

    /**
     * @inheritdoc
     */
    protected function getEditHtml()
    {
        $style = $this->getSettings('STYLE');
        $size = $this->getSettings('SIZE');

        $link = '';

        if ($this->getSettings('TRANSLIT')) {

            //TODO: refactor this!
            $uniqId = get_class($this->entityName) . '_' . $this->getCode();
            $nameId = 'name_link_' . $uniqId;
            $linkedFunctionName = 'set_linked_' . get_class($this->entityName) . '_CODE';//FIXME: hardcode here!!!

            if (isset($this->entityName->{$this->entityName->pk()})) {
                $pkVal = $this->entityName->{$this->entityName->pk()};
            } else {
                $pkVal = '_new_';
            }

            $nameId .= $pkVal;
            $linkedFunctionName .= $pkVal;

            $link = '<image id="' . $nameId . '" title="' . Loc::getMessage("IBSEC_E_LINK_TIP") . '" class="linked" src="/bitrix/themes/.default/icons/iblock/link.gif" onclick="' . $linkedFunctionName . '()" />';
        }

        return '<input type="text"
                       name="' . $this->getEditInputName() . '"
                       value="' . static::prepareToTagAttr($this->getValue()) . '"
                       size="' . $size . '"
                       style="' . $style . '"/>' . $link;
    }

    protected function getMultipleEditHtml()
    {
        $style = $this->getSettings('STYLE');
        $size = $this->getSettings('SIZE');
        $uniqueId = $this->getEditInputHtmlId();

        $rsEntityData = null;

        if (!empty($this->data['ID'])) {
            $entityName = $this->entityName;
            $rsEntityData = $entityName::getList(array(
                'select' => array('REFERENCE_' => $this->getCode() . '.*'),
                'filter' => array('=ID' => $this->data['ID'])
            ));
        }

        ob_start();
        ?>

        <div id="<?= $uniqueId ?>-field-container" class="<?= $uniqueId ?>">
        </div>

        <script>
            var multiple = new MultipleWidgetHelper(
                '#<?= $uniqueId ?>-field-container',
                '{{field_original_id}}<input type="text" name="<?= $this->getCode()?>[{{field_id}}][<?=$this->getMultipleField('VALUE')?>]" style="<?=$style?>" size="<?=$size?>" value="{{value}}">'
            );
            <?
            if ($rsEntityData)
            {
                while($referenceData = $rsEntityData->fetch())
                {
                    if (empty($referenceData['REFERENCE_' . $this->getMultipleField('ID')]))
                    {
                        continue;
                    }

                    ?>
            multiple.addField({
                value: '<?= static::prepareToJs($referenceData['REFERENCE_' . $this->getMultipleField('VALUE')]) ?>',
                field_original_id: '<input type="hidden" name="<?= $this->getCode()?>[{{field_id}}][<?= $this->getMultipleField('ID') ?>]"' +
                ' value="<?= $referenceData['REFERENCE_' . $this->getMultipleField('ID')] ?>">',
                field_id: <?= $referenceData['REFERENCE_' . $this->getMultipleField('ID')] ?>
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

    protected function getMultipleValueReadonly()
    {
        $rsEntityData = null;
        if (!empty($this->data['ID'])) {
            $entityName = $this->entityName;
            $rsEntityData = $entityName::getList(array(
                'select' => array('REFERENCE_' => $this->getCode() . '.*'),
                'filter' => array('=ID' => $this->data['ID'])
            ));
        }

        $result = '';
        if ($rsEntityData) {
            while ($referenceData = $rsEntityData->fetch()) {
                if (empty($referenceData['REFERENCE_VALUE'])) {
                    continue;
                }

                $result .= '<div class="wrap_text" style="margin-bottom: 5px">' .
                    static::prepareToOutput($referenceData['REFERENCE_VALUE']) . '</div>';
            }
        }

        return $result;
    }

    /**
     * Р“РµРЅРµСЂРёСЂСѓРµС‚ HTML РґР»СЏ РїРѕР»СЏ РІ СЃРїРёСЃРєРµ
     * @see AdminListHelper::addRowCell();
     * @param \CAdminListRow $row
     * @param array $data - РґР°РЅРЅС‹Рµ С‚РµРєСѓС‰РµР№ СЃС‚СЂРѕРєРё
     */
    public function generateRow(&$row, $data)
    {
        if ($this->getSettings('MULTIPLE')) {
        } else {
            if ($this->getSettings('EDIT_LINK') || $this->getSettings('SECTION_LINK')) {
                $entityClass = $this->entityName;
                $pk = $entityClass::getEntity()->getPrimary();

                if ($this->getSettings('SECTION_LINK')) {
                    $params = $this->helper->isPopup() ? $_GET : array();
                    $params['ID'] = $this->data[$pk];
                    $listHelper = $this->helper->getHelperClass($this->helper->isPopup() ? AdminSectionListHelper::className() : AdminListHelper::className());
                    $pageUrl = $listHelper::getUrl($params);
                    $value = '<span class="adm-submenu-item-link-icon adm-list-table-icon iblock-section-icon"></span>';
                } else {
                    $editHelper = $this->helper->getHelperClass(AdminEditHelper::className());
                    $pageUrl = $editHelper::getUrl(array(
                        'ID' => $this->data[$pk]
                    ));
                }

                $value .= '<a href="' . $pageUrl . '">' . static::prepareToOutput($this->getValue()) . '</a>';
            } else {
                $value = static::prepareToOutput($this->getValue());
            }

            if ($this->getSettings('EDIT_IN_LIST') AND !$this->getSettings('READONLY')) {
                $row->AddInputField($this->getCode(), array('style' => 'width:90%'));
            }

            $row->AddViewField($this->getCode(), $value);
        }
    }

    /**
     * @inheritdoc
     */
    public function showFilterHtml()
    {
        if ($this->getSettings('MULTIPLE')) {
        } else {
            print '<tr>';
            print '<td>' . $this->getSettings('TITLE') . '</td>';

            if ($this->isFilterBetween()) {
                list($from, $to) = $this->getFilterInputName();
                print '<td>
            <div class="adm-filter-box-sizing">
                <span style="display: inline-block; left: 11px; top: 5px; position: relative;">РћС‚:</span>
                <div class="adm-input-wrap" style="display: inline-block">
                    <input type="text" class="adm-input" name="' . $from . '" value="' . $$from . '">
                </div>
                <span style="display: inline-block; left: 11px; top: 5px; position: relative;">Р”Рѕ:</span>
                <div class="adm-input-wrap" style="display: inline-block">
                    <input type="text" class="adm-input" name="' . $to . '" value="' . $$to . '">
                </div>
            </div>
            </td> ';
            } else {
                print '<td><input type="text" name="' . $this->getFilterInputName() . '" size="47" value="' . $this->getCurrentFilterValue() . '"></td>';
            }

            print '</tr>';
        }
    }
}