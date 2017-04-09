<?php
/**
 * Created by OOO 1C-SOFT.
 * User: dremin_s
 * Date: 28.03.2017
 */

namespace DigitalWand\AdminHelper\Helper;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;

Loc::loadMessages(__FILE__);

class AdminSimpleHelper extends AdminBaseHelper
{

	public static $titlePage = '';

	public function show()
	{
		if (!$this->hasReadRights()) {
			$this->addErrors(Loc::getMessage('DIGITALWAND_ADMIN_HELPER_ACCESS_FORBIDDEN'));
			$this->showMessages();

			return;
		}

		$this->setTitle(static::$titlePage);

		$this->showProlog();

		ob_start();
		echo $this->getContent();
		$content = ob_get_contents();
		ob_end_clean();
		$this->showEpilog();

		echo $content;
	}

	protected function getCss()
	{
		return [];
	}

	protected function getJs()
	{
		return [];
	}

	protected function getBXLibs()
	{
		return [];
	}

	/**
	 * Отрисовка верхней части страницы.
	 *
	 * @api
	 */
	protected function showProlog()
	{
		foreach ($this->getCss() as $css) {
			$this->app->SetAdditionalCSS($css);
		}

		if(count($this->getBXLibs()) > 0){
			\CUtil::InitJSCore(static::getBXLibs());
		}
		foreach ($this->getJs() as $jsPath) {
			Asset::getInstance()->addJs($jsPath);
		}

	}

	protected function getContent()
	{
		return false;
	}

	/**
	 * Отрисовка нижней части страницы.
	 *
	 * @api
	 */
	protected function showEpilog()
	{

	}


	/**
	 * @inheritdoc
	 */
	public static function getUrl(array $params = array())
	{
		return static::getViewURL(static::getViewName(), static::$editPageUrl, $params);
	}
}