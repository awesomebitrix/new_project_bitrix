<?php namespace Esd\HL\Type;

/**
 * Class IBlockElementRef
 * @package Esd\HL\Type
 */
class IBlockElementRef extends \CUserTypeIBlockElement implements IType
{
	public function GetUserTypeDescription()
	{
		return array(
			"USER_TYPE_ID" => "IBlockElementRef",
			"CLASS_NAME" => "\\Esd\\HL\\Type\\IBlockElementRef",
			"DESCRIPTION" => "Адекватная привязка к элементам ИБ",
			"BASE_TYPE" => "int",
		);
	}

	public function GetEditFormHTML($arUserField, $arHtmlControl)
	{
		return self::getHtmlField($arUserField, $arHtmlControl);
	}

	/**
	 * Эта функция вызывается при выводе формы настройки свойства.
	 *
	 * <p>Возвращает html для встраивания в 2-х колоночную таблицу.
	 * в форму usertype_edit.php</p>
	 * <p>т.е. tr td bla-bla /td td edit-edit-edit /td /tr </p>
	 * @param array|bool $arUserField Массив описывающий поле. Для нового (еще не добавленного поля - <b>false</b>)
	 * @param array $arHtmlControl Массив управления из формы. Пока содержит только один элемент NAME (html безопасный)
	 * @return string HTML для вывода.
	 * @static
	 */
	public function GetSettingsHTML($arUserField = false, $arHtmlControl, $bVarsFromForm)
	{
		$result = '';

		if($bVarsFromForm)
			$iblock_id = $GLOBALS[$arHtmlControl["NAME"]]["IBLOCK_ID"];
		elseif(is_array($arUserField))
			$iblock_id = $arUserField["SETTINGS"]["IBLOCK_ID"];
		else
			$iblock_id = "";
		if(\Bitrix\Main\Loader::includeModule('iblock'))
		{
			$result .= '
			<tr>
				<td>'.GetMessage("USER_TYPE_IBEL_DISPLAY").':</td>
				<td>
					'.GetIBlockDropDownList($iblock_id, $arHtmlControl["NAME"].'[IBLOCK_TYPE_ID]', $arHtmlControl["NAME"].'[IBLOCK_ID]', false, 'class="adm-detail-iblock-types"', 'class="adm-detail-iblock-list"').'
				</td>
			</tr>
			';
		}
		else
		{
			$result .= '
			<tr>
				<td>'.GetMessage("USER_TYPE_IBEL_DISPLAY").':</td>
				<td>
					<input type="text" size="6" name="'.$arHtmlControl["NAME"].'[IBLOCK_ID]" value="'.htmlspecialcharsbx($value).'">
				</td>
			</tr>
			';
		}

		if($bVarsFromForm)
			$ACTIVE_FILTER = $GLOBALS[$arHtmlControl["NAME"]]["ACTIVE_FILTER"] === "Y"? "Y": "N";
		elseif(is_array($arUserField))
			$ACTIVE_FILTER = $arUserField["SETTINGS"]["ACTIVE_FILTER"] === "Y"? "Y": "N";
		else
			$ACTIVE_FILTER = "N";

		if($bVarsFromForm)
			$value = $GLOBALS[$arHtmlControl["NAME"]]["DEFAULT_VALUE"];
		elseif(is_array($arUserField))
			$value = $arUserField["SETTINGS"]["DEFAULT_VALUE"];
		else
			$value = "";

		if($bVarsFromForm)
			$value = $GLOBALS[$arHtmlControl["NAME"]]["DISPLAY"];
		elseif(is_array($arUserField))
			$value = $arUserField["SETTINGS"]["DISPLAY"];
		else
			$value = "LIST";

		if($bVarsFromForm)
			$value = intval($GLOBALS[$arHtmlControl["NAME"]]["LIST_HEIGHT"]);
		elseif(is_array($arUserField))
			$value = intval($arUserField["SETTINGS"]["LIST_HEIGHT"]);
		else
			$value = 5;

		return $result;
	}

