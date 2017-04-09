<?namespace Esd\HL\Admin;
/**
 * Class AdminForm
 * @package Esd\HL\Admin
 */
class AdminForm extends AdminList
{
	function showUserFieldsWithReadyData(\CAdminForm $CAdminForm, $PROPERTY_ID, $readyData, $bVarsFromForm, $primaryIdName = 'VALUE_ID')
	{
		/**
		 * @global \CMain $APPLICATION
		 * @global \CUserTypeManager $USER_FIELD_MANAGER
		 */
		global $USER_FIELD_MANAGER, $APPLICATION;

		$ID_HL = $PROPERTY_ID;
		$PROPERTY_ID = 'HLBLOCK_'.$PROPERTY_ID;

		if($USER_FIELD_MANAGER->GetRights($PROPERTY_ID) >= "W")
		{
			$CAdminForm->BeginCustomField("USER_FIELDS_ADD", GetMessage("admin_lib_add_user_field"));
			?>
			<tr>
				<td colspan="2" align="left">
					<a href="/bitrix/admin/userfield_edit.php?lang=<?echo LANGUAGE_ID?>&amp;ENTITY_ID=<?echo urlencode($PROPERTY_ID)?>&amp;back_url=<?echo urlencode($APPLICATION->GetCurPageParam($CAdminForm->name.'_active_tab=user_fields_tab', array($CAdminForm->name.'_active_tab')))?>"><?echo $CAdminForm->GetCustomLabelHTML()?></a>
				</td>
			</tr>
			<?
			$CAdminForm->EndCustomField("USER_FIELDS_ADD", '');
		}

		$arUserFields = $USER_FIELD_MANAGER->getUserFieldsWithReadyData($PROPERTY_ID, $readyData, LANGUAGE_ID, false, $primaryIdName);

		$arOptions = $this->getOptions($ID_HL);
		$arTypes = $this->getUserTypes();

		foreach($arUserFields as $FIELD_NAME => $arUserField) {
			if(array_key_exists($FIELD_NAME, $arOptions['fields_type'])){
				$type = $arOptions['fields_type'][$FIELD_NAME]['type'];
				$arUserField['USER_TYPE'] = $arTypes[$type];
			}

			$arUserField["VALUE_ID"] = intval($readyData[$primaryIdName]);
			if(array_key_exists($FIELD_NAME, $CAdminForm->arCustomLabels))
				$strLabel = $CAdminForm->arCustomLabels[$FIELD_NAME];
			else
				$strLabel = $arUserField["EDIT_FORM_LABEL"]? $arUserField["EDIT_FORM_LABEL"]: $arUserField["FIELD_NAME"];
			$arUserField["EDIT_FORM_LABEL"] = $strLabel;

			$CAdminForm->BeginCustomField($FIELD_NAME, $strLabel, $arUserField["MANDATORY"]=="Y");

			if($arUserField['EDIT_IN_LIST'] != 'N'){
				if(isset($_REQUEST['def_'.$FIELD_NAME]))
					$arUserField['SETTINGS']['DEFAULT_VALUE'] = $_REQUEST['def_'.$FIELD_NAME];

				echo $USER_FIELD_MANAGER->GetEditFormHTML($bVarsFromForm, $GLOBALS[$FIELD_NAME], $arUserField);

				$form_value = $GLOBALS[$FIELD_NAME];
				if(!$bVarsFromForm)
					$form_value = $arUserField["VALUE"];
				elseif($arUserField["USER_TYPE"]["BASE_TYPE"]=="file")
					$form_value = $GLOBALS[$arUserField["FIELD_NAME"]."_old_id"];

				$hidden = "";
				if(is_array($form_value))
				{
					foreach($form_value as $value)
						$hidden .= '<input type="hidden" name="'.$FIELD_NAME.'[]" value="'.htmlspecialcharsbx($value).'">';
				}
				else
				{
					$hidden .= '<input type="hidden" name="'.$FIELD_NAME.'" value="'.htmlspecialcharsbx($form_value).'">';
				}
			} else {
				$strField =  '<tr>';
				$strField .= 	'<td>'.$arUserField["EDIT_FORM_LABEL"].'</td>';
				$strField .= 	'<td>'.$arUserField['VALUE'].'</td>';
				$strField .= '</tr>';
				echo $strField;
			}


			$CAdminForm->EndCustomField($FIELD_NAME, $hidden);
		}
	}
}