<?php 
/*
 * 
 * figure out where you are and get config files in order to set up env
 * 
 */

if ($Path == ""){
	$RootPath = $_SERVER['DOCUMENT_ROOT'] . "/";
} else {
	$RootPath = $Path;
}

// assume on one control DB so get details from server paths

require ($RootPath . "config/ServerConfig/" . basename($RootPath) . ".inc.php");

if (!$mysqli = mysqli_connect(getServerDBHost(), getServerDBUsername(), getServerDBUserPassword())){
	$MYSQL_ERRNO = 0;
	$MYSQL_ERROR = "Connection Failed to the host " . getServerDBHost();
	return 0;
} else if (!mysqli_select_db($mysqli,getServerDBName())){
	$MYSQL_ERRNO = mysqli_errno($Connection);
	$MYSQL_ERROR = mysqli_error($Connection);
	return 0;
}

// see if HTTP_HOST set up get details from DB
if ($_SERVER ['HTTP_HOST'] != "") {
	$url = $_SERVER['HTTP_HOST'];
} elseif ($HOSTURL != "") {
	$url = $HOSTURL;
}

// not get environemt info
$sql = "select * from ENVIRONMENTS, COMPANY where EN_COKEY = CO_KEY AND EN_URL = '" . $url . "'";
$result = mysqli_query ($mysqli, $sql );
if (! $result)	{ echo  sql_error (); }
$ENVIRONMENTS = mysqli_fetch_assoc($result);

// now get env paras
$sql = "select PA_NAME, EP_VALUE from ENVIRONMENT_PARAMETERS, PARAMETERS where EP_PAKEY = PA_KEY AND EP_ENKEY = ". $ENVIRONMENTS['EN_KEY'];
$result2 = mysqli_query($mysqli,$sql);
if (!$result2) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
while($qd2 = mysqli_fetch_assoc($result2)){
	$ENVIRONMENTS['PARAMETERS'][$qd2['PA_NAME']] = $qd2['EP_VALUE'];
}




// print_r($ENVIRONMENTS);

// echo $ConfigFile;

//either set above or as a var in calling script
if ($ENVIRONMENTS['EN_PATHFILE'] != "") {
	require ($RootPath . "config/" . $ENVIRONMENTS['EN_PATHFILE'] . '.inc.php');
}
if (!$ConfigFound) {
	exit ( "Config loading failure!" );
} else {
	// pull in other libs
	require ($RootPath . "config/db.inc.php");
	require ($RootPath . "config/smarty.inc.php");
	
	// now get all variable functions
	$sql = "select * from COMPANY_FUNCTIONS, FUNCTIONS where CF_FUKEY = FU_KEY AND CF_COMPANY = '" . $ENVIRONMENTS['CO_COMPANY']. "'";
	$result = mysqli_query ($mysqli, $sql );
	if (! $result)	{ echo  sql_error (); }
	while ($qd = mysqli_fetch_assoc($result)){
		require (getAppRoot() . "functions/" . $qd['FU_NAME']);
	}
	
}
?>