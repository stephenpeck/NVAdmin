<?php

/*********************************************************************
 
 Show list of contracts
 
 ********************************************************************/


function ShowWorkflowParas ( $arCtl, $arSAFE_REQUEST ) {

	/**
	 * **********************************************************************************************
	 * Get Data
	 * **********************************************************************************************
	 */
	
	$KD_WORKFLOWS_PARAS = $arSAFE_REQUEST['KD_WORKFLOWS_PARAS'];
	
	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	// get document details
	$query = "SELECT * 
				FROM KD_WORKFLOWS_PARAS
				WHERE WFP_KEY = '" . $KD_WORKFLOWS_PARAS ['WFP_KEY'] . "'";

//	echo $query;
	
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	$KD_WORKFLOWS_PARAS = mysqli_fetch_assoc ( $result );
	
	// linked paras and docs
	$query = "SELECT * 
				FROM KD_WORKFLOWS_PARAS_ITEMS
				WHERE WPI_WFPKEY = '" . $KD_WORKFLOWS_PARAS ['WFP_KEY']. "'
				ORDER BY WPI_CODE";
	
	//echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	while ( $qd = mysqli_fetch_assoc ( $result ) ) {
		$arKD_WORKFLOWS_PARAS_ITEMS [] = $qd;
	}
	/**
	 * **********************************************************************************************
	 * Build Drop Downs
	 * **********************************************************************************************
	 */
	
	/**
	 * **********************************************************************************************
	 * Assign Template Variables
	 * **********************************************************************************************
	 */
	$smarty = getSmarty ();
	
	// results info
	$smarty->assign ( 'KD_WORKFLOWS_PARAS', $KD_WORKFLOWS_PARAS );
	$smarty->assign ( 'arKD_WORKFLOWS_PARAS_ITEMS', $arKD_WORKFLOWS_PARAS_ITEMS );
	$smarty->assign ( 'DocTypeList', $arDocTypes );
	$smarty->assign ( 'ParaTypeList', $arParaTypes );
	$smarty->assign ( 'SESSION', $_SESSION );
	$smarty->assign ( 'arCtl', $arCtl );
	
	$smarty->display ( 'keydex/ShowWorkflowParas.tpl' );
}
function ListWorkflowParas ( $arCtl, $arSAFE_REQUEST ) {
	/**
	 * **********************************************************************************************
	 * Get Data
	 * **********************************************************************************************
	 */
	$KD_WORKFLOWS_PARAS = $arSAFE_REQUEST['KD_WORKFLOWS_PARAS'];
	
	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	$query = "SELECT * 
				FROM KD_WORKFLOWS_PARAS
				ORDER BY WFP_DESCRIPTION";
	
	// if (isset($arCtl['Run'])) {
	
	// echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	$arCtl ['count'] = 0;
	while ( $qd = mysqli_fetch_assoc ( $result ) ) {
		$arKD_WORKFLOWS_PARAS [] = $qd;
		$arCtl ['count'] ++;
	}
	// }
	
	/**
	 * **********************************************************************************************
	 * Build Drop Downs
	 * **********************************************************************************************
	 */

	/**
	 * **********************************************************************************************
	 * Assign Template Variables
	 * **********************************************************************************************
	 */
	$smarty = getSmarty ();
	
	// results info
	$smarty->assign ( 'arKD_WORKFLOWS_PARAS', $arKD_WORKFLOWS_PARAS );
	$smarty->assign ( 'SESSION', $_SESSION );
	$smarty->assign ( 'arCtl', $arCtl );
	
	$smarty->display ( 'keydex/ListWorkflowParas.tpl' );
}

/**
 * *******************************************************************
 *
 * Update \ Create New Parts
 *
 * ******************************************************************
 */
