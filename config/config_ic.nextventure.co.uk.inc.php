
<?php
/**
 * adm paras
 * 
 * 
 */

// so we know config fuile was pulled in
$ConfigFound = TRUE;

function getConfigFileName() {
	return "config_ic.nextventure.co.uk.inc.php";
}

// templates directory
function getTemplateDir() {
	return "C:/web/templates";
}
// CC
function getCSS() {
	return "NextVenture";
}

function getFunctionName() {
	return "IC";
}
/**
 * Returns the name of this server/codebase to make lookups and brand stamping easier
 *
 * @return String Server Name
 * 
 */
function getServerName() {
	return "ic";
}
function isLive() {
	return true;
}


function getLogOnVar() {
	return getServerName () . "_loggedon";
}
function getDomain() {
	return "ic.nextventure.co.uk";
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
	return "C:/web/";
}


/**
 * Returns the base URL (web location) of the site
 *
 * @return String URL of website
 * @since 2006-04-03
 */
function getWebRoot() {
	return "http://ic.nextventure.co.uk/";
}
function getAdminCommand() {
	return "http://ic.nextventure.co.uk/index.php";
}

/**
 * Returns the path to the smarty library
 *
 * @return String Path to smarty library
 */
function getSmartyRoot() {
	$smartyRoot = "c:/web/libs/Smarty/";
	
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
function getSQLDBHost() {

	$serverName = "OGIWINDOWSSERVE\SQLEXPRESS"; //serverName\instanceName
	
	return $serverName;
}
/**
 * Returns the username for database access
 *
 * @return String authorised database username
 */
function getDBUsername() {
	return 'xsd';
}

function getSQLDBUsername() {
	return 'OpenICP';
}

/**
 * Returns the password for database access
 *
 * @return String authorised database password
 */
function getDBUserPassword() {
	return 'newbernard';
}

function getSQLDBUserPassword() {
	return 'NextR1sk!';
}
/**
 * Returns the database name to use
 *
 * @return String database name
 */
function getDBName() {
	return 'NV_IC';
}

function getSQLDBName() {
	
	return 'InfoCentrePlus';
}

function getConfigDBName() {
	return 'NV_ADMIN_IC';
}
function getControlDBName() {
	return 'NV_ADMIN_CONTROL';
}

// encruption stuff

function getIV() {
	return "2202197011041968";
}
function getEMethod() {
	return "AES-256-CBC";
}
function getHash() {
	return "5ynPtpNUlAv3cZCV0fJsJEkCTpeoZwUV";
}

?>
