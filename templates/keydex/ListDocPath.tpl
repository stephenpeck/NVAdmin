{include file=$SESSION.ENVIRONMENT.EN_HEADER}


<div class="row">
  <div class="col-sm-1"></div>
  <div class="col-sm-10">
  
		<table  class="table table-striped">
			<thead>
				<tr  >
					<td> Import </td>
					<td> Description </td>
					<td> Active </td>
					<td>  </td>
				</tr>
			</thead>	
		{if $arCtl.count == "0"}
		
		   	<tr height='60''>
		
		   	   <td colspan=10>No Document Pathways  Found <BR></td>
		
		   	</tr>
		
		{else}
		
			{section name=row loop=$arKD_DOCPATH}
		
			<form action='{$SESSION.PostAction}' method="POST">
			<input type=hidden name=KD_DOCPATH[DP_KEY] value={$arKD_DOCPATH[row].DP_KEY}>
			<input type=hidden name=action value=UpdDocPath>
				<tr >
					<td> {$arKD_DOCPATH[row].DP_IMPORTTYPE} </td>
					<td> {$arKD_DOCPATH[row].DP_DESCRIPTION} </td>
					<td> {$arKD_DOCPATH[row].DP_ACTIVE} </td>
					<td> <A HREF="{$SESSION.PostAction}?action=ShowDocPath&KD_DOCPATH[DP_KEY]={$arKD_DOCPATH[row].DP_KEY}">Path Maintenance</A></td>
				</tr>
			</form>
		
			{/section}
		
		{/if}
	
		<tr height='30' >	<th colspan=8 aligh='left'> Add New Document Path</th>	</tr>
	
		<tr class='RowNormal'>
			<form action='{$SESSION.PostAction}' method="POST">
			<input type=hidden name=KD_DOCPATH[DP_KEY] value="">
			<input type=hidden name=action value=UpdDocPath>
				<td> {html_options  class="form-control" name="KD_DOCPATH[DP_IMPORTTYPE]" options=$ImportTypeList } </td>
				<td>  <input type=text class="form-control" size=50 name=KD_DOCPATH[DP_DESCRIPTION]> </td>
	 			<td> {html_options  class="form-control" name="KD_DOCPATH[DP_ACTIVE]" options=$YesNoList }</td>
				<td  ><button type="submit" class="btn btn-default "> Add New </button></td>
			</form>
		</tr>
	</table>

  </div>
  <div class="col-sm-1"></div>
</div>
{include file=$SESSION.ENVIRONMENT.EN_FOOTER}
