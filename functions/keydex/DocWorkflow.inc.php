<?php

/*********************************************************************
 
 Show list of contracts
 
 ********************************************************************/
function ShowDocWorkflow($arCtl, $arSAFE_REQUEST) {

	$KD_WORKFLOWS = $arSAFE_REQUEST['KD_WORKFLOWS'];
	/**
	 * **********************************************************************************************
	 * Get Data
	 * **********************************************************************************************
	 */
	
	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	// get document details
	$query = "SELECT * 
				FROM KD_WORKFLOWS, KD_DOCDEF
				WHERE WF_DDKEY = DD_KEY 
				AND WF_KEY = '" . $KD_WORKFLOWS ['WF_KEY'] . "'";
	
//	echo $query;
	
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	$KD_WORKFLOWS = mysqli_fetch_assoc ( $result );

	$arSource[''] = "Select Source Fields";
	
	$query = "SELECT *
				FROM KD_DOCDEF,KD_DOCDEF_KEYS
				WHERE DDK_DDKEY = DD_KEY
				AND DD_KEY = '" . $KD_WORKFLOWS ['DD_KEY']. "'
				ORDER BY DDK_KEY";
	
	// echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	while ( $qd = mysqli_fetch_assoc ( $result ) ) {
		// get fields
		$arSource["[DOC][" . $qd['DD_KEYSTORE_DOCTYPE'] . "][" . $qd['DDK_KEYSTORE_NAME'] . "]"] = "Doc - " . $qd['DD_DESCRIPTION'] . " - " . $qd['DDK_KEYSTORE_NAME'];
	}
	

	
	// linked paras and docs
	$query = "SELECT * 
				FROM KD_WORKFLOWS_LINKED_PARAS, KD_WORKFLOWS_PARAS
				WHERE WFP_KEY = WFLP_WFPKEY 
				AND WFLP_WFKEY = '" . $KD_WORKFLOWS ['WF_KEY']. "'
				ORDER BY WFP_DESCRIPTION";
	
	// echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	while ( $qd = mysqli_fetch_assoc ( $result ) ) {
		$arKD_WORKFLOWS_LINKED_PARAS [] = $qd;
		
		$arSource["[PARA][" . $qd['WFP_DESCRIPTION'] . "]"] = "Para - " . $qd['WFP_DESCRIPTION'] . " - " . $qd['WFLP_VALUE_NAME'];
		
	}
	$query = "SELECT *
				FROM KD_WORKFLOWS_LINKED_DOCTYPES, KD_DOCDEF
				WHERE DD_KEY = WFLD_DDKEY
				AND WFLD_WFKEY = '" . $KD_WORKFLOWS ['WF_KEY']. "'
				ORDER BY DD_DESCRIPTION";
	
	// echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	$count=0;
	while ( $qd = mysqli_fetch_assoc ( $result ) ) {
		$arKD_WORKFLOWS_LINKED_DOCTYPES [$count] = $qd;
		
		foreach ( $_SESSION ['arDocTypes'] as $key => $arDocTypeDetails ) {
			if ($arDocTypeDetails ['Name'] == $qd['DD_KEYSTORE_DOCTYPE']){
				// get keys
				$IndexCount =1;
				$arKD_WORKFLOWS_LINKED_DOCTYPES [$count]['FieldList'][''] = "Select Link Key";
				foreach($arDocTypeDetails['Keys'] as $key => $arFieldDetails){
					$arKD_WORKFLOWS_LINKED_DOCTYPES [$count]['FieldList'][$IndexCount] = $arFieldDetails['Name'];
					$IndexCount++;
				}
			}
		}
		$count++;
	}

	$query = "SELECT *
				FROM KD_WORKFLOWS_LINKED_DOCTYPES, KD_DOCDEF,KD_DOCDEF_KEYS
				WHERE DD_KEY = WFLD_DDKEY
				AND DDK_DDKEY = DD_KEY
				AND WFLD_WFKEY = '" . $KD_WORKFLOWS ['WF_KEY']. "'
				ORDER BY DD_DESCRIPTION";
	
	// echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	while ( $qd = mysqli_fetch_assoc ( $result ) ) {
		// get fields
		$arSource["[DOC][" . $qd['DD_KEYSTORE_DOCTYPE'] . "][" . $qd['DDK_KEYSTORE_NAME'] . "]"]  = "Doc - " . $qd['DD_DESCRIPTION'] . " - " . $qd['DDK_KEYSTORE_NAME'];
		

	}
	
	
	// put in some constants
	$arSource["[DOC][BLANK]"]  = " Constant - Blank";
	
	
	$query = "SELECT *
				FROM KD_WORKFLOWS_STEPS
				WHERE WFS_WFKEY = '" . $KD_WORKFLOWS ['WF_KEY']. "'
				ORDER BY WFS_SEQ";
	
	// echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	$count=0;
	while ( $qd = mysqli_fetch_assoc ( $result ) ) {
		$arKD_WORKFLOWS_STEPS [$count] = $qd;

		if ($qd['WFS_TYPE'] == "R"){
			$query = "SELECT *
				FROM KD_WORKFLOWS_STEPS_RULES
				WHERE WFSR_WFSKEY = '" . $qd ['WFS_KEY']. "'
				ORDER BY WFSR_ORDER";
			
			// echo $query;
			$result2 = mysqli_query ( $mysqli, $query );
			if (! $result2)
				error_message ( sql_error () );
			while ( $qd2 = mysqli_fetch_assoc ( $result2 ) ) {
				$arKD_WORKFLOWS_STEPS [$count]['RULES'][] = $qd2;
			}
		} else {
			$arKD_WORKFLOWS_STEPS [$count]['RULES'][0]['TEXT'] = "Custom function " . $qd['WFS_LOGICFUNCTION'];
			$arKD_WORKFLOWS_STEPS [$count]['RULES'][0]['FUNCTION'] = $qd['WFS_LOGICFUNCTION'];
		}

		$count++;
	}
	/**
	 * **********************************************************************************************
	 * Build Drop Downs
	 * **********************************************************************************************
	 */
	
	$arDocTypes [' '] = "Select a Document Type";
	$sql = "select * from KD_DOCDEF";
	$result = mysqli_query ( $mysqli, $sql);
	if (! $result)	error_message ( sql_error () );
	while ($qd = mysqli_fetch_assoc($result)){
		foreach ( $_SESSION ['arDocTypes'] as $key => $arDocTypeDetails ) {
			if ($arDocTypeDetails ['Name'] == $qd['DD_KEYSTORE_DOCTYPE']){
				$arDocTypes [$qd['DD_KEY']] = $arDocTypeDetails ['Name'];
				// also get keys
			}
		}
	}
	
	$query = "SELECT *
				FROM KD_DOCDEF_KEYS
				WHERE DDK_DDKEY = '" . $KD_WORKFLOWS ['DD_KEY']. "'
				ORDER BY DDK_KEYSTORE_KEYORDER";
	
	// echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	$count=0;
	$IndexCount = 1;
	$arStatusIndexList[''] = "Select Status Key";
	$arDocIndexList[''] = "Select Doc Key";
	while ( $qd = mysqli_fetch_assoc ( $result ) ) {
		$arStatusIndexList[$qd['DDK_KEYSTORE_KEYORDER']] = $qd['DDK_KEYSTORE_NAME'];
		$arDocIndexList[$qd['DDK_KEYSTORE_KEYORDER']] = $qd['DDK_KEYSTORE_NAME'];
		
	}
	
	
	$query = "SELECT *
				FROM KD_WORKFLOWS_PARAS
				ORDER BY WFP_DESCRIPTION";
	
	// echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	$arParaTypes [' '] = "Select a Parameter Table";
	while ( $qd = mysqli_fetch_assoc ( $result ) ) {
		$arParaTypes [$qd['WFP_KEY']] = $qd['WFP_DESCRIPTION'];
	}
	
	$arOperatorList[''] = "Select Operator";
	$arOperatorList['EQ'] = "True if Equal";
	$arOperatorList['NEQ'] = "True if Not Equal";
	
	$arJoinList[''] = "";
	$arJoinList['AND'] = "AND";
	$arJoinList['OR'] = "OR";
	

	$arActionList[''] = "No Action";
	$arActionList['EmailDocOwner'] = "Email Document Owner";
	$arActionList['EmailProcessOwner'] = "Email Process Owner";
	
	/**
	 * **********************************************************************************************
	 * Assign Template Variables
	 * **********************************************************************************************
	 */
	$smarty = getSmarty ();
	
	// results info
	$smarty->assign ( 'KD_WORKFLOWS', $KD_WORKFLOWS );
	$smarty->assign ( 'arKD_WORKFLOWS_LINKED_DOCTYPES', $arKD_WORKFLOWS_LINKED_DOCTYPES );
	$smarty->assign ( 'arKD_WORKFLOWS_LINKED_PARAS', $arKD_WORKFLOWS_LINKED_PARAS );
	$smarty->assign ( 'arKD_WORKFLOWS_STEPS', $arKD_WORKFLOWS_STEPS );
	$smarty->assign ( 'OperatorList', $arOperatorList );
	$smarty->assign ( 'SourceList', $arSource );
	$smarty->assign ( 'ActionList', $arActionList );
	$smarty->assign ( 'DocSourceList', $arDocSource );
	$smarty->assign ( 'JoinList', $arJoinList );
	$smarty->assign ( 'DocTypeList', $arDocTypes );
	$smarty->assign ( 'StatusIndexList', $arStatusIndexList );
	$smarty->assign ( 'DocIndexList', $arDocIndexList );
	$smarty->assign ( 'ParaTypeList', $arParaTypes );
	$smarty->assign ( 'SESSION', $_SESSION );
	$smarty->assign ( 'arCtl', $arCtl );
	
	$smarty->display ( 'keydex/ShowDocWorkflow.tpl' );
}
function ListDocWorkflow($arCtl, $arSAFE_REQUEST) {

	$KD_WORKFLOWS = $arSAFE_REQUEST['KD_WORKFLOWS'];
	/**
	 * **********************************************************************************************
	 * Get Data
	 * **********************************************************************************************
	 */
	
	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	$query = "SELECT * 
				FROM KD_WORKFLOWS, KD_DOCDEF
				WHERE WF_DDKEY = DD_KEY
				ORDER BY WF_NAME";
	
	// if (isset($arCtl['Run'])) {
	
//	 echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	$arCtl ['count'] = 0;
	while ( $qd = mysqli_fetch_assoc ( $result ) ) {
		$arKD_WORKFLOWS [] = $qd;
		$arCtl ['count'] ++;
	}
	// }
	
	/**
	 * **********************************************************************************************
	 * Build Drop Downs
	 * **********************************************************************************************
	 */

	$arDocTypes [' '] = "Select a Document Type";
	$sql = "select * from KD_DOCDEF";
	$result = mysqli_query ( $mysqli, $sql);
	if (! $result)	error_message ( sql_error () );
	while ($qd = mysqli_fetch_assoc($result)){
		foreach ( $_SESSION ['arDocTypes'] as $key => $arDocTypeDetails ) {
			if ($arDocTypeDetails ['Name'] == $qd['DD_KEYSTORE_DOCTYPE']){
				$arDocTypes [$qd['DD_KEY']] = $arDocTypeDetails ['Name'];
			}
		}
	}
	
	/**
	 * **********************************************************************************************
	 * Assign Template Variables
	 * **********************************************************************************************
	 */
	$smarty = getSmarty ();
	
	// results info
	$smarty->assign ( 'arKD_WORKFLOWS', $arKD_WORKFLOWS );
	$smarty->assign ( 'DocTypeList', $arDocTypes );
	$smarty->assign ( 'SESSION', $_SESSION );
	$smarty->assign ( 'arCtl', $arCtl );
	
	$smarty->display ( 'keydex/ListDocWorkflow.tpl' );
}

