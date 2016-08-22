{include file=$SESSION.ENVIRONMENT.EN_HEADER}

<div class="row">
  <div class="col-sm-1"></div>
  <div class="col-sm-10">

		


	<form role="form" action='{$SESSION.PostAction}' method='post' >
	<input type=hidden name=action value="UpdDocDef">
	<input type=hidden name=arCtl[Screen] value="ShowDocDef">
	<input type=hidden name=KD_DOCDEF[DD_KEY] value={$KD_DOCDEF.DD_KEY}>
	<input type=hidden name=arCtl[DD_COMPANY] value={$arCtl.DD_COMPANY}>
		<table  class="table table-striped">
		<thead>
			<tr class="Title"><th colspan=10>Document Details</th></tr>
			</thead>
			<tr >
				<td> KeyStore Doc Type </td>
				<td > {html_options class="form-control" name="KD_DOCDEF[DD_KEYSTORE_DOCTYPE]" options=$arDocTypes selected=$KD_DOCDEF.DD_KEYSTORE_DOCTYPE} </TD>
			</tr>
			<tr >
				<td> Description </td>
				<td > <input type=text class="form-control" name="KD_DOCDEF[DD_DESCRIPTION]" value="{$KD_DOCDEF.DD_DESCRIPTION}" size="150" > </TD>
			</tr>
			<tr >
				<td> Header Paper Used? </td>
				<td> {html_options class="form-control" name="KD_DOCDEF[DD_LAYOUT]" options=$LayoutList selected=$KD_DOCDEF.DD_LAYOUT}</td>
			</tr>
			<tr >
				<td colspan="3" ><button type="submit" class="btn btn-default " name=arCtl[Run] value="Update"> Update</button></td>
			</tr>
		</table>
	</form>
<br>
		<table class="table table-bordered table-hover">
			<thead>
			<tr class="Title"><th colspan=10>Document Keys</th></tr>
			
			<tr >
				<th align=left colspan=2> KeyStore Name </td>
				<th align=left > Type </TD>
				<th align=left > Mandatory </TD>
				<th align=left > Default </TD>				
				<th align=left > Read Only </TD>				
				<td >  </TD>
				<td >  </TD>
			
			</tr>
			</thead>
			
				{section name=row loop=$arKD_DOCDEF_KEYS}
					<form role="form" action='{$SESSION.PostAction}' method='post' >
					<input type=hidden name=action value="UpdDocKeys">
					<input type=hidden name=KD_DOCDEF[DD_KEY] value={$KD_DOCDEF.DD_KEY}>
					<input type=hidden name=KD_DOCDEF_KEYS[DDK_KEY] value={$arKD_DOCDEF_KEYS[row].DDK_KEY}>
						<tr  >
							<td> {$arKD_DOCDEF_KEYS[row].DDK_KEYSTORE_KEYORDER} </td>
							<td> {$arKD_DOCDEF_KEYS[row].DDK_KEYSTORE_NAME} </td>
							<td> {html_options class="form-control" name="KD_DOCDEF_KEYS[DDK_TYPE]" options=$arKeyTypeList selected=$arKD_DOCDEF_KEYS[row].DDK_TYPE} </td>
							<td> {html_options class="form-control" name="KD_DOCDEF_KEYS[DDK_MANDATORY]" options=$arYesNoList selected=$arKD_DOCDEF_KEYS[row].DDK_MANDATORY} </td>
							<td> <input class="form-control" type=text name=KD_DOCDEF_KEYS[DDK_DEFAULT] value="{$arKD_DOCDEF_KEYS[row].DDK_DEFAULT}" size="20"> </td>
							<td> {html_options class="form-control" name="KD_DOCDEF_KEYS[DDK_READONLY]" options=$arYesNoList selected=$arKD_DOCDEF_KEYS[row].DDK_READONLY} </td>
							<td ><button type="submit" class="btn btn-default " name=arCtl[Run] value="Update"> Update</button></td>
					</form>
					<form role="form" action='{$SESSION.PostAction}' method='post' >
					<input type=hidden name=action value="DelDocKeys">
					<input type=hidden name=KD_DOCDEF[DD_KEY] value={$KD_DOCDEF.DD_KEY}>
					<input type=hidden name=KD_DOCDEF_KEYS[DDK_KEY] value={$arKD_DOCDEF_KEYS[row].DDK_KEY}>
							<td ><button type="submit" class="btn btn-default " name=arCtl[Run] value="Delete"> Delete</button></td>
					</form>
						</tr>
				{/section}

			<tr class='Title'>
				<td colspan="10"> Add Document Key </td>
			</tr>
					<form role="form" action='{$SESSION.PostAction}' method='post' >
					<input type=hidden name=action value="UpdDocKeys">
					<input type=hidden name=KD_DOCDEF[DD_KEY] value={$KD_DOCDEF.DD_KEY}>
					<input type=hidden name=KD_DOCDEF_KEYS[DDK_KEY] value=>
						<tr  >
							<td colspan=2> {html_options class="form-control" name="KD_DOCDEF_KEYS[DDK_KEYSTORE_NAME]" options=$arDocTypeKeys } </td>
							<td> {html_options class="form-control" name="KD_DOCDEF_KEYS[DDK_TYPE]" options=$arKeyTypeList } </td>
							<td> {html_options class="form-control" name="KD_DOCDEF_KEYS[DDK_MANDATORY]" options=$arYesNoList } </td>
							<td> <input type=text class="form-control" name=KD_DOCDEF_KEYS[DDK_DEFAULT]  size="20"> </td>
							<td> {html_options class="form-control" name="KD_DOCDEF_KEYS[DDK_READONLY]" options=$arYesNoList } </td>
							<td colspan=2><button type="submit" class="btn btn-default " name=arCtl[Run] value="Add"> Add</button></td>
					</form>
					
						</tr>
	
			
		</table>
