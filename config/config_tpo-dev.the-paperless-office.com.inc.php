<?php
/**
 * adm paras
 * 
 * 
 */

// so we know config fuile was pulled in
$ConfigFound = TRUE;

function getConfigFileName() {
	return "config_demo-dev.the-paperless-office.com.inc.php";
}

// templates directory
function getTemplateDir() {
	return "/var/www/html/NV_ADMIN_DEV/templates";
}

/**
 * Returns the name of this server/codebase to make lookups and brand stamping easier
 *
 * @return String Server Name
 * 
 */
function getServerName() {
	return "demo-dev";
}

function getLogOnVar() {
	return getServerName () . "_loggedon";
}
function getDomain() {
	return $_SERVER['HTTP_HOST'];
}

//move to KC_COOMAPNY
function getCompany() {
	return "demo";
}
function getKSPassword() {
	return "demo99";
}

function getCompanyCode() {
	return "DEMO";
}
/**
 * Returns the web application root path
 *
 * This is needed in a number of places, mainly to simplify the includes throughout
 * the application.
 *
 * @return String Path to the web application
 */
function getAppRoot() {
	return "/var/www/html/NV_ADMIN_DEV/";
}

function getPHPBin() {
	return "/usr/bin/php";
}

/**
 * Returns the base URL (web location) of the site
 *
 * @return String URL of website
 * @since 2006-04-03
 */
function getWebRoot() {
	return "http://demo-dev.the-paperless-office.com/";
}
function getAdminCommand() {
	
	if ($_SERVER['HTTPS']){
		$url = "https://" . $_SERVER['HTTP_HOST'] . "/index.php";
	} else {
		$url = "http://" . $_SERVER['HTTP_HOST'] . "/index.php";
	}
	
	return $url;
}


/**
 * Returns the path to the smarty library
 *
 * @return String Path to smarty library
 */
function getSmartyRoot() {
	$smartyRoot = "/var/www/html/NV_ADMIN_DEV/libs/Smarty/";
	
	return $smartyRoot;
}

/**
 * Returns the Database Hostname
 *
 * @return String Database host name
 */
function getDBHost() {
	return 'localhost';
}

/**
 * Returns the username for database access
 *
 * @return String authorised database username
 */
function getDBUsername() {
	return 'root';
}

/**
 * Returns the password for database access
 *
 * @return String authorised database password
 */
function getDBUserPassword() {
	return 'moonmash';
}

/**
 * Returns the database name to use
 *
 * @return String database name
 */
function getDBName() {
	return 'NV_TPO_DEV';
}
function getConfigDBName() {
	return 'NV_TPO_DEV';
}

function getControlDBName() {
	return 'NV_ADMIN_CONTROL';
}
?>
