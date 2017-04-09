<?php
/**
 * Created by PhpStorm.
 * User: dremin_s
 * Date: 04.08.2016
 * Time: 14:41
 */

namespace DigitalWand\AdminHelper\Widget;

use Bitrix\Main\Application;
use Bitrix\Main\Page\Asset;

class AreaWidget extends HelperWidget
{
	protected $root;

	public function __construct(array $settings = [])
	{
		parent::__construct($settings);
		$this->root = Application::getDocumentRoot();
	}


	protected function getEditHtml()
	{
		$file = $this->getSettings('FILE');
		global $APPLICATION;
		$Asset = Asset::getInstance();
		if (count($this->getSettings('css')) > 0){
			foreach ($this->getSettings('css') as $css) {
				$APPLICATION->SetAdditionalCSS($css);
			}
		}

		if (count($this->getSettings('js')) > 0){
			foreach ($this->getSettings('js') as $js) {
				$Asset->addJs($js);
			}
		}
		if (count($this->getSettings('BX_LIBS')) > 0){
			\CUtil::InitJSCore($this->getSettings('BX_LIBS'));
		}
		if (strlen($file) > 0){
			ob_start();
			if (file_exists($this->root.$file)){
				$APPLICATION->IncludeComponent(
					"bitrix:main.include",
					".default",
					Array(
						"AREA_FILE_SHOW" => "file",     // Показывать включаемую область
						'PATH' => $file,
						'PARAMS' => $this->getSettings('PARAMS'),
					)
				);
			}
			$out = ob_get_contents();
			ob_end_clean();
			return $out;
		} elseif (strlen($this->getSettings('HTML')) > 0) {
			ob_start();
			echo $this->getSettings('HTML');
			$out = ob_get_contents();
			ob_end_clean();

			return $out;
		}

		return false;
	}

	public function generateRow(&$row, $data)
	{
		return false;
	}

	public function showFilterHtml()
	{
		return false;
	}

	public function showBasicEditField()
	{
		print '<tr>';
		print '<td colspan="2" width="100%">';
		print '<b style="text-align: center; display: block">'.$this->getSettings('TITLE').'</b>';
		print $this->getEditHtml();
		print '</td>';
		print '</tr>';
	}
}