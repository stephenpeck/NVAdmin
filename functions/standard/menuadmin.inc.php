<?php
/**
 * Menu Admin
 */

/**
 * List Menu Groups
 */

function ListMenuGroup($arCtl,$REQUEST){
	/************************************************************************************************
	Get Data
	***********************************************************************************************/
	// DB Connection
	$db = db_connect(getConfigDBName()); 

	$sql  = "SELECT * FROM MENU_GROUP ORDER BY MG_SEQ, MG_NAME";	
	
	$result = mysqli_query($db,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__); 
	
	while($qd = mysqli_fetch_assoc($result)){
		$arMENU_GROUP[] = $qd;	
	}
	
	//Building Yes/No drop down menu
	$arYesNo['N'] = "N";
	$arYesNo['Y'] = "Y";
	
	
	/************************************************************************************************
	Assign Template Variables
	***********************************************************************************************/
	$smarty = getSmarty();

	// results info
	$smarty->assign('arMENU_GROUP',  $arMENU_GROUP);
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('arYesNo',    $arYesNo);
	$smarty->assign('DateTime', date("d/m/Y"));
	$smarty->assign('SESSION',   $_SESSION);
	
	$smarty->display('admin/ListMenuGroup.tpl');

}

/**
 * Show Menu Groups
 */

function ShowMenuGroup($arCtl, $REQUEST){
	/************************************************************************************************
	Get Data
	***********************************************************************************************/
	// DB Connection
	$db = db_connect(getConfigDBName()); 
	
	
	$MENU_GROUP = $REQUEST['MENU_GROUP'];
	
	$sql  = "SELECT * FROM MENU_GROUP where MG_KEY = '" . $MENU_GROUP['MG_KEY'] . "'";	
// 	echo $sql;
	
	$result = mysqli_query($db,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__); 
	$MENU_GROUP = mysqli_fetch_assoc($result);

	// if there user is selected then go and get menu items
	if ($MENU_GROUP['MG_KEY'] != ""){
		$sql = "select * from MENU_GROUP, MENU_ITEMS where MG_KEY = MI_MGKEY and MG_KEY = '" . $MENU_GROUP['MG_KEY'] . "'";
		$result = mysqli_query($db,$sql);
		if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__); 
		while ($qd = mysqli_fetch_assoc($result)){
			$arMENU_ITEMS[] = $qd;
			$arExistingMenu[$qd['MI_KEY']] = $qd['MI_NAME'];
		}
	}
	
	//Building Yes/No drop down menu
	$arYesNo['Y'] = "Y";
	$arYesNo['N'] = "N";
	
	/************************************************************************************************
	Assign Template Variables
	***********************************************************************************************/
	$smarty = getSmarty();

	// results info
	$smarty->assign('MENU_GROUP',  $MENU_GROUP);
	$smarty->assign('arMENU_ITEMS',  $arMENU_ITEMS);
	$smarty->assign('arYesNo',    $arYesNo);
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('SESSION',   $_SESSION);

	$smarty->display('admin/ShowMenuGroup.tpl');
		
}

function getMenuDetails($MI_KEY){
	/************************************************************************************************
	 Get Data
	 ***********************************************************************************************/
	// DB Connection
	$db = db_connect(getConfigDBName());

	$sql  = "SELECT * FROM MENU_ITEMS where MI_KEY = '" . $MI_KEY . "'";
	// 	echo $sql;

	$result = mysqli_query($db,$sql);
	if (!$result) errormessage(sql_error, $sql, NULL, __LINE__, __FILE__);
	$MENU_ITEMS = mysqli_fetch_assoc($result);

	if ($MENU_ITEMS['MN_SCRIPT'] == ""){
		$MENU_ITEMS['MN_SCRIPT'] = "ShowHome";
	}
	
	return $MENU_ITEMS;
}

/**
 * Update Menu Group
 */
function UpdMenuGroup($arCtl, $REQUEST){
	// DB Connection
	$mysqli = db_connect(getConfigDBName());

	$MENU_GROUP = $REQUEST['MENU_GROUP'];

	if ($MENU_GROUP['MG_KEY']!= "") {

		$query = "UPDATE MENU_GROUP SET
					MG_NAME = '" . $MENU_GROUP['MG_NAME'] . "',
					MG_SEQ = '" . $MENU_GROUP['MG_SEQ'] . "',
					MG_DISPLAY = '" . $MENU_GROUP['MG_DISPLAY'] . "'					
					WHERE MG_KEY = '" . $MENU_GROUP['MG_KEY'] . "'";
		$result = mysqli_query($mysqli,$query);
		if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__); 

	} else {

		$query = "INSERT MENU_GROUP (
					MG_NAME,
					MG_SEQ,
					MG_DISPLAY)
					 VALUES (
					'" . $MENU_GROUP['MG_NAME'] . "',
					'" . $MENU_GROUP['MG_SEQ'] . "',
					'" . $MENU_GROUP['MG_DISPLAY'] . "')";

		$result = mysqli_query($mysqli,$query);
		if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__); 
		$_SESSION['DB']['MENU_GROUP']['MG_KEY'] = mysqli_insert_id($mysqli);
	}


