<?php

/*********************************************************************
 
 Show list of contracts
 
 ********************************************************************/
function ShowDocDef($arCtl, $arSAFE_REQUEST) {
	/**
	 * **********************************************************************************************
	 * Get Data
	 * **********************************************************************************************
	 */
	
	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	$KD_DOCDEF = $arSAFE_REQUEST['KD_DOCDEF'];
	
	// get document details
	$query = "SELECT * 
				FROM KD_DOCDEF
				WHERE DD_KEY = '" . $KD_DOCDEF ['DD_KEY'] . "'";

// 	 echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	$KD_DOCDEF = mysqli_fetch_assoc ( $result );
	
	// get index keys
	
	$arDocTypeKeys[''] = "Select Doc Key";
	foreach ( $_SESSION ['arDocTypes'] as $key => $arDocTypeDetails ) {
		if ($arDocTypeDetails ['Name'] == $KD_DOCDEF['DD_KEYSTORE_DOCTYPE']){

			foreach($arDocTypeDetails ['Keys'] as $FieldNo => $arKeyName){
				$DBKey = $FieldNo+1;
				$arDocTypeKeys[$DBKey . "-" . $arKeyName ['Name']] = $DBKey . "-" . $arKeyName ['Name'];
			}
		}
	}
	
	// now get the regex (for doc def)
	$query = "SELECT *
				FROM KD_DOCDEF_KEYS
				WHERE DDK_DDKEY = " . $KD_DOCDEF ['DD_KEY'];
	
// 	 echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	while ( $qd = mysqli_fetch_assoc ( $result ) ) {
		$arKD_DOCDEF_KEYS [] = $qd;
	}
	// now get the regex (for doc def)
	$query = "SELECT * 
				FROM KD_DOCREGEX
				WHERE DR_DDKEY = '" . $KD_DOCDEF ['DD_KEY'] . "'
				AND DR_TYPE = 'DOC'
				ORDER BY DR_SEQ";
	
	// echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	while ( $qd = mysqli_fetch_assoc ( $result ) ) {
		$arKD_DOCREGEX_HEAD [] = $qd;
	}
	
	
	$query = "SELECT * 
				FROM KD_DOCREGEX
				WHERE DR_DDKEY = '" . $KD_DOCDEF ['DD_KEY'] . "'
				AND DR_TYPE = 'INDEX'
				ORDER BY DR_INDEX, DR_SEQ";
	
	// echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	while ( $qd = mysqli_fetch_assoc ( $result ) ) {
		$arKD_DOCREGEX_INDEX [] = $qd;
	}
	
	$arDocTypes [' '] = "..";
	foreach ( $_SESSION ['arDocTypes'] as $key => $arDocTypeDetails ) {
		$arDocTypes [$arDocTypeDetails ['Name']] = $arDocTypeDetails ['Name'];
	}
	
	/**
	 * **********************************************************************************************
	 * Build Drop Downs
	 * **********************************************************************************************
	 */
	
	// Create Menu Drop Down
	
	
	$arKeyTypeList['TEXT'] = "Normal";
	$arKeyTypeList['DATE'] = "Date";
	
	$arType [''] = "Select Regex Type";
	$arType ['DOC'] = "Document Regex";
	$arType ['INDEX'] = "Index Regex";
	
	$arRegexYesNo ['Y'] = "Regex True";
	$arRegexYesNo ['N'] = "Regex False";

	$arYesNo ['Y'] = "Y";
	$arYesNo ['N'] = "N";
	
	// Create Menu Drop Down
	$arLayout [''] = "No Headed Paper";
	$arLayout ['1.pdf'] = "Headed Paper 1";
	$arLayout ['2.pdf'] = "Headed Paper 2";
	
	$arIndexField [''] = "Select Index Field";
	$arIndexField ['KDU_INDEX1'] = "Index 1";
	$arIndexField ['KDU_INDEX2'] = "Index 2";
	$arIndexField ['KDU_INDEX3'] = "Index 3";
	$arIndexField ['KDU_INDEX4'] = "Index 4";
	$arIndexField ['KDU_INDEX5'] = "Index 5";
	$arIndexField ['KDU_INDEX6'] = "Index 6";
	
	/**
	 * **********************************************************************************************
	 * Assign Template Variables
	 * **********************************************************************************************
	 */
	$smarty = getSmarty ();
	
	// results info
	$smarty->assign ( 'LayoutList', $arLayout );
	$smarty->assign ( 'KD_DOCDEF', $KD_DOCDEF );
	$smarty->assign ( 'arKD_DOCDEF_KEYS', $arKD_DOCDEF_KEYS );
	$smarty->assign ( 'arDocTypeKeys', $arDocTypeKeys );
	$smarty->assign ( 'arKD_WORKFLOWS', $arKD_WORKFLOWS );
	$smarty->assign ( 'arKD_DOCREGEX_HEAD', $arKD_DOCREGEX_HEAD );
	$smarty->assign ( 'arKD_DOCREGEX_INDEX', $arKD_DOCREGEX_INDEX );
	$smarty->assign ( 'arDocTypes', $arDocTypes );
	$smarty->assign ( 'IndexList', $arIndexField );
	$smarty->assign ( 'arKeyTypeList', $arKeyTypeList );
	$smarty->assign ( 'Type', $arType );
	$smarty->assign ( 'arRegexYesNoList', $arRegexYesNo );
	$smarty->assign ( 'arYesNoList', $arYesNo );
	$smarty->assign ( 'SESSION', $_SESSION );
	$smarty->assign ( 'arCtl', $arCtl );
	
	$smarty->display ( 'keydex/ShowDocDef.tpl' );
}

