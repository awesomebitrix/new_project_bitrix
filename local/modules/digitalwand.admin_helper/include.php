<?php
spl_autoload_register(function ($className) {
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
	$filePath = str_replace('DigitalWand/AdminHelper/','',$filePath);
	$filePath = preg_replace('#DigitalWand\/AdminHelper\/#i','',$filePath);
	$filePath = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $filePath);

	if (is_readable($filePath) && is_file($filePath)) {
		/** @noinspection PhpIncludeInspection */
		require_once $filePath;
	}
});

$path = '/local/modules/ab.tools/asset';
$jsLibs = [
	'bootstrap' => [
		'css' => [
			$path.'/css/bootstrap.min00.css',
			$path.'/css/admin_bootstrap_debug.css',
		]
	],
	'admin_helper' => [
		'js' => [
			$path.'/6-shim.min.js',
			$path.'/js/shim/es6-sham.min.js',
			$path.'/js/react/react-with-addons.min.js',
			$path.'/js/react/react-dom.min.js',
			$path.'/js/is.min.js',
			$path.'/js/sweet_alert/sweetalert.min.js',
			'/local/modules/digitalwand.admin_helper/asset/builds/ComponentCreate.js'
		],
		'css' => [
			$path.'/css/sweetalert.css',
			$path.'/css/animate.min.css',
			$path.'/css/preloaders.css',
			$path.'/css/font-awesome.min.css',
			'/local/modules/digitalwand.admin_helper/asset/css/digitalwand.admin_helper.css',
		],
	],
];
foreach ($jsLibs as $name => $lib) {
	CJSCore::RegisterExt($name, $lib);
}