<?php
spl_autoload_register(function ($className) {
	preg_match('/^(.*?)([\w]+)$/i', $className, $matches);
	if (count($matches) < 3){
		return;
	}

	$filePath = implode(DIRECTORY_SEPARATOR, array(
		__DIR__,
		"lib",
		str_replace('\\', DIRECTORY_SEPARATOR, trim($matches[1], '\\')),
		str_replace('_', DIRECTORY_SEPARATOR, $matches[2]).'.php',
	));
	$filePath = str_replace('AB'.DIRECTORY_SEPARATOR.'Tools'.DIRECTORY_SEPARATOR, '', $filePath);
	$filePath = preg_replace('#AB\/Tools\/#', '', $filePath);
	$filePath = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $filePath);

	if (is_readable($filePath) && is_file($filePath)){
		/** @noinspection PhpIncludeInspection */
		require_once $filePath;
	}
});

//$root = \Bitrix\Main\Application::getDocumentRoot();
//$routesFile = $root.'/local/php_interface/ab.tools/routes.php';
//if(file_exists($routesFile) && is_readable($routesFile)){
//	/** @noinspection PhpIncludeInspection */
//	require_once $routesFile;
//}

//$ev = \Bitrix\Main\EventManager::getInstance();
//$ev->registerEventHandlerCompatible('fileman','OnBeforeHTMLEditorScriptRuns', 'ab.tools', '\AB\Tools\EventHandlers', 'onIncludeHTMLEditorScript');
$path = '/local/modules/ab.tools/asset';
$asset = [
	'react' => [
		'js' => [
//			$path.'/js/shim/es6-shim.min.js',
//			$path.'/js/shim/es6-sham.min.js',
			$path.'/js/react/react-with-addons.min.js',
			$path.'/js/react/react-dom.min.js',
		],
		'css' => [
			$path.'/css/preloaders.css',
			$path.'/css/animate.min.css',
		],
	],
	'isjs' => [
		'js' => [$path.'/js/is.min.js'],
	],
	'sweetalert' => [
		'js' => [
			$path.'/js/sweetalert.min.js',
		],
		'css' => [
			$path.'/css/sweetalert.css',
		],
	],
	'mask' => ['js' => $path.'/js/jquery.mask.min.js'],
];
foreach ($asset as $name => $arItem) {
	CJSCore::RegisterExt($name, $arItem);
}