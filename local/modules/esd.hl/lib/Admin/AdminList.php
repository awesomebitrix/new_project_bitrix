<?php namespace Esd\HL\Admin;

use Bitrix\Main\Entity;
use Esd\HL\Options;
use Bitrix\Main\Type;

class AdminList
{
	protected $headers = array();
	protected $filter = array();
	protected $select = array();
	protected $userFields = array();
	/** @var  \Esd\HL\MainTable */
	protected $mainTable;
	protected $entity;
	protected $options = null;

	private $hEntityName = 'HLBLOCK_';
	private $userTypes;
	private $USER_FIELD_MANAGER;

	public function __construct(\Esd\HL\MainTable $mainTable)
	{
		global $USER_FIELD_MANAGER;
		$this->USER_FIELD_MANAGER = $USER_FIELD_MANAGER;
		$this->mainTable = $mainTable;
		$this->entity = $this->mainTable->getHLEntity();
		$hBlock = $this->mainTable->getHBlock();
		$this->hEntityName .= $hBlock['ID'];
		$this->userTypes = $this->USER_FIELD_MANAGER->GetUserType();
		$this->userFields = $USER_FIELD_MANAGER->GetUserFields('HLBLOCK_'.$hBlock['ID'], false, LANGUAGE_ID);
	}

	public function setHeaders($headers = array())
	{
		$this->userFields = $this->USER_FIELD_MANAGER->GetUserFields($this->hEntityName, null, LANGUAGE_ID);
		$this->headers = array(
			array(
				'id' => 'ID',
				'content' => 'ID',
				'sort' => 'ID',
				'default' => true
			)
		);

		foreach ($this->userFields as $code => $field) {
			if ($field["SHOW_IN_LIST"] == "Y") {
//				$field["FIELD_NAME"] = preg_replace('#^UF_#i', '', $field["FIELD_NAME"]);
				$this->headers[] = array(
					"id" => $field["FIELD_NAME"],
					"content" => $field["LIST_COLUMN_LABEL"] ? $field["LIST_COLUMN_LABEL"] : $field["FIELD_NAME"],
					"sort" => $field["MULTIPLE"] == "N" ? $field["FIELD_NAME"] : false,
				);
			}
		}
	}

	/**
	 * @method getUserFields - get param userFields
	 * @param string $code
	 * @return array
	 */
	public function getUserFields($code = '')
	{
		if (strlen($code) == 0)
			return $this->userFields;
		else
			return $this->userFields[$code];
	}

	/**
	 * @method getUserFields - get param userFields
	 * @param string $code
	 * @return array
	 */
	public function getHeaders($code = '')
	{
		if (strlen($code) == 0)
			return $this->headers;
		else
			return $this->headers[$code];
	}

	/**
	 * @method addViewField
	 * @param $entity_id
	 * @param array $arRes
	 * @param \CAdminListRow $row
	 */
	public function addViewField($entity_id, array $arRes, \CAdminListRow &$row)
	{
		$arUserFields = $this->USER_FIELD_MANAGER->GetUserFields($entity_id);

		$arOption = $this->getOptions();
		if(isset($arOption['fields_type']) && is_array($arOption['fields_type'])){
			foreach ($arOption['fields_type'] as $code => $type) {
				$arUserFields[$code]['USER_TYPE'] = $this->userTypes[$type['type']];
			}
		}

		foreach($arUserFields as $FIELD_NAME=>$arUserField){
			if($arUserField["SHOW_IN_LIST"]=="Y" && array_key_exists($FIELD_NAME, $arRes)){
				$this->USER_FIELD_MANAGER->AddUserField($arUserField, $arRes[$FIELD_NAME], $row);
			}
		}
	}

	/**
	 * @method getOptions
	 * @param int $id
	 * @return null|array
	 * @throws \Bitrix\Main\ArgumentNullException
	 */
	public function getOptions($id = 0)
	{
		if(!is_null($this->options)){
			return $this->options;
		} else {
			$arMenu = unserialize(\Bitrix\Main\Config\Option::get(PW_HL_MODULE, 'menu_items'));
			$arFieldsParam =  Options::getFieldsParam();
			if(intval($id) > 0){
				$this->options['menu_items'] = $arMenu[$id];
				$this->options['fields_type'] = $arFieldsParam[$id];
			} else {
				$this->options['menu_items'] = unserialize(\Bitrix\Main\Config\Option::get(PW_HL_MODULE, 'menu_items'));
				$this->options['fields_type'] = Options::getFieldsParam();
			}

			return $this->options;
		}
	}

	/**
	 * @method getUserTypes - get param userTypes
	 * @return array|bool
	 */
	public function getUserTypes()
	{
		return $this->userTypes;
	}

	/**
	 * @method getUManager - get param USER_FIELD_MANAGER
	 * @return \CUserTypeManager
	 */
	public function getUManager()
	{
		return $this->USER_FIELD_MANAGER;
	}

