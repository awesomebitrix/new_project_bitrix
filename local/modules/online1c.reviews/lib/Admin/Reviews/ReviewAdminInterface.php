<?php
/**
 * Created by OOO 1C-SOFT.
 * User: dremin_s
 * Date: 09.03.2017
 */
namespace Online1c\Reviews\Admin\Reviews;

use Bitrix\Main\Localization\Loc;
use DigitalWand\AdminHelper\Helper\AdminInterface;
use DigitalWand\AdminHelper\Widget;
use Online1c\Reviews\ReviewsTable;
use Online1c\Reviews\TypesTable;

class ReviewAdminInterface extends AdminInterface
{
	public function fields()
	{
		return [
			'TAB_1' => [
				'NAME' => 'Отзыв',
				'FIELDS' => [
					'ID' => [
						'WIDGET' => new Widget\NumberWidget([
							'HIDE_WHEN_CREATE' => true,
							'READONLY' => true,
						])
					],
					'USER_ID' => [
						'WIDGET' => new Widget\UserWidget(['FORM_ID' => ReviewEditHelper::generateFormId().'_form']),
						'REQUIRED' => true
					],
					'ELEMENT_CODE' => [
						'WIDGET'=>new Widget\StringWidget(),
						'REQUIRED' => true
					],
					'ACTIVE' => [
						'WIDGET' => new Widget\ComboBoxWidget([
							'VARIANTS' => [
								ReviewsTable::REVIEW_NO_ACTIVE => 'На модерации',
								ReviewsTable::REVIEW_ACTIVE => 'Опубликован'
							],
							'EDIT_IN_LIST' => true
						])
					],
					'TYPE_ID' => [
						'WIDGET' => new Widget\ComboBoxWidget([
							'VARIANTS' => $this->getTypes()
						])
					],
					'DATE_CREATE' => [
						'WIDGET'=> new Widget\DateTimeWidget(),
						'READONLY' => true,
						'HIDE_WHEN_CREATE' => true,
					],
					'FIO' => [
						'WIDGET'=>new Widget\StringWidget(['SIZE' => 50]),
					],
					'EMAIL' => [
						'WIDGET'=>new Widget\StringWidget(['SIZE' => 50]),
					],
					'PHONE' => [
						'WIDGET'=>new Widget\StringWidget(['SIZE' => 50]),
					],
					'IP' => [
						'WIDGET'=>new Widget\StringWidget(),
						'READONLY' => true,
						'HIDE_WHEN_CREATE' => true,
					],
					'PAGE' => [
						'WIDGET'=>new Widget\StringWidget(['SIZE' => 50]),
					],
					'LIKE' => ['WIDGET' => new Widget\NumberWidget()],
					'DISLIKE' => ['WIDGET' => new Widget\NumberWidget()],
					'RATING_VAL' => [
						'WIDGET' => new Widget\StringWidget(),
						'READONLY' => true,
						'HIDE_WHEN_CREATE' => true,
					]
				]
			],
			'TAB_2' => [
				'NAME' => 'Комментарий',
				'FIELDS' => [
					'TEXT' => [
						'WIDGET' => new Widget\VisualEditorWidget([])
					],
				]
			],
			'TAB_3' => [
				'NAME' => 'Плюсы',
				'FIELDS' => [
					'ADVANTAGE' => [
						'WIDGET' => new Widget\VisualEditorWidget([])
					],
				]
			],
			'TAB_4' => [
				'NAME' => 'Минусы',
				'FIELDS' => [
					'DISADVANTAGE' => [
						'WIDGET' => new Widget\VisualEditorWidget([])
					],
				]
			],
		];
	}

	/**
	 * @method getTypes
	 * @return array
	 */
	protected function getTypes()
	{
		$types = [];
		$oTypes = TypesTable::getList([]);
		while ($rs = $oTypes->fetch()){
			$types[$rs['ID']] = $rs['TITLE'];
		}

		return $types;
	}

	public function helpers()
	{
		return [
			'\Online1c\Reviews\Admin\Reviews\ReviewListHelper',
			'\Online1c\Reviews\Admin\Reviews\ReviewEditHelper',
		];
	}

}