function ListDocDef($arCtl, $arSAFE_REQUEST) {

	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	$KD_DOCDEF = $arSAFE_REQUEST['KD_DOCDEF'];
	
	$query = "SELECT * 
				FROM KD_DOCDEF
				LEFT OUTER JOIN KD_WORKFLOWS on WF_DDKEY = DD_KEY";
	
	// if (isset($arCtl['Run'])) {
	
	// echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	$arCtl ['count'] = 0;
	while ( $qd = mysqli_fetch_assoc ( $result ) ) {
		$arKD_DOCDEF [] = $qd;
		$arCtl ['count'] ++;
	}
	// }
	
	/**
	 * **********************************************************************************************
	 * Build Drop Downs
	 * **********************************************************************************************
	 */
// 	$query = "SELECT *
// 				FROM KD_WORKFLOWS
// 				ORDER BY WF_NAME";
	
// 	// echo $query;
// 	$arKD_WORKFLOWS [''] = "No Workflow";
// 	$result = mysqli_query ( $mysqli, $query );
// 	if (! $result)
// 		error_message ( sql_error () );
// 	while ( $qd = mysqli_fetch_assoc ( $result ) ) {
// 		$arKD_WORKFLOWS [] = $qd;
// 	}
	
	$arDocTypes [' '] = "Select a Document Type";
	foreach ( $_SESSION ['arDocTypes'] as $key => $arDocTypeDetails ) {
		$arDocTypes [$arDocTypeDetails ['Name']] = $arDocTypeDetails ['Name'];
	}
	
	/**
	 * **********************************************************************************************
	 * Assign Template Variables
	 * **********************************************************************************************
	 */
	$smarty = getSmarty ();
	
	// results info
	$smarty->assign ( 'arKD_DOCDEF', $arKD_DOCDEF );
	$smarty->assign ( 'arDocTypes', $arDocTypes );
	$smarty->assign ( 'arKD_WORKFLOWS', $arKD_WORKFLOWS );
	$smarty->assign ( 'SESSION', $_SESSION );
	$smarty->assign ( 'arCtl', $arCtl );
	
	$smarty->display ( 'keydex/ListDocDef.tpl' );
}

/**
 * *******************************************************************
 *
 * Update \ Create New Parts
 *
 * ******************************************************************
 */
function UpdDocDef($arCtl, $arSAFE_REQUEST) {

	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	$KD_DOCDEF = $arSAFE_REQUEST['KD_DOCDEF'];
	
	if ($KD_DOCDEF ['DD_KEY'] != "") {
		
		$query = "UPDATE KD_DOCDEF SET
					DD_DESCRIPTION = '" . $KD_DOCDEF ['DD_DESCRIPTION'] . "',
					DD_LAYOUT = '" . $KD_DOCDEF ['DD_LAYOUT'] . "',
					DD_KEYSTORE_DOCTYPE = '" . $KD_DOCDEF ['DD_KEYSTORE_DOCTYPE'] . "'
					WHERE DD_KEY = '" . $KD_DOCDEF ['DD_KEY'] . "'";
		echo $query;
		
		$result = mysqli_query ( $mysqli, $query );
		if (! $result)
			error_message ( sql_error () );
		
	} else {
		
		$query = "INSERT KD_DOCDEF (
					DD_DESCRIPTION,
					DD_LAYOUT,
					DD_KEYSTORE_DOCTYPE)
					 VALUES (
					'" . $KD_DOCDEF ['DD_DESCRIPTION'] . "',
					'" . $KD_DOCDEF ['DD_LAYOUT'] . "',
					'" . $KD_DOCDEF ['DD_KEYSTORE_DOCTYPE'] . "')";
		
		$result = mysqli_query ( $mysqli, $query );
		if (! $result)
			error_message ( sql_error () );
		$KD_DOCDEF ['DD_KEY'] = mysqli_insert_id ( $mysqli );
	}

	if ($arCtl ['Screen'] == "ListDocument") {
		//ListDocDef ( $arCtl, $KD_DOCDEF );
	} else {
		//ShowDocDef ( $arCtl, $KD_DOCDEF );
	}
	
	header("location:" . getAdminCommand());	// echo $query;
}

