<? /* Template Name: Excess Refund Claim */ ?>
<? /** Excess Refund Product Claim Form **/ ?>

<? get_Header('basic'); ?>

<script type="text/javascript">

function showerror(errormsg){
	//alert(errormsg);
	$('#singupErr').show();
	$('#singupErr').html(errormsg);

}

</script>


<div class="body_container01">
	<div class="innerbox" id="innerbox"> 
	
	
	
    <form id="excess-refund-claim-form" method="POST" class="excess-refund-claim" >
	<strong>Excess Refund Request</strong>
    <hr />
	<p style="font-family:'OpenSans',Arial,sans-serif; text-align:justify; line-height:20px; font-size:15px; color:#000;">Thank you for your Excess Refund Request.</p>
	<p style="font-family:'OpenSans',Arial,sans-serif; text-align:justify; line-height:20px; font-size:15px; color:#000;">In order for us to refund your excess, we do need you to submit certain documents so that we can verify the charges and incident.</p>
	<p style="font-family:'OpenSans',Arial,sans-serif; text-align:justify; line-height:20px; font-size:15px; color:#000;">Please check the list below prior to starting the claim, as we will be unable to process your refund without them.   We recommend that you have everything to hand so that you can reference it whilst you complete the form.  Once we have all the information required from you, we endeavour to complete your refund within 30 days.</p>	
	<p class="listreq">1) Copy of the rental agreement</p>
 	<p class="listreq">2) Copy of the supplier accident report form</p>
	<p class="listreq">3) Copy of the driver's photocard licence</p>
	<p class="listreq">4) Copy of your vehicle check in & check out documents which clearly show the new damage being charged for.</p>
	<p class="listreq">5) Copy of the final invoice confirming the amount paid for accident/damage/loss</p>
	<p class="listreq">6) Copy of the repair invoice</p>
	<p class="listreq">7) Copy of charge receipt (if not on the rental agreement)</p>
	<p class="listreq">8) Evidence of payment (Credit card / Bank Statement showing debit of the damage amount)</p>
	<p class="listreq">9) If a third-party is involved,</p>
	<p class="listreq">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;9.1) Copy of police report if a 3rd party was involved in the incident</p>
	<p class="listreq">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;9.2) For 3rd Party claims, confirmation from the supplier that your claim has been finalised and party identified for damage liability.</p>
	<p class="listreq"><b>Please note: </b>Total Files size should be 15MB otherwise you won't be able to upload files. </p>
	<hr />	
	<input type="checkbox" name="reqdocuments" /> Yes, I have the above
	
	<hr />
	<div id="singupErr" class="ltbx_err"></div>
	<ul>
    	<li>
        	<label>Your booking number<em>*</em></label>
            <input type="text" name="bookingno" id="bookingno" />
        </li>
        <li>
        	<label>Email address used when making the booking<em>*</em></label>
            <input type="text" name="customeremail" id="customeremail" />
        </li>
    </ul>
    <p>Fields marked with a<em>*</em>are mandatory</p>
    <hr />
    <input id="submitbooking" type="submit" value="Submit" name="submitbooking" class="btn-orange"  />
    <a href="#" class="btn-grey">Cancel</a>
    
    </form>

<?
if(isset($_POST["submitbooking"]))
{
 $bookingno=$_POST['bookingno'];
 $customeremail=$_POST['customeremail'];
 if($bookingno == "" || $customeremail == ""){
	$emptyfiledmsg = "Invalid Booking number or Email address";
	echo '<script type="text/javascript"> showerror("'.$emptyfiledmsg.'"); </script>'; 
 }
 else{
  	echo '<script type="text/javascript"> document.getElementById("excess-refund-claim-form").style.display = "none"</script>';
	display($bookingno,$customeremail);
 }
}
?>

<div id="displayhere" style="display:none;"></div>
<?

function display($bookingno,$customeremail){
	$getBookingId = $bookingno;
	$getCustomerEmail = $customeremail;
	$bookingInfo = sprintf('http://mx2.atlaschoice.com:8080/ServiceZ/booking/getInfo/reservationId/%s/email/%s', $getBookingId, $getCustomerEmail);
	//echo $bookingInfo;
	//echo sprintf('<!-- %s -->', $leadingPricesUri); 


	if(isset($getBookingId) && isset($getCustomerEmail))
	{
		$getBookingDetail = file_get_contents($bookingInfo);
		//echo htmlspecialchars($getBookingDetail);
	
		if(($getBookingDetail = json_decode($getBookingDetail)))
		{
			if($getBookingDetail->isError || !$getBookingDetail->isFinished)
			{
				$getBookingDetail = null;
			}
			elseif($getBookingDetail->isFinished)
			{
				$getBookingDetail = simplexml_load_string($getBookingDetail->data);
			}
		}
	}
//print_r($getBookingDetail);
foreach($getBookingDetail->Responses->BookingInfoRS->Extras as $allextras){
	foreach($allextras->Extra as $extraname){
		if($extraname[acrissCode] == "DER" && $extraname == "Excess Refund - Excess Refund Product"){
			$allextrasnames = $extraname;
		}
	}
	
}


	foreach ($getBookingDetail->Responses->BookingInfoRS as $bookingInfoRs){
		$bookingStatus = (string)$bookingInfoRs->attributes()->status;
		$excessDetail = $bookingInfoRs->Extras->Extra;
		//print_r($bookingInfoRs);
		if($bookingStatus == 'failed'){
			//echo $bookingStatus;
			$bookingError = $bookingInfoRs->Errors->Error;
			if($bookingError == 'Reservation number not valid.'){
				$bookingErrorMsg = 'Booking number not valid.Please try again';
			}
			else{
				$bookingErrorMsg = 'Email is incorrect.Please try again';
			}
			//echo '<script type="text/javascript"> showerror("'.$bookingErrorMsg.'"); </script>'; 
			echo '<script type="text/javascript"> showerror("'.$bookingErrorMsg.'"); </script>'; 
		      echo '<script type="text/javascript"> document.getElementById("excess-refund-claim-form").style.display = "block"</script>';

	 	}
		else{
			//echo $bookingStatus;
			//echo $excessDetail;
			if($bookingStatus == 'COMPLETED' && $allextrasnames == 'Excess Refund - Excess Refund Product' && isset($_POST['reqdocuments'])){
				//echo $bookingStatus;
				//echo $excessDetail;	
				?>
				<input type="hidden" name="bookingid"  id="bookingid" value="<?=$bookingno;?>" >
				<input type="hidden" name="email"  id="email" value="<?=$customeremail;?>" >
				<script type="text/javascript">
					var bookingno = $('#bookingid').val();
					var customeremail = $('#email').val();

					location.href = 'http://www2.atlaschoice.com/excess-refund-request/upload/?'+bookingno+'/'+customeremail;
				</script>		
			<?
			}
			else{
				$bookingErrorMsgCheck = "Please confirm that you have required documents or It seems either you don't have required documents or Excess Refund Product with us.";
				echo '<script type="text/javascript"> showerror("'.$bookingErrorMsgCheck.'"); </script>'; 
			        echo '<script type="text/javascript"> document.getElementById("excess-refund-claim-form").style.display = "block"</script>';
			}

		}
	   
	}

}

?>

    </div>
</div>

<? get_Footer('seo'); ?> 	