<?
use Bitrix\Main\Localization\Loc,
	Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

if (class_exists("online1c_iblock"))
	return;

class online1c_iblock extends CModule
{
	var $MODULE_ID = "online1c.iblock";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $PARTNER_NAME;
	var $PARTNER_URI;

	protected $EventManager;

	function __construct()
	{
		$arModuleVersion = array();

		include(dirname(__FILE__)."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

		$this->MODULE_NAME = GetMessage("ONLINE_1C_IBLOCK_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("ONLINE_1C_IBLOCK_INSTALL_DESC");

		$this->EventManager = \Bitrix\Main\EventManager::getInstance();
	}


	/**
	 * @method DoInstall
	 * @return bool
	 */
	function DoInstall()
	{
		ModuleManager::registerModule($this->MODULE_ID);
		$this->addEvents();

		return true;
	}

	/**
	 * @method DoUninstall
	 * @return bool
	 */
	function DoUninstall()
	{
		ModuleManager::unRegisterModule($this->MODULE_ID);
		$this->delEvents();

		return true;
	}

	/**
	 * @method addEvents
	 */
	public function addEvents()
	{
		$this->EventManager->registerEventHandler('iblock', 'OnAfterIBlockPropertyAdd', $this->MODULE_ID, '\Online1c\Iblock\Events\PropHandler', 'onClearMetaPropCache');
		$this->EventManager->registerEventHandler('iblock', 'OnIBlockPropertyDelete', $this->MODULE_ID, '\Online1c\Iblock\Events\PropHandler', 'onClearMetaPropCache');
		$this->EventManager->registerEventHandler('iblock', 'OnIBlockDelete', $this->MODULE_ID, '\Online1c\Iblock\Events\PropHandler', 'onClearMetaPropCache');
	}

	/**
	 * @method delEvents
	 */
	public function delEvents()
	{
		$this->EventManager->unRegisterEventHandler('iblock', 'OnAfterIBlockPropertyAdd', $this->MODULE_ID, '\Online1c\Iblock\Events\PropHandler', 'onClearMetaPropCache');
		$this->EventManager->unRegisterEventHandler('iblock', 'OnIBlockPropertyDelete', $this->MODULE_ID, '\Online1c\Iblock\Events\PropHandler', 'onClearMetaPropCache');
		$this->EventManager->unRegisterEventHandler('iblock', 'OnIBlockDelete', $this->MODULE_ID, '\Online1c\Iblock\Events\PropHandler', 'onClearMetaPropCache');
	}
}