function UpdDocKeys($arCtl, $arSAFE_REQUEST) {

	// DB Connection
	
	$KD_DOCDEF = $arSAFE_REQUEST['KD_DOCDEF'];
	$KD_DOCDEF_KEYS = $arSAFE_REQUEST['KD_DOCDEF_KEYS'];
	
	$mysqli = db_connect ( getDBName () );

	$arKeyFields = explode("-",$KD_DOCDEF_KEYS ['DDK_KEYSTORE_NAME']);
	
	
	if ($KD_DOCDEF_KEYS ['DDK_KEY'] != "") {

		$query = "UPDATE KD_DOCDEF_KEYS SET
					DDK_TYPE = '" . $KD_DOCDEF_KEYS ['DDK_TYPE'] . "',
					DDK_MANDATORY = '" . $KD_DOCDEF_KEYS ['DDK_MANDATORY'] . "',
					DDK_READONLY = '" . $KD_DOCDEF_KEYS ['DDK_READONLY'] . "',
					DDK_DEFAULT = '" . $KD_DOCDEF_KEYS ['DDK_DEFAULT'] . "'
					WHERE DDK_KEY = '" . $KD_DOCDEF_KEYS ['DDK_KEY'] . "'";

		$result = mysqli_query ( $mysqli, $query );
		if (! $result)
			error_message ( sql_error () );
	} else {

		$query = "INSERT KD_DOCDEF_KEYS (
					DDK_DDKEY,
					DDK_KEYSTORE_NAME,
					DDK_KEYSTORE_KEYORDER,
					DDK_TYPE,
					DDK_MANDATORY,
					DDK_READONLY,
					DDK_DEFAULT )
					 VALUES (
					'" . $KD_DOCDEF ['DD_KEY'] . "',
					'" . $arKeyFields[1]  . "',
					'" . $arKeyFields[0]  . "',
					'" . $KD_DOCDEF_KEYS ['DDK_TYPE']  . "',
					'" . $KD_DOCDEF_KEYS ['DDK_MANDATORY']  . "',
					'" . $KD_DOCDEF_KEYS ['DDK_READONLY']  . "',
					'" . $KD_DOCDEF_KEYS ['DDK_DEFAULT'] . "')";

		$result = mysqli_query ( $mysqli, $query );
		if (! $result)
			error_message ( sql_error () );
		$KD_DOCDEF_KEYS ['DDK_KEY'] = mysqli_insert_id ( $mysqli );
	}

	header("location:" . getAdminCommand());
}

