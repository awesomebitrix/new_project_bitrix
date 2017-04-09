<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<?if($arParams['ONLY_AUTH'] === 'Y' && $arParams['LIST_ONLY'] !== 'Y' && !$USER->IsAuthorized()):?>
	<?$APPLICATION->IncludeComponent(
		"bitrix:system.auth.form",
		$arParams['SYSTEM_AUTH_TEMPLE'],
		Array(
			"REGISTER_URL"           => "",      // Страница регистрации
			"FORGOT_PASSWORD_URL"    => "",      // Страница забытого пароля
			"PROFILE_URL"            => "",      // Страница профиля
			"SHOW_ERRORS"            => "N",     // Показывать ошибки
		),
		$component,
		array('HIDE_ICONS' => 'Y')
	);?>
<?else:?>
	<div id="o1c_review_wr"></div>
<?endif;?>
