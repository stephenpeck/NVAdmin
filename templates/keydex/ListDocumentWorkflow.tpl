{include file=$SESSION.ENVIRONMENT.EN_HEADER}


<div class="row">
  <div class="col-sm-1"></div>
  <div class="col-sm-10">

	<br>
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
					<td> Text Search </td><td><input  class="form-control" type=text size=10 name="arCtl[KDU_TEXT_SEARCH]" value="{$arCtl.KDU_TEXT_SEARCH}" ></td>
					<td > <button type="submit" class="small"> Find </button> </TD>
				</tr>
			</thead>
			</table>
		</form>
	
			<table class="table table-striped table-bordered table-hover" >
			<thead>
			<tr  >
				<th rowspan="2" width=10% > Date </td>
				<th rowspan="2" width=10% > Type </td>
				<th rowspan="2" width=5% > Size (KB) </td>
				<th colspan="4" width=25% align="center" > Index </td>
				<th rowspan="2" width=10% > WF History </td>
				<th rowspan="2" width=10% > Processing Status </td>
				<th rowspan="2" width=5% > Doc URL </td>
				<th rowspan="2" width=5% >  </td>
			</tr>
			<tr class="RowNotSoStrong" >
				<th> Supplier Account </td>
				<th> Reference </td>
				<th> Value </td>
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
					<td> {$arKD_DOCUMENT[row].KDU_DOCTYPE} </td>
					<td align="right"> {$arKD_DOCUMENT[row].SIZE} &nbsp;</td>
					<td> {$arKD_DOCUMENT[row].KDU_INDEX1} </td>
					<td> {$arKD_DOCUMENT[row].KDU_INDEX2} </td>
					<td> {$arKD_DOCUMENT[row].KDU_INDEX3} </td>
					<td> {$arKD_DOCUMENT[row].KDU_INDEX4} </td>
					<td>
						{if $arKD_DOCUMENT[row].WFLOG_COUNT == "0"}
							None
						{elseif $arKD_DOCUMENT[row].WFLOG_COUNT == "-1"}
							No Workflow
						{else}
							<A HREF=index.php?action=ListWorkflowLog&KD_WORKFLOWS_LOG[WFL_KDUKEY]={$arKD_DOCUMENT[row].KDU_KEY} target="_blank">Show ({$arKD_DOCUMENT[row].WFLOG_COUNT})</A>
						{/if}
					</td>
					<td>
					{if $arKD_DOCUMENT[row].KDU_DOCURL == ""}
						{$arKD_DOCUMENT[row].KDU_STATUS} {$arKD_DOCUMENT[row].KDU_PROCESSING_STATUS} {$arKD_DOCUMENT[row].KDU_ERROR_STATUS}
						</td><td> <A HREF="{$arKD_DOCUMENT[row].KDU_FILENAME}" target="_blank">Show</A></td>
					{elseif $arKD_DOCUMENT[row].KDU_DOCURL != ""}
						{$arKD_DOCUMENT[row].KDU_STATUS} {$arKD_DOCUMENT[row].KDU_PROCESSING_STATUS} {$arKD_DOCUMENT[row].KDU_ERROR_STATUS}
						</td><td> <A HREF="{$arKD_DOCUMENT[row].KDU_DOCURL}" target="_blank">Show</A></td>
					{/if}
					<td> <A HREF="index.php?action=ShowDocument&arCtl[KDU_STATUS]={$arCtl.KDU_STATUS|urlencode}&arCtl[KDU_PROCESSING_STATUS]={$arCtl.KDU_PROCESSING_STATUS}&arCtl[KDU_TEXT_SEARCH_2]={$arCtl.KDU_TEXT_SEARCH_2}&arCtl[KDU_TEXT_SEARCH]={$arCtl.KDU_TEXT_SEARCH}&arCtl[KDU_INDEX_STATUS]={$arCtl.KDU_INDEX_STATUS}&arCtl[KDU_SOURCE]={$arCtl.KDU_SOURCE}&KD_DOCUMENT[KDU_KEY]={$arKD_DOCUMENT[row].KDU_KEY}&arCtl[Run]=Y">Amend</A></td>
	
			
				</tr>
		
			{/section}
		
		{/if}
	
	</table>

  </div>
  <div class="col-sm-1"></div>
</div>
{include file=$SESSION.ENVIRONMENT.EN_FOOTER}