<br>
		<table class="table table-bordered table-hover">
			<tr class="Title"><th colspan=10>Document Definition Regex</th></tr>
			<tr class="RowNormal">
				<td> Seq </td>
				<td > True / False </TD>
				<td > Regex 1 </TD>
				<td > True / False </TD>				
				<td > Regex 2 </TD>
				<td > True / False </TD>
				<td > Regex 3 </TD>
				<td > True / False </TD>				
				<td > Regex 4 </TD>
				<td colspan="2">  </TD>
			</tr>
			{if $arCtl.DocRegexCount == "0"}
			
			   	<tr height='60' class='RowNormal'>
			
			   	   <td colspan=10>No Documents Found <BR></td>
			
			   	</tr>
			
			{else}
			
				{section name=row loop=$arKD_DOCREGEX_HEAD}
					<form role="form" action='{$SESSION.PostAction}' method='post' >
					<input type=hidden name=action value="UpdDocRegex">
					<input type=hidden name=KD_DOCDEF[DD_KEY] value={$KD_DOCDEF.DD_KEY}>
					<input type=hidden name=KD_DOCREGEX[DR_KEY] value={$arKD_DOCREGEX_HEAD[row].DR_KEY}>
						<tr '>
							<td> <input type=text class="form-control" name=KD_DOCREGEX[DR_SEQ] value="{$arKD_DOCREGEX_HEAD[row].DR_SEQ}" size="3"> </td>
							<td> {html_options class="form-control" name="KD_DOCREGEX[DR_TRUE]" options=$arRegexYesNoList selected=$arKD_DOCREGEX_HEAD[row].DR_TRUE} </td>
							<td> <input type=text class="form-control" name=KD_DOCREGEX[DR_REGEX1] value="{$arKD_DOCREGEX_HEAD[row].DR_REGEX1}" size="30"> </td>
							<td> {html_options class="form-control" name="KD_DOCREGEX[DR_TRUE2]" options=$arRegexYesNoList selected=$arKD_DOCREGEX_HEAD[row].DR_TRUE2} </td>
							<td> <input type=text class="form-control" name=KD_DOCREGEX[DR_REGEX2] value="{$arKD_DOCREGEX_HEAD[row].DR_REGEX2}" size="30"> </td>
							<td> {html_options class="form-control" name="KD_DOCREGEX[DR_TRUE3]" options=$arRegexYesNoList selected=$arKD_DOCREGEX_HEAD[row].DR_TRUE3} </td>
							<td> <input type=text class="form-control" name=KD_DOCREGEX[DR_REGEX3] value="{$arKD_DOCREGEX_HEAD[row].DR_REGEX3}" size="30"> </td>
							<td> {html_options class="form-control" name="KD_DOCREGEX[DR_TRUE4]" options=$arRegexYesNoList selected=$arKD_DOCREGEX_HEAD[row].DR_TRUE4} </td>
							<td> <input type=text class="form-control" name=KD_DOCREGEX[DR_REGEX4] value="{$arKD_DOCREGEX_HEAD[row].DR_REGEX4}" size="30"> </td>
							<td ><button type="submit" class="btn btn-default " name=arCtl[Run] value="Update"> Update</button></td>
					</form>
					<form role="form" action='{$SESSION.PostAction}' method='post' >
					<input type=hidden name=action value="DelDocRegex">
					<input type=hidden name=KD_DOCDEF[DD_KEY] value={$KD_DOCDEF.DD_KEY}>
					<input type=hidden name=KD_DOCREGEX[DR_KEY] value={$arKD_DOCREGEX_HEAD[row].DR_KEY}>
							<td ><button type="submit" class="btn btn-default " name=arCtl[Run] value="Delete"> Delete</button></td>
					</form>
						</tr>
				{/section}

			{/if}

			<tr class='Title'>
				<td colspan="10"> Add New Document Regex </td>
			</tr>
			<form role="form" action='{$SESSION.PostAction}' method='post' >
			<input type=hidden name=action value="UpdDocRegex">
			<input type=hidden name=KD_DOCDEF[DD_KEY] value="{$KD_DOCDEF.DD_KEY}">
			<input type=hidden name=KD_DOCREGEX[DR_TYPE] value="DOC">
			<input type=hidden name=arCtl[DD_COMPANY] value="{$arCtl.DD_COMPANY}">
				<tr  height='30'>
					<td> <input type=text class="form-control" name=KD_DOCREGEX[DR_SEQ] size="3"> </td>
					<td> {html_options class="form-control" name="KD_DOCREGEX[DR_TRUE]" options=$arRegexYesNoList} </td>
					<td> <input type=text class="form-control" name=KD_DOCREGEX[DR_REGEX1] size="30"> </td>
					<td> {html_options class="form-control" name="KD_DOCREGEX[DR_TRUE2]" options=$arRegexYesNoList} </td>
					<td> <input type=text class="form-control" name=KD_DOCREGEX[DR_REGEX2] size="30"> </td>
					<td> {html_options class="form-control" name="KD_DOCREGEX[DR_TRUE3]" options=$arRegexYesNoList} </td>
					<td> <input type=text class="form-control" name=KD_DOCREGEX[DR_REGEX3] size="30"> </td>
					<td> {html_options class="form-control" name="KD_DOCREGEX[DR_TRUE4]" options=$arRegexYesNoList} </td>
					<td> <input type=text class="form-control" name=KD_DOCREGEX[DR_REGEX4] size="30"> </td>					
					<td colspan=2 ><button type="submit" class="btn btn-default " name=arCtl[Run] value="Add"> Add</button></td>
					
				</tr>
			</form>	
		</table>
