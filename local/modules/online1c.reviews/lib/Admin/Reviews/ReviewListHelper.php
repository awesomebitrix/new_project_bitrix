<?php
/**
 * Created by OOO 1C-SOFT.
 * User: dremin_s
 * Date: 09.03.2017
 */
namespace Online1c\Reviews\Admin\Reviews;

use DigitalWand\AdminHelper\Helper;

class ReviewListHelper extends Helper\AdminListHelper
{
	protected static $model = '\Online1c\Reviews\ReviewsTable';

	public static $titlePage = 'Список отзывов';
}