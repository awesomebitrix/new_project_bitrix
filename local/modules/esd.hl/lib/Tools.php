<?php namespace Esd\HL;

class Tools
{
	public static $menu;
	public static function getModule()
	{
		return parse_ini_file(__DIR__.'/../module.ini');
	}
}