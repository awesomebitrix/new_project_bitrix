<?php
/**
 * Created by OOO 1C-SOFT.
 * User: dremin_s
 * Date: 14.03.2017
 */

namespace DigitalWand\AdminHelper;


use Bitrix\Main\Application;
use Bitrix\Main\IO;
use Esd\Debug;

class ComponentCreator
{

	protected $root;

	public function __construct()
	{
		$this->root = Application::getDocumentRoot();
	}

	/**
	 * @method getNamespaceList
	 * @param array $data
	 *
	 * @return array|null
	 */
	public function getNamespaceListAction($data = [])
	{
		$folder = $data['folder'];

		if(strlen($folder) == 0){
			return [];
		}

		$root = Application::getDocumentRoot();
		$result = [];
		foreach (new \DirectoryIterator($root.'/'.$folder.'/components') as $fileInfo) {
			if($fileInfo->isDot() || $fileInfo->getFilename() === 'bitrix' || $fileInfo->isFile())
				continue;

			$result[] = $fileInfo->getFilename();
		}

		natsort($result);

		return array_values($result);
	}

	public function createAction($data = [])
	{

		$mainFolder = $data['FOLDER'];
		$folder = $data['NAMESPACE'];
		$name = $data['NAME'];
		if(strlen($data['NAMESPACE_NEW']) > 0){
			if(!preg_match('/^[a-zA-Z._]+$/i', $data['NAMESPACE_NEW'])){
				throw new \Exception('Новое пространство имен должно содержать только латинские буквы и знаки "." и "_" ', 403);
			}

			$folder = $data['NAMESPACE_NEW'];
		}

		if(strlen($data['TEMPLATE']) == 0){
			$data['TEMPLATE'] = '.default';
		}

		if(!isset($folder) || empty($folder)){
			throw new \Exception('Нет папки (пространства имен) для компонента', 404);
		}

		if(strlen($name) == 0){
			throw new \Exception('Нет названия компонента', 403);
		}

		$dirComponent = new IO\Directory($this->root.'/'.$mainFolder.'/components/'.$folder.'/'.$name);
		if($dirComponent->isExists()){
			throw new \Exception('Компонент с таким именем '.$name.' уже существует', 500);
		}

		$dirComponent->create();

		$dirCopy = $this->root.'/local/modules/digitalwand.admin_helper/copy';

		if(CopyDirFiles($dirCopy.'/components/simple', $dirComponent->getPath(), true, true)){
			$classFile = new IO\File($dirComponent->getPath().'/class.tpl');

			$arClassName = explode('\\', $data['CLASS_NAME']);
			$classComponent = array_pop($arClassName);

			TrimArr($arClassName);
			$namespaceComponent = '';
			if(count($arClassName) > 0){
				$namespaceComponent = 'namespace '.implode('\\', $arClassName).';';
			}

			$contentClass = $classFile->getContents();
			$contentClass = str_replace(
				['#NAMESPACE_CMP#', '#CLASS_CMP#'],
				[$namespaceComponent, $classComponent],
				$contentClass
			);
			$classFile->putContents($contentClass);

			if(!$classFile->rename($dirComponent->getPath().'/class.php')){
				throw new \Exception('Не удалос переименовать файл class.tpl в class.php', 500);
			}

			unset($classFile);

			if($data['TEMPLATE'] !== '.default'){
				$dirTemplate = new IO\Directory($dirComponent->getPath().'/templates/.default');
				$dirTemplate->rename($dirComponent->getPath().'/templates/'.$data['TEMPLATE']);
				unset($dirTemplate);
			}

			if($data['USE_NPM'] == 'Y'){
				if(strlen($data['APP_NAME']) == 0){
					throw new \Exception('Нет названия react-приложения', 500);
				}
				$reactAppDir = new IO\Directory($dirComponent->getPath().'/app');
				$reactAppDir->create();

				if($data['ADD_REDUX'] == 'Y'){
					$bCopyReact = CopyDirFiles($dirCopy.'/react/redux', $reactAppDir->getPath(), true, true);
				} else {
					$bCopyReact = CopyDirFiles($dirCopy.'/react/simple', $reactAppDir->getPath(), true, true);
				}

				if($bCopyReact){
					$reactAppClass = new IO\File($reactAppDir->getPath().'/app.tpl');
					$contentApp = $reactAppClass->getContents();
					$contentApp = str_replace('#APP_NAME#', $data['APP_NAME'],$contentApp);
					$reactAppClass->putContents($contentApp);
					$reactAppClass->rename($reactAppDir->getPath().'/app.js');
					unset($reactAppClass);
					unset($reactAppDir);
				}
			}
		}
	}
}