function ListWorkflowLog($arCtl, $arSAFE_REQUEST) {

	$KD_WORKFLOWS_LOG = $arSAFE_REQUEST['KD_WORKFLOWS_LOG'];
	/**
	 * **********************************************************************************************
	 * Get Data
	 * **********************************************************************************************
	 */

	// DB Connection
	$mysqli = db_connect ( getDBName () );

	$query = "SELECT *
				FROM KD_WORKFLOWS_LOG
				WHERE WFL_KDUKEY = '" . $KD_WORKFLOWS_LOG['WFL_KDUKEY'] . "' 
				ORDER BY WFL_KEY ASC";

	// if (isset($arCtl['Run'])) {

	//	 echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	$arCtl ['count'] = 0;
	while ( $qd = mysqli_fetch_assoc ( $result ) ) {
		$arKD_WORKFLOWS_LOG [] = $qd;
		$arCtl ['count'] ++;
	}
	// }

	/**
	 * **********************************************************************************************
	 * Assign Template Variables
	 * **********************************************************************************************
	 */
	$smarty = getSmarty ();

	// results info
	$smarty->assign ( 'arKD_WORKFLOWS_LOG', $arKD_WORKFLOWS_LOG );
	$smarty->assign ( 'SESSION', $_SESSION );
	$smarty->assign ( 'arCtl', $arCtl );

	$smarty->display ( 'keydex/ListWorkFlowLog.tpl' );
}


