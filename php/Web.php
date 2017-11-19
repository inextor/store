<?php
namespace Web;

class Web
{
	const RELEASE_VERSION		= 11;
	const SHOW_MINIFIED_JS		= 0;
	const SHOW_MINIFIED_CSS		= 0;
	const SHOW_ONE_CSS_FILE		= 1;

	public static function getJsTags()
	{
		return '<script src="js/PromiseUtil.js"></script>'.PHP_EOL
				.'<script src="js/WebUtils.js"></script>'.PHP_EOL
				.'<script src="php/getJs.php?version'.self::RELEASE_VERSION.'"></script>'.PHP_EOL;

	}

	public static function getCssTags()
	{
		if( self::SHOW_ONE_CSS_FILE )
			return '<link rel="stylesheet" type="text/css" media="screen" href="php/getCss.php?version='.self::RELEASE_VERSION.'" />'.PHP_EOL;

		return '<link rel="stylesheet" type="text/css" media="screen" href="css/default.css?version='.self::RELEASE_VERSION.'" />'.PHP_EOL
		 		.'<link rel="stylesheet" type="text/css" media="screen" href="css/all.css?version='.self::RELEASE_VERSION.'" />'.PHP_EOL
		 		.'<link rel="stylesheet" type="text/css" media="screen" href="css/pageLogin.css?version='.self::RELEASE_VERSION.'" />'.PHP_EOL;

	}
}
