<?php namespace Online1c\Reviews;
/** @var \CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @var \CBitrixComponent $component */
/** @global \CUser $USER */
/** @global \CMain $APPLICATION */

use AB\Tools\Debug;
use AB\Tools\Helpers\DataCache;
use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Web;

Loc::loadLanguageFile(__FILE__);

Loader::includeModule('online1c.reviews');
Loader::includeModule('ab.tools');

class ReviewsList extends \CBitrixComponent
{
	/** @var array|bool|\CDBResult|\CUser|mixed */
	protected $USER;
	private $cmpName = '';

	const LANG_PREFIX = 'O1C_REVIEW_CMP.';

	/**
	 * @param \CBitrixComponent|bool $component
	 */
	function __construct($component = false)
	{
		parent::__construct($component);
		global $USER;
		$this->USER = $USER;

		$name = md5($this->getCmpName());
		if (strlen($_SESSION[$name]) > 0){
			$arParams = MainHelper::decodeComponentParams($_SESSION[$name]);
			$this->arParams = $arParams;
		}
	}

	private function getCmpName()
	{
		$dir = dirname(__FILE__);
		$arDir = explode(DIRECTORY_SEPARATOR, $dir);
		$name = array_pop($arDir);
		$namespace = array_pop($arDir);
		unset($arDir);

		return $namespace.':'.$name;
	}

	/**
	 * @method getLangMsg
	 * @param $code
	 *
	 * @return string
	 */
	public function getLangMsg($code)
	{
		return Loc::getMessage(self::LANG_PREFIX.$code);
	}

	/**
	 * @method onPrepareComponentParams
	 * @param array $arParams
	 *
	 * @return array
	 */
	public function onPrepareComponentParams($arParams)
	{
		if (strlen($arParams['NAME_BLOCK']) == 0){
			$arParams['NAME_BLOCK'] = $this->getLangMsg('NAME_BLOCK');
		}

		if ($arParams['ELEMENT_CODE'])
			$this->arParams = $arParams;

		return $arParams;
	}

	/**
	 * @method getUser
	 * @return \CUser
	 */
	public function getUser()
	{
		global $USER;
		if (!is_object($USER))
			$USER = new \CUser();

		return $USER;
	}

	/**
	 * @method getListAction
	 * @param array $data
	 *
	 * @return array
	 */
	public function getListAction($data = [])
	{
		if ((int)$data['page'] == 0)
			$data['page'] = 1;

		$this->request->set(['page' => $data['page']]);
		$result = [];

		if (strlen($this->arParams['ELEMENT_CODE']) > 0){

			$dataCache = new DataCache(
				$this->arParams['CACHE_TIME'],
				ReviewsTable::CACHE_DIR,
				ReviewsTable::CACHE_ID.$this->arParams['ELEMENT_CODE']
			);

			if ($dataCache->isValid() && $this->arParams['clear_cache'] !== 'Y'){
				$result = $dataCache->getData();
			} else {
				$result['NAME_BLOCK'] = $this->arParams['NAME_BLOCK'];
				$oReviews = ReviewsTable::getList([
					'select' => [
						'*',
						'USER_NAME' => 'USER.NAME',
						'USER_LOGIN' => 'USER.LOGIN',
						'USER_EMAIL' => 'USER.EMAIL',
						'USER_PHOTO_ID' => 'USER.PERSONAL_PHOTO'
					],
					'filter' => ['=ELEMENT_CODE' => $this->arParams['ELEMENT_CODE'], '=ACTIVE' => 'Y'],
					'order' => ['ID' => 'ASC'],
					'limit' => 30,
					'count_total' => true,
				]);
				$result['CNT'] = $oReviews->getCount();
				$result['CNT_FORMAT'] = MainHelper::getCountLang($result['CNT']);

				$avatarSize = [
					'width' => strlen($this->arParams['AVATAR_WIDTH']) > 0 ? $this->arParams['AVATAR_WIDTH'] : 200,
					'height' => strlen($this->arParams['AVATAR_HEIGHT']) > 0 ? $this->arParams['AVATAR_HEIGHT'] : 200,
				];

				while ($rs = $oReviews->fetch()) {
					$time = $rs['DATE_CREATE'];
					if ($time instanceof DateTime){
						$rs['DATE_CREATE'] = $time->format('d').' '.\FormatDate('F', $time->getTimestamp()).' '.$time->format('Y');
					}

					if(intval($rs['USER_PHOTO_ID']) > 0){
						$rs['AVATAR'] = \CFile::ResizeImageGet($rs['USER_PHOTO_ID'], $avatarSize, BX_RESIZE_IMAGE_EXACT, true);
					}

					$result['ITEMS'][] = $rs;
				}
			}

			$result['CURRENT_USER'] = [
				'LOGIN' => $this->getUser()->GetLogin(),
				'EMAIL' => $this->getUser()->GetEmail(),
				'NAME' => $this->getUser()->GetFirstName(),
				'LAST_NAME' => $this->getUser()->GetLastName(),
			];

			$dataCache->addCache($result);
		}

		return $result;
	}

