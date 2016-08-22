<?php


function ListCPC($arCtl,$arSAFE_REQUEST) {
	
	$mysqli = db_connect(getBookingDBName());

	if ($arCtl['branch_id'] != ""){

		// now do employees
		$sql = "select  employees.* 
			from companies, brands, branches, employees
			where brands.company_id = companies.id
			AND branches.brand_id = brands.id
			AND branches.id = employees.Branch_id
			AND employees.dqc_card_expiry <= '" . $arCtl['dqc_card_expiry'] . "'
			AND companies.id = '" . $arCtl['company_id'] . "'
			AND brands.id ='" . $arCtl['brand_id'] . "'
			AND branches.id ='" . $arCtl['branch_id'] . "'
			AND cpc_required = 1";
		
		$RunEmployee = true;
		
	} elseif ($arCtl['brand_id'] != ""){

		$sql = "select  branches.id id, brands.id brand_id,branches.name name, count(distinct branches.name) TOTBRANCHES, count(distinct employees.id) TOTEMPLOYEES
			from companies, brands, branches, employees
			where brands.company_id = companies.id
			AND branches.brand_id = brands.id
			AND employees.dqc_card_expiry <= '" . $arCtl['dqc_card_expiry'] . "'
			AND branches.id = employees.Branch_id
			AND companies.id = '" . $arCtl['company_id'] . "'
			AND brands.id ='" . $arCtl['brand_id'] . "'
			AND cpc_required = 1
			group by branches.name";
		
		$RunBranch = true;

	} else {

		$sql = "select  brands.id id, brands.name name, count(distinct branches.name) TOTBRANCHES, count(*) TOTEMPLOYEES
			from companies, brands, branches, employees
			where brands.company_id = companies.id
			AND branches.brand_id = brands.id
			AND employees.dqc_card_expiry <= '" . $arCtl['dqc_card_expiry'] . "'
			AND branches.id = employees.Branch_id
			AND companies.id = '" . $arCtl['company_id'] . "'
			AND cpc_required = 1
			group by brands.name";

		$RunBrand = true;
		
	}
	

	//echo $sql; 
	if ($arCtl['Run'] != ""){
		$result = mysqli_query ($mysqli, $sql );
		if (! $result)	{ echo  sql_error (); }
		$count=0;
		while ($qd = mysqli_fetch_assoc($result)){
			$arCPC[$count] = $qd;

			$arCPC[$count]['CPCHoursReq'] = $qd['TOTEMPLOYEES'] * 35;
				
			if ($RunBrand){
				$sql = "select sum(duration) TOT from cpcuploads, employees, brands, branches 
							where employees.licence = cpcuploads.licence
							AND brands.id = " . $qd['id'] . "
							AND branches.brand_id = brands.id
							AND cpc_required = 1
							AND branches.id = employees.branch_id
							and (completion_date >= dqc_card_start or dqc_card_start is null)
							and dqc_card_expiry <= '" . $arCtl['dqc_card_expiry'] . "'";
				//echo $sql;

				$result2 = mysqli_query ($mysqli, $sql );
				if (! $result2)	{ echo  sql_error (); }
				$qd2 = mysqli_fetch_assoc($result2);
				$arCPC[$count]['CPCHours'] = $qd2['TOT'];

				$sql = "select round(sum(duration),0) TOT from third_party_uploads, employees, brands, branches
							where employees.licence = third_party_uploads.licence
							AND brands.id = " . $qd['id'] . "
							AND branches.brand_id = brands.id
							AND cpc_required = 1
							AND branches.id = employees.branch_id
							and (completed_date >= dqc_card_start or dqc_card_start is null)
							and dqc_card_expiry <= '" . $arCtl['dqc_card_expiry'] . "'";				//echo $sql;
				//echo $sql;
				
				$result2 = mysqli_query ($mysqli, $sql );
				if (! $result2)	{ echo  sql_error (); }
				$qd2 = mysqli_fetch_assoc($result2);
				$arCPC[$count]['TPHours'] = $qd2['TOT'];

				$arCPC[$count]['brand_id'] = $qd['id'];
				$arCPC[$count]['branch_id'] = "";
				
				
			} else if ($RunBranch) {
												
				$sql = "select sum(duration) TOT from cpcuploads, employees, brands, branches 
							where employees.licence = cpcuploads.licence
							AND branches.id = " . $qd['id'] . "
							AND cpc_required = 1
							AND branches.id = employees.Branch_id
							AND branches.brand_id = brands.id
							and completion_date >= DATE_SUB('" . $arCtl['dqc_card_expiry'] . "',INTERVAL 5 YEAR)
							and dqc_card_expiry <= '" . $arCtl['dqc_card_expiry'] . "'";
				//echo $sql;
				
				$result2 = mysqli_query ($mysqli, $sql );
				if (! $result2)	{ echo  sql_error (); }
				$qd2 = mysqli_fetch_assoc($result2);
				$arCPC[$count]['CPCHours'] = $qd2['TOT'];

				$sql = "select round(sum(duration),0) TOT from third_party_uploads, employees, brands, branches
							where employees.licence = third_party_uploads.licence
							AND branches.id = " . $qd['id'] . "
							AND cpc_required = 1
							AND branches.id = employees.branch_id
							AND branches.brand_id = brands.id
							and completed_date >= DATE_SUB('" . $arCtl['dqc_card_expiry'] . "',INTERVAL 5 YEAR)
							and dqc_card_expiry <= '" . $arCtl['dqc_card_expiry'] . "'";				//echo $sql;

				//echo $sql;
				
				$result2 = mysqli_query ($mysqli, $sql );
				if (! $result2)	{ echo  sql_error (); }
				$qd2 = mysqli_fetch_assoc($result2);
				$arCPC[$count]['TPHours'] = $qd2['TOT'];
				
				$arCPC[$count]['brand_id'] = $qd['brand_id'];
				$arCPC[$count]['branch_id'] = $qd['id'];
				
				
			} else if ($RunEmployee) {
				
				$arCPC[$count]['CPCHoursReq'] = 35;
												
				$sql = "select sum(duration) TOT from cpcuploads, employees, brands, branches 
							where employees.licence = cpcuploads.licence
							AND employees.id = " . $qd['id'] . "
							AND branches.id = employees.Branch_id
							AND branches.brand_id = brands.id
							and completion_date >= DATE_SUB('" . $arCtl['dqc_card_expiry'] . "',INTERVAL 5 YEAR)
							and dqc_card_expiry <= '" . $arCtl['dqc_card_expiry'] . "'";
				//echo $sql;
				
				$result2 = mysqli_query ($mysqli, $sql );
				if (! $result2)	{ echo  sql_error (); }
				$qd2 = mysqli_fetch_assoc($result2);
				$arCPC[$count]['CPCHours'] = $qd2['TOT'];

				$sql = "select round(sum(duration),0) TOT from third_party_uploads, employees, brands, branches
							where employees.licence = third_party_uploads.licence
							AND employees.id = " . $qd['id'] . "
							AND branches.id = employees.branch_id
							AND branches.brand_id = brands.id
							and completed_date >= DATE_SUB('" . $arCtl['dqc_card_expiry'] . "',INTERVAL 5 YEAR)
							and dqc_card_expiry <= '" . $arCtl['dqc_card_expiry'] . "'";				//echo $sql;

				//echo $sql;
				
				$result2 = mysqli_query ($mysqli, $sql );
				if (! $result2)	{ echo  sql_error (); }
				$qd2 = mysqli_fetch_assoc($result2);
				$arCPC[$count]['TPHours'] = $qd2['TOT'];
				
				$arCPC[$count]['brand_id'] = $qd['brand_id'];
				$arCPC[$count]['branch_id'] = $qd['id'];
				
				
			} 				
			
			if ($arCPC[$count]['TPHours'] == ""){
				$arCPC[$count]['TPHours'] = 0;
			}
			if ($arCPC[$count]['CPCHours'] == ""){
				$arCPC[$count]['CPCHours'] = 0;
			}
			$Tot = $arCPC[$count]['CPCHours'] + $arCPC[$count]['TPHours'];
			$arCPC[$count]['PER_COMPLETE_UPLOADED'] = round(($Tot/$arCPC[$count]['CPCHoursReq']) * 100,0) ;
			
			// create totals
			
			$arTotals['TOTEMPLOYEES'] = $arTotals['TOTEMPLOYEES'] + $qd['TOTEMPLOYEES'];
			$arTotals['CPCHours'] = $arTotals['CPCHours'] + $arCPC[$count]['CPCHours'];
			$arTotals['TPHours'] = $arTotals['TPHours'] + $arCPC[$count]['TPHours'];
			$arTotals['CPCHoursReq'] = $arTotals['CPCHoursReq'] + $arCPC[$count]['CPCHoursReq'];
				
			
			$count++;
		}
	}

	$Tot = $arTotals['CPCHours'] + $arTotals['TPHours'];
	$arTotals['PER_COMPLETE_UPLOADED'] = round(($Tot/$arTotals['CPCHoursReq']) * 100,0) ;
	
	
	// get dropd downs
	
	$sql = "select * from companies order by name";
	$result2 = mysqli_query ($mysqli, $sql );
	if (!$result2)	{ echo  sql_error (); }
	$arCompany[''] = "Select Customer";
	while ($qd2 = mysqli_fetch_assoc($result2)){
		$arCompany[$qd2['id']] = $qd2['name'];
	}

	if ($arCtl['company_id'] != ""){
		// now get brands
		$sql = "select * from brands where company_id = '" . $arCtl['company_id'] . "' order by name";
		$result2 = mysqli_query ($mysqli, $sql );
		if (!$result2)	{ echo  sql_error (); }
		$arBrand[''] = "Select Brand";
		while ($qd2 = mysqli_fetch_assoc($result2)){
			$arBrand[$qd2['id']] = $qd2['name'];
		}
		
		if ($arCtl['brand_id'] != ""){
			// now get brands
			$sql = "select * from branches where brand_id = '" . $arCtl['brand_id'] . "' order by name";
			//echo $sql;
			$result2 = mysqli_query ($mysqli, $sql );
			if (!$result2)	{ echo  sql_error (); }
			$arBranch[''] = "Select Branches";
			while ($qd2 = mysqli_fetch_assoc($result2)){
				$arBranch[$qd2['id']] = $qd2['name'] . " " . $qd2['code'];
			}
		}	
		
	}
	
	
	if ($arCtl['dqc_card_expiry'] == ""){
		$arCtl['dqc_card_expiry'] = date('Y-m-d'); 
	}
	
	//print_r($arCPC);
	
	// set up smarty
	$smarty = getSmarty();
	
	// results info
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('arCompanyList',    $arCompany);
	$smarty->assign('arBrandList',    $arBrand);
	$smarty->assign('arBranchList',    $arBranch);
	$smarty->assign('arCPC',    $arCPC);
	$smarty->assign('arTotals',    $arTotals);
	$smarty->assign('SESSION',   $_SESSION);
	
	// display using template name provided
	$smarty->display("custom/ListCPC.tpl");
	
}

