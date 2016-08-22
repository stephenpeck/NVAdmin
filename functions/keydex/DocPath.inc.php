<?php

/*********************************************************************
 
 Show list of contracts
 
 ********************************************************************/
function ShowDocPath($arCtl, $arSAFE_REQUEST) {
	/**
	 * **********************************************************************************************
	 * Get Data
	 * **********************************************************************************************
	 */
	
	$KD_DOCPATH = $arSAFE_REQUEST['KD_DOCPATH'];
	
	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	// get document details
	$query = "SELECT * 
				FROM KD_DOCPATH
				WHERE DP_KEY = '" . $KD_DOCPATH ['DP_KEY'] . "'";
	
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	$KD_DOCPATH = mysqli_fetch_assoc ( $result );
	
	// now get the regex (for doc def)
	$query = "SELECT * 
				FROM KD_DOCPATH_STEPS
				WHERE DS_DPKEY = '" . $KD_DOCPATH ['DP_KEY'] . "'
				ORDER BY DS_SEQ";
	
	// echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	while ( $qd = mysqli_fetch_assoc ( $result ) ) {
		$arKD_DOCPATH_STEPS [] = $qd;
	}
	
	/**
	 * **********************************************************************************************
	 * Build Drop Downs
	 * **********************************************************************************************
	 */
	
	$arDocTypes [''] = "No Document Type";
	foreach ( $_SESSION ['arDocTypes'] as $key => $arDocTypeDetails ) {
		$arDocTypes [$arDocTypeDetails ['Id']] = $arDocTypeDetails ['Name'];
	}
	
	$arImportType [''] = "Manual";
	$arImportType ['Email'] = "Email";
	$arImportType ['FTPPUT'] = "FTP Put";
	$arImportType ['FTPGET'] = "FTP Get";
	
	$arYesNo [''] = "..";
	$arYesNo ['Y'] = "Yes";
	$arYesNo ['N'] = "No";
	
	$arFrequency [''] = "..";
	$arFrequency ['C'] = "Constant (Every 10 Mins)";
	$arFrequency ['H'] = "Hourly";
	$arFrequency ['D'] = "Daily";
	
	$arFunction [''] = "..";
	$arFunction ['IMPORT'] = "Import";
	$arFunction ['ImportDir'] = "Import and assign DocType from directory";
	$arFunction ['OCRDoc'] = "OCR Image";
	$arFunction ['PDFTEXT'] = "Extract Text from PDF";
	$arFunction ['REGEX_KEY'] = "Run Regex for Doc Index";
	$arFunction ['REGEX_DOC'] = "Run Regex for Doc Type";
	$arFunction ['FORMAT'] = "Format Document using Doc Type layout";
	$arFunction ['KeystoreFile'] = "File Document";
	
	/**
	 * **********************************************************************************************
	 * Assign Template Variables
	 * **********************************************************************************************
	 */
	$smarty = getSmarty ();
	
	// results info
	$smarty->assign ( 'KD_DOCPATH', $KD_DOCPATH );
	$smarty->assign ( 'arKD_DOCPATH_STEPS', $arKD_DOCPATH_STEPS );
	$smarty->assign ( 'FrequencyList', $arFrequency );
	$smarty->assign ( 'FunctionList', $arFunction );
	$smarty->assign ( 'DocTypeList', $arDocTypes );
	$smarty->assign ( 'ImportTypeList', $arImportType );
	$smarty->assign ( 'YesNoList', $arYesNo );
	$smarty->assign ( 'SESSION', $_SESSION );
	$smarty->assign ( 'arCtl', $arCtl );
	
	$smarty->display ( 'keydex/ShowDocPath.tpl' );
}
function ListDocPath($arCtl, $arSAFE_REQUEST) {
	/**
	 * **********************************************************************************************
	 * Get Data
	 * **********************************************************************************************
	 */
	
	$KD_DOCPATH = $arSAFE_REQUEST['KD_DOCPATH'];
	
	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	$query = "SELECT * 
				FROM KD_DOCPATH
				ORDER BY DP_IMPORTTYPE, DP_DDKEY";
	
	// if (isset($arCtl['Run'])) {
	
	// echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	$arCtl ['count'] = 0;
	while ( $qd = mysqli_fetch_assoc ( $result ) ) {
		$arKD_DOCPATH [] = $qd;
		$arCtl ['count'] ++;
	}
	// }
	
	/**
	 * **********************************************************************************************
	 * Build Drop Downs
	 * **********************************************************************************************
	 */
	$arDocTypes [''] = "No Document Type";
	foreach ( $_SESSION ['arDocTypes'] as $key => $arDocTypeDetails ) {
		$arDocTypes [$arDocTypeDetails ['Id']] = $arDocTypeDetails ['Name'];
	}
	
	$arImportType ['Manual'] = "Manual";
	$arImportType ['Email'] = "Email";
	$arImportType ['FTPPUT'] = "FTP Put";
	$arImportType ['FTPGET'] = "FTP Get";
	
	$arYesNo [''] = "..";
	$arYesNo ['Y'] = "Yes";
	$arYesNo ['N'] = "No";
	
	$arFrequency [''] = "..";
	$arFrequency ['C'] = "Constant";
	$arFrequency ['H'] = "Hourly";
	$arFrequency ['D'] = "Daily";
	
	/**
	 * **********************************************************************************************
	 * Assign Template Variables
	 * **********************************************************************************************
	 */
	$smarty = getSmarty ();
	
	// results info
	$smarty->assign ( 'arKD_DOCPATH', $arKD_DOCPATH );
	$smarty->assign ( 'SESSION', $_SESSION );
	$smarty->assign ( 'arCtl', $arCtl );
	$smarty->assign ( 'FrequencyList', $arFrequency );
	$smarty->assign ( 'DocTypeList', $arDocTypes );
	$smarty->assign ( 'ImportTypeList', $arImportType );
	$smarty->assign ( 'YesNoList', $arYesNo );
	
	$smarty->display ( 'keydex/ListDocPath.tpl' );
}

