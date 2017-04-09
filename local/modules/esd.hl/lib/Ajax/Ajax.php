<?php namespace Esd\HL\Ajax;

use Bitrix\Main\Text\Encoding;
use Esd\Debug;
use Bitrix\Main\Context;

/**
 * Class Ajax
 * @package Esd\HL\Ajax
 */
class Ajax
{
	protected $request;
	protected $server;
	protected $dataPost = array();
	protected $class;
	protected $action;
	protected $result;
	protected $errors;
	protected $info;
	protected $paramsClass;

	/** @var Ajax */
	private static $instance = null;
	protected $showErrorAll = true;

	/**
	 * Ajax constructor.
	 * @param string $class
	 * @param $paramsClass
	 * @param string $action
	 */
	public function __construct($class = '', $action = '', $paramsClass = false)
	{
		$this->request = Context::getCurrent()->getRequest();
		$this->server = Context::getCurrent()->getServer();

		if(strlen($class) > 0)
			$this->class = $class;

		if(strlen($action) > 0)
			$this->action = $action;

		if($paramsClass){
			$this->paramsClass = $paramsClass;
		}

		$this->setData();
	}

	/**
	 * @method instance
	 * @param string $class
	 * @param string $action
	 * @param $paramsClass
	 * @return Ajax
	 */
	public static function instance($class = '', $action = '', $paramsClass = false)
	{
		if(is_null(self::$instance)){
			self::$instance = new Ajax($class, $action);
		}
		return self::$instance;
	}

	/**
	 * @method setData
	 * @return $this
	 */
	public function setData()
	{
		$post = $this->getPost();
		$this->dataPost = $this->sanitizeData($post['DATA']);

		if(strlen($post['CLASS']) > 0)
			$this->class = $post['CLASS'];

		if(strlen($post['ACTION']) > 0)
			$this->action = $post['ACTION'];

		return $this;
	}

	/**
	 * @method init
	 * @return $this
	 * @throws AjaxException
	 */
	public function init()
	{
		$result = null;
		$action = $this->action;

		try{
			if(strlen($this->class)){
				if(class_exists($this->class)){
					if(is_callable(array($this->class, $action))){
						$initClass = new \ReflectionClass($this->class);
						$ob = $initClass->newInstance($this->paramsClass);
						$result = $ob->$action($this->dataPost);
					} else {
						throw new AjaxException(sprintf('Метод %s не может быть вызван',$action));
					}
				} else {
					throw new AjaxException(sprintf('Класса %s нет',$this->class));
				}
			} else {
				if(is_callable($this->action)){
					$result = call_user_func($this->action, $this->dataPost);
				} else {
					throw new AjaxException(sprintf('Метод %s не может быть вызван',$action));
				}
			}
		} catch (\Exception $error){
			$arError = array('msg'=>$error->getMessage());
			if($this->showErrorAll){
				$arError['code'] = $error->getCode();
				$arError['file'] = $error->getFile();
				$arError['line'] = $error->getLine();
				$arError['trace'] = $error->getTrace();
				$arError['type'] = 'danger';
			}
			$this->errors[] = $arError;
		}

		$this->setResult($result);
		return $this;
	}

	/**
	 * @method setResult
	 * @param $result
	 */
	public function setResult($result)
	{
		if(count($this->errors) > 0){
			$this->result['INFO'] = $this->errors;
		}
		$this->result['DATA'] = $result;
	}

	/**
	 * @method getResult
	 * @param bool|true $json
	 * @return string|array|mixed
	 */
	public function getResult($json = true)
	{
		if($json){
			return json_encode($this->result);
		}
		return $this->result;
	}

	/**
	 * @method sanitizeData
	 * @param $data
	 * @return mixed
	 */
	public function sanitizeData($data)
	{
		foreach ($data as $code => $value) {
			if(is_array($value)){
				$data[$code] = $this->sanitizeData($value);
			} else {
				$data[$code] = htmlspecialcharsbx($value);
			}
		}
		return $data;
	}


	/**
	 * @method getPost
	 * @return array|null
	 */
	public function getPost()
	{
		$post = null;
		$contentType = $this->server['HTTP_ACCEPT'];
		if(preg_match('{json}i', $contentType) !== false){
			$post = json_decode(file_get_contents('php://input'), 1);
		} elseif($this->request->isPost()) {
			$post = $this->request->getPostList()->toArray();
		} else {
			$post = $this->request->toArray();
		}

		return $post;
	}

	/**
	 * @method getRequest - get param request
	 * @return \Bitrix\Main\HttpRequest
	 */
	public function getRequest()
	{
		return $this->request;
	}

	/**
	 * @method getShowErrorAll - get param showErrorAll
	 * @return boolean
	 */
	public function getShowErrorAll()
	{
		return $this->showErrorAll;
	}

	/**
	 * @method setShowErrorAll
	 * @param $showErrorAll
	 * @return $this
	 */
	public function setShowErrorAll($showErrorAll)
	{
		$this->showErrorAll = $showErrorAll;
		return $this;
	}

	public function getDataPost()
	{
		return $this->dataPost;
	}
}
