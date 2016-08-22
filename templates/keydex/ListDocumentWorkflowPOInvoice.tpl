{include file=$SESSION.ENVIRONMENT.EN_HEADER}

<div class="row">
  <div class="col-sm-1"></div>
  <div class="col-sm-10">
		<form role="form" action='{$SESSION.PostAction}' method='post' >
		<input type=hidden name=action value="ListDocumentWorkflow">
		<input type=hidden name=arCtl[Run] value="Y">
		<input type=hidden name=arCtl[WF_KEY] value={$arCtl.WF_KEY}>
			<table class="table table-striped table-hover " >
			<thead>
				<tr ><th colspan=17>Search Criteria</th></tr>
				<tr >
					<td> Workflow </td><td>{$KD_WORKFLOWS.WF_DESCRIPTION}</td>
					<td> Status </td><td>{html_options class="form-control" name="arCtl[WFS_STATUS]" options=$StatusList selected=$arCtl.WFS_STATUS}</td>
					<td> Text Search </td><td><input class="form-control" type=text size=10 name="arCtl[KDU_TEXT_SEARCH]" value="{$arCtl.KDU_TEXT_SEARCH}" ></td>
					<td  ><button type="submit" class="btn btn-default "> Find </button></td>
				</tr>
			</thead>
			</table>
		</form>
	
			<table class="table table-striped table-hover" >
			<thead>
			<tr  >
				<th rowspan="2" width=10% > Date </td>
				<th rowspan="2" > Upload Path </td>
				<th rowspan="2" > Document </td>
				<th rowspan="2" width=5% > Size (KB) </td>
				<th colspan="7" width=25% align="center" > Index </td>
				<th rowspan="2" width=5% > Doc URL </td>
				<th rowspan="2" width=5% >  </td>
			</tr>
			<tr  >
				<th> Supplier Account </td>
				<th> Reference </td>
				<th> Value </td>
				<th> Cost Centre </td>
				<th> Company </td>
				<th> DuplicateCheck </td>
				<th> Status </td>
			</tr>
			</thead>
	
		{if $arCtl.count == "0"}
		
		   	<tr >
		
		   	   <td colspan=10>No Documents Found <BR></td>
		
		   	</tr>
		
		{else}
		
			{section name=row loop=$arKD_DOCUMENT}
		
				<tr >
					<td> {$arKD_DOCUMENT[row].KDU_DATE} </td>
					<td> {$arKD_DOCUMENT[row].DP_DESCRIPTION} </td>
					<td> {$arKD_DOCUMENT[row].KDU_DOCTYPE} </td>
					<td align="right"> {$arKD_DOCUMENT[row].SIZE} &nbsp;</td>
					<td> {$arKD_DOCUMENT[row].KDU_INDEX1} </td>
					<td> {$arKD_DOCUMENT[row].KDU_INDEX2} </td>
					<td> {$arKD_DOCUMENT[row].KDU_INDEX3} </td>
					<td> {$arKD_DOCUMENT[row].KDU_INDEX5} </td>
					<td> {$arKD_DOCUMENT[row].KDU_INDEX6} </td>
					<td> {$arKD_DOCUMENT[row].KDU_INDEX7} </td>
					<td> {$arKD_DOCUMENT[row].KDU_INDEX4} </td>
					{if $arKD_DOCUMENT[row].KDU_DOCURL == ""}
						<td> <A HREF="{$arKD_DOCUMENT[row].KDU_FILENAME}" target="_blank">Show</A></td>
					{elseif $arKD_DOCUMENT[row].KDU_DOCURL != ""}
						<td> <A HREF="{$arKD_DOCUMENT[row].KDU_DOCURL}" target="_blank">Show</A></td>
					{/if}
					<td> <A HREF="index.php?action=ShowDocEdit&arCtl[WF_KEY]={$arCtl.WF_KEY}&arCtl[WFS_STATUS]={$arCtl.WFS_STATUS}&arCtl[KDU_TEXT_SEARCH]={$arCtl.KDU_TEXT_SEARCH}&KD_DOCUMENT[KDU_KEY]={$arKD_DOCUMENT[row].KDU_KEY}&arCtl[Run]=Y">Amend</A></td>
			
				</tr>
		
			{/section}
		
		{/if}
	
	</table>

  </div>
  <div class="col-sm-1"></div>
</div>
{include file=$SESSION.ENVIRONMENT.EN_FOOTER}
