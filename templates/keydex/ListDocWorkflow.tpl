{include file=$SESSION.ENVIRONMENT.EN_HEADER}


<div class="row">
  <div class="col-sm-1"></div>
  <div class="col-sm-10">

		<table class="table table-striped table-hover">
		<thead>
		<tr >
			<th> Name </td>
			<th> Description </td>
			<th> Doc Type </td>
			<th>  </td>
		</tr>
		</thead>

	{if $arCtl.count == "0"}
	
	   	<tr height='60' class='RowNormal'>
	
	   	   <td colspan=10>No Document Workflows Found <BR></td>
	
	   	</tr>
	
	{else}
	
		{section name=row loop=$arKD_WORKFLOWS}
	
		<form role="form" action='{$SESSION.PostAction}' method='post' >
		<input type=hidden name=KD_WORKFLOWS[WF_KEY] value={$arKD_WORKFLOWS[row].WF_KEY}>
		<input type=hidden name=action value=UpdDocPath>
			<tr class='{cycle values="RowNormal,RowWhite"}' height='30'>
				<td> {$arKD_WORKFLOWS[row].WF_NAME} </td>
				<td> {$arKD_WORKFLOWS[row].WF_DESCRIPTION} </td>
	  			<td> {$arKD_WORKFLOWS[row].DD_DESCRIPTION} ({$arKD_WORKFLOWS[row].DD_KEYSTORE_DOCTYPE})</td>
				<td> <A HREF="index.php?action=ShowDocWorkflow&KD_WORKFLOWS[WF_KEY]={$arKD_WORKFLOWS[row].WF_KEY}">Workflow Maintenance</A></td>
			</tr>
		</form>
	
		{/section}
	
	{/if}

	<tr class='Title' >	<th colspan=8 aligh='left'> Add New Document Workflow</th>	</tr>

	<tr >
		<form role="form" action='{$SESSION.PostAction}' method='post' >
	<input type=hidden name=KD_WORKFLOWS[WF_KEY] value="">
		<input type=hidden name=action value=UpdDocWorkflow>
			<td>  <input class="form-control" type=text size=20 name=KD_WORKFLOWS[WF_NAME]> </td>
			<td>  <input class="form-control" type=text size=50 name=KD_WORKFLOWS[WF_DESCRIPTION]> </td>
  			<td> {html_options class="form-control" name="KD_WORKFLOWS[WF_DDKEY]" options=$DocTypeList }</td>
			<td  ><input type=submit name=arCtl[Run] value="Add"></td>
		</form>
	</tr>
</table>

  </div>
  <div class="col-sm-1"></div>
</div>
{include file=$SESSION.ENVIRONMENT.EN_FOOTER}
