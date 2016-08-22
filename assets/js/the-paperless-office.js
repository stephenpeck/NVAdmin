$(document).ready(function(){
   $("#SelectDocTypeId").change(function(){
		// first close all
		$(".DocDetailsDiv").hide();
	   
		// find open specifc one selected
	   var doctype = "";
	   	doctype = $("select option:selected").val()
	   	doctype = "#" + doctype;
		$(doctype).show();
   });
});