/**
 * *******************************************************************
 *
 * Update \ Create New Parts
 *
 * ******************************************************************
 */
function UpdDocWorkflow($arCtl, $arSAFE_REQUEST) {

	$KD_WORKFLOWS = $arSAFE_REQUEST['KD_WORKFLOWS'];


	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	if ($KD_WORKFLOWS ['WF_KEY'] != "") {
		
		$query = "UPDATE KD_WORKFLOWS SET
					WF_DDKEY = '" . $KD_WORKFLOWS ['WF_DDKEY'] . "',
					WF_NAME = '" . $KD_WORKFLOWS ['WF_NAME'] . "',
					WF_STATUS_INDEX_NO = '" . $KD_WORKFLOWS ['WF_STATUS_INDEX_NO'] . "',
					WF_DESCRIPTION = '" . $KD_WORKFLOWS ['WF_DESCRIPTION'] . "'
					WHERE WF_KEY = '" . $KD_WORKFLOWS ['WF_KEY'] . "'";

		$result = mysqli_query ( $mysqli, $query );
		if (! $result)
			error_message ( sql_error () );
		
	} else {
		
		$query = "INSERT KD_WORKFLOWS (
					WF_DDKEY,
					WF_NAME,
					WF_STATUS_INDEX_NO,
					WF_DESCRIPTION)
					 VALUES (
					'" . $KD_WORKFLOWS ['WF_DDKEY'] . "',
					'" . $KD_WORKFLOWS ['WF_NAME'] . "',
					'" . $KD_WORKFLOWS ['WF_STATUS_INDEX_NO'] . "',
					'" . $KD_WORKFLOWS ['WF_DESCRIPTION'] . "')";
		
		$result = mysqli_query ( $mysqli, $query );
		if (! $result)
			error_message ( sql_error () );
		$KD_WORKFLOWS ['WF_KEY'] = mysqli_insert_id ( $mysqli );
	}
	
	header("location:" . getAdminCommand());
		
}
function UpdDocWorkflowSteps($arCtl, $arSAFE_REQUEST) {

	$KD_WORKFLOWS = $arSAFE_REQUEST['KD_WORKFLOWS'];
	$KD_WORKFLOWS_STEPS = $arSAFE_REQUEST['KD_WORKFLOWS_STEPS'];
	
	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	if ($KD_WORKFLOWS_STEPS ['WFS_KEY'] != "") {
		
		$query = "UPDATE KD_WORKFLOWS_STEPS SET
					WFS_SEQ = '" . $KD_WORKFLOWS_STEPS ['WFS_SEQ'] . "',
					WFS_DESCRIPTION = '" . $KD_WORKFLOWS_STEPS ['WFS_DESCRIPTION'] . "',
					WFS_SUCCESSFUNCTION = '" . $KD_WORKFLOWS_STEPS ['WFS_SUCCESSFUNCTION'] . "',
					WFS_FAILFUNCTION = '" . $KD_WORKFLOWS_STEPS ['WFS_FAILFUNCTION'] . "',
					WFS_STATUS = '" . $KD_WORKFLOWS_STEPS ['WFS_STATUS'] . "'
					WHERE WFS_KEY = '" . $KD_WORKFLOWS_STEPS ['WFS_KEY'] . "'";
		//echo $query;
		
		$result = mysqli_query ( $mysqli, $query );
		if (! $result)
			error_message ( sql_error () );
	} else {
		
		$query = "INSERT KD_WORKFLOWS_STEPS (
					WFS_WFKEY,
					WFS_SEQ,
					WFS_DESCRIPTION,
					WFS_FAILFUNCTION,
					WFS_SUCCESSFUNCTION,
					WFS_STATUS)
					 VALUES (
					'" . $KD_WORKFLOWS_STEPS ['WFS_WFKEY'] . "',
					'" . $KD_WORKFLOWS_STEPS ['WFS_SEQ'] . "',
					'" . $KD_WORKFLOWS_STEPS ['WFS_DESCRIPTION'] . "',
					'" . $KD_WORKFLOWS_STEPS ['WFS_FAILFUNCTION'] . "',
					'" . $KD_WORKFLOWS_STEPS ['WFS_SUCCESSFUNCTION'] . "',
					'" . $KD_WORKFLOWS_STEPS ['WFS_STATUS'] . "')";
		
		//echo $query;
		
		$result = mysqli_query ( $mysqli, $query );
		if (! $result)
			error_message ( sql_error () );
		$KD_WORKFLOWS_STEPS ['WFS_KEY'] = mysqli_insert_id ( $mysqli );
	}
	
	header("location:" . getAdminCommand());

}
function UpdDocWorkflowStepRules($arCtl , $arSAFE_REQUEST) {

	$KD_WORKFLOWS = $arSAFE_REQUEST['KD_WORKFLOWS'];
	$KD_WORKFLOWS_STEPS = $arSAFE_REQUEST['KD_WORKFLOWS_STEPS'];
	$KD_WORKFLOWS_STEPS_RULES = $arSAFE_REQUEST['KD_WORKFLOWS_STEPS_RULES'];
	
	// DB Connection
	$mysqli = db_connect ( getDBName () );

	if ($KD_WORKFLOWS_STEPS_RULES ['WFSR_KEY'] != "") {

		$query = "UPDATE KD_WORKFLOWS_STEPS_RULES SET
					WFSR_SOURCE1 = '" . $KD_WORKFLOWS_STEPS_RULES ['WFSR_SOURCE1'] . "',
					WFSR_OPERATOR = '" . mysqli_real_escape_string($mysqli,$KD_WORKFLOWS_STEPS_RULES ['WFSR_OPERATOR']) . "',
					WFSR_SOURCE2 = '" . $KD_WORKFLOWS_STEPS_RULES ['WFSR_SOURCE2'] . "',
					WFSR_JOIN = '" . $KD_WORKFLOWS_STEPS_RULES ['WFSR_JOIN'] . "'
					WHERE WFSR_KEY = '" . $KD_WORKFLOWS_STEPS_RULES ['WFSR_KEY'] . "'";

		//echo $query;
		
		$result = mysqli_query ( $mysqli, $query );
		if (! $result)
			error_message ( sql_error () );
	} else {

		$query = "INSERT KD_WORKFLOWS_STEPS_RULES (
					WFSR_WFSKEY,
					WFSR_SOURCE1,
					WFSR_OPERATOR,
					WFSR_SOURCE2,
					WFSR_JOIN )
					 VALUES (
					'" . $KD_WORKFLOWS_STEPS_RULES ['WFSR_WFSKEY'] . "',
					'" . $KD_WORKFLOWS_STEPS_RULES ['WFSR_SOURCE1'] . "',
					'" . mysqli_real_escape_string($mysqli,$KD_WORKFLOWS_STEPS_RULES ['WFSR_OPERATOR']) . "',
					'" . $KD_WORKFLOWS_STEPS_RULES ['WFSR_SOURCE2'] . "',
					'" . $KD_WORKFLOWS_STEPS_RULES ['WFSR_JOIN'] . "')";
		
		//echo $query;

		$result = mysqli_query ( $mysqli, $query );
		if (! $result)
			error_message ( sql_error () );
		$KD_WORKFLOWS_STEPS_RULES ['WFSR_KEY'] = mysqli_insert_id ( $mysqli );
	}

	// echo $query;
	header("location:" . getAdminCommand());
}

