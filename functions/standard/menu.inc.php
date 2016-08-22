<?php

/**
 * Display appropriate menu

/**
 * List Menu Groups
*/

function ListMenuGroup($arCtl){
	/************************************************************************************************
	 Get Data
	 ***********************************************************************************************/
	// DB Connection
	$db = db_connect();

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

	$smarty->display('ListMenuGroup.tpl');

}

/**
 * Show Menu Groups
 */

function ShowMenuGroup($arCtl, $MENU_GROUP){
	/************************************************************************************************
	 Get Data
	 ***********************************************************************************************/
	// DB Connection
	$db = db_connect();

	$sql  = "SELECT * FROM MENU_GROUP where MG_KEY = '" . $MENU_GROUP['MG_KEY'] . "'";

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
	$smarty->assign('DateTime', date("d/m/Y"));
	$smarty->assign('SESSION',   $_SESSION);

	$smarty->display('ShowMenuGroup.tpl');

}



/**
 * Update Menu Group
 */
function UpdMenuGroup($arCtl, $MENU_GROUP){
	// DB Connection
	$mysqli = db_connect();

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
		$MENU_GROUP['MG_KEY'] = mysqli_insert_id($mysqli);
	}

	//echo $query;
	ShowMenuGroup($arCtl,$MENU_GROUP);
}

/**
 * Delete Menu Group and their Menu Items
 */


function DelMenuGroup($arCtl, $MENU_GROUP, $MENU_ITEMS){
	// DB Connection
	$mysqli = db_connect();

	$query = "DELETE FROM MENU_GROUP WHERE MG_KEY = '" . $MENU_GROUP['MG_KEY'] . "'";
	echo $query;
	$result = mysqli_query($mysqli,$query);
	if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__);


	$query = "DELETE FROM MENU_ITEMS WHERE MI_MGKEY = '" . $MENU_GROUP['MG_KEY'] . "'";
	//echo $query;
	$result = mysqli_query($mysqli,$query);
	if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__);


	ListMenuGroup($arCtl,$MENU_GROUP);
}

/**
 * Update Menu Items
 */

function UpdMenuItems($arCtl,$MENU_GROUP, $MENU_ITEMS){
	// DB Connection
	$mysqli = db_connect();

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

	//echo $query;
	ShowMenuGroup($arCtl,$MENU_GROUP);
}

/**
 * Delete Menu Items
 */

function DelMenuItems($arCtl, $MENU_GROUP, $MENU_ITEMS){
	// DB Connection
	$mysqli = db_connect();

	$query = "DELETE FROM MENU_ITEMS WHERE MI_KEY = '" . $MENU_ITEMS['MI_KEY'] . "'";
	//	echo $query;
	$result = mysqli_query($mysqli,$query);
	if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__);

	ShowMenuGroup($arCtl,$MENU_GROUP);
}



/**
 * Old Code
 */


function ListMenu($arCtl, $MENU){
	/************************************************************************************************
	 Get Data
	 ***********************************************************************************************/
	// DB Connection
	$mysqli = db_connect();

	$query = "SELECT * FROM MENU WHERE MN_MENU = '" . $arCtl['MN_MENU'] . "'";
	if(isset($arCtl['Run'])){
		$result = mysql_query($query); if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__);

		while($qd = mysql_fetch_array($result)){
			$arMENU[] = $qd;
		}
	}
	/************************************************************************************************
	 Build Drop Downs
	 ***********************************************************************************************/
	$query   = "SELECT MN_MENU FROM MENU GROUP BY MN_MENU";
	$result2 = mysql_query($query,$mysqli);
	if(!$result2) error_message(sql_error, $query, NULL, __LINE__, __FILE__);

	$arMenus[''] = 'Choose Menu';
	while($qd2 = mysql_fetch_assoc($result2)){
		$arMenus[$qd2['MN_MENU']] = $qd2['MN_MENU'];
	}
	/************************************************************************************************
	 Assign Template Variables
	 ***********************************************************************************************/
	$smarty = getSmarty();

	// results info
	$smarty->assign('arMENU',   $arMENU);
	$smarty->assign('MenuList', $arMenus);
	$smarty->assign('arCtl',    $arCtl);

	// contract info
	$smarty->assign('UserName', $_SESSION['SName']);
	$smarty->assign('DateTime', strftime('%c'));
	$smarty->assign('ProgName', 'ListMenu');
	$smarty->assign('SESSION',  $_SESSION);
	$smarty->assign('Menu',     $arCtl['Menu']);
	$smarty->assign('Title',    "Configuração Admin");
	$smarty->assign('DateTime', date("d/m/Y"));
	$smarty->assign('action',   $action);

	if(!isset($arCtl['PrevMenu'])){
		$arCtl['PrevMenu'] = $arCtl['Menu'];
	}

	$smarty->assign('prevmenu', $arCtl['PrevMenu']);
		
	$smarty->display('admin/ListMenu.tpl');
}

/**
 * Update \ Create New Parts
 **/
function UpdMenu($arCtl, $MENU){
	// DB Connection
	$mysqli = db_connect();

	if ($MENU['MN_KEY']!= "") {

		$query = "UPDATE MENU SET
					MN_ACTION = '" . $MENU['MN_ACTION'] . "',
					MN_ORDER = '" . $MENU['MN_ORDER'] . "',
					MN_SHOW = '" . $MENU['MN_SHOW'] . "',
					MN_SECGROUP = '" . $MENU['MN_SECGROUP'] . "',
					MN_SECLEVEL = '" . $MENU['MN_SECLEVEL'] . "'
					WHERE MN_KEY = '" . $MENU['MN_KEY'] . "'";

		$result = mysql_query($query); if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__);
	}else{
		$query = "INSERT MENU (
						MN_MENU,
						MN_NAME,
						MN_ORDER,
						MN_ACTION,
						MN_SHOW,
						MN_SECGROUP,
						MN_SECLEVEL
					) VALUES (
						'" . $MENU['MN_MENU'] . "',
						'" . $MENU['MN_NAME'] . "',
						'" . $MENU['MN_ORDER'] . "',
						'" . $MENU['MN_ACTION'] . "',
						'" . $MENU['MN_SHOW'] . "',
						'" . $MENU['MN_SECGROUP'] . "',
						'" . $MENU['MN_SECLEVEL'] . "'
					)";

		$result = mysql_query($query); if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__);
			
		$MENU['MN_KEY'] = mysql_insert_id($mysqli);
	}

	//	echo $query;

	ListMenu($arCtl,$MENU);
}

/**
 * Delete Menu Entry
 **/
function DelMenu($arCtl,$MENU){
	// DB Connection
	$mysqli = db_connect();

	$query  = "DELETE FROM MENU WHERE MN_KEY = '" . $MENU['MN_KEY'] . "'";
	$result = mysqli_query($mysqli,$query);
	if (!$result) error_message(sql_error, $query, NULL, __LINE__, __FILE__);

	ListMenu($arCtl, $MENU);
}

?>


?>
