<?php

/**
 * Static web control script
 */
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);
ini_set("display_errors", 1);
ini_set('memory_limit', '2048M');

ini_set('session.cache_limiter','public');
session_cache_limiter(false);

//set up core files
require ("./config/config.inc.php");

// echo "<pre> acrtl";
// print_r($arCtl);
// echo "action" . $action;
//  echo "request";
//   print_r($_REQUEST);
// echo "post";
// print_r($_POST);
// echo "session";
// print_r($_SESSION);
// // echo "menu group";
// // echo $action;
// echo "</pre>";

// set up sessions
if (session_id()){
	// checking if a session has already been started if so stop it
	session_destroy();
	unset($_SESSION);
} 
$previous_name = session_name("NVAdmin");
session_set_cookie_params(0, '/', $ENVIRONMENTS['EN_URL']);
session_start();

$_SESSION['ENVIRONMENT'] = $ENVIRONMENTS;

// echo "<pre> acrtl";
// print_r($arCtl);
// echo "action" . $action;
// echo "request";
// print_r($_REQUEST);
// print_r($_SERVER);
// echo "post";
// print_r($_POST);
// echo "session";
// print_r($_SESSION);
// // echo "menu group";
// // echo $action;
// echo "</pre>";

// load standard functions


require (getAppRoot() . "functions/standard/users.inc.php");
require (getAppRoot() . "functions/standard/logon.inc.php");
require (getAppRoot() . "functions/standard/menuadmin.inc.php");
require (getAppRoot() . "functions/standard/security.inc.php");
require (getAppRoot() . "functions/standard/common.inc.php");
require (getAppRoot() . "functions/standard/scraper.inc.php");
require (getAppRoot() . "functions/standard/dbfunctions.inc.php");
require (getAppRoot() . "functions/standard/parameters.inc.php");

// check for sql injections
$arSAFE_REQUEST = protect_against_sql_attack($_REQUEST);

//stick existing session stuff in
unset($_SESSION['MenuKey']);
unset($_SESSION['action']);

foreach ($_SESSION as $TableName => $arFields){
	if (!isset($arSAFE_REQUEST[$TableName])){
		$arSAFE_REQUEST[$TableName] = $_SESSION[$TableName];
	}
}

// now we want to loop around and set the REQUEST vars into our table vars
foreach ($arSAFE_REQUEST as $TableName => $arFields){
	// check if array
	if (is_array($arFields)){
		// now we can assume the TableName var is a table so use that as a var
		eval("\$\$TableName = \$arFields;");
		// now overwrite session with full value
		$arSAFE_REQUEST[$TableName] = $arFields;
		$_SESSION[$TableName] = $arFields;
	}else{
		// not an array there is only one thing we use with is action
		if ($TableName == "action"){
			$action = $arFields;
			$_SESSION['action'] = $arFields;
		} elseif ($TableName == "MenuKey") {
			$_SESSION['MenuKey'] = $arFields;
			// get rest of menu
			$_SESSION['SelectedMenu'] = getMenuDetails($arFields);
			$action = $_SESSION['SelectedMenu']['MI_SCRIPT'];
		} 
	}
}

// check erorrs
if (isset($_SESSION['Error'])){
	$arCtl['Error'] = $_SESSION['Error'];
	unset($_SESSION['Error']);
}

// should be a para
$_SESSION['PostAction'] = getAdminCommand();


// check if logged on
if ($action == "Login"){
	Login($arCtl, $arSAFE_REQUEST);
	exit();
} elseif (!$_SESSION['LoggedOn']){
	ShowLogin($arCtl,$arSAFE_REQUEST);
	exit();
}

// now if no action passed from REQUEST see if there is one in session
if (trim($action) == "" and trim($_SESSION['NextAction']) != ""){
	$action = $_SESSION['NextAction'];
	unset ($_SESSION['NextAction']);
} elseif (trim($action) == "") {
	$action = "ShowHome";
}


// now check access and also stick in menu details
$_SESSION['SelectedMenu'] = checkAccess($action);
$_SESSION['BACKURL'] = getAdminCommand() . "?action=" . $_SESSION['SelectedMenu']['MI_BACKSCRIPT'];
// used when doing updates so redirect knows action to use
$_SESSION['NextAction'] = $_SESSION['SelectedMenu']['MI_NEXTSCRIPT'];


// print_r($arCtl);
// echo "HH" . $action;
// print_r($_REQUEST);
// print_r($_SESSION);
// // echo "menu group";
//  echo $action;
// exit();


/*************************************************************************
 * Now decide What to do
 * **********************************************************************/

 // new quote
 // go to page of existing quote

// now run
$action($arCtl,$arSAFE_REQUEST);



?>
