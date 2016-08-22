<?php

/**
 * Returns a correctly configured smarty object.
 *
 * Here the Singleton design pattern is used to check if the smarty object
 * has already been created.  If the does not exist we must create it and
 * configure it correctly.
 *
 * @return Correcty configured Smarty object (for our application)
 */
function getSmarty() {

	static $Smarty;

	if (!is_object($Smarty)) {
		define('SMARTY_DIR', getSmartyRoot());
		require_once(SMARTY_DIR . 'Smarty.class.php');

		$Smarty = new Smarty();
		$Smarty->template_dir 	= getAppRoot() . "templates/";
		$Smarty->compile_dir 	= getAppRoot() . "templates/templates_c/";
		$Smarty->config_dir 	= getAppRoot() . "templates/configs/";
		$Smarty->caching 		= FALSE;
		$Smarty->force_compile 	= TRUE;
	}

	return $Smarty;
}

?>
