<?php
\Bitrix\Main\Loader::includeModule('highloadblock');

$arIni = parse_ini_file('module.ini');
define('PW_HL_MODULE', $arIni['id']);
define('PW_HL_CLASS', $arIni['css.id']);
define('PW_HL_M_VERSION', $arIni['version']);

/**
 * Include class file.
 * Standard: PSR-0.md
 *
 * @param string $className
 */
spl_autoload_register(
	function ($className) {
		preg_match('/^(.*?)([\w]+)$/i', $className, $matches);
		if (count($matches) < 3) {
			return;
		}

		$filePath = implode(DIRECTORY_SEPARATOR, array(
			__DIR__,
			"lib",
			str_replace('\\', DIRECTORY_SEPARATOR, trim($matches[1], '\\')),
			str_replace('_', DIRECTORY_SEPARATOR, $matches[2]) . '.php'
		));
		$filePath = str_replace('Esd/HL/','',$filePath);
		$filePath = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $filePath);
		if (is_readable($filePath) && is_file($filePath)) {
			/** @noinspection PhpIncludeInspection */
			require_once $filePath;
		} /*else {
			$entityName = str_replace('Table','',$className);
			if(strlen($entityName) > 3){
				try{
					$arBlock = Bitrix\Highloadblock\HighloadBlockTable::getRow(array(
						'filter'=>array('NAME'=>$entityName)
					));
					\Esd\HL\MainTable::compileEntity($arBlock);
				} catch (\Exception $e){}
			}
		}*/
	});

//require_once(dirname(__FILE__).'/tools/UserTypes.php');