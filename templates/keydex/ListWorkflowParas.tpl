{include file=$SESSION.ENVIRONMENT.EN_HEADER}


<div class="row">
  <div class="col-sm-1"></div>
  <div class="col-sm-10">

		<table  class="table table-striped">
		<thead>
			<tr >
				<th> Para Name </td>
				<th></td>
				<th></td>
			</tr>
		</thead>

	{if $arCtl.count == "0"}
	
	   	<tr height='60' class='RowNormal'>
	
	   	   <td colspan=10>No Paras Found <BR></td>
	
	   	</tr>
	
	{else}
	
		{section name=row loop=$arKD_WORKFLOWS_PARAS}
	
		<form role="form" action='{$SESSION.PostAction}' method='post' >
		<input type=hidden name=KD_WORKFLOWS_PARAS[WFP_KEY] value={$arKD_WORKFLOWS_PARAS[row].WFP_KEY}>
		<input type=hidden name=action value=UpdWorkflowParas>
			<tr class='{cycle values="RowNormal,RowWhite"}' height='30'>
				<td> <input type=text class="form-control" size=50 name=KD_WORKFLOWS_PARAS[WFP_DESCRIPTION] value="{$arKD_WORKFLOWS_PARAS[row].WFP_DESCRIPTION}" > </td>
				<td><button type="submit" class="btn btn-default "> Upd</button></td>
	  			<td> <A HREF=index.php?action=ShowWorkflowParas&KD_WORKFLOWS_PARAS[WFP_KEY]={$arKD_WORKFLOWS_PARAS[row].WFP_KEY}>Show</A></td>
			</tr>
		</form>
	
		{/section}
	
	{/if}

	<tr class='Title' >	<th colspan=8 aligh='left'> Add Workflow Parameter Table </th>	</tr>

	<tr >
		<form role="form" action='{$SESSION.PostAction}' method='post' >
		<input type=hidden name=KD_WORKFLOWS_PARAS[WF_KEY] value="">
		<input type=hidden name=action value=UpdWorkflowParas>
			<td>  <input  class="form-control" type=text size=50 name=KD_WORKFLOWS_PARAS[WFP_DESCRIPTION]> </td>
			<td colspan=2  ><button type="submit" class="btn btn-default "> Add New </button></td>
		</form>
	</tr>
</table>

  </div>
  <div class="col-sm-1"></div>
</div>
{include file=$SESSION.ENVIRONMENT.EN_FOOTER}
