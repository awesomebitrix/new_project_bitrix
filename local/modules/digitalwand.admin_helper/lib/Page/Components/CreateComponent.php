<?php
/**
 * Created by OOO 1C-SOFT.
 * User: dremin_s
 * Date: 14.03.2017
 */

namespace DigitalWand\AdminHelper\Page\Components;

use Bitrix\Main\Localization\Loc;
use DigitalWand\AdminHelper\Helper\AdminInterface;
use DigitalWand\AdminHelper\Widget;

class CreateComponent extends AdminInterface
{
	static public $module = 'digitalwand.admin_helper';

	public function fields()
	{
		return [
			'TAB_1' => [
				'NAME' => 'Создание компонента',
				'FIELDS' => [
					'CREATE_COMPONENT' => [
						'WIDGET' => new Widget\AreaWidget([
							'HTML' => '<div id="admin_component_create"></div>',
							'BX_LIBS' => ['bootstrap','jquery', 'popup', 'admin_helper']
						])
					],

				],
			],
		];
	}

	public function helpers()
	{
		return [
			'\DigitalWand\AdminHelper\Page\Components\CreateComponentHelper'
		];
	}

}