{include file=$SESSION.ENVIRONMENT.EN_HEADER}


<div class="row">
  <div class="col-sm-1"></div>
  <div class="col-sm-10">
	<form action=index.php method="POST">
	<input type=hidden name=action value="ListDocument">
	<input type=hidden name=arCtl[Run] value="Y">
		<table  class="table table-striped table-hover">
			<thead>
			<tr ><th colspan=17>Search Criteria</th></tr>
			<tr >
				<td> Doc Type  </td><td>{html_options class="form-control" name="arCtl[KDU_DDKEY]" options=$arDocTypes selected=$arCtl.KDU_DDKEY}</td>
				<td> Search </td><td><input class="form-control" type=text size=10 name="arCtl[KDU_TEXT_SEARCH]" value="{$arCtl.KDU_TEXT_SEARCH}" ></td>
				<td> Records </td><td>{html_options class="form-control" name="arCtl[Records]" options=$RecordsList selected=$arCtl.Records}</td>
				<td> Sort </td><td>{html_options class="form-control" name="arCtl[Sort]" options=$SortList selected=$arCtl.Sort}</td>
				<td  ><button type="submit" class="btn btn-default "> Find </button></td>
			</tr>
			</thead>
		</table>
	</form>

	<table  class="table table-striped table-hover">
		<thead>
		<tr>
			<td rowspan="2" width=10% > Date </td>
			<td rowspan="2"  > Doc Load </td>
			<td rowspan="2"  > Type </td>
			<td rowspan="2"  > Size (KB) </td>
			<td colspan="5" width=25% align="center" > Index </td>
			<td rowspan="2" width=10% > WF Status </td>
			<td rowspan="2" width=5% > Doc URL </td>
			<td rowspan="2" width=5% >  </td>
		</tr>
		<tr class="RowNotSoStrong" >
			<td> 1 </td>
			<td> 2 </td>
			<td> 3 </td>
			<td> 4 </td>
			<td> 5 </td>
		</tr>
		</thead>

	{if $arCtl.count == "0"}
	
	   	<tr height='60' class='RowNormal'>
	
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
				<td> {$arKD_DOCUMENT[row].KDU_INDEX4} </td>
				<td> {$arKD_DOCUMENT[row].KDU_INDEX5} </td>
				<td>
					{if $arKD_DOCUMENT[row].WF_STATUS == ""}
						Not Workflow
					{else}
						{$arKD_DOCUMENT[row].WF_STATUS}
					{/if}
				</td>
				{if $arKD_DOCUMENT[row].KDU_DOCURL == ""}
					<td> <A HREF="{$arKD_DOCUMENT[row].KDU_FILENAME}" target="_blank">Show</A></td>
				{elseif $arKD_DOCUMENT[row].KDU_DOCURL != ""}
					<td> <A HREF="{$arKD_DOCUMENT[row].KDU_DOCURL}" target="_blank">Show</A></td>
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
