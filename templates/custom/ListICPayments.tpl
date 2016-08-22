{include file="header/admin_header.tpl"}


 <div class="row">
  <div class="col-sm-1"></div>
  <div class="col-sm-10">
  
  <table class="table table-striped">
    <thead>
    	<form action={$SESSION.PostAction} method=post class="form-inline" role="form">
		<input type=hidden name=action value="ListICPayments">
	      <tr >
    		<th> Date From:  </th>
    		<th> <input type=text class="form-control" name=arCtl[date_from] value="{$arCtl.date_from}"> </th>
    		<th> Date To:  </th>
    		<th> <input type=text class="form-control" name=arCtl[date_to] value="{$arCtl.date_to}"> </th>
    		<th colpsan=4> <button type="submit" name=arCtl[Run] value="Run" class="btn btn-default ">Show</button></th>
	      </tr>
	 </form>
	<thead>
   </table>    
  <table class="table table-striped">
    <thead>


	      <tr>
	        <th>Date</td>
	        <th>Type</td>
	        <th>Ref</td>
	        <th>Pay Method</td>
	        <th>Insurer</td>
	        <th>Amount</td>
	        <th>Culm</td>
	        <th>Reconciled</td>
	        <th ></td>
	      </tr>
    </thead>
    <tbody>
    
		    {section name=row2 loop=$arPayments}
		    
		    	{if $arPayments[row2].Title != ""}
		    	
			      <tr>
			        <th>{$arPayments[row2].Title}</td>
			        <th>Customer</td>
			        <th>{$arPayments[row2].CulmTotalCustomer}</td>
			        <th>Insurer</td>
			        <th>{$arPayments[row2].CulmTotalInsurer}</td>
			        <th>Balance</td>
			        <th>{$arPayments[row2].CulmTotal}</td>
			        <td></td>
			      </tr>	
		    	
		    	{else}
		    
			      <tr>
			        <td>{$arPayments[row2].Date}</td>
			        <td>{$arPayments[row2].Type}</td>
			        <td>{$arPayments[row2].Polref}</td>
			        <td>{$arPayments[row2].Insurer}</td>
			        <td>{$arPayments[row2].PayMethod}</td>
			        <td>{$arPayments[row2].Amount}</td>
			        <td>{$arPayments[row2].CulmTotal}</td>
			        <td></td>
			      </tr>	
			      
			     {/if}
		    
		    {/section}
				      		
			

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
	      </tr>	
		

    </tbody>
  
  </table>
  
  
  </div>
  <div class="col-sm-1"></div>
</div>


{include file="header/admin_footer.tpl"}