// 	echo $query;
	
	// now redirect (action in session)
	header("location:" . getAdminCommand());
	
} 

/**
 * Delete Menu Group and their Menu Items
 */


function DelMenuGroup($arCtl, $REQUEST){
	// DB Connection
	$mysqli = db_connect(getConfigDBName());

	$MENU_GROUP = $REQUEST['MENU_GROUP'];
	$MENU_ITEMS = $REQUEST['MENU_ITEMS'];
	
	$query = "DELETE FROM MENU_GROUP WHERE MG_KEY = '" . $MENU_GROUP['MG_KEY'] . "'";
	echo $query;
	$result = mysqli_query($mysqli,$query);
	if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__); 
	

	$query = "DELETE FROM MENU_ITEMS WHERE MI_MGKEY = '" . $MENU_GROUP['MG_KEY'] . "'";
	echo $query;
	$result = mysqli_query($mysqli,$query);
	if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__); 	
	
	// now redirect (action in session)
	header("location:" . getAdminCommand());

} 

/**
 * Update Menu Items
 */

function UpdMenuItems($arCtl,$REQUEST){
	// DB Connection
	$mysqli = db_connect(getConfigDBName()); 

	$MENU_GROUP = $REQUEST['MENU_GROUP'];
	$MENU_ITEMS = $REQUEST['MENU_ITEMS'];
	
	if ($MENU_ITEMS['MI_KEY'] != "") {

		$query = "UPDATE MENU_ITEMS SET
					MI_NAME = '" . $MENU_ITEMS['MI_NAME'] . "',
					MI_SCRIPT = '" . $MENU_ITEMS['MI_SCRIPT'] . "',
					MI_BACKSCRIPT = '" . $MENU_ITEMS['MI_BACKSCRIPT'] . "',
					MI_NEXTSCRIPT = '" . $MENU_ITEMS['MI_NEXTSCRIPT'] . "',
					MI_LEVEL = '" . $MENU_ITEMS['MI_LEVEL'] . "',
					MI_ORDER = '" . $MENU_ITEMS['MI_ORDER'] . "',
					MI_DISPLAY = '" . $MENU_ITEMS['MI_DISPLAY'] . "'
					WHERE MI_KEY = '" . $MENU_ITEMS['MI_KEY'] . "'";
		$result = mysqli_query($mysqli,$query);
		if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__); 

	} else {

		$query = "INSERT MENU_ITEMS (
					MI_MGKEY,
					MI_NAME,
					MI_SCRIPT,
					MI_NEXTSCRIPT,
					MI_BACKSCRIPT,
					MI_LEVEL,
					MI_ORDER,
					MI_DISPLAY ) 
					VALUES (
					'" . $MENU_GROUP['MG_KEY'] . "',
					'" . $MENU_ITEMS['MI_NAME'] . "',
					'" . $MENU_ITEMS['MI_SCRIPT'] . "',
					'" . $MENU_ITEMS['MI_NEXTSCRIPT'] . "',
					'" . $MENU_ITEMS['MI_BACKSCRIPT'] . "',
					'" . $MENU_ITEMS['MI_LEVEL'] . "',
					'" . $MENU_ITEMS['MI_ORDER'] . "',
					'" . $MENU_ITEMS['MI_DISPLAY'] . "')";
		
		$result = mysqli_query($mysqli,$query);
		if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__); 
		$MENU_ITEMS['MI_KEY'] = mysqli_insert_id($mysqli);	
	}

// 	echo $query;
	
	// now redirect (action in session)
	header("location:" . getAdminCommand());
	
}

/**
 * Delete Menu Items
 */

function DelMenuItems($arCtl, $REQUEST){
	// DB Connection
	$mysqli = db_connect(getConfigDBName()); 

	$MENU_GROUP = $REQUEST['MENU_GROUP'];
	$MENU_ITEMS = $REQUEST['MENU_ITEMS'];
	
	$query = "DELETE FROM MENU_ITEMS WHERE MI_KEY = '" . $MENU_ITEMS['MI_KEY'] . "'";
//	echo $query;
	$result = mysqli_query($mysqli,$query);
	if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__); 

	// now redirect (action in session)
	header("location:" . getAdminCommand());

} 



?>
