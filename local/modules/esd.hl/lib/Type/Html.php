<?php namespace Esd\HL\Type;
use Bitrix\Main\Config\Option;

/**
 * Class Html
 * @package Esd\HL\Type
 */
class Html implements IType
{
	function GetUserTypeDescription()
	{
		return array(
			"USER_TYPE_ID" => "Html",
			"CLASS_NAME" => "\\Esd\\HL\\Type\\Html",
			"DESCRIPTION" => "Html/text",
			"BASE_TYPE" => "string",
		);
	}

	/**
	 * Эта функция вызывается при добавлении нового свойства.
	 *
	 * <p>Эта функция вызывается для конструирования SQL запроса
	 * создания колонки для хранения не множественных значений свойства.</p>
	 * <p>Значения множественных свойств хранятся не в строках, а столбиках (как в инфоблоках)
	 * и тип такого поля в БД всегда text.</p>
	 * @param array $arUserField Массив описывающий поле
	 * @return string
	 * @static
	 */
	function GetDBColumnType($arUserField)
	{
		global $DB;
		switch(strtolower($DB->type))
		{
			case "mysql":
				return "text";
			case "oracle":
				return "varchar2(2000 char)";
			case "mssql":
				return "varchar(2000)";
		}
	}

	/**
	 * Эта функция вызывается перед сохранением метаданных свойства в БД.
	 *
	 * <p>Она должна "очистить" массив с настройками экземпляра типа свойства.
	 * Для того что бы случайно/намеренно никто не записал туда всякой фигни.</p>
	 * @param array $arUserField Массив описывающий поле. <b>Внимание!</b> это описание поля еще не сохранено в БД!
	 * @return array Массив который в дальнейшем будет сериализован и сохранен в БД.
	 * @static
	 */
	function PrepareSettings($arUserField)
	{
		return array(
			"DEFAULT_VALUE" => $arUserField["SETTINGS"]["DEFAULT_VALUE"],
			"HEIGHT" => $arUserField["SETTINGS"]["HEIGHT"]
		);
	}

	/**
	 * Эта функция вызывается при выводе формы настройки свойства.
	 *
	 * <p>Возвращает html для встраивания в 2-х колоночную таблицу.
	 * в форму usertype_edit.php</p>
	 * <p>т.е. tr td bla-bla /td td edit-edit-edit /td /tr </p>
	 * @param array $arUserField Массив описывающий поле. Для нового (еще не добавленного поля - <b>false</b>)
	 * @param array $arHtmlControl Массив управления из формы. Пока содержит только один элемент NAME (html безопасный)
	 * @return string HTML для вывода.
	 * @static
	 */
	function GetSettingsHTML($arUserField = false, $arHtmlControl, $bVarsFromForm)
	{
		$defaultHeight = $arUserField['SETTINGS']['HEIGHT'];
		if(intval($defaultHeight) == 0)
			$defaultHeight = 200;
		ob_start();?>
		<tr>
			<td>Высота окна редактора (px):</td>
			<td>
				<input type="text" name="<?=$arHtmlControl['NAME']?>[HEIGHT]" value="<?=$defaultHeight?>" />
			</td>
		</tr>
		<?$result = ob_get_contents();
		ob_get_clean();

		return $result;
	}

	function GetAdminListEditHTML($arUserField, $arHtmlControl)
	{
		return false;
	}

	/**
	 * Эта функция вызывается при выводе формы редактирования значения свойства.
	 *
	 * <p>Возвращает html для встраивания в ячейку таблицы.
	 * в форму редактирования сущности (на вкладке "Доп. свойства")</p>
	 * <p>Элементы $arHtmlControl приведены к html безопасному виду.</p>
	 * @param array $arUserField Массив описывающий поле.
	 * @param array $arHtmlControl Массив управления из формы. Содержит элементы NAME и VALUE.
	 * @return string HTML для вывода.
	 * @static
	 */

	public function GetEditFormHTML($arUserField, $arHtmlControl)
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