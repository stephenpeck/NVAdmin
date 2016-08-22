{include file=$SESSION.ENVIRONMENT.EN_HEADER}


<div class="row">
  <div class="col-sm-1"></div>
  <div class="col-sm-10">
  
		<table  class="table table-striped">
	
	<form action="{$SESSION.PostAction}" method="POST"  ENCTYPE="MULTIPART/FORM-DATA">
	<input type=hidden name=KD_DOCPATH[DP_KEY] value={$KD_DOCPATH.DP_KEY}>
	<input type=hidden name=action value=UpdDocPath>
		<thead>
		<tr class="Title">
			<td colspan=5> Document Capture Pathway </td>
		</tr>
		</thead>
		
		<tr>
			<td> Import Type {$KD_DOCPATH.DP_IMPORTTYPE} </td><td> <input type=TEXT size=100 name=KD_DOCPATH[DP_DOCDIR] value="{$KD_DOCPATH.DP_DOCDIR}" >  </td>
			<td> Active </td><td colspan=2> {html_options name="KD_DOCPATH[DP_ACTIVE]" options=$YesNoList selected=$KD_DOCPATH.DP_ACTIVE}</td>
		</tr><tr >	
			<td> Description </td> <td colspan=4> <input type=TEXT size=100 name=KD_DOCPATH[DP_DESCRIPTION] value="{$KD_DOCPATH.DP_DESCRIPTION}" > </td>
		</tr><tr >	
			<td> Doc Type </td><td> {html_options name="KD_DOCPATH[DP_DDKEY]" options=$DocTypeList selected=$KD_DOCPATH.DP_DDKEY}</td>
			<td> Frequency </td><td  colspan=2> {html_options name="KD_DOCPATH[DP_FREQUENCY]" options=$FrequencyList selected=$KD_DOCPATH.DP_FREQUENCY} </td>
		</tr><tr >	
			<td colspan=5 align=center> <input type=submit value="Update"> </td>
	</form>
		<form action="{$SESSION.PostAction}" method="POST"  ENCTYPE="MULTIPART/FORM-DATA">
	<input type=hidden name=KD_DOCPATH[DP_KEY] value={$KD_DOCPATH.DP_KEY}>
	<input type=hidden name=action value=DelDocPath>
		</tr><tr >	
			<td colspan=5 align=center> <input type=submit value="Delete"> </td>
		</tr>
	</form>

		<tr >
			<td> Sequence </td>
			<td> Function </td>
			<td> Description </td>
			<td colspan=2>  </td>
		</tr>
	{if $arCtl.count == "0"}
	
	   	<tr >
	
	   	   <td colspan=10>No Document Pathways Steps Found <BR></td>
	
	   	</tr>
	
	{else}
	
		{section name=row loop=$arKD_DOCPATH_STEPS}
	
		<form action="{$SESSION.PostAction}" method="POST"  ENCTYPE="MULTIPART/FORM-DATA">
		<input type=hidden name=KD_DOCPATH_STEPS[DS_KEY] value={$arKD_DOCPATH_STEPS[row].DS_KEY}>
		<input type=hidden name=KD_DOCPATH[DP_KEY] value={$KD_DOCPATH.DP_KEY}>
		<input type=hidden name=KD_DOCPATH_STEPS[DS_DPKEY] value={$KD_DOCPATH.DP_KEY}>
		<input type=hidden name=action value=UpdDocPathSteps>
			<tr >
				<td> <input type=text size=2 name=KD_DOCPATH_STEPS[DS_SEQ] value="{$arKD_DOCPATH_STEPS[row].DS_SEQ}"> </td>
				<td> {html_options name="KD_DOCPATH_STEPS[DS_FUNCTION]" options=$FunctionList selected=$arKD_DOCPATH_STEPS[row].DS_FUNCTION}  </td>
				<td> <input type=text size=50 name=KD_DOCPATH_STEPS[DS_DESCRIPTION] value="{$arKD_DOCPATH_STEPS[row].DS_DESCRIPTION}"> </td>
			<td  ><input type=submit name=arCtl[Run] value="Update"></td>
		</form>
		<form action="{$SESSION.PostAction}" method="POST"  ENCTYPE="MULTIPART/FORM-DATA">
		<input type=hidden name=KD_DOCPATH_STEPS[DS_KEY] value={$arKD_DOCPATH_STEPS[row].DS_KEY}>
		<input type=hidden name=KD_DOCPATH[DP_KEY] value={$KD_DOCPATH.DP_KEY}>
		<input type=hidden name=KD_DOCPATH_STEPS[DS_DPKEY] value={$KD_DOCPATH.DP_KEY}>
		<input type=hidden name=action value=DelDocPathSteps>
			<td  ><input type=submit name=arCtl[Run] value="Del"></td>
			</tr>
		</form>
	
		{/section}
	
	{/if}

	<tr class='Title' >	<th colspan=8 aligh='left'> Add New Path Step</th>	</tr>

	<tr >
		<form action="{$SESSION.PostAction}" method="POST"  ENCTYPE="MULTIPART/FORM-DATA">
		<input type=hidden name=KD_DOCPATH[DP_KEY] value={$KD_DOCPATH.DP_KEY}>
		<input type=hidden name=KD_DOCPATH_STEPS[DS_DPKEY] value={$KD_DOCPATH.DP_KEY}>
		<input type=hidden name=action value=UpdDocPathSteps>
				<td> <input type=text size=2 name=KD_DOCPATH_STEPS[DS_SEQ]> </td>
				<td> {html_options name="KD_DOCPATH_STEPS[DS_FUNCTION]" options=$FunctionList }  </td>
				<td> <input type=text size=50 name=KD_DOCPATH_STEPS[DS_DESCRIPTION]> </td>
			<td  ><input type=submit name=arCtl[Run] value="Add"></td>
		</form>
	</tr>
</table>

  </div>
  <div class="col-sm-1"></div>
</div>
{include file=$SESSION.ENVIRONMENT.EN_FOOTER}
