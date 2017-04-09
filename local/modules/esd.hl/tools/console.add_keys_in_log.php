#!/usr/bin/php
<?php
define("LANG", "ru");
define("NO_AGENT_STATISTIC", true);
define("NO_KEEP_STATISTIC", true);
//define('SITE_ID','es');

$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__)."/../../../..");
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

require_once($DOCUMENT_ROOT.'/bitrix/modules/main/include.php');

use \Bitrix\Main\Loader;
use Esd\HL\MainTable;
use Esd\HL\LogTable;

Loader::includeModule("esd.hl");

//echo $DOCUMENT_ROOT."\r\n\r\n";
$param = explode(':',$argv[1]);
$params = [];

foreach ($argv as $k => $value) {
	if($k > 0){
		$arVal = explode(':',$value);
		$params[$arVal[0]] = $arVal[1];
	}
}
$entity = intval($params['entity']);
if($entity == 0){
	echo "\r\n Нет ИД HL-блока\r\n\r\n";
	exit;
}

$result = [];
$out = '';
$MainTable = new MainTable($entity);
$query = new Bitrix\Main\Entity\Query($MainTable->getHLEntity());
$obItems = $query->setSelect(array('ID','UF_DATE','UF_USER'))
	->setFilter(array())
	->setLimit(0)
	->exec();

$bar = new Esd\ProgressBar();
$bar->reset('* %fraction% [%bar%] %percent%', '=>', '-', 100, $obItems->getSelectedRowsCount());

$i = 0;
while($item = $obItems->fetch()) {
	$log = [
		'ELEMENT_ID'=>$item['ID'],
		'ENTITY_NAME'=>$MainTable->getHLEntity()->getName()
	];
	$rowLog = LogTable::getRow(array(
		'select'=>array('ID'),
		'filter'=>$log
	));
	if(is_null($rowLog)){
		if($item['UF_DATE'] instanceof Bitrix\Main\Type\DateTime){
			$log['DATE_X'] = $item['UF_DATE'];
		}
		$log['USER_X'] = $item['UF_USER'];
		$resAddLog = LogTable::add($log);
		if(!$resAddLog->isSuccess()){
			\Esd\Debug::toLog(implode(', ', $resAddLog->getErrorMessages()));
		}
	}
	$bar->update($i);
	$i++;
}
echo "\r\n\r\n";