	public function addFilterFields(&$arFilterFields)
	{
		$arUserFields = $this->getUserFields();
		foreach($arUserFields as $FIELD_NAME=>$arUserField){
			if($arUserField["SHOW_FILTER"]!="N" && $arUserField["USER_TYPE"]["BASE_TYPE"]!="file"){
				$arFilterFields[]="find_".$FIELD_NAME;
				if($arUserField['USER_TYPE_ID'] == 'datetime' || $arUserField['USER_TYPE_ID'] == 'date'){
					$arFilterFields[] = 'find_'.$FIELD_NAME.'_to';
				}
			}
		}
	}

	public function addAdminListFilter(&$arFilter)
	{
		$arUserFields = $this->getUserFields();
		foreach($arUserFields as $FIELD_NAME => $arUserField)
		{
			$value = $GLOBALS["find_".$FIELD_NAME];
			if(
				$arUserField["SHOW_FILTER"] != "N"
				&& $arUserField["USER_TYPE"]["BASE_TYPE"] != "file"
				&& $this->getUManager()->IsNotEmpty($value)
			){
				if($arUserField['USER_TYPE_ID'] == 'datetime' || $arUserField['USER_TYPE_ID'] == 'date'){
					$dateFilterFrom = $GLOBALS["find_".$FIELD_NAME];
					$dateFilterTo = $GLOBALS["find_".$FIELD_NAME.'_to'];

					switch($arUserField['USER_TYPE_ID']){
						case'datetime':
							$dateFrom = new Type\DateTime($dateFilterFrom);
							$dateTo = new Type\DateTime($dateFilterTo);
							break;
						case 'date':
							$dateFrom = new Type\Date($dateFilterFrom);
							$dateTo = new Type\Date($dateFilterTo);
							break;
					}
					if(strlen($dateFilterFrom) > 0 && strlen($dateFilterTo) > 0){
						$arFilter['><'.$FIELD_NAME] = [$dateFrom, $dateTo];
					} elseif(strlen($dateFilterFrom) > 0 && strlen($dateFilterTo) == 0){
						$arFilter['>'.$FIELD_NAME] = $dateFrom;
					} elseif(strlen($dateFilterFrom) == 0 && strlen($dateFilterTo) > 0){
						$arFilter['<'.$FIELD_NAME] = $dateFilterTo;
					}
				} else {
					if($arUserField["SHOW_FILTER"] == "I")
						$arFilter["=".$FIELD_NAME] = $value;
					elseif($arUserField["SHOW_FILTER"] == "S")
						$arFilter["%".$FIELD_NAME] = $value;
					else
						$arFilter[$FIELD_NAME] = $value;
				}
			}
		}
	}

	public function addFindFields(&$arFindFields)
	{
		$arUserFields = $this->getUserFields();
		foreach($arUserFields as $FIELD_NAME=>$arUserField) {
			if($arUserField["SHOW_FILTER"]!="N" && $arUserField["USER_TYPE"]["BASE_TYPE"]!="file") {
				if($arUserField["USER_TYPE"] && is_callable(array($arUserField["USER_TYPE"]["CLASS_NAME"], "getfilterhtml"))) {
					if($arUserField["LIST_FILTER_LABEL"]) {
						$arFindFields[$FIELD_NAME] = $arUserField["LIST_FILTER_LABEL"];
					} else {
						$arFindFields[$FIELD_NAME] = $arUserField["FIELD_NAME"];
					}
				}
			}
		}
	}

	public function getFilterHTML($arUserField, $filter_name, $filter_value)
	{
		if($arUserField['USER_TYPE_ID'] != 'datetime' && $arUserField['USER_TYPE_ID'] != 'date'){
			if($arUserField["USER_TYPE"]) {
				if(is_callable(array($arUserField["USER_TYPE"]["CLASS_NAME"], "getfilterhtml"))) {
					$html = call_user_func_array(
							array($arUserField["USER_TYPE"]["CLASS_NAME"], "getfilterhtml"),
							array(
								$arUserField,
								array(
									"NAME" => $filter_name,
									"VALUE" => htmlspecialcharsex($filter_value),
								),
							)
						).\CAdminCalendar::ShowScript();
					return '<tr><td>'.htmlspecialcharsbx($arUserField["LIST_FILTER_LABEL"]? $arUserField["LIST_FILTER_LABEL"]: $arUserField["FIELD_NAME"]).':</td><td>'.$html.'</td></tr>';
				}
			}
		} else {
			$resStr = '<tr><td>';
			$resStr .= htmlspecialcharsbx($arUserField["LIST_FILTER_LABEL"]? $arUserField["LIST_FILTER_LABEL"]: $arUserField["FIELD_NAME"]);
			$resStr .= '</td><td>';
			$resStr .= CalendarPeriod(
				"find_".$arUserField["FIELD_NAME"],
				htmlspecialcharsex($GLOBALS["find_".$arUserField['FIELD_NAME']]),
				"find_".$arUserField["FIELD_NAME"]."_to",
				htmlspecialcharsex($GLOBALS["find_".$arUserField['FIELD_NAME'].'_to']),
				"find_form",
				"N"
			);
			$resStr .= '</td></tr>';
			return $resStr;
		}

		return '';
	}
}