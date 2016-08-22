{include file="header/admin_header.tpl"}


 <div class="row">
  <div class="col-sm-1"></div>
  <div class="col-sm-10">
  
  <table class="table table-striped">
    <thead>
    	<form action={$SESSION.PostAction} method=post class="form-inline" role="form">
		<input type=hidden name=action value="ListCPCEmployees">
	      <tr >
    		<th> Expiry:  </th>
    		<th> <input type=text class="form-control" name=arCtl[dqc_card_expiry] value="{$arCtl.dqc_card_expiry}"> </th>
    		<th> Name:  </th>
    		<th> <input type=text class="form-control" name=arCtl[name] value="{$arCtl.name}"> </th>
    		<th> Licence:  </th>
    		<th> <input type=text class="form-control" name=arCtl[licence] value="{$arCtl.licence}"> </th>
    		<th> Payroll:  </th>
    		<th> <input type=text class="form-control" name=arCtl[payoll] value="{$arCtl.payroll}"> </th>
    		<th rowspan=2> <button type="submit" name=arCtl[Run] value="Run" class="btn btn-default ">Show</button></th>
	      </tr>
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
    		<th colspan=2>  </th>
	      </tr>
	 </form>
	<thead>
   </table>    
  <table class="table table-striped">
    <thead>

	      <tr>
	        <th class="col-md-2">Name</th>
	        <th class="col-md-1">Licence</th>
	        <th class="col-md-1">Payroll</th>
	        <th class="col-md-1">CPC Start</th>
	        <th class="col-md-1">CPC Expiry</th>
	        <th class="col-md-1">Hours</th>
	        <th class="col-md-1">3rd Party Hours</th>
	        <th class="col-md-1">% Complete</th>
	        <th class="col-md-1">Total Incl Booked</th>
	        <th class="col-md-1">% Complete</th>
	        <th class="col-md-2"></td>
	      </tr>
    </thead>
    <tbody>
    
	    {section name=row loop=$arCPC}

	    	<form action={$SESSION.PostAction} method=post class="form-inline" role="form">
			<input type=hidden name=action value="ListCPC">
			<input type=hidden name=arCtl[Run] value="Y">
			<input type=hidden name=arCtl[company_id] value="{$arCtl.company_id}">
			<input type=hidden name=arCtl[brand_id] value="{$arCPC[row].brand_id}">
			<input type=hidden name=arCtl[branch_id] value="{$arCPC[row].branch_id}">
			<input type=hidden name=arCtl[dqc_card_expiry] value="{$arCtl.dqc_card_expiry}">
			
			
		      <tr>	
		        <td>{$arCPC[row].name}</td>
		        <td>{$arCPC[row].licence}</td>
		        <td>{$arCPC[row].payroll}</td>
		        <td>{$arCPC[row].dqc_card_start}</td>
		        <td>{$arCPC[row].dqc_card_expiry}</td>
		        <td align=right>{$arCPC[row].CPCHours}</td>
		        <td align=right>{$arCPC[row].TPHours}</td>
		        <td align=right>{$arCPC[row].PER_COMPLETE_UPLOADED} </td>
		        <td align=right>{$arCPC[row].TotHouseBooked} </td>
		        <td align=right>{$arCPC[row].PER_COMPLETE} </td>
		        <th class="col-md-2"></td>
		      </tr>	
			</form>
			
			
		{/section}      

		      <tr>	
		        <th>Totals</td>
		        <td></td>
		        <td></td>
		        <td></td>
		        <td></td>
		        <td></td>
		        <th align=right>{$arTotals.CPCHours}</td>
		        <th align=right>{$arTotals.TPHours}</td>
		        <th align=right>{$arTotals.PER_COMPLETE_UPLOADED} </td>
		        <th align=right>{$arTotals.TotHouseBooked} </td>
		        <th align=right>{$arTotals.PER_COMPLETE} </td>
		        <th class="col-md-2"></td>
		      </tr>	
		

    </tbody>
  
  </table>
  
  
  </div>
  <div class="col-sm-1"></div>
</div>


{include file="header/admin_footer.tpl"}