/**
 * *******************************************************************
 *
 * Update \ Create New Parts
 *
 * ******************************************************************
 */
function UpdDocPath($arCtl, $arSAFE_REQUEST) {
	/**
	 * **********************************************************************************************
	 * Get Data
	 * **********************************************************************************************
	 */
	
	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	$KD_DOCPATH = $arSAFE_REQUEST['KD_DOCPATH'];
	
	if ($KD_DOCPATH ['DP_KEY'] != "") {
		
		$query = "UPDATE KD_DOCPATH SET
					DP_DDKEY = '" . $KD_DOCPATH ['DP_DDKEY'] . "',
					DP_DOCDIR = '" . $KD_DOCPATH ['DP_DOCDIR'] . "',
					DP_DESCRIPTION = '" . $KD_DOCPATH ['DP_DESCRIPTION'] . "',
					DP_ACTIVE = '" . $KD_DOCPATH ['DP_ACTIVE'] . "',
					DP_FREQUENCY = '" . $KD_DOCPATH ['DP_FREQUENCY'] . "'
					WHERE DP_KEY = '" . $KD_DOCPATH ['DP_KEY'] . "'";

		$result = mysqli_query ( $mysqli, $query );
		if (! $result)
			error_message ( sql_error () );
		
	} else {
		
		$query = "INSERT KD_DOCPATH (
					DP_DDKEY,
					DP_IMPORTTYPE,
					DP_DOCDIR,
					DP_DESCRIPTION,
					DP_FREQUENCY,
					DP_ACTIVE)
					 VALUES (
					'" . $KD_DOCPATH ['DP_DDKEY'] . "',
					'" . $KD_DOCPATH ['DP_IMPORTTYPE'] . "',
					'" . $KD_DOCPATH ['DP_DOCDIR'] . "',
					'" . $KD_DOCPATH ['DP_DESCRIPTION'] . "',
					'" . $KD_DOCPATH ['DP_FREQUENCY'] . "',
					'" . $KD_DOCPATH ['DP_ACTIVE'] . "')";
		
		$result = mysqli_query ( $mysqli, $query );
		if (! $result)
			error_message ( sql_error () );
		$KD_DOCPATH ['DP_KEY'] = mysqli_insert_id ( $mysqli );
	}
	
	$_SESSION['KD_DOCPATH']['DP_KEY'] = $KD_DOCPATH ['DP_KEY'];

	header("location:" . getAdminCommand());
	// echo $query;
}
function UpdDocPathSteps($arCtl, $arSAFE_REQUEST) {
	/**
	 * **********************************************************************************************
	 * Get Data
	 * **********************************************************************************************
	 */
	
	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	$KD_DOCPATH = $arSAFE_REQUEST['KD_DOCPATH'];
	$KD_DOCPATH_STEPS = $arSAFE_REQUEST['KD_DOCPATH_STEPS'];
	
	if ($KD_DOCPATH_STEPS ['DS_KEY'] != "") {
		
		$query = "UPDATE KD_DOCPATH_STEPS SET
					DS_SEQ = '" . $KD_DOCPATH_STEPS ['DS_SEQ'] . "',
					DS_FUNCTION = '" . $KD_DOCPATH_STEPS ['DS_FUNCTION'] . "',
					DS_DOCDIR = '" . $KD_DOCPATH_STEPS ['DS_DOCDIR'] . "',
					DS_DESCRIPTION = '" . $KD_DOCPATH_STEPS ['DS_DESCRIPTION'] . "'
					WHERE DS_KEY = '" . $KD_DOCPATH_STEPS ['DS_KEY'] . "'";
		
		$result = mysqli_query ( $mysqli, $query );
		if (! $result)
			error_message ( sql_error () );
	} else {
		
		$query = "INSERT KD_DOCPATH_STEPS (
					DS_DPKEY,
					DS_SEQ,
					DS_FUNCTION,
					DS_DOCDIR,
					DS_DESCRIPTION )
					 VALUES (
					'" . $KD_DOCPATH_STEPS ['DS_DPKEY'] . "',
					'" . $KD_DOCPATH_STEPS ['DS_SEQ'] . "',
					'" . $KD_DOCPATH_STEPS ['DS_FUNCTION'] . "',
					'" . $KD_DOCPATH_STEPS ['DS_DOCDIR'] . "',
					'" . $KD_DOCPATH_STEPS ['DS_DESCRIPTION'] . "')";
		
		$result = mysqli_query ( $mysqli, $query );
		if (! $result)
			error_message ( sql_error () );
		$KD_DOCPATH_STEPS ['DS_KEY'] = mysqli_insert_id ( $mysqli );
	}
	
	// echo $query;
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
function DelDocPath($arCtl, $arSAFE_REQUEST) {

	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	$KD_DOCPATH = $arSAFE_REQUEST['KD_DOCPATH'];
	
	$query = "DELETE FROM KD_DOCPATH WHERE DP_KEY = '" . $KD_DOCPATH ['DP_KEY'] . "'";
	// echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	
	header("location:" . getAdminCommand());
}
function DelDocPathSteps($arCtll, $arSAFE_REQUEST) {

	
	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	$KD_DOCPATH = $arSAFE_REQUEST['KD_DOCPATH'];
	$KD_DOCPATH_STEPS = $arSAFE_REQUEST['KD_DOCPATH_STEPS'];
	
	$query = "DELETE FROM KD_DOCPATH_STEPS WHERE DS_KEY = '" . $KD_DOCPATH_STEPS ['DS_KEY'] . "'";
	// echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	
	// echo $query;
	header("location:" . getAdminCommand());
}
?>