function ListCPCEmployees($arCtl,$arSAFE_REQUEST) {

	$mysqli = db_connect(getBookingDBName());

	// now do employees
	$sql = "select  employees.*
		from companies, brands, branches, employees
		where brands.company_id = companies.id
		AND branches.brand_id = brands.id
		AND branches.id = employees.Branch_id
		AND companies.id = '" . $arCtl['company_id'] . "'
		AND cpc_required = 1";

	if ($arCtl['brand_id'] != ""){
		$sql .= " AND brands.id ='" . $arCtl['brand_id'] . "'";
	}

	if ($arCtl['branch_id'] != ""){
		$sql .= " AND branches.id ='" . $arCtl['branch_id'] . "'";
	}

	if ($arCtl['licence'] != ""){
		$sql .= " AND licence ='" . $arCtl['licence'] . "'";
	}
	
	if ($arCtl['payroll'] != ""){
		$sql .= " AND payroll ='" . $arCtl['payroll'] . "'";
	}

	if ($arCtl['dqc_card_expiry'] != ""){
		$sql .= " AND dqc_card_expiry <= '" . $arCtl['dqc_card_expiry'] . "'";
	}
	
	$sql .= " order by name";
	
//	echo $sql;
	if ($arCtl['Run'] != ""){
		$result = mysqli_query ($mysqli, $sql );
		if (! $result)	{ echo  sql_error (); }
		$count=0;
		$arTotals['TOTEMPLOYEES'] = 0;
		$arTotals['CPCHours'] = 0;
		$arTotals['TPHours'] = 0;
		$arTotals['CPCHoursReq'] = 0;
		
		while ($qd = mysqli_fetch_assoc($result)){
			$arCPC[$count] = $qd;


			$arCPC[$count]['CPCHoursReq'] = 35;

			$sql = "select sum(duration) TOT from cpcuploads, employees, brands, branches
						where employees.licence = cpcuploads.licence
						AND employees.id = " . $qd['id'] . "
						AND branches.id = employees.Branch_id
						AND branches.brand_id = brands.id
						and completion_date >= DATE_SUB('" . $arCtl['dqc_card_expiry'] . "',INTERVAL 5 YEAR)
						and dqc_card_expiry <= '" . $arCtl['dqc_card_expiry'] . "'";
//			echo $sql;

			$result2 = mysqli_query ($mysqli, $sql );
			if (! $result2)	{ echo  sql_error (); }
			$qd2 = mysqli_fetch_assoc($result2);
			$arCPC[$count]['CPCHours'] = $qd2['TOT'];

			$sql = "select round(sum(duration),0) TOT from third_party_uploads, employees, brands, branches
						where employees.licence = third_party_uploads.licence
						AND employees.id = " . $qd['id'] . "
						AND branches.id = employees.branch_id
						AND branches.brand_id = brands.id
						and completed_date >= DATE_SUB('" . $arCtl['dqc_card_expiry'] . "',INTERVAL 5 YEAR)
						and dqc_card_expiry <= '" . $arCtl['dqc_card_expiry'] . "'";				//echo $sql;

			//echo $sql;

			$result2 = mysqli_query ($mysqli, $sql );
			if (! $result2)	{ echo  sql_error (); }
			$qd2 = mysqli_fetch_assoc($result2);
			$arCPC[$count]['TPHours'] = $qd2['TOT'];

			$arCPC[$count]['brand_id'] = $qd['brand_id'];
			$arCPC[$count]['branch_id'] = $qd['id'];

				
			if ($arCPC[$count]['TPHours'] == ""){
				$arCPC[$count]['TPHours'] = 0;
			}
			if ($arCPC[$count]['CPCHours'] == ""){
				$arCPC[$count]['CPCHours'] = 0;
			}
			$Tot = $arCPC[$count]['CPCHours'] + $arCPC[$count]['TPHours'];
			$arCPC[$count]['PER_COMPLETE_UPLOADED'] = round(($Tot/$arCPC[$count]['CPCHoursReq']) * 100,0) ;
				
			// create totals
				
			$arTotals['TOTEMPLOYEES'] = $arTotals['TOTEMPLOYEES'] + 1;
			$arTotals['CPCHours'] = $arTotals['CPCHours'] + $arCPC[$count]['CPCHours'];
			$arTotals['TPHours'] = $arTotals['TPHours'] + $arCPC[$count]['TPHours'];
			$arTotals['CPCHoursReq'] = $arTotals['CPCHoursReq'] + $arCPC[$count]['CPCHoursReq'];

				
			$count++;
		}

		$Tot = $arTotals['CPCHours'] + $arTotals['TPHours'];
		$arTotals['PER_COMPLETE_UPLOADED'] = round(($Tot/$arTotals['CPCHoursReq']) * 100,0) ;
		
	}



	// get dropd downs

	$sql = "select * from companies order by name";
	$result2 = mysqli_query ($mysqli, $sql );
	if (!$result2)	{ echo  sql_error (); }
	$arCompany[''] = "Select Customer";
	while ($qd2 = mysqli_fetch_assoc($result2)){
		$arCompany[$qd2['id']] = $qd2['name'];
	}

	if ($arCtl['company_id'] != ""){
		// now get brands
		$sql = "select * from brands where company_id = '" . $arCtl['company_id'] . "' order by name";
		$result2 = mysqli_query ($mysqli, $sql );
		if (!$result2)	{ echo  sql_error (); }
		$arBrand[''] = "Select Brand";
		while ($qd2 = mysqli_fetch_assoc($result2)){
			$arBrand[$qd2['id']] = $qd2['name'];
		}

		if ($arCtl['brand_id'] != ""){
			// now get brands
			$sql = "select * from branches where brand_id = '" . $arCtl['brand_id'] . "' order by name";
			//echo $sql;
			$result2 = mysqli_query ($mysqli, $sql );
			if (!$result2)	{ echo  sql_error (); }
			$arBranch[''] = "Select Branches";
			while ($qd2 = mysqli_fetch_assoc($result2)){
				$arBranch[$qd2['id']] = $qd2['name'] . " " . $qd2['code'];
			}
		}

	}


	if ($arCtl['dqc_card_expiry'] == ""){
		$arCtl['dqc_card_expiry'] = date('Y-m-d');
	}

	//print_r($arCPC);

	// set up smarty
	$smarty = getSmarty();

	// results info
	$smarty->assign('arCtl',    $arCtl);
	$smarty->assign('arCompanyList',    $arCompany);
	$smarty->assign('arBrandList',    $arBrand);
	$smarty->assign('arBranchList',    $arBranch);
	$smarty->assign('arCPC',    $arCPC);
	$smarty->assign('arTotals',    $arTotals);
	$smarty->assign('SESSION',   $_SESSION);

	// display using template name provided
	$smarty->display("custom/ListCPCEmployees.tpl");

}

?>