function UpdWorkflowLinkedDoc($arCtl , $arSAFE_REQUEST) {

	$KD_WORKFLOWS = $arSAFE_REQUEST['KD_WORKFLOWS'];
	$KD_WORKFLOWS_LINKED_DOCTYPES = $arSAFE_REQUEST['KD_WORKFLOWS_LINKED_DOCTYPES'];

	// DB Connection
	$mysqli = db_connect ( getDBName () );

	if ($KD_WORKFLOWS_LINKED_DOCTYPES ['WFLD_KEY'] != "") {
		$query = "UPDATE KD_WORKFLOWS_LINKED_DOCTYPES SET
					WFLD_LINK_KEY = '" . $KD_WORKFLOWS_LINKED_DOCTYPES ['WFLD_LINK_KEY'] . "',
					WFLD_LINK_KEY_FROM = '" . $KD_WORKFLOWS_LINKED_DOCTYPES ['WFLD_LINK_KEY_FROM'] . "'
					WHERE WFLD_KEY = '" . $KD_WORKFLOWS_LINKED_DOCTYPES ['WFLD_KEY'] . "'";
		
		$result = mysqli_query ( $mysqli, $query );
		if (! $result)
			error_message ( sql_error () );
	} else {
		$query = "INSERT KD_WORKFLOWS_LINKED_DOCTYPES (
				WFLD_WFKEY,
				WFLD_LINK_KEY,
				WFLD_LINK_KEY_FROM,
				WFLD_DDKEY)
				 VALUES (
				'" . $KD_WORKFLOWS_LINKED_DOCTYPES ['WFLD_WFKEY'] . "',
				'" . $KD_WORKFLOWS_LINKED_DOCTYPES ['WFLD_LINK_KEY'] . "',
				'" . $KD_WORKFLOWS_LINKED_DOCTYPES ['WFLD_LINK_KEY_FROM'] . "',
				'" . $KD_WORKFLOWS_LINKED_DOCTYPES ['WFLD_DDKEY'] . "')";

		$result = mysqli_query ( $mysqli, $query );
		if (! $result)
			error_message ( sql_error () );
		$KD_WORKFLOWS_LINKED_DOCTYPES ['WFLD_KEY'] = mysqli_insert_id ( $mysqli );
	}
	ShowDocWorkflow ( $arCtl, $KD_WORKFLOWS );

}

