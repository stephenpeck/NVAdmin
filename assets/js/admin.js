

//
//
// Trigger functions
//
//

	$(function() {
		$('.recon').click(function() {
			/* current in table row */
			var bk_amount = parseFloat($(this).closest( "tr" ).find(".bk_amount").html());			
			var bk_key = $(this).closest( "tr" ).find(".bk_key").html();			
			$("#bk_amount").val((bk_amount).toFixed(2));
			$("#bk_amount_form").val((bk_amount).toFixed(2));
			$("#bk_key").val(bk_key);
			$("#bk_reconamount").val("0");
			// now open modal
			$('#Payment').modal('show');
			
		});
	});
	$(function() {
		$('#ShowReconRows').click(function() {
			$('.recontable tr').each(function() {
				$(this).find(".reconrows").addClass("in");
			});
			
		});
	});

	$(function() {
		$('.reconselect').change(function() {
			/* current in table row */
			
			$("#ReconBtn").removeClass("in");
			var bk_reconamount = 0.00

			$('.recontable tr').each(function() {
			  // need this to skip the first row
			  if($(this).find(".reconselect").is(':checked')) {
					var checked_amount = parseFloat($(this).find(".bk_reconamount").html());
					bk_reconamount = bk_reconamount + checked_amount;		
			  }

//				alert(bk_reconamount);	
				$("#bk_reconamount").val((bk_reconamount).toFixed(2));
				if ($("#bk_amount").val() == $("#bk_reconamount").val()){
	//				alert("matched");
					$("#ReconBtn").addClass("in");
				} else {
					$("#ReconBtn").removeClass("in");
				}
				
				
			});

		});
	});

	$(function() {
		$('.manualreconselect').change(function() {
			/* current in table row */
			
			$("#ReconBtn").removeClass("in");
			var bk_reconamount = parseFloat($("#bk_amount").val());

			$("#bk_reconamount").val((bk_reconamount).toFixed(2));
			if ($("#bk_amount").val() == $("#bk_reconamount").val()){
//				alert("matched");
				$("#ReconBtn").addClass("in");
			}
				

		});
	});


//
//
//  JS functions
//
//