	/**
	 * @method getParamsAction
	 * @return null|array
	 */
	public function getParamsAction()
	{
		$result = null;
		foreach ($this->arParams as $code => $val) {
			if (substr($code, 0, 1) === '~')
				continue;

			$result[$code] = $val;
		}

		return $result;
	}

	/**
	 * @method saveCommentAction
	 * @param array $data
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function saveCommentAction($data = [])
	{
		$save = [
			'ELEMENT_CODE' => $this->arParams['ELEMENT_CODE'],
			'FIO' => $data['FIO'],
			'TEXT' => $data['TEXT'],
			'TEXT_TEXT_TYPE' => 'html',
			'ADVANTAGE' => $data['ADVANTAGE'],
			'ADVANTAGE_TEXT_TYPE' => 'html',
			'DISADVANTAGE' => $data['DISADVANTAGE'],
			'DISADVANTAGE_TEXT_TYPE' => 'html',
			'ACTIVE' => $this->arParams['PREMODERATE'] == 'Y' ? 'N' : 'Y',
			'TYPE_ID' => $this->arParams['TYPE_ID'],
			'RATING_VAL' => $data['RATING_VAL'],
		];

		$result = ReviewsTable::add($save);

		if (!$result->isSuccess()){
			throw new \Exception(implode(', ', $result->getErrorMessages()), 500);
		}

		return ['ID' => $result->getId(), 'ACTIVE' => $save['ACTIVE']];
	}

	/**
	 * @method likeAction
	 * @param array $data
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function likeAction($data = [])
	{


		$id = (int)$data['id'];
		if($id == 0){
			throw new \Exception('Нет данных для изменения лайков');
		}

		$item = ReviewsTable::getRow([
			'select' => ['ID', 'LIKE', 'DISLIKE','ELEMENT_CODE'],
			'filter' => ['=ID' => $id]
		]);

		if(is_null($item)){
			throw new \Exception('Отзыв не найден');
		}

		$save = [];
		if($data['status'] === '+'){
			$save['LIKE'] = $item['LIKE'] + 1;
			if($_SESSION['add_like'][$id] === true){
				throw new \Exception('Вы уже отметились', 403);
			}
		} else {
			$save['DISLIKE'] = $item['DISLIKE'] + 1;
			if($_SESSION['add_dislike'][$id] === true){
				throw new \Exception('Вы уже отметились', 403);
			}
		}
		$save['ELEMENT_CODE'] = $item['ELEMENT_CODE'];
		$res = ReviewsTable::update($item['ID'], $save);
		if($res->isSuccess()){
			if($data['status'] === '+'){
				$_SESSION['add_like'][$id] = true;
			} else {
				$_SESSION['add_dislike'][$id] = true;
			}

		} else {
			throw new \Exception(implode(', ', $res->getErrorMessages()));
		}

		return array_merge($item, $save);
	}

	/**
	 * @method executeComponent
	 * @return mixed|void
	 */
	public function executeComponent()
	{
		if ($this->request->get('clear_cache') === 'Y' && $this->getUser()->IsAdmin()){
			$this->arParams['clear_cache'] = 'Y';
		}
		$_SESSION[md5($this->getName())] = MainHelper::encodeComponentParams($this->arParams);

//		$this->arResult = $this->getListAction();

		$this->includeComponentTemplate();
	}
}