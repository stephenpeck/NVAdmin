<!--- Start of Footer --->

<br>
</div> <!-- end of container -->

{literal}  
 
	<script src="/assets/js/admin.js"></script>
	
{/literal}  


<footer >
	 <div class="row">
	  <div class="col-sm-1"></div>
	  <div class="col-sm-5">
			
			&copy; Next Venture Trading Ltd 2016
		
	  </div>
	  <div class="col-sm-5" align="right">
			<a href="admin.php?action=ShowHome">Home</A>
			&nbsp;	
			{if $SESSION.BACKURL != "" }		
				<a href="{$SESSION.BACKURL}">Back</A>
			{/if}
		
	  </div>
	  <div class="col-sm-1"></div>
	</div>

</footer>

 </body>
 
 </html>