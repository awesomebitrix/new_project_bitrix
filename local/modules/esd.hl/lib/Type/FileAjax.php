<?php namespace Esd\HL\Type;

use Esd\Debug;

class FileAjax extends \CUserTypeFile implements IType
{
	function GetUserTypeDescription()
	{
		return array(
			"USER_TYPE_ID" => "FileAjax",
			"CLASS_NAME" => "\\Esd\\HL\\Type\\FileAjax",
			"DESCRIPTION" => "Ajax - загрузка файлов",
			"BASE_TYPE" => "int"
		);
	}

	function GetEditFormHTML($arUserField, $arHtmlControl)
	{
		\Bitrix\Main\Loader::includeModule("fileman");

		$result = '';
		$arHtmlControl["VALIGN"] = "middle";
		$arHtmlControl["ROWCLASS"] = "adm-detail-file-row";

		if(($p=strpos($arHtmlControl["NAME"], "["))>0)
			$strOldIdName = substr($arHtmlControl["NAME"], 0, $p)."_old_id".substr($arHtmlControl["NAME"], $p);
		else
			$strOldIdName = $arHtmlControl["NAME"]."_old_id";

//		PR($arUserField);
		PR($arHtmlControl);

		if($arUserField['MULTIPLE'] != 'Y'){
			$inputName = array();
			$file_id = $arHtmlControl['VALUE'];
			$name = $arHtmlControl['NAME'];
			$key = 0;
			$inputName[$name.'[n0]'] = $file_id;
			PR($inputName);
			$result = \Bitrix\Main\UI\FileInput::createInstance((
				array(
					"name" => $name."[n#IND#]",
					"description" => 'N',
					"maxCount"=>1,
					"upload" => true,
					"medialib" => true,
					"fileDialog" => true,
					"cloud" => true,
				)
			))->show($inputName);

		} else {

		}

		return $result;
	}

	function OnBeforeSave($arUserField, $value)
	{
		Debug::toLog($arUserField);
		$ID = 0;
		$CFile = new \CFile();
		if(strlen($value) > 0){
			$arFileTmp = $CFile->MakeFileArray($value);
			$ID = $CFile->SaveFile($arFileTmp,'/hl');
		}
		$value = $ID;
		return $ID;
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