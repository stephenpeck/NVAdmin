<?php

function UpdUploadLog($arCtl,$KD_UPLOADS){
	
	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	$sql = "select * from KD_UPLOADS where KUP_KEY = " . $KD_UPLOADS['KUP_KEY'];
	$result = mysqli_query ($mysqli, $sql );
	if (! $result)	error_message ( sql_error () );
	$KD_UPLOADS_ORIG = mysqli_fetch_assoc($result);
	
	$KD_UPLOADS['KUP_TEXT'] = $KD_UPLOADS_ORIG['KUP_TEXT'] . "\n ***** " . date('Y-m-d H:i') . "\n" . $KD_UPLOADS['KUP_TEXT'] . "\n ***** \n";

	$KD_UPLOADS_UPDATE = array_merge($KD_UPLOADS_ORIG,$KD_UPLOADS);
	
	// adds a comment to upload log
	$sql = "UPDATE KD_UPLOADS 
				set KUP_TEXT = '" . $KD_UPLOADS_UPDATE['KUP_TEXT'] . "',
				KUP_DOCS_COUNT = '" . $KD_UPLOADS_UPDATE['KUP_DOCS_COUNT'] . "',
				KUP_FINISHDATE = '" . $KD_UPLOADS_UPDATE['KUP_FINISHDATE'] . "',
				KUP_STATUS = '" . $KD_UPLOADS_UPDATE['KUP_STATUS'] . "'
				where KUP_KEY = " . $KD_UPLOADS['KUP_KEY'];
	echo $sql;
	$result = mysqli_query ($mysqli, $sql );
	if (! $result)	error_message ( sql_error () );
	
	
}


function UpdWorkflowLog($arCtl,$KD_DOCUMENT){

	// DB Connection
	$mysqli = db_connect ( getDBName () );

	// adds a comment to upload log
	$sql = "INSERT INTO KD_WORKFLOWS_LOG (WFL_DESCRIPTION,WFL_DATE,WFL_KDUKEY, WFL_SUMMARY, WFL_TYPE) values ('" . mysqli_real_escape_string($mysqli,$arCtl['WFL_DESCRIPTION']) . "',now(),'" . $KD_DOCUMENT['KDU_KEY'] . "','" . $arCtl['WFL_SUMMARY'] . "','" . $arCtl['WFL_TYPE'] . "')";
	//echo $sql;
	$result = mysqli_query ($mysqli, $sql );
	if (! $result)	error_message ( sql_error () );


}




?>