function UpdDocRegex($arCtl, $arSAFE_REQUEST) {

	// DB Connection
	
	$KD_DOCDEF = $arSAFE_REQUEST['KD_DOCDEF'];
	$KD_DOCREGEX = $arSAFE_REQUEST['KD_DOCREGEX'];
	
	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	if ($KD_DOCREGEX ['DR_KEY'] != "") {
		
		$query = "UPDATE KD_DOCREGEX SET
					DR_SEQ = '" . $KD_DOCREGEX ['DR_SEQ'] . "',
					DR_TRUE = '" . mysql_escape_string ( $KD_DOCREGEX ['DR_TRUE'] ) . "',
					DR_TRUE2 = '" . mysql_escape_string ( $KD_DOCREGEX ['DR_TRUE2'] ) . "',
					DR_TRUE3 = '" . mysql_escape_string ( $KD_DOCREGEX ['DR_TRUE3'] ) . "',
					DR_TRUE4 = '" . mysql_escape_string ( $KD_DOCREGEX ['DR_TRUE4'] ) . "',
					DR_MODIFIER = '" . mysql_escape_string ( $KD_DOCREGEX ['DR_MODIFIER'] ) . "',
					DR_REGEX1 = '" . mysql_escape_string ( $KD_DOCREGEX ['DR_REGEX1'] ) . "',
					DR_REGEX2 = '" . mysql_escape_string ( $KD_DOCREGEX ['DR_REGEX2'] ) . "',
					DR_REGEX3 = '" . mysql_escape_string ( $KD_DOCREGEX ['DR_REGEX3'] ) . "',
					DR_REGEX4 = '" . mysql_escape_string ( $KD_DOCREGEX ['DR_REGEX4'] ) . "',
					DR_INDEX = '" . $KD_DOCREGEX ['DR_INDEX'] . "'
					WHERE DR_KEY = '" . $KD_DOCREGEX ['DR_KEY'] . "'";
		
		$result = mysqli_query ( $mysqli, $query );
		if (! $result)
			error_message ( sql_error () );
	} else {
		
		$query = "INSERT KD_DOCREGEX (
					DR_TYPE,
					DR_DDKEY,
					DR_MODIFIER,
					DR_TRUE,
					DR_TRUE2,
					DR_TRUE3,
					DR_TRUE4,
					DR_SEQ,
					DR_REGEX1,
					DR_REGEX2,
					DR_REGEX3,
					DR_REGEX4,
					DR_INDEX )
					 VALUES (
					'" . $KD_DOCREGEX ['DR_TYPE'] . "',
					'" . $KD_DOCDEF ['DD_KEY'] . "',
					'" . mysql_escape_string ( $KD_DOCREGEX ['DR_MODIFIER'] ) . "',
					'" . $KD_DOCREGEX ['DR_TRUE'] . "',
					'" . $KD_DOCREGEX ['DR_TRUE2'] . "',
					'" . $KD_DOCREGEX ['DR_TRUE3'] . "',
					'" . $KD_DOCREGEX ['DR_TRUE4'] . "',
					'" . $KD_DOCREGEX ['DR_SEQ'] . "',
					'" . mysql_escape_string ( $KD_DOCREGEX ['DR_REGEX1'] ) . "',
					'" . mysql_escape_string ( $KD_DOCREGEX ['DR_REGEX2'] ) . "',
					'" . mysql_escape_string ( $KD_DOCREGEX ['DR_REGEX3'] ) . "',
					'" . mysql_escape_string ( $KD_DOCREGEX ['DR_REGEX4'] ) . "',
					'" . $KD_DOCREGEX ['DR_INDEX'] . "')";
		
		$result = mysqli_query ( $mysqli, $query );
		if (! $result)
			error_message ( sql_error () );
		$KD_DOCREGEX ['DR_KEY'] = mysqli_insert_id ( $mysqli );
	}
	
	// echo $query;
	header("location:" . getAdminCommand());
}
/**
 * *******************************************************************
 *
 * Delete Menu Entry
 *
 * ******************************************************************
 */
function DelDocDef($arCtl, $arSAFE_REQUEST) {
	
	$KD_DOCDEF = $arSAFE_REQUEST['KD_DOCDEF'];
	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	$query = "DELETE FROM KD_DOCDEF WHERE DD_KEY = '" . $KD_DOCDEF ['DD_KEY'] . "'";
	// echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	
	header("location:" . getAdminCommand());
}
function DelDocRegex($arCtl, $arSAFE_REQUEST) {
	
	$KD_DOCDEF = $arSAFE_REQUEST['KD_DOCDEF'];
	$KD_DOCREGEX = $arSAFE_REQUEST['KD_DOCREGEX'];
	
	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	$query = "DELETE FROM KD_DOCREGEX WHERE DR_KEY = '" . $KD_DOCREGEX ['DR_KEY'] . "'";
	// echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	
	header("location:" . getAdminCommand());
}

function DelDocKeys($arCtl, $arSAFE_REQUEST) {
	
	$KD_DOCDEF = $arSAFE_REQUEST['KD_DOCDEF'];
	$KD_DOCDEF_KEYS = $arSAFE_REQUEST['KD_DOCDEF_KEYS'];
	
	// DB Connection
	$mysqli = db_connect ( getDBName () );

	$query = "DELETE FROM KD_DOCDEF_KEYS WHERE DDK_KEY = '" . $KD_DOCDEF_KEYS ['DDK_KEY'] . "'";
	// echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );

	header("location:" . getAdminCommand());
}
?>
