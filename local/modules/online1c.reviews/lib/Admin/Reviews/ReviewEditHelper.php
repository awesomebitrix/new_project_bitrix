<?php
/**
 * Created by OOO 1C-SOFT.
 * User: dremin_s
 * Date: 09.03.2017
 */
namespace Online1c\Reviews\Admin\Reviews;

use DigitalWand\AdminHelper\Helper;

class ReviewEditHelper extends Helper\AdminEditHelper
{
	protected static $model = '\Online1c\Reviews\ReviewsTable';

	public static $titlePage = 'Отзыв';

}