function UpdWorkflowLinkedParas($arCtl , $arSAFE_REQUEST) {

	$KD_WORKFLOWS = $arSAFE_REQUEST['KD_WORKFLOWS'];
	$KD_WORKFLOWS_LINKED_PARAS = $arSAFE_REQUEST['KD_WORKFLOWS_LINKED_PARAS'];

	// DB Connection
	$mysqli = db_connect ( getDBName () );

	if ($KD_WORKFLOWS_LINKED_PARAS ['WFLP_KEY'] != "") {

		$query = "UPDATE KD_WORKFLOWS_LINKED_PARAS SET
					WFLP_CODE_NAME = '" . $KD_WORKFLOWS_LINKED_PARAS ['WFLP_CODE_NAME'] . "',
					WFLP_VALUE_NAME = '" . $KD_WORKFLOWS_LINKED_PARAS ['WFLP_VALUE_NAME'] . "'
					WHERE WFLP_KEY = '" . $KD_WORKFLOWS_LINKED_PARAS ['WFLP_KEY'] . "'";

		$result = mysqli_query ( $mysqli, $query );
		if (! $result)
			error_message ( sql_error () );
	} else {

		$query = "INSERT KD_WORKFLOWS_LINKED_PARAS (
					WFLP_WFKEY,
					WFLP_WFPKEY,
					WFLP_CODE_NAME,
					WFLP_VALUE_NAME )
					 VALUES (
					'" . $KD_WORKFLOWS_LINKED_PARAS ['WFLP_WFKEY'] . "',
					'" . $KD_WORKFLOWS_LINKED_PARAS ['WFLP_WFPKEY'] . "',
					'" . $KD_WORKFLOWS_LINKED_PARAS ['WFLP_CODE_NAME'] . "',
					'" . $KD_WORKFLOWS_LINKED_PARAS ['WFLP_VALUE_NAME'] . "')";

		$result = mysqli_query ( $mysqli, $query );
		if (! $result)
			error_message ( sql_error () );
		$KD_WORKFLOWS_LINKED_PARAS ['WFLP_KEY'] = mysqli_insert_id ( $mysqli );
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
function DelDocWorkflow($arCtl, $arSAFE_REQUEST) {

	$KD_WORKFLOWS = $arSAFE_REQUEST['KD_WORKFLOWS'];
	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	$query = "DELETE FROM KD_WORKFLOWS WHERE WF_KEY = '" . $KD_WORKFLOWS ['WF_KEY'] . "'";
	// echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	
	header("location:" . getAdminCommand());
}
function DelDocWorkflowSteps($arCtl, $arSAFE_REQUEST) {

	$KD_WORKFLOWS = $arSAFE_REQUEST['KD_WORKFLOWS'];
	$KD_WORKFLOWS_STEPS = $arSAFE_REQUEST['KD_WORKFLOWS_STEPS'];
	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	$query = "DELETE FROM KD_WORKFLOW_STEPS WHERE WFS_KEY = '" . $KD_WORKFLOWS_STEPS ['WFS_KEY'] . "'";
	// echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	
	header("location:" . getAdminCommand());
}
	
function DelWorkflowLinkedDoc($arCtl, $arSAFE_REQUEST) {

	$KD_WORKFLOWS = $arSAFE_REQUEST['KD_WORKFLOWS'];
	$KD_WORKFLOWS_LINKED_DOCTYPES = $arSAFE_REQUEST['KD_WORKFLOWS_LINKED_DOCTYPES'];
	
	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	$query = "DELETE FROM KD_WORKFLOWS_LINKED_DOCTYPES WHERE WFLD_KEY = '" . $KD_WORKFLOWS_LINKED_DOCTYPES ['WFLD_KEY'] . "'";
	// echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	
	header("location:" . getAdminCommand());
}
function DelWorkflowLinkedParas($arCtl, $arSAFE_REQUEST) {

	$KD_WORKFLOWS = $arSAFE_REQUEST['KD_WORKFLOWS'];
	$KD_WORKFLOWS_LINKED_PARAS = $arSAFE_REQUEST['KD_WORKFLOWS_LINKED_PARAS'];
		

	// DB Connection
	$mysqli = db_connect ( getDBName () );

	$query = "DELETE FROM KD_WORKFLOWS_LINKED_PARAS WHERE WFLP_KEY = '" . $KD_WORKFLOWS_LINKED_PARAS ['WFLP_KEY'] . "'";
	// echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );

	header("location:" . getAdminCommand());
}
	
function checkWorkflow($arCtl,$KD_DOCUMENT){

	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
 	//print_r($KD_DOCUMENTS);
	
	// check if doc in workflow
	$sql = "select * from KD_WORKFLOWS, KD_DOCUMENT where WF_DDKEY = KDU_DDKEY and KDU_KEY = " . $KD_DOCUMENT['KDU_KEY'];
	//echo $sql;
	$result = mysqli_query ( $mysqli, $sql );
	if (! $result)
		error_message ( sql_error () );
	$KD_DOCUMENT = mysqli_fetch_assoc($result);
	
	print_r($KD_DOCUMENT);
	
	// get data model associated to this doc
	$arWFData = getWorkflowData($KD_DOCUMENT);
	
	print_r($arWFData);
	
	if (isset($KD_DOCUMENT['KDU_KEY'])){
		// if yes check if complete
		$sql = "select * from KD_WORKFLOWS_STEPS where WFS_WFKEY = " . $KD_DOCUMENT['WF_KEY'] . " order by WFS_SEQ DESC LIMIT 1";
		echo $sql;
		$result = mysqli_query ( $mysqli, $sql );
		if (! $result)
			error_message ( sql_error () );
		$LAST_KD_WORKFLOWS_STEPS = mysqli_fetch_assoc($result);
		
		print_r($LAST_KD_WORKFLOWS_STEPS);
		
		if ($LAST_KD_WORKFLOWS_STEPS['WFS_STATUS'] == $KD_DOCUMENT['KDU_INDEX' . $KD_DOCUMENT['KDU_WF_INDEX_NO']]){
			// then complete
		} else {
			// if not process workflow
			processWorkflow($arWFData, $arCtl,$KD_DOCUMENT);		
		}
	}
	
	
}
function processWorkflow($arWFData,$arCtl,$KD_DOCUMENT){

	// DB Connection
	$mysqli = db_connect ( getDBName () );
	
	//echo "process workflow \n";
	
	$sql = "select * from KD_WORKFLOWS_STEPS where WFS_WFKEY = " . $KD_DOCUMENT['WF_KEY'] . " order by WFS_SEQ ASC";
	echo $sql;
	$result = mysqli_query ( $mysqli, $sql );
	if (! $result)
		error_message ( sql_error () );
	$actionRules = false;
	while($qd = mysqli_fetch_assoc($result)){

		if ($actionRules){
			$arKD_WORKFLOWS_STEPS[] = $qd;
		}
		
//		print_r($qd);
		// find out where the status is and remove any previous steps	
		if ($qd['WFS_STATUS'] == $KD_DOCUMENT['KDU_INDEX' . $KD_DOCUMENT['WF_STATUS_INDEX_NO']]){
			// so action next step of rules
			$arKD_WORKFLOWS_STEPS[] = $qd;
			$actionRules = true;
		} 		
	}	

	
	// now evaluate
	$actionRules = false;
	foreach ($arKD_WORKFLOWS_STEPS as $key => $KD_WORKFLOWS_STEPS){

	//	echo $KD_WORKFLOWS_STEPS['WFS_SEQ'] . " for status " . $KD_WORKFLOWS_STEPS['WFS_STATUS'] . "<BR>";
		
		if ($actionRules){

			$actionRules = false;
				
			// action rules for step
			$sql = "select * from KD_WORKFLOWS_STEPS_RULES where WFSR_WFSKEY = " . $KD_WORKFLOWS_STEPS['WFS_KEY'];
// 			echo $sql;
			$result2 = mysqli_query ( $mysqli, $sql );
			if (! $result2) error_message ( sql_error () );
			$arRules = array();
			while($qd2 = mysqli_fetch_assoc($result2)){
				$arRules[] = $qd2;
			}

			//print_r($qd2);
			$arCtl['WFL_DESCRIPTION'] = "Evaluating seq " . $KD_WORKFLOWS_STEPS['WFS_SEQ'] . " for status " . $KD_WORKFLOWS_STEPS['WFS_STATUS'] . "(" . $KD_WORKFLOWS_STEPS['WFS_DESCRIPTION'] . ")";
				
			$arResult = evaluateRules($arWFData,$arRules,$KD_DOCUMENT);

// 			print_r($arResult);
// 			exit();
			
			if ($arResult['Status'] == "OK"){
				// update current doc
				$KD_DOCUMENT['KDU_INDEX' . $KD_DOCUMENT['WF_STATUS_INDEX_NO']] = $KD_WORKFLOWS_STEPS['WFS_STATUS'];
				
				// update doc status
				$sql = "UPDATE KD_DOCUMENT set KDU_INDEX" . $KD_DOCUMENT['WF_STATUS_INDEX_NO'] . " = '" . $KD_WORKFLOWS_STEPS['WFS_STATUS'] .  "' where KDU_KEY = " . $KD_DOCUMENT['KDU_KEY'];
				echo $sql;
				$result3 = mysqli_query ( $mysqli, $sql );
				if (! $result3) error_message ( sql_error () );

				// update keystore
				$arCtl['Status'] = $KD_WORKFLOWS_STEPS['WFS_STATUS'];
				DocUpdate($arCtl,$KD_DOCUMENT);
				
				// check success action
				if ($KD_WORKFLOWS_STEPS['WFS_SUCCESSFUNCTION'] != ""){
					$arCtl['Message'] = "Document " . $KD_DOCUMENT['KDU_KEY'] . " successfully updated to status " . $KD_WORKFLOWS_STEPS['WFS_STATUS'];
					$actionResult = $KD_WORKFLOWS_STEPS['WFS_SUCCESSFUNCTION']($arCtl,$KD_WORKFLOWS_STEPS,$KD_DOCUMENT);	
					$arCtl['WFL_DESCRIPTION'] = "Updated to Status " . $KD_WORKFLOWS_STEPS['WFS_STATUS'] . "  and ran action " . $KD_WORKFLOWS_STEPS['WFS_SUCCESSFUNCTION'] . " Details: <br>" . $arResult['Workings'] . "<br>";
				} else {
					$arCtl['WFL_DESCRIPTION'] = "Updated to Status " . $KD_WORKFLOWS_STEPS['WFS_STATUS'] . "  Details: <br>" . $arResult['Workings'] . "<br>";
				}
				
				// update WF log
				$arCtl['WFL_TYPE'] = "ProcessRule";
				$arCtl['WFL_SUMMARY'] = "Evaluating doc no " . $KD_DOCUMENT['KDU_KEY'] . " and successfully moved to  " . $KD_WORKFLOWS_STEPS['WFS_STATUS'];
				UpdWorkflowLog($arCtl,$KD_DOCUMENT);
				
			} else {

				// check failure action
				if ($KD_WORKFLOWS_STEPS['WFS_FAILFUNCTION'] != ""){
					$arCtl['WFL_DESCRIPTION'] = "Not updated to status " . $KD_WORKFLOWS_STEPS['WFS_STATUS'] . "  and ran action " . $KD_WORKFLOWS_STEPS['WFS_FAILUREFUNCTION'] . " Details: <br>" . $arResult['Workings'] . "<br>";
					$arCtl['Message'] = "Document " . $KD_DOCUMENT['KDU_KEY'] . " not updated to status " . $KD_WORKFLOWS_STEPS['WFS_STATUS'];
					$actionResult = $KD_WORKFLOWS_STEPS['WFS_FAILFUNCTION']($arCtl,$KD_WORKFLOWS_STEPS,$KD_DOCUMENT);
				} else {
					$arCtl['WFL_DESCRIPTION'] = "Not updated to status " . $KD_WORKFLOWS_STEPS['WFS_STATUS'] . "  Details: <br>" . $arResult['Workings'] . "<br>";
				}						
					
				// update log and leave
				$arCtl['WFL_TYPE'] = "ProcessRule";
				$arCtl['WFL_SUMMARY'] = "Evaluating doc no " . $KD_DOCUMENT['KDU_KEY'] . " for " . $KD_WORKFLOWS_STEPS['WFS_NEXTACTION'] . " unsuccessfull! ";
				UpdWorkflowLog($arCtl,$KD_DOCUMENT);
				// exit so stop evaluating
				break;
			}

		}
		
		// check doc status
		if ($KD_WORKFLOWS_STEPS['WFS_STATUS'] == $KD_DOCUMENT['KDU_INDEX' . $KD_DOCUMENT['WF_STATUS_INDEX_NO']]){
			// so action next step of rules
			$actionRules = true;
			//echo "true so action rules";
		} else {
			//echo "not true";
		}
		
	}
	
}	

function evaluateRules($arWFData,$arRules,$KD_DOCUMENTS) {


	$arOperatorMapping['EQ'] = "==";
	$arOperatorMapping['NEQ'] = "!=";
	
	//echo "evaluate rules \n";
	
	// get rule info
	$Rules=0;
	foreach($arRules as $key => $arRule){
		// add in syntax to array
		$arRule['WFSR_SOURCE1'] = str_replace("[","['",$arRule['WFSR_SOURCE1']);
		$arRule['WFSR_SOURCE1'] = str_replace("]","']",$arRule['WFSR_SOURCE1']);
		$arRule['WFSR_SOURCE2'] = str_replace("[","['",$arRule['WFSR_SOURCE2']);
		$arRule['WFSR_SOURCE2'] = str_replace("]","']",$arRule['WFSR_SOURCE2']);
		
		$ruleToEval .= "\$arWFData" . $arRule['WFSR_SOURCE1'] . " " . $arOperatorMapping[$arRule['WFSR_OPERATOR']] . " " . "\$arWFData" . $arRule['WFSR_SOURCE2'] . " " . $arRule['WFSR_JOIN'] . " ";
		
		$Rules++;
	}

	if ($Rules == 0){
		// no wrules so no processing
		$Status = "No Rules";
		
	} else {
		$evalStatment = "return(" . $ruleToEval . ");";
		
		echo eval($evalStatment);
		
		if(eval($evalStatment)){
			$Status = "OK";
		} else {
			$Status = "Not OK";
		}
	}
	
	ob_start();
	echo "<br>Data Model --- ";	
	print_r($arWFData);
	echo "<br>Rule --- ";
	echo $ruleToEval;
	echo "<br>Status --- ";
	echo $Status;
	$arResult['Workings'] = ob_get_contents();
	ob_end_clean();

	$arResult['Status'] = $Status;
	
	return $arResult;
	
}

function getWorkflowData($KD_DOCUMENT) {

	// DB Connection
	$mysqli = db_connect ( getDBName () );

	// get source doc
	$query = "SELECT *
				FROM KD_DOCUMENT
				WHERE KDU_KEY = '" . $KD_DOCUMENT ['KDU_KEY']. "'";
	
	//echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)	error_message ( sql_error () );
	$KD_DOCUMENT = mysqli_fetch_assoc ( $result );

	// get source doc
	$query = "SELECT *
				FROM KD_WORKFLOWS
				WHERE WF_DDKEY = '" . $KD_DOCUMENT ['KDU_DDKEY']. "'";
	
	//echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)	error_message ( sql_error () );
	$KD_WORKFLOWS = mysqli_fetch_assoc ( $result );
	
	
	// get workflow details
	$query = "SELECT *
				FROM KD_DOCDEF,KD_DOCDEF_KEYS
				WHERE DDK_DDKEY = DD_KEY
				AND DD_KEY = '" . $KD_DOCUMENT ['KDU_DDKEY']. "'
				ORDER BY DDK_KEY";
	
	// echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)	error_message ( sql_error () );
	while ( $qd = mysqli_fetch_assoc ( $result ) ) {
		$arWfData['DOC'][$qd['DD_KEYSTORE_DOCTYPE']][$qd['DDK_KEYSTORE_NAME']] = $KD_DOCUMENT['KDU_INDEX'. $qd['DDK_KEYSTORE_KEYORDER']];
	}
	

	// get linked docs
	$query = "SELECT *
				FROM KD_WORKFLOWS_LINKED_DOCTYPES, KD_DOCDEF,KD_DOCDEF_KEYS
				WHERE DD_KEY = WFLD_DDKEY
				AND DDK_DDKEY = DD_KEY
				AND WFLD_WFKEY = '" . $KD_WORKFLOWS ['WF_KEY']. "'";
	
// 	 echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	while ( $qd = mysqli_fetch_assoc ( $result ) ) {
		// get KD_DOUCMENT
		$sql = "select * from KD_DOCUMENT 
				where KDU_DDKEY = '" . $qd['WFLD_DDKEY'] . "' and KDU_INDEX" . $qd['WFLD_LINK_KEY_FROM'] . " = '" . $KD_DOCUMENT['KDU_INDEX'. $qd['WFLD_LINK_KEY']]. "'";  
// 		echo $sql;
		$result2 = mysqli_query ( $mysqli, $sql );
		if (! $result2)
			error_message ( sql_error () );
		$LINKED_KD_DOCUMENT = mysqli_fetch_assoc ( $result2 );
		// get fields
		$arWfData['DOC'][$qd['DD_KEYSTORE_DOCTYPE']][$qd['DDK_KEYSTORE_NAME']] = $LINKED_KD_DOCUMENT['KDU_INDEX'. $qd['DDK_KEYSTORE_KEYORDER']];

		$arWfData['DOCLIST'][] = $LINKED_KD_DOCUMENT;
		
	}
	
	
	// get para docs
	

	$query = "SELECT *
				FROM KD_WORKFLOWS_LINKED_PARAS , KD_WORKFLOWS_PARAS
				WHERE WFP_KEY = WFLP_WFPKEY 
				AND WFLP_WFKEY = '" . $KD_WORKFLOWS ['WF_KEY']. "'";
	
	//echo $query;
	$result = mysqli_query ( $mysqli, $query );
	if (! $result)
		error_message ( sql_error () );
	$count=0;
	while ( $qd = mysqli_fetch_assoc ( $result ) ) {
		// get KD_PARA VALUE
		$sql = "select * from KD_WORKFLOWS_PARAS_ITEMS
				where WPI_WFPKEY = '" . $qd['WFLP_WFPKEY'] . "' and WPI_CODE = '" . $KD_DOCUMENT['KDU_INDEX'. $qd['WFLP_CODE_NAME']]. "'";
		//echo $sql;
		$result2 = mysqli_query ( $mysqli, $sql );
		if (! $result2)
			error_message ( sql_error () );
		$LINKED_WORKFLOW_PARA = mysqli_fetch_assoc ( $result2 );
	
		// get fields
		$arWfData['PARA'][$qd['WFP_DESCRIPTION']] = $LINKED_WORKFLOW_PARA['WPI_VALUE'];
		$arWfData['PARALIST'][$count] = $LINKED_WORKFLOW_PARA;
		$arWfData['PARALIST'][$count]['TITLE'] = $qd['WFP_DESCRIPTION'];
		$count++;
	}
	
	// add constants
	
	$arWfData['CON']['BLANK'] = "";

	return $arWfData;

}

