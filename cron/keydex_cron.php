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
$DP_FREQUENCY = $argv [3];

require ($Path . "config/config.inc.php"); // general paths etc
$arCtl['ENVIRONMENTS'] = $ENVIRONMENTS;
// include (getAppRoot() . "functions/keydex/Common.inc.php");
// include (getAppRoot() . "functions/keydex/ImportTypes.inc.php");


/**
 * ***********************************************************************************************************************
 *
 * Check OK to run Script
 *
 * ************************************************************************************************************************
 */
// Check if process is already running

// set lock vars
$lockfile_status = "OK";
$lockfile = getAppRoot() . "cron/locks/KeyDex-" . $ENVIRONMENTS['EN_URL'] . "-" . $DP_FREQUENCY . ".lock";

echo $lockfile;

if (! (file_exists ( $lockfile ))) {
	if (! $fp = fopen ( $lockfile, "w" )) {
		$lockfile_status = "Error - Cannot open file " . $lockfile;
		$KD_UPLOADS ['KUP_STATUS'] = "NOT OK";
		$KD_UPLOADS ['KUP_TEXT'] = $lockfile_status;
	} else {
		// write date into new lock file
		if ((fwrite ( $fp, date ( "Y-m-d H:i:s" ) )) === FALSE) {
			$lockfile_status = "Error - Cannot write to file " . $lockfile;
		}
		fclose ( $fp );
	}
} else {
	// lock file exists
	// so now check date of file and if greater than 3 hours old carry on anyway
	$checkdate = date ( 'U' ) - (3 * 60 * 60);

	echo "File Dte " . date ( 'Y-m-d H:i:s', $checkdate );
	echo "File Dte " . date ( 'Y-m-d H:i:s', filemtime ( $lockfile ) );

	if (filemtime ( $lockfile ) > $checkdate) {
		$lockfile_status = "locked";
		print "lockFile (" . $lockfile . ")<pre>";
		print_r ( $lockfile_status );
		print "</pre>";
		$KD_UPLOADS ['KUP_STATUS'] = "NOT OK";
		$KD_UPLOADS ['KUP_TEXT'] = $lockfile_status;
	}
}

// if lock file not already existing start process
if ($lockfile_status != "OK") {
	exit();
}
/**
 * ***********************************************************************************************************************
 *
 * OK to run and lockfile set so run script
 *
 * ************************************************************************************************************************
 */
// DB Connection
$mysqli = db_connect ( getDBName () );

echo getDBName();
// get all doc paths

$sql = "select * from KD_DOCPATH where DP_FREQUENCY = '" . $DP_FREQUENCY . "'";
echo $sql;
$result = mysqli_query ($mysqli, $sql );
if (! $result)	error_message ( sql_error () );

while ($KD_DOCPATH = mysqli_fetch_assoc($result)){

	// create import record
	$sql = "INSERT into KD_UPLOADS (KUP_DATE,KUP_DPKEY,KUP_STATUS) values (now()," . $KD_DOCPATH['DP_KEY'] . ",'Started')";
	$result2 = mysqli_query ($mysqli, $sql );
	if (! $result2)	error_message ( sql_error () );
	$KD_UPLOADS['KUP_KEY'] = mysqli_insert_id($mysqli);

	// decide what import type and check for files
	if ($KD_DOCPATH['DP_IMPORTTYPE'] == "Manual"){
		$FileCount = importmanual($arCtl,$KD_DOCPATH,$KD_UPLOADS);
	}elseif ($KD_DOCPATH['DP_IMPORTTYPE'] == "Email"){
		
	}elseif ($KD_DOCPATH['DP_IMPORTTYPE'] == "FTPPUT"){
		$FileCount = importftpput($arCtl,$KD_DOCPATH,$KD_UPLOADS);
	}elseif ($KD_DOCPATH['DP_IMPORTTYPE'] == "FTPGET"){

	}
	
	if ($FileCount > 0){
		$sql = "UPDATE KD_UPLOADS set KUP_TEXT = 'Files found Started', KUP_DOCS_COUNT=" . $FileCount . ",KUP_STATUS='In Progress' where KUP_KEY = " . $KD_UPLOADS['KUP_KEY'];
		$result2 = mysqli_query ($mysqli, $sql );
		if (! $result2)	error_message ( sql_error () );
		// run doc path processing
		
		$cmd = "php " . $Path . "scripts/rundocpath.php " . $Path . " " . $HOSTURL . " " . $KD_UPLOADS['KUP_KEY'] . " " . $KD_DOCPATH['DP_KEY'] . " 2>/dev/null >&- <&- >/dev/null & ";
		$cmd = "php " . $Path . "scripts/rundocpath.php " . $Path . " " . $HOSTURL . " " . $KD_UPLOADS['KUP_KEY'] . " " . $KD_DOCPATH['DP_KEY'] . " ";
		echo $cmd;
		exec($cmd);
	} else {
		
		$sql = "UPDATE KD_UPLOADS set KUP_TEXT = 'No Files found', KUP_FINISHDATE=now(), KUP_DOCS_COUNT=0,KUP_STATUS='Complete' where KUP_KEY = " . $KD_UPLOADS['KUP_KEY'];
		$result2 = mysqli_query ($mysqli, $sql );
		if (! $result2)	error_message ( sql_error () );
		
	}
	
	
}


/**
 * ***********************************************************************************************************************
 *
 * now unset lockfile
 *
 * ************************************************************************************************************************
 */

// now remove lock file
if (! unlink ( $lockfile )) {
	print $lockfile_status = "Error - Could not delete file " . $lockfile;
} else {
	echo "lock file deleted";
}


?>