function UpdWorkflowParas ( $arCtl, $arSAFE_REQUEST ) {

	$KD_WORKFLOWS_PARAS = $arSAFE_REQUEST['KD_WORKFLOWS_PARAS'];
	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	if ($KD_WORKFLOWS_PARAS ['WFP_KEY'] != "") {
		
		$query = "UPDATE KD_WORKFLOWS_PARAS SET
					WFP_DESCRIPTION = '" . $KD_WORKFLOWS_PARAS ['WFP_DESCRIPTION'] . "'
					WHERE WFP_KEY = '" . $KD_WORKFLOWS_PARAS ['WFP_KEY'] . "'";

		$result = mysqli_query ( $mysqli, $query );
		if (! $result)
			error_message ( sql_error () );
		
	} else {
		
		$query = "INSERT KD_WORKFLOWS_PARAS (
					WFP_DESCRIPTION)
					 VALUES (
					'" . $KD_WORKFLOWS_PARAS ['WFP_DESCRIPTION'] . "')";
		
		$result = mysqli_query ( $mysqli, $query );
		if (! $result)
			error_message ( sql_error () );
		$KD_WORKFLOWS_PARAS ['WFP_KEY'] = mysqli_insert_id ( $mysqli );
	}
	
	ListWorkflowParas ( $arCtl, $KD_WORKFLOWS );
	
}
function UpdWorkflowParaItems ( $arCtl, $arSAFE_REQUEST ) {
	

	$KD_WORKFLOWS_PARAS = $arSAFE_REQUEST['KD_WORKFLOWS_PARAS'];		
	$KD_WORKFLOWS_PARAS_ITEMS = $arSAFE_REQUEST['KD_WORKFLOWS_PARAS_ITEMS'];		
	
	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	if ($KD_WORKFLOWS_PARAS_ITEMS ['WPI_KEY'] != "") {
		
		$query = "UPDATE KD_WORKFLOWS_PARAS_ITEMS SET
					WPI_CODE = '" . $KD_WORKFLOWS_PARAS_ITEMS ['WPI_CODE'] . "',
					WPI_ATTRIBUTE1 = '" . $KD_WORKFLOWS_PARAS_ITEMS ['WPI_ATTRIBUTE1'] . "',
					WPI_ATTRIBUTE2 = '" . $KD_WORKFLOWS_PARAS_ITEMS ['WPI_ATTRIBUTE2'] . "',
					WPI_ATTRIBUTE3 = '" . $KD_WORKFLOWS_PARAS_ITEMS ['WPI_ATTRIBUTE3'] . "',
					WPI_VALUE = '" . $KD_WORKFLOWS_PARAS_ITEMS ['WPI_VALUE'] . "'
					WHERE WPI_KEY = '" . $KD_WORKFLOWS_PARAS_ITEMS ['WPI_KEY'] . "'";
		
		$result = mysqli_query ( $mysqli, $query );
		if (! $result)
			error_message ( sql_error () );
	} else {
		
		$query = "INSERT KD_WORKFLOWS_PARAS_ITEMS (
					WPI_WFPKEY,
					WPI_CODE,
					WPI_ATTRIBUTE1,
					WPI_ATTRIBUTE2,
					WPI_ATTRIBUTE3,
					WPI_VALUE )
					 VALUES (
					'" . $KD_WORKFLOWS_PARAS_ITEMS ['WPI_WFPKEY'] . "',
					'" . $KD_WORKFLOWS_PARAS_ITEMS ['WPI_CODE'] . "',
					'" . $KD_WORKFLOWS_PARAS_ITEMS ['WPI_ATTRIBUTE1'] . "',
					'" . $KD_WORKFLOWS_PARAS_ITEMS ['WPI_ATTRIBUTE2'] . "',
					'" . $KD_WORKFLOWS_PARAS_ITEMS ['WPI_ATTRIBUTE3'] . "',
					'" . $KD_WORKFLOWS_PARAS_ITEMS ['WPI_VALUE'] . "')";
		
		$result = mysqli_query ( $mysqli, $query );
		if (! $result)
			error_message ( sql_error () );
		$KD_WORKFLOWS_PARAS_ITEMS ['WPI_KEY'] = mysqli_insert_id ( $mysqli );
	}
	
	// echo $query;
	ShowWorkflowParas ( $arCtl, $KD_WORKFLOWS_PARAS );
}
/**
 * *******************************************************************
 *
 * Delete Menu Entry
 *
 * ******************************************************************
 */
function DelWorkflowParas ( $arCtl , $arSAFE_REQUEST ) {
	

	$KD_WORKFLOWS_PARAS = $arSAFE_REQUEST['KD_WORKFLOWS_PARAS'];	
	
	$mysqli = db_connect ( getDBName () );

	$query = "DELETE FROM KD_WORKFLOWS_PARAS_ITEMS WHERE WPI_WFPKEY = '" . $KD_WORKFLOWS_PARAS ['WFP_KEY'] . "'";
	// echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	
	
	$query = "DELETE FROM KD_WORKFLOWS_PARAS WHERE WFP_KEY = '" . $KD_WORKFLOWS_PARAS ['WFP_KEY'] . "'";
	// echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	
	ListWorkflowParas ( $arCtl, $KD_WORKFLOWS_PARAS );
}
function DelWorkflowParaItems ( $arCtl, $arSAFE_REQUEST ) {
	
	$KD_WORKFLOWS_PARAS = $arSAFE_REQUEST['KD_WORKFLOWS_PARAS'];		
	$KD_WORKFLOWS_PARAS_ITEMS = $arSAFE_REQUEST['KD_WORKFLOWS_PARAS_ITEMS'];	
	
	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	$query = "DELETE FROM KD_WORKFLOWS_PARAS_ITEMS WHERE WPI_KEY = '" . $KD_WORKFLOWS_PARAS_ITEMS ['WPI_KEY'] . "'";
	// echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	
	ShowWorkflowParas ( $arCtl, $KD_WORKFLOWS_PARAS );
}


?>
