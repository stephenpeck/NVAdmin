{include file=$SESSION.ENVIRONMENT.EN_HEADER}


<div class="row">
  <div class="col-sm-1"></div>
  <div class="col-sm-10">

		<table  class="table table-striped">
		<form action='{$SESSION.PostAction}' method="POST">
		<input type=hidden name=KD_WORKFLOWS_PARAS[WFP_KEY] value="{$KD_WORKFLOWS_PARAS.WFP_KEY}">
		<input type=hidden name=action value=DelWorkflowParas>
		<thead>
			<tr >
				<th colspan=3> Para Name </td>
				<th colspan=3 > {$KD_WORKFLOWS_PARAS.WFP_DESCRIPTION}</td>
				<th><button type="submit" class="btn btn-default " name=arCtl[Run] value="Delete"> Delete</button></td>
			</tr>
		</thead>
		</form>

	{if $arCtl.count == "0"}
	
	   	<tr height='60' class='RowNormal'>
	
	   	   <td colspan=10>No Paras Found <BR></td>
	
	   	</tr>
	
	{else}
		<thead>
			<tr >
				<td>  Parameter Match Key </td>
				<td>  Match Value </td>
				<td>  Contact </td>
				<td colspan=2>  Additional Data </td>
				<td colspan=2></td>
			</tr>
		</thead>
		{section name=row loop=$arKD_WORKFLOWS_PARAS_ITEMS}
	
		<form action=index.php method="POST">
		<input type=hidden name=KD_WORKFLOWS_PARAS[WFP_KEY] value="{$KD_WORKFLOWS_PARAS.WFP_KEY}">
		<input type=hidden name=KD_WORKFLOWS_PARAS_ITEMS[WPI_KEY] value={$arKD_WORKFLOWS_PARAS_ITEMS[row].WPI_KEY}>
		<input type=hidden name=action value=UpdWorkflowParaItems>
			<tr >
				<td>  <input class"form-control" type=text name=KD_WORKFLOWS_PARAS_ITEMS[WPI_CODE] value="{$arKD_WORKFLOWS_PARAS_ITEMS[row].WPI_CODE}"> </td>
				<td>  <input class"form-control" type=text name=KD_WORKFLOWS_PARAS_ITEMS[WPI_VALUE]  value="{$arKD_WORKFLOWS_PARAS_ITEMS[row].WPI_VALUE}"> </td>
				<td>  <input class"form-control" type=text size=50 name=KD_WORKFLOWS_PARAS_ITEMS[WPI_ATTRIBUTE1]  value="{$arKD_WORKFLOWS_PARAS_ITEMS[row].WPI_ATTRIBUTE1}"> </td>
				<td>  <input class"form-control" type=text name=KD_WORKFLOWS_PARAS_ITEMS[WPI_ATTRIBUTE2]  value="{$arKD_WORKFLOWS_PARAS_ITEMS[row].WPI_ATTRIBUTE2}"> </td>
				<td>  <input class"form-control" type=text name=KD_WORKFLOWS_PARAS_ITEMS[WPI_ATTRIBUTE3]  value="{$arKD_WORKFLOWS_PARAS_ITEMS[row].WPI_ATTRIBUTE3}"> </td>
				<td><button type="submit" class="btn btn-default " name=arCtl[Run] value="Upd"> Update</button></td>
			</form>
		<form action=index.php method="POST">
		<input type=hidden name=KD_WORKFLOWS_PARAS[WFP_KEY] value="{$KD_WORKFLOWS_PARAS.WFP_KEY}">
		<input type=hidden name=KD_WORKFLOWS_PARAS_ITEMS[WPI_KEY] value={$arKD_WORKFLOWS_PARAS_ITEMS[row].WPI_KEY}>
		<input type=hidden name=action value=DelWorkflowParaItems>
				<td><button type="submit" class="btn btn-default " name=arCtl[Run] value="Del"> Del</button></td>
			</tr>
		</form>
	
		{/section}
	
	{/if}

	<tr class="Title" >	<th colspan=8 aligh='left'> Add Workflow Parameter </th>	</tr>

	<tr >
		<form action=index.php method="POST">
		<input type=hidden name=KD_WORKFLOWS_PARAS_ITEMS[WFPI_KEY] value=>
		<input type=hidden name=KD_WORKFLOWS_PARAS[WFP_KEY] value="{$KD_WORKFLOWS_PARAS.WFP_KEY}">
		<input type=hidden name=KD_WORKFLOWS_PARAS_ITEMS[WPI_WFPKEY] value="{$KD_WORKFLOWS_PARAS.WFP_KEY}">
		<input type=hidden name=action value=UpdWorkflowParaItems>
			<td>  <input type=text class"form-control" name=KD_WORKFLOWS_PARAS_ITEMS[WPI_CODE]> </td>
			<td>  <input type=text class"form-control" name=KD_WORKFLOWS_PARAS_ITEMS[WPI_VALUE]> </td>
			<td>  <input class"form-control" type=text size=50 name=KD_WORKFLOWS_PARAS_ITEMS[WPI_ATTRIBUTE1] > </td>
			<td>  <input class"form-control" type=text name=KD_WORKFLOWS_PARAS_ITEMS[WPI_ATTRIBUTE2] > </td>
			<td>  <input class"form-control" type=text name=KD_WORKFLOWS_PARAS_ITEMS[WPI_ATTRIBUTE3] > </td>
			<td  colspan=2 ><button type="submit" class="btn btn-default " name=arCtl[Run] value="Add"> Add</button></td>
		</form>
	</tr>
</table>


  </div>
  <div class="col-sm-1"></div>
</div>
{include file=$SESSION.ENVIRONMENT.EN_FOOTER}
