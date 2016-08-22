<?php

/*********************************************************************
 
 Show list of contracts
 
 ********************************************************************/
function DelSystem($arCtl) {
	
	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	// del docs
	
	$query = "DELETE FROM KD_DOCUMENT";
	// echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );

	$query = "DELETE FROM KD_DOCUMENT_ITEMS";
	// echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	
	// del logs
	$query = "DELETE FROM KD_UPLOADS";
	// echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	
	$query = "DELETE FROM KD_MESSAGES";
	// echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	
	
	// echo $query;
	header("location:" . getAdminCommand());
}
?>
