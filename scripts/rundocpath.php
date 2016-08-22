<?php

/*************************************************************************************************************************
 * 
 * Bring in config and application fuctions
 * 
 **************************************************************************************************************************/
error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", 1);

ini_set ( "memory_limit", "64M" );
set_time_limit ( "2400" );


$Path = $argv [1];
$HOSTURL = $argv [2];
$KD_UPLOADS['KUP_KEY'] = $argv [3];
$KD_UPLOADS['DP_KEY'] = $argv [4];

require ($Path . "config/config.inc.php"); // general paths etc


$arCtl['ENVIRONMENTS'] = $ENVIRONMENTS;
// include (getAppRoot() . "functions/keydex/Common.inc.php");
// include (getAppRoot() . "functions/keydex/DocPathFunctions.inc.php");
// include (getAppRoot() . "functions/keydex/DocLoad.inc.php");
// include (getAppRoot() . "functions/keydex/DocWorkflow.inc.php");

// DB Connection
$mysqli = db_connect ( getDBName () );

// get all doc paths

$sql = "select * from KD_DOCPATH, KD_DOCPATH_STEPS where DP_KEY = DS_DPKEY AND DP_KEY = '" . $KD_UPLOADS['DP_KEY'] . "'";
echo $sql;
$result = mysqli_query ($mysqli, $sql );
if (! $result)	error_message ( sql_error () );
$ErrorCount=0;
while ($KD_DOCPATH_STEPS = mysqli_fetch_assoc($result)){

// 	echo "Running steps \n";
// 	print_r($KD_DOCPATH_STEPS);
	
	// now run each step on each doc
	$arResult = $KD_DOCPATH_STEPS['DS_FUNCTION']($arCtl,$KD_DOCPATH,$KD_UPLOADS);

	//print_r($arResult);
	
	$KD_UPLOADS['KUP_TEXT'] = $arResult['StatusMessage'];
	if ($arResult['Status'] == "Not OK"){
		$KD_UPLOADS['KUP_STATUS'] = "Error";
		$ErrorCount++;
	}
	
	UpdUploadLog($arCtl,$KD_UPLOADS);
}

if ($ErrorCount == 0){

	$KD_UPLOADS['KUP_TEXT'] = " Doc Path Steps Completed No Errors";
	$KD_UPLOADS['KUP_STATUS'] = "Complete";
	$KD_UPLOADS['KUP_FINISHDATE'] = date('Y-m-d H:i');;
	UpdUploadLog($arCtl,$KD_UPLOADS);
	
	// check docs for workflow
	$sql = "select * from KD_DOCUMENT where KDU_KUPKEY = " . $KD_UPLOADS['KUP_KEY'];
	echo $sql;
	$result = mysqli_query ($mysqli, $sql );
	if (! $result)	error_message ( sql_error () );
	while ($KD_DOCUMENT = mysqli_fetch_assoc($result)){
		
		echo "checking workflow";
		print_r($KD_DOCUMENT);
		
		checkWorkflow($arCtl,$KD_DOCUMENT);
	}	
	
} 


?>