{include file=$SESSION.ENVIRONMENT.EN_HEADER}

<div class="row">
  <div class="col-sm-1"></div>
  <div class="col-sm-10">

		<table  class="table table-striped">
		<thead>
			<tr >
				<th> KeyStore Doc Type </td>
				<th> Description </td>
				<th> WorkFlow </td>
				<th></td>
			</tr>
		</thead>

	{if $arCtl.count == "0"}
	
	   	<tr >
	
	   	   <td colspan=10>No Documents Found <BR></td>
	
	   	</tr>
	
	{else}
	
		{section name=row loop=$arKD_DOCDEF}
	
		<form action="{$SESSION.PostAction}" method="POST" >
		<input type=hidden name=KD_DOCDEF[DD_KEY] value={$arKD_DOCDEF[row].DD_KEY}>
		<input type=hidden name=action value=UpdDocDef>
			<tr >
	  			<td> {html_options name="KD_DOCDEF[DD_KEYSTORE_DOCTYPE]" options=$arDocTypes selected=$arKD_DOCDEF[row].DD_KEYSTORE_DOCTYPE}</td>
				<td> {$arKD_DOCDEF[row].DD_DESCRIPTION} </td>
				<td> {$arKD_DOCDEF[row].WF_DESCRIPTION} </td>
	  			<td> <A HREF=index.php?action=ShowDocDef&KD_DOCDEF[DD_KEY]={$arKD_DOCDEF[row].DD_KEY}>Show</A></td>
			</tr>
		</form>
	
		{/section}
	
	{/if}

	<tr height='30' >	<th colspan=8 aligh='left'> Add New Document for {$arCtl.DD_COMPANY} </th>	</tr>

	<tr >
		<form action="{$SESSION.PostAction}" method="POST" >
		<input type=hidden name=KD_DOCDEF[DD_KEY] value="">
		<input type=hidden name=action value=UpdDocDef>
  			<td> {html_options name="KD_DOCDEF[DD_KEYSTORE_DOCTYPE]" options=$arDocTypes }</td>
			<td>  <input type=text class="form-control" name=KD_DOCDEF[DD_DESCRIPTION]> </td>
			<td> {html_options class="form-control" name="KD_DOCDEF[DD_WORKFLOW]" selected=$arKD_DOCDEF[row].DD_WORKFLOW} </td>
			<td><button type="submit" class="btn btn-default " name=arCtl[Run] value="Add"> Add</button></td>
		</form>
	</tr>
</table>

  </div>
  <div class="col-sm-1"></div>
</div>

{include file=$SESSION.ENVIRONMENT.EN_FOOTER}
