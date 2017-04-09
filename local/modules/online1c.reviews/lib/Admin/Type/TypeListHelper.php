<?php
/**
 * Created by OOO 1C-SOFT.
 * User: dremin_s
 * Date: 07.03.2017
 */

namespace Online1c\Reviews\Admin\Type;

use DigitalWand\AdminHelper\Helper;

class TypeListHelper extends Helper\AdminListHelper
{
	protected static $model = '\\Online1c\\Reviews\\TypesTable';
	public static $titlePage = 'Типы отзывов';
}