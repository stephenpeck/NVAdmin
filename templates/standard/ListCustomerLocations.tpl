{include file="header/admin_header.tpl"} <div class="row">  <div class="col-sm-1"></div>  <div class="col-sm-10">  <table class="table table-striped">    <thead>    	<form action={$SESSION.PostAction} method=post class="form-inline" role="form">		<input type=hidden name=action value="ListCustomerLocations">	      <tr >    		<th> Customer:  </th>    		<th> {html_options class="form-control" name=arCtl[CU_KEY] options=$arCustomerList selected=$arCtl.CU_KEY} </th>    		<th> <button type="submit" name=arCtl[Run] value="Run" class="btn btn-default ">Show</button></th>	      </tr>	 </form>	</thead>   </table>    	  <table class="table table-striped">	    <thead>			<tr ><th colspan="9">Customer Admin</th></tr>			<tr  align="center">				<th width=15%>Code</th>				<th colspan=3 >Name</th>				<th>Group</th>				<th></th>			</tr>		</thead>			{section name=menu loop=$arLOCATIONS}			    <form action={$SESSION.PostAction} method=post class="form-inline" role="form">					<input type=hidden name=action value="UpdCustomerLocations">					<input type=hidden name=arCtl[CU_KEY] value="{$arCtl.CU_KEY}">					<input type=hidden name=LOCATIONS[LO_KEY] value="{$arLOCATIONS[menu].LO_KEY}">					<tr >						<td><input type=text class="form-control" name=LOCATIONS[LO_CODE] value="{$arLOCATIONS[menu].LO_CODE}"></td>						<td colspan=3><input type=text class="form-control" name=LOCATIONS[LO_NAME] value="{$arLOCATIONS[menu].LO_NAME}"></td>						<td>{html_options class="form-control" name=LOCATIONS[LO_CUGKEY] options=$arCustomerGroups selected=$arLOCATIONS[menu].LO_CUGKEY}</td>						<td> <a href="#{$arLOCATIONS[menu].LO_KEY}" data-toggle="collapse"> Details </A> </td>					</tr >					<tr  class="collapse"  id={$arLOCATIONS[menu].LO_KEY}>						<td colspan=6 >						  <table class="table table-striped">						  	<tr>								<td> Address </td>								<td colspan=3 width=60%> <input type=text class="form-control" name=LOCATIONS[LO_ADDRESS1] value="{$arLOCATIONS[menu].LO_ADDRESS1}">									<input type=text class="form-control" name=LOCATIONS[LO_ADDRESS2] value="{$arLOCATIONS[menu].LO_ADDRESS2}">									<input type=text class="form-control" name=LOCATIONS[LO_ADDRESS3] value="{$arLOCATIONS[menu].LO_ADDRESS3}">								</td>								<td> Postcode </td>								<td>									<input type=text class="form-control" name=LOCATIONS[LO_POSTCODE] value="{$arLOCATIONS[menu].LO_POSTCODE}">								 </td>							</tr>							<tr>								<td colspan=3  align="center"><button type="submit" name=arCtl[Run] value="Run" class="btn btn-primary ">Save</button></td>							</form>						    <form action={$SESSION.PostAction} method=post class="form-inline" role="form">								<input type=hidden name=action value="DelCustomerLocations">								<input type=hidden name=LOCATIONS[LO_KEY] value="{$arLOCATIONS[menu].LO_KEY}">								<input type=hidden name=arCtl[CU_KEY] value="{$arCtl.CU_KEY}">								<input type=hidden name=LOCATIONS[LO_CUKEY] value="{$arCtl.CU_KEY}">									<td colspan="3" align="center"><button type="submit" name=arCtl[Run] value="Run" class="btn btn-primary ">Del</button></td>								</tr>							</form>							</table>						</td>					</tr>			{/section}							    <form action={$SESSION.PostAction} method=post class="form-inline" role="form">					<input type=hidden name=action value="UpdCustomerLocations">					<input type=hidden name=arCtl[CU_KEY] value="{$arCtl.CU_KEY}">					<input type=hidden name=LOCATIONS[LO_CUKEY] value="{$arCtl.CU_KEY}">					<tr >						<td><input type=text class="form-control" name=LOCATIONS[LO_CODE] value="{$arLOCATIONS[menu].LO_CODE}"></td>						<td colspan=3><input type=text class="form-control" name=LOCATIONS[LO_NAME] value="{$arLOCATIONS[menu].LO_NAME}"></td>						<td>{html_options class="form-control" name=LOCATIONS[LO_CUGKEY] options=$arCustomerGroups selected=$arLOCATIONS[menu].LO_CUGKEY}</td>						<td>  </td>					</tr >					<tr>						<td> Address </td>						<td colspan=3> <input type=text class="form-control" name=LOCATIONS[LO_ADDRESS1] value="{$arLOCATIONS[menu].LO_ADDRESS1}">							<input type=text class="form-control" name=LOCATIONS[LO_ADDRESS2] value="{$arLOCATIONS[menu].LO_ADDRESS2}">							<input type=text class="form-control" name=LOCATIONS[LO_ADDRESS3] value="{$arLOCATIONS[menu].LO_ADDRESS3}">						</td>						<td> Postcode </td>						<td>							<input type=text class="form-control" name=LOCATIONS[LO_POSTCODE] value="{$arLOCATIONS[menu].LO_POSTCODE}">						 </td>					</tr>					<tr>						<td colspan=6><button type="submit" name=arCtl[Run] value="Run" class="btn btn-primary ">Add New Location</button></td>					</tr>				</form>						</table>		  </div>  <div class="col-sm-1"></div></div>	    
{include file="header/admin_footer.tpl"}
