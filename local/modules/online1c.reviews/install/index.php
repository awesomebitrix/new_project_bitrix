<?
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Application;

Loc::loadMessages(__FILE__);

Class online1c_reviews extends CModule
{
	var $MODULE_ID = 'online1c.reviews';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;

	function __construct()
	{
		$arModuleVersion = array();
		include(__DIR__."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = Loc::getMessage("ONLINE1C_REVIEWS_MODULE_NAME");
		$this->MODULE_DESCRIPTION = Loc::getMessage("ONLINE1C_REVIEWS_MODULE_DESC");

		$this->PARTNER_NAME = getMessage("ONLINE1C_REVIEWS_PARTNER_NAME");
		$this->PARTNER_URI = getMessage("ONLINE1C_REVIEWS_PARTNER_URI");

		$this->exclusionAdminFiles = array(
			'..',
			'.',
			'menu.php',
			'operation_description.php',
			'task_description.php',
		);
	}

	function InstallDB($arParams = array())
	{
		\Online1c\Reviews\TypesTable::createTable();
		\Online1c\Reviews\ReviewsTable::createTable();
		\Online1c\Reviews\RatingElementTable::createTable();
	}

	function UnInstallDB($arParams = array())
	{
		\Bitrix\Main\Config\Option::delete($this->MODULE_ID);
		\Online1c\Reviews\TypesTable::dropTable();
		\Online1c\Reviews\ReviewsTable::dropTable();
		\Online1c\Reviews\RatingElementTable::dropTable();
	}

	function InstallEvents()
	{
		\Bitrix\Main\EventManager::getInstance()->registerEventHandler("iblock", "OnAfterIBlockElementUpdate", $this->MODULE_ID, '\Online1c\Reviews\EventHandlers\OnAfterIBlockElementUpdateHandler', "handler");

	}

	function UnInstallEvents()
	{
		\Bitrix\Main\EventManager::getInstance()->unRegisterEventHandler("iblock", "OnAfterIBlockElementUpdate", $this->MODULE_ID, '\Online1c\Reviews\EventHandlers\OnAfterIBlockElementUpdateHandler', "handler");

	}

	function InstallFiles($arParams = array())
	{
		$path = $this->GetPath()."/copy/components";

		$dir = new Bitrix\Main\IO\Directory($path);
		$dir->create();
		CopyDirFiles($path, $_SERVER["DOCUMENT_ROOT"]."/local/components", true, true);

//		if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath().'/install/files')){
//			$this->copyArbitraryFiles();
//		}

		return true;
	}

	function UnInstallFiles()
	{
		\Bitrix\Main\IO\Directory::deleteDirectory($_SERVER["DOCUMENT_ROOT"].'/local/components/online1c/');

//		if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath().'/install/files')){
//			$this->deleteArbitraryFiles();
//		}

		return true;
	}

	function copyArbitraryFiles()
	{
		$rootPath = $_SERVER["DOCUMENT_ROOT"];
		$localPath = $this->GetPath().'/install/files';

		$dirIterator = new RecursiveDirectoryIterator($localPath, RecursiveDirectoryIterator::SKIP_DOTS);
		$iterator = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::SELF_FIRST);

		foreach ($iterator as $object) {
			$destPath = $rootPath.DIRECTORY_SEPARATOR.$iterator->getSubPathName();
			($object->isDir()) ? mkdir($destPath) : copy($object, $destPath);
		}
	}

	function deleteArbitraryFiles()
	{
		$rootPath = $_SERVER["DOCUMENT_ROOT"];
		$localPath = $this->GetPath().'/install/files';

		$dirIterator = new RecursiveDirectoryIterator($localPath, RecursiveDirectoryIterator::SKIP_DOTS);
		$iterator = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::SELF_FIRST);

		foreach ($iterator as $object) {
			if (!$object->isDir()){
				$file = str_replace($localPath, $rootPath, $object->getPathName());
				\Bitrix\Main\IO\File::deleteFile($file);
			}
		}
	}

	function createNecessaryIblocks()
	{
		return true;
	}

	function deleteNecessaryIblocks()
	{
		return true;
	}

	function createNecessaryMailEvents()
	{
		return true;
	}

	function deleteNecessaryMailEvents()
	{
		return true;
	}

	function isVersionD7()
	{
		return CheckVersion(\Bitrix\Main\ModuleManager::getVersion('main'), '14.00.00');
	}

	function GetPath($notDocumentRoot = false)
	{
		if ($notDocumentRoot){
			return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
		} else {
			return dirname(__DIR__);
		}
	}

	function getSitesIdsArray()
	{
		$ids = Array();
		$rsSites = CSite::GetList($by = "sort", $order = "desc");
		while ($arSite = $rsSites->Fetch()) {
			$ids[] = $arSite["LID"];
		}

		return $ids;
	}

	function DoInstall()
	{

		global $APPLICATION;
		if ($this->isVersionD7()){
			\Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);
			\Bitrix\Main\Loader::includeModule($this->MODULE_ID);

			$this->InstallDB();
			$this->createNecessaryMailEvents();
			$this->InstallEvents();
			$this->InstallFiles();
			$this->addRulesUrl();

			\Online1c\Reviews\TypesTable::add([
				'TITLE' => 'Каталог',
				'CODE' => 'catalog',
				'TYPE_ID' => 'iblock',
				'IBLOCK_ID' => 14
			]);

		} else {
			$APPLICATION->ThrowException(Loc::getMessage("ONLINE1C_REVIEWS_INSTALL_ERROR_VERSION"));
		}

		$APPLICATION->IncludeAdminFile(Loc::getMessage("ONLINE1C_REVIEWS_INSTALL"), $this->GetPath()."/install/step.php");
	}

	function DoUninstall()
	{

		global $APPLICATION;

		$context = Application::getInstance()->getContext();
		$request = $context->getRequest();
		\Bitrix\Main\Loader::includeModule($this->MODULE_ID);

		$this->UnInstallFiles();
		$this->deleteNecessaryMailEvents();
		$this->UnInstallEvents();

		if ($request["savedata"] != "Y")
			$this->UnInstallDB();

		\Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);
		$this->deleteRulesUrl();

		$APPLICATION->IncludeAdminFile(Loc::getMessage("ONLINE1C_REVIEWS_UNINSTALL"), $this->GetPath()."/install/unstep.php");
	}

	protected function addRulesUrl()
	{
		foreach ($this->getSiteList() as $siteId) {
			$rulesUrl = \Bitrix\Main\UrlRewriter::getList(
				$siteId,
				['CONDITION' => '/reviews/ajax','ID' => 'online1c:reviews.list']
			);
			if(count($rulesUrl) == 0){
				\Bitrix\Main\UrlRewriter::add($siteId, [
					'CONDITION' => '/reviews/ajax',
					'RULE' => '',
					'ID' => 'online1c:reviews.list',
					'PATH' => '\\Online1c\\Reviews\\ReviewsList',
					'SORT' => 100520,
				]);
			}
		}
	}

	protected function deleteRulesUrl()
	{
		foreach ($this->getSiteList() as $siteId) {
			\Bitrix\Main\UrlRewriter::delete($siteId, ['CONDITION' => '/reviews/ajax','ID' => 'online1c:reviews.list']);
		}
	}

	protected function getSiteList()
	{
		$arSites = [];
		$oSiteList = \Bitrix\Main\SiteTable::getList([
			'select' => ['LID'],
		]);
		while ($site = $oSiteList->fetch()) {
			$arSites[] = $site['LID'];
		}

		return $arSites;
	}
}