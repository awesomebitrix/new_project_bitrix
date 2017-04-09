<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
?>
<html>
<head>
<?$APPLICATION->ShowHead();?>
<title><?$APPLICATION->ShowTitle()?></title>
<meta name="viewport" content="width=device-width">
<? $Asset = \Bitrix\Main\Page\Asset::getInstance();
	$Asset->addCss("/local/dist/css/main.css");
	$Asset->addCss("/local/dist/css/media.css");
	$Asset->addCss("/local/dist/libs/bootstrap/css/bootstrap.min.css");
	$Asset->addCss("/local/dist/libs/slick-carousel/slick/slick.css");
	$Asset->addCss("/local/dist/libs/slick-carousel/slick/slick-theme.css");
	$Asset->addCss("/local/dist/libs/aos-master/aos.css");
	$Asset->addCss("/local/dist/libs/hover-master/hover-min.css");
	$Asset->addJs("/local/dist/js/jquery.min.js");
    $Asset->addJs("/local/dist/js/common.js");
    $Asset->addJs("/local/dist/libs/bootstrap/js/bootstrap.min.js");
    $Asset->addJs("/local/dist/libs/aos-master/aos.js");
    $Asset->addJs("/local/dist/libs/slick-carousel/slick/slick.min.js");
	?>
<!--fancybox 3-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.0.47/jquery.fancybox.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.0.47/jquery.fancybox.min.js"></script>
<!--fontawesome-->
	<script src="https://use.fontawesome.com/1f409c8f81.js"></script>
<!--input mask-->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery.mask/1.14.10/jquery.mask.min.js"></script>
</head>

<body>
<?$APPLICATION->ShowPanel()?>