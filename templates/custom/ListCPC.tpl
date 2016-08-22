{include file="header/admin_header.tpl"}


 <div class="row">
  <div class="col-sm-1"></div>
  <div class="col-sm-10">
  
  <table class="table table-striped">
    <thead>
    	<form action={$SESSION.PostAction} method=post class="form-inline" role="form">
		<input type=hidden name=action value="ListCPC">
	      <tr >
    		<th> Customer:  </th>
    		<th> {html_options class="form-control" name=arCtl[company_id] options=$arCompanyList selected=$arCtl.company_id} </th>
    		<th> Brand:  </th>
    		{if $arCtl.company_id != ""}
	    		<th> {html_options class="form-control" name=arCtl[brand_id] options=$arBrandList selected=$arCtl.brand_id} </th>
	    	{else}
	    		<th>  </th>
	    	{/if}
    		<th> Branch:  </th>
    		{if $arCtl.brand_id != ""}
	    		<th> {html_options class="form-control" name=arCtl[branch_id] options=$arBranchList selected=$arCtl.branch_id} </th>
	    	{else}
	    		<th>  </th>
	    	{/if}
    		<th> Expiry:  </th>
    		<th> <input type=text class="form-control" name=arCtl[dqc_card_expiry] value="{$arCtl.dqc_card_expiry}"> </th>
    		<th colpsan=4> <button type="submit" name=arCtl[Run] value="Run" class="btn btn-default ">Show</button></th>
	      </tr>
	 </form>
	<thead>
   </table>    
  <table class="table table-striped">
    <thead>

  		{if $arCtl.branch_id == ""}

	      <tr>
	        <th class="col-md-2">Name</th>
	        <th class="col-md-1">Employees</th>
	        <th class="col-md-1">Hours Req</th>
	        <th class="col-md-1">Hours</th>
	        <th class="col-md-1">3rd Party Hours</th>
	        <th class="col-md-1">% Complete</th>
	        <th class="col-md-1">Total Incl Booked</th>
	        <th class="col-md-1">% Complete</th>
	        <th class="col-md-2"></td>
	      </tr>
	    {else}
	        <th class="col-md-2">Name</th>
	        <th class="col-md-1">Licence</th>
	        <th class="col-md-1">Payroll</th>
	        <th class="col-md-1">CPC </th>
	        <th class="col-md-1">CPC Start</th>
	        <th class="col-md-1">CPC Expiry</th>
	        <th class="col-md-1">Hours</th>
	        <th class="col-md-1">3rd Party Hours</th>
	        <th class="col-md-1">% Complete</th>
	        <th class="col-md-1">Total Incl Booked</th>
	        <th class="col-md-1">% Complete</th>
	        <th class="col-md-2"></td>
	    
	    {/if}
    </thead>
    <tbody>
    
	    {section name=row loop=$arCPC}

  		{if $arCtl.branch_id == ""}

	    
	    	<form action={$SESSION.PostAction} method=post class="form-inline" role="form">
			<input type=hidden name=action value="ListCPC">
			<input type=hidden name=arCtl[Run] value="Y">
			<input type=hidden name=arCtl[company_id] value="{$arCtl.company_id}">
			<input type=hidden name=arCtl[brand_id] value="{$arCPC[row].brand_id}">
			<input type=hidden name=arCtl[branch_id] value="{$arCPC[row].branch_id}">
			<input type=hidden name=arCtl[dqc_card_expiry] value="{$arCtl.dqc_card_expiry}">
			
		      <tr>
		        <td>{$arCPC[row].name}</td>
		        <td>{$arCPC[row].TOTEMPLOYEES}</td>
		        <td>{$arCPC[row].CPCHoursReq}</td>
		        <td>{$arCPC[row].CPCHours}</td>
		        <td>{$arCPC[row].TPHours}</td>
		        <td>{$arCPC[row].PER_COMPLETE_UPLOADED} </td>
		        <td>{$arCPC[row].TotHouseBooked} </td>
		        <td>{$arCPC[row].PER_COMPLETE} </td>
		        <td><button type="submit" name=arCtl[Run] value="Run" class="btn btn-default ">Show</button></td>
		      </tr>	
			</form>
			
	    {else}
		      <tr>	
		        <td>{$arCPC[row].name}</td>
		        <td>{$arCPC[row].licence}</td>
		        <td>{$arCPC[row].payroll}</td>
		        <td>{$arCPC[row].cpc_required}</td>
		        <td>{$arCPC[row].dqc_card_start}</td>
		        <td>{$arCPC[row].dqc_card_expiry}</td>
		        <td>{$arCPC[row].CPCHours}</td>
		        <td>{$arCPC[row].TPHours}</td>
		        <td>{$arCPC[row].PER_COMPLETE_UPLOADED} </td>
		        <td>{$arCPC[row].TotHouseBooked} </td>
		        <td>{$arCPC[row].PER_COMPLETE} </td>
		        <th class="col-md-2"></td>
		      </tr>	
	    {/if}
			
			
		{/section}      

  		{if $arCtl.branch_id == ""}

		      <tr>
		        <th>Totals</td>
		        <th>{$arTotals.TOTEMPLOYEES}</td>
		        <th>{$arTotals.CPCHoursReq}</td>
		        <th>{$arTotals.CPCHours}</td>
		        <th>{$arTotals.TPHours}</td>
		        <th>{$arTotals.PER_COMPLETE_UPLOADED} </td>
		        <th>{$arTotals.TotHouseBooked} </td>
		        <th>{$arTotals.PER_COMPLETE} </td>
		        <th></td>
		      </tr>	

		{else}
		      <tr>	
		        <th>Totals</td>
		        <td></td>
		        <td></td>
		        <td></td>
		        <td></td>
		        <td></td>
		        <th>{$arTotals.CPCHours}</td>
		        <th>{$arTotals.TPHours}</td>
		        <th>{$arTotals.PER_COMPLETE_UPLOADED} </td>
		        <th>{$arTotals.TotHouseBooked} </td>
		        <th>{$arTotals.PER_COMPLETE} </td>
		        <th class="col-md-2"></td>
		      </tr>	
		
		{/if}

    </tbody>
  
  </table>
  
  
  </div>
  <div class="col-sm-1"></div>
</div>


{include file="header/admin_footer.tpl"}