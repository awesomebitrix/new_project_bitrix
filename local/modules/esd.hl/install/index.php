<?
use Bitrix\Main\Localization\Loc,
	Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

if (class_exists("esd_hl"))
	return;

class esd_hl extends CModule
{
	var $MODULE_ID = "esd.hl";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $PARTNER_NAME;
	var $PARTNER_URI;

	protected $errors;
	protected $APP;
	protected $EventManager;

	function __construct()
	{
		global $APPLICATION;
		$arModuleVersion = array();

		include(dirname(__FILE__)."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

		$this->MODULE_NAME = GetMessage("ESD_HL_INSTALL_NAME");
		$this->APP = $APPLICATION;
//		$this->MODULE_DESCRIPTION = GetMessage("ST_INSTALL_DESCRIPTION");

		$this->EventManager = \Bitrix\Main\EventManager::getInstance();
	}

	/**
	 * @method InstallFiles
	 * @return bool
	 */
	function InstallFiles()
	{
		CopyDirFiles(
			dirname(__FILE__)."/admin",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true
		);
		CopyDirFiles(
			dirname(__FILE__)."/themes/.default/css",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default/".$this->MODULE_ID."/css",
			true, true
		);
		CopyDirFiles(
			dirname(__FILE__)."/themes/.default/fonts",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default/".$this->MODULE_ID."/fonts",
			true, true
		);
		CopyDirFiles(
			dirname(__FILE__)."/themes/.default/js",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$this->MODULE_ID,
			true, true
		);

		return true;
	}

	/**
	 * @method UnInstallFiles
	 * @return bool
	 */
	function UnInstallFiles()
	{
		DeleteDirFilesEx("/bitrix/themes/.default/".$this->MODULE_ID);
		DeleteDirFilesEx("/bitrix/js/".$this->MODULE_ID);
		DeleteDirFiles(dirname(__FILE__)."/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");

		return true;
	}

	/**
	 * @method DoInstall
	 * @return bool
	 */
	function DoInstall()
	{
		global $DB;
		$this->errors = false;
		$this->errors = $DB->RunSQLBatch(dirname(__FILE__)."/mysql/install.sql");
		if($this->errors){
			$this->APP->ThrowException(implode("", $this->errors));
			return false;
		}
		ModuleManager::registerModule($this->MODULE_ID);

		$this->addEvents();

		$this->InstallFiles();

		return true;
	}

	/**
	 * @method DoUninstall
	 * @return bool
	 */
	function DoUninstall()
	{
		global $DB;
		$this->errors = false;
		$this->errors = $DB->RunSQLBatch(dirname(__FILE__)."/mysql/uninstall.sql");
		if($this->errors){
			$this->APP->ThrowException(implode("", $this->errors));
			return false;
		}

		ModuleManager::unRegisterModule($this->MODULE_ID);

		$this->delEvents();

		$this->UnInstallFiles();

		return true;
	}

	/**
	 * @method addEvents
	 */
	public function addEvents()
	{
		$this->EventManager->registerEventHandler('main','OnBuildGlobalMenu',$this->MODULE_ID,'\Esd\HL\EventHandlers','buildAdminMenu');

		$this->EventManager->registerEventHandler('main','OnUserTypeBuildList',$this->MODULE_ID,'\Esd\HL\Type\UserRef','GetUserTypeDescription');
		$this->EventManager->registerEventHandler('main','OnUserTypeBuildList',$this->MODULE_ID,'\Esd\HL\Type\Html','GetUserTypeDescription');
		$this->EventManager->registerEventHandler('main','OnUserTypeBuildList',$this->MODULE_ID,'\Esd\HL\Type\IBlockElementRef','GetUserTypeDescription');

		$this->EventManager->registerEventHandler('','ProgramKeysOnBeforeUpdate',$this->MODULE_ID,'\Esd\HL\EventHandlers','onUpdateChangeInfo');
		$this->EventManager->registerEventHandler('','ProgramKeysOnBeforeAdd',$this->MODULE_ID,'\Esd\HL\EventHandlers','onAddChangeInfo');
		$this->EventManager->registerEventHandler('','GameKeysOnBeforeUpdate',$this->MODULE_ID,'\Esd\HL\EventHandlers','onUpdateChangeInfo');
		$this->EventManager->registerEventHandler('','GameKeysOnBeforeAdd',$this->MODULE_ID,'\Esd\HL\EventHandlers','onAddChangeInfo');
	}

	/**
	 * @method delEvents
	 */
	public function delEvents()
	{
		$this->EventManager->unRegisterEventHandler('main','OnBuildGlobalMenu',$this->MODULE_ID,'\Esd\HL\EventHandlers','buildAdminMenu');

		$this->EventManager->unRegisterEventHandler('main','OnUserTypeBuildList',$this->MODULE_ID,'\Esd\HL\Type\UserRef','GetUserTypeDescription');
		$this->EventManager->unRegisterEventHandler('main','OnUserTypeBuildList',$this->MODULE_ID,'\Esd\HL\Type\Html','GetUserTypeDescription');
		$this->EventManager->unRegisterEventHandler('main','OnUserTypeBuildList',$this->MODULE_ID,'\Esd\HL\Type\IBlockElementRef','GetUserTypeDescription');

		$this->EventManager->unRegisterEventHandler('','ProgramKeysOnBeforeUpdate',$this->MODULE_ID,'\Esd\HL\EventHandlers','onUpdateChangeInfo');
		$this->EventManager->unRegisterEventHandler('','ProgramKeysOnBeforeAdd',$this->MODULE_ID,'\Esd\HL\EventHandlers','onAddChangeInfo');
		$this->EventManager->unRegisterEventHandler('','GameKeysOnBeforeUpdate',$this->MODULE_ID,'\Esd\HL\EventHandlers','onUpdateChangeInfo');
		$this->EventManager->unRegisterEventHandler('','GameKeysOnBeforeAdd',$this->MODULE_ID,'\Esd\HL\EventHandlers','onAddChangeInfo');
	}
}