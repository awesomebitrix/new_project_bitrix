<?php namespace Online1c\Iblock\Tools;

use Bitrix\Main\Data;

class IblockCache
{
	protected $ttl;
	protected $dir;
	protected $cacheId;

	/** @var  Data\Cache */
	private $DataCache;
	/** @var  Data\TaggedCache */
	private $TagCache;
	/** @var bool  */
	private $valid = false;

	/**
	 * DataCache constructor.
	 *
	 * @param $ttl
	 * @param $dir
	 * @param $cacheId
	 */
	public function __construct($ttl = 86400, $dir, $cacheId)
	{
		$this->ttl = $ttl;
		$this->dir = $dir;
		$this->cacheId = $cacheId;

		$this->DataCache = Data\Cache::createInstance();

		$this->TagCache = new Data\TaggedCache();

		$this->valid = $this->DataCache->initCache($ttl, $cacheId, $dir);

		$request = \Bitrix\Main\Context::getCurrent()->getRequest();
		global $USER;
		if($USER->IsAdmin() && $request['clear_cache'] == 'Y'){
			$this->isValid = false;
			$this->TagCache->clearByTag($cacheId);
		}
	}

	/**
	 * @method startTag
	 */
	public function startTag()
	{
		$this->DataCache->startDataCache();
		$this->TagCache->startTagCache($this->getDir());
	}

	public function getData()
	{
		return $this->DataCache->getVars();
	}

	/**
	 * @method endTag
	 * @param $data
	 */
	public function endTag($data)
	{
		$this->TagCache->registerTag($this->getCacheId());
		$this->TagCache->endTagCache();
		$this->DataCache->endDataCache($data);
	}

	/**
	 * @method writeVars
	 * @param bool $data
	 */
	public function addCache($data = false)
	{
		$this->startTag();
		$this->endTag($data);
	}

	/**
	 * @method clearTag
	 * @param $tag
	 */
	public static function clearTag($tag)
	{
		$TagCache = new Data\TaggedCache();
		$TagCache->clearByTag($tag);
	}

	/**
	 * @method getIsValid - get param isValid
	 * @return boolean
	 */
	public function isValid()
	{
		return $this->valid;
	}

	/**
	 * @method getTtl - get param ttl
	 * @return int
	 */
	public function getTtl()
	{
		return $this->ttl;
	}

	/**
	 * @method setTtl - set param Ttl
	 * @param int $ttl
	 */
	public function setTtl($ttl)
	{
		$this->ttl = $ttl;
	}

	/**
	 * @method getDir - get param dir
	 * @return mixed
	 */
	public function getDir()
	{
		return $this->dir;
	}

	/**
	 * @method setDir - set param Dir
	 * @param mixed $dir
	 */
	public function setDir($dir)
	{
		$this->dir = $dir;
	}

	/**
	 * @method getCacheId - get param cacheId
	 * @return mixed
	 */
	public function getCacheId()
	{
		return $this->cacheId;
	}

	/**
	 * @method setCacheId - set param CacheId
	 * @param mixed $cacheId
	 */
	public function setCacheId($cacheId)
	{
		$this->cacheId = $cacheId;
	}

	/**
	 * @method setNoValid
	 */
	public function setNoValid()
	{
		$this->valid = false;
	}

	/**
	 * @method getDataCache - get param DataCache
	 * @return Data\Cache
	 */
	public function getDataCache()
	{
		return $this->DataCache;
	}


}