<BR>


		<table class="table table-bordered table-hover">
			<tr class="Title"><th colspan=10>Index Definition Regex</th></tr>
			<tr >
				<td> Index </td>
				<td> Seq </td>
				<td > Regex 1 </TD>
				<td > Regex 2 </TD>
				<td > Regex 3 </TD>
				<td > Modifier </TD>
				<td colspan="2">  </TD>
			</tr>
			{if $arCtl.IndexRegexCount == "0"}
			
			   	<tr >
			
			   	   <td colspan=10>No Regex <BR></td>
			
			   	</tr>
			
			{else}
			
				{section name=row loop=$arKD_DOCREGEX_INDEX}
					<form role="form" action='{$SESSION.PostAction}' method='post' >
					<input type=hidden name=action value="UpdDocRegex">
					<input type=hidden name=KD_DOCDEF[DD_KEY] value="{$KD_DOCDEF.DD_KEY}">
					<input type=hidden name=arCtl[DD_COMPANY] value="{$arCtl.DD_COMPANY}">
					<input type=hidden name=KD_DOCREGEX[DR_KEY] value="{$arKD_DOCREGEX_INDEX[row].DR_KEY}">
						<tr class='{cycle values="RowNormal,RowWhite"}'>
							<td>{html_options class="form-control" name="KD_DOCREGEX[DR_INDEX]" options=$IndexList selected=$arKD_DOCREGEX_INDEX[row].DR_INDEX}</td>
							<td> <input type=text class="form-control" name=KD_DOCREGEX[DR_SEQ] value="{$arKD_DOCREGEX_INDEX[row].DR_SEQ}" size="3"> </td>
							<td> <input type=text class="form-control" name=KD_DOCREGEX[DR_REGEX1] value="{$arKD_DOCREGEX_INDEX[row].DR_REGEX1}" size="30"> </td>
							<td> <input type=text class="form-control" name=KD_DOCREGEX[DR_REGEX2] value="{$arKD_DOCREGEX_INDEX[row].DR_REGEX2}" size="30"> </td>
							<td> <input type=text class="form-control" name=KD_DOCREGEX[DR_REGEX3] value="{$arKD_DOCREGEX_INDEX[row].DR_REGEX3}" size="30"> </td>
							<td> <input type=text class="form-control" name=KD_DOCREGEX[DR_MODIFIER] value='{$arKD_DOCREGEX_INDEX[row].DR_MODIFIER}' size="100"> </td>
							<td><button type="submit" class="btn btn-default " name=arCtl[Run] value="Update"> Update</button></td>
					</form>
					<form role="form" action='{$SESSION.PostAction}' method='post' >
					<input type=hidden name=action value="UpdDocRegex">
					<input type=hidden name=KD_DOCDEF[DD_KEY] value="{$KD_DOCDEF.DD_KEY}">
					<input type=hidden name=arCtl[DD_COMPANY] value="{$arCtl.DD_COMPANY}">
					<input type=hidden name=KD_DOCREGEX[DR_KEY] value="{$arKD_DOCREGEX_INDEX[row].DR_KEY}">
							<td><button type="submit" class="btn btn-default " name=arCtl[Run] value="Del"> Del</button></td>
					</form>

						</tr>
					</form>	
				{/section}

			{/if}

			<tr class='Title'>
				<td colspan="10"> Add New Document Regex </td>
			</tr>
			<form role="form" action='{$SESSION.PostAction}' method='post' >
			<input type=hidden name=action value="UpdDocRegex">
			<input type=hidden name=KD_DOCDEF[DD_KEY] value={$KD_DOCDEF.DD_KEY}>
			<input type=hidden name=arCtl[DD_COMPANY] value={$arCtl.DD_COMPANY}>
			<input type=hidden name=KD_DOCREGEX[DR_TYPE] value="INDEX">
				<tr >
					<td>{html_options class="form-control" name="KD_DOCREGEX[DR_INDEX]" options=$IndexList }</td>
					<td> <input type=text class="form-control" name=KD_DOCREGEX[DR_SEQ] size="3"> </td>
					<td> <input type=text class="form-control" name=KD_DOCREGEX[DR_REGEX1] size="30"> </td>
					<td> <input type=text class="form-control" name=KD_DOCREGEX[DR_REGEX2] size="30"> </td>
					<td> <input type=text class="form-control" name=KD_DOCREGEX[DR_REGEX3] size="30"> </td>
					<td> <input type=text class="form-control" name=KD_DOCREGEX[DR_MODIFIER] size="100"> </td>
					<td colspan=2><button type="submit" class="btn btn-default " name=arCtl[Run] value="Add"> Add</button></td>
					
				</tr>
			</form>	
		</table>

  </div>
  <div class="col-sm-1"></div>
</div>
{include file=$SESSION.ENVIRONMENT.EN_FOOTER}
