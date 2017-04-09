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
$this->setFrameMode(true); ?>

<div class="menu_let"> <!-- общий див со всем меню -->
	<!-- начинаем обход элементов меню -->
	<? foreach ($arResult['SECTIONS'] as $id => $arSection): ?>
		<!-- выводим первый уровень (все что лежит в $arSection - это первый уровень) -->
		<a href="<?=$arSection['SECTION_PAGE_URL']?>"><?=$arSection['NAME']?></a>
		<!-- проверяем есть ли у текущего пункта подуровни -->
		<? if (count($arSection['CHILD']) > 0){ ?>

			<!-- если есть подуровни, то оборачиваем все подуровни в свой тег (если нужно) -->
			<div class="submenu">
				<!-- начинаем обход подуровней -->
				<? foreach ($arSection['CHILD'] as $arSection2) { ?>
					<!-- выводим подуровни (все что лежит в $arSection2 - это второй уровень) -->

					<a href="<?=$arSection2['SECTION_PAGE_URL']?>"><?=$arSection2['NAME']?></a>

				<? } ?>
			</div>


		<? } ?>

	<? endforeach; ?>
</div>