	/**
	 * @method GetEditFormHTMLMulty
	 * @param $arUserField
	 * @param $arHtmlControl
	 * @return string
	 */
	public function GetEditFormHTMLMulty($arUserField, $arHtmlControl)
	{
		return self::getHtmlField($arUserField, $arHtmlControl);
	}

	public static function getHtmlField($arUserField, $arHtmlControl)
	{
		global $APPLICATION;

		ob_start();
		$APPLICATION->IncludeComponent('hl:admin.field.edit', self::getDesc('USER_TYPE_ID'), array(
			'FIELD'=>$arUserField,
			'HTML_CTRL'=>$arHtmlControl,
			'CACHE_TYPE'=>'N',
		));
		$return = ob_get_contents();
		ob_get_clean();

		return $return;
	}

	/**
	 * Эта функция вызывается при выводе фильтра на странице списка.
	 *
	 * <p>Возвращает html для встраивания в ячейку таблицы.</p>
	 * <p>Элементы $arHtmlControl приведены к html безопасному виду.</p>
	 * @param array $arUserField Массив описывающий поле.
	 * @param array $arHtmlControl Массив управления из формы. Содержит элементы NAME и VALUE.
	 * @return string HTML для вывода.
	 * @static
	 */
	function GetFilterHTML($arUserField, $arHtmlControl)
	{
		return '<input type="text" '.
		'name="'.$arHtmlControl["NAME"].'" '.
		'size="'.$arUserField["SETTINGS"]["SIZE"].'" '.
		'value="'.$arHtmlControl["VALUE"].'"'.
		'>';
	}

	/**
	 * Эта функция вызывается при выводе значения свойства в списке элементов.
	 *
	 * <p>Возвращает html для встраивания в ячейку таблицы.</p>
	 * <p>Элементы $arHtmlControl приведены к html безопасному виду.</p>
	 * @param array $arUserField Массив описывающий поле.
	 * @param array $arHtmlControl Массив управления из формы. Содержит элементы NAME и VALUE.
	 * @return string HTML для вывода.
	 * @static
	 */
	function GetAdminListViewHTML($arUserField, $arHtmlControl)
	{
		if(strlen($arHtmlControl["VALUE"])>0)
			return $arHtmlControl["VALUE"];
		else
			return '&nbsp;';
	}

	/**
	 * Эта функция вызывается при выводе значения <b>множественного</b> свойства в списке элементов.
	 *
	 * <p>Возвращает html для встраивания в ячейку таблицы.</p>
	 * <p>Если класс не предоставляет такую функцию,
	 * то менеджер типов "соберет" требуемый html из вызовов GetAdminListViewHTML</p>
	 * <p>Элементы $arHtmlControl приведены к html безопасному виду.</p>
	 * <p>Поле VALUE $arHtmlControl - массив.</p>
	 * @param array $arUserField Массив описывающий поле.
	 * @param array $arHtmlControl Массив управления из формы. Содержит элементы NAME и VALUE.
	 * @return string HTML для вывода.
	 * @static
	 */
	function GetAdminListViewHTMLMulty($arUserField, $arHtmlControl)
	{
		return implode(", ", $arHtmlControl["VALUE"]);
	}

	/**
	 * Эта функция вызывается при выводе значения свойства в списке элементов в режиме <b>редактирования</b>.
	 *
	 * <p>Возвращает html для встраивания в ячейку таблицы.</p>
	 * <p>Элементы $arHtmlControl приведены к html безопасному виду.</p>
	 * @param array $arUserField Массив описывающий поле.
	 * @param array $arHtmlControl Массив управления из формы. Содержит элементы NAME и VALUE.
	 * @return string HTML для вывода.
	 * @static
	 */
	function GetAdminListEditHTML($arUserField, $arHtmlControl)
	{
		return '<input type="text" name="'.$arHtmlControl["NAME"].'" value="'.$arHtmlControl["VALUE"].'" />';
	}

	/**
	 * @method getDesc
	 * @param string $key
	 * @return array
	 */
	public static function getDesc($key = '')
	{
		$arDesc = self::GetUserTypeDescription();
		if(strlen($key) == 0){
			return $arDesc;
		} else {
			return $arDesc[$key];
		}
	}
}