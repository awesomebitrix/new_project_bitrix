<?php
/**
 * Created by OOO 1C-SOFT.
 * User: dremin_s
 * Date: 01.03.2017
 */

namespace Online1c\Reviews\Admin\Type;

use Bitrix\Main\Localization\Loc;
use DigitalWand\AdminHelper\Helper\AdminInterface;
use DigitalWand\AdminHelper\Widget;

class TypeAdminInterface extends AdminInterface
{
	public function fields()
	{
		return [
			'TAB_1' => [
				'NAME' => 'Тип отзывов',
				'FIELDS' => [
					'ID' => [
						'WIDGET' => new Widget\NumberWidget([
							'HIDE_WHEN_CREATE' => true,
							'READONLY' => true,
						])
					],
					'TITLE' => ['WIDGET' => new Widget\StringWidget(['REQUIRED' => true])],
					'CODE' => ['WIDGET' => new Widget\StringWidget(['REQUIRED' => true])],
					'XML_ID' => ['WIDGET' => new Widget\StringWidget()],
					'TYPE_ID' => [
						'WIDGET' => new Widget\ComboBoxWidget([
							'VARIANTS' => [
								'page' => 'Страница',
								'iblock' => 'Инфоблок'
							],
							'REQUIRED' => true
						])
					],
					'IBLOCK_ID' => [
						'WIDGET' => new Widget\IblockWidget()
					]
				]
			]
		];
	}

	public function helpers()
	{
		return [
			'\Online1c\Reviews\Admin\Type\TypeListEditHelper',
			'\Online1c\Reviews\Admin\Type\TypeListHelper',
		];
	}

}