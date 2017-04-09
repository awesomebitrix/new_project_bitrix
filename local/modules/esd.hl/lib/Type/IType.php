<?php namespace Esd\HL\Type;

interface IType
{
	public function GetUserTypeDescription();

	public function GetDBColumnType($arUserField);

	public function GetEditFormHTML($arUserField, $arHtmlControl);

	public function GetAdminListViewHTML($arUserField, $arHtmlControl);

	public static function getDesc($key);
}