/**********************************************************************************************
 * 
 * success and fail function for workflow steps
 * 
 **********************************************************************************************/

function EmailDocOwner($arCtl,$KD_WORKFLOWS_STEPS,$KD_DOCUMENT) {
	
	// DB Connection
	$mysqli = db_connect ( getDBName () );	
	
	// find owner by looking at doc
	$sql = "select * from KD_DOCUMENT, USERS where KDU_USKEY = US_KEY AND KDU_KEY=" . $KD_DOCUMENT['KDU_KEY'];
	$result2 = mysqli_query ( $mysqli, $sql );
	if (! $result2)
		error_message ( sql_error () );
	$DOCUMENT_USERS = mysqli_fetch_assoc ( $result2 );

	$sql = "select * from KD_WORKFLOW where WF_KEY = " . $KD_WORKFLOW_STEPS['WFS_WFKEY'];
	$result2 = mysqli_query ( $mysqli, $sql );
	if (! $result2)
		error_message ( sql_error () );
	$KD_WORKFLOW = mysqli_fetch_assoc ( $result2 );
	
	$email = $DOCUMENT_USERS['US_EMAIL'];
	$subject = " Document Owner Alert for " . $KD_WORKFLOW['WF_DESCRIPTION'];
	$body = $arCtl['Message'];
	
	mail($email,$subject,$body);
	
}

function EmailProcessOwner($arCtl,$KD_WORKFLOWS_STEPS,$KD_DOCUMENT) {

	// DB Connection
	$mysqli = db_connect ( getDBName () );

	$sql = "select * from KD_WORKFLOWS, USERS_WORKFLOWS where USF_WFKEY = WF_KEY AND USF_PROCESSOWNER = 'Y' AND WF_KEY = " . $KD_WORKFLOWS_STEPS['WFS_WFKEY'];
	$result2 = mysqli_query ( $mysqli, $sql );
	if (! $result2)
		error_message ( sql_error () );
	$KD_WORKFLOW = mysqli_fetch_assoc ( $result2 );
	
	$email = $DOCUMENT_USERS['US_EMAIL'];
	$subject = " Workflow Owner Alert for " . $KD_WORKFLOW['WF_DESCRIPTION'];
	$body = $arCtl['Message'];
	
	mail($email,$subject,$body);

}
?>
