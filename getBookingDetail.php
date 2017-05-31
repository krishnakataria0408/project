<? /* Template Name: Excess Refund Claim Upload*/ ?>
<? /** Excess Refund Product Claim Form **/ ?>

<? get_Header('excess'); ?>

<div class="body_container01">
	<div class="innerbox" id="innerbox"> 
	
	
	<? 
		$getBookingUrl = explode('/',$_SERVER['QUERY_STRING']);
		$getBookingNo = trim($getBookingUrl[0]);
		$getCustomerEmail = trim($getBookingUrl[1]);


	
	?>	
	
	<div id="singupErr" class="ltbx_err" style="display:none;"></div>
    <form id="excess-refund-claim-upload-form" method="POST"  action="review-excess-form/" class="excess-refund-claim" enctype="multipart/form-data" >
	<!-- action="review-excess-form/" onsubmit="return atlasChoice.getBookingInfo()" -->
	<strong>Excess Refund Request</strong>
    <hr />

	<ul>
    	<li>
        	<label>Your booking number<em>*</em></label>
            <input type="text" name="bookingno" id="bookingno" value="<?=$getBookingNo?>" readonly="readonly" />
	    <input type="hidden" name="bookingno_disp" value="<?=$getBookingNo?>"  />
        </li>
        <li>
        	<label>Your email address<em>*</em></label>
            <input type="text" name="customeremail" id="customeremail" value="<?=$getCustomerEmail?>" readonly="readonly" />
        </li>
    </ul>

    <?	$bookingInfo = sprintf('http://mx2.atlaschoice.com:8080/ServiceZ/booking/getInfo/reservationId/%s/email/%s', $getBookingNo, $getCustomerEmail);
	//echo $bookingInfo;
	//echo sprintf('<!-- %s -->', $leadingPricesUri); 


	if(isset($getBookingNo) && isset($getCustomerEmail))
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
	//print_r($getBookingDetail);  die();
	$bookingStatus = (string)$getBookingDetail->Responses->BookingInfoRS->attributes()->status; 
	$excessDetail = (string)$getBookingDetail->Responses->BookingInfoRS->Extras->Extra; 
	//echo $bookingStatus;	
		if($bookingStatus == 'failed'){
			
			//echo $bookingStatus;
			$bookingError = $getBookingDetail->Responses->BookingInfoRS->Errors->Error;
			if($bookingError == 'Reservation number not valid.'){
				$bookingErrorMsg = 'Booking number not valid. Please try again from here ';
			}
			else{
				$bookingErrorMsg = 'Email is incorrect. Please try again from here ';
			}			
			echo '<script type="text/javascript"> showerror("'.$bookingErrorMsg.'"); </script>';    	
			
			
	 	}
		else{ ?>

	
	<?   
		$titleFirstName = $getBookingDetail->Responses->BookingInfoRS->CustName->attributes()->title."   ".(string)$getBookingDetail->Responses->BookingInfoRS->CustName->attributes()->firstName;
	?>

    <div id="renterDetailHead" class="blockHeadTitle">Renter details</div>
    <div id="uploadreport" style="display:block;">
    <table class="detailTable" id="renterDetailTable">	
	<tr>
	  <td class="leftTitle">Title, First name</td>
	  <td colspan="3"><input type="text" name="titleFirstName" id="firstname" value="<?=$titleFirstName?>" readonly="readonly" /></td>
	</tr>
	<tr>
	  <td class="leftTitle">Last name</td>
	  <td colspan="3"><input type="text" name="lastName" id="lastname" value="<?=(string)$getBookingDetail->Responses->BookingInfoRS->CustName->attributes()->lastName;?>" readonly="readonly" /></td>
	</tr>
	<tr>
	  <td class="leftTitle">Address</td>
	  <td colspan="3"><input type="text" name="address" id="address" value="<?=(string)$getBookingDetail->Responses->BookingInfoRS->Contact->attributes()->address;?>" readonly="readonly" /></td>
	</tr>
	<tr>
	  <td class="leftTitle">Postcode</td>
	  <td><input type="text" name="postcode" id="postcode" value="<?=(string)$getBookingDetail->Responses->BookingInfoRS->Contact->attributes()->zip;?>" readonly="readonly" /></td>
	  <td class="leftTitle">City</td>
	  <td><input type="text" name="city" id="city" value="<?=(string)$getBookingDetail->Responses->BookingInfoRS->Contact->attributes()->city?>" readonly="readonly" /></td>
	</tr>
	<tr>
	  <td class="leftTitle">Country</td>
	  <td colspan="3"><input type="text" name="country" id="country" value="<?=(string)$getBookingDetail->Responses->BookingInfoRS->Contact->attributes()->country;?>" readonly="readonly" /></td>
	</tr>
	<tr>
	  <td class="leftTitle">Telephone Number</td>
	  <td colspan="3"><input type="text" name="telephone" id="telephone" value="<?=(string)$getBookingDetail->Responses->BookingInfoRS->Contact->attributes()->phone;?>" readonly="readonly" /></td>
	</tr>
	<tr>
	  <td class="leftTitle">E-mail address</td>
	  <td colspan="3"><input type="text" name="email" id="email" value="<?=(string)$getBookingDetail->Responses->BookingInfoRS->Contact->attributes()->email;?>" readonly="readonly" /></td>
	</tr>
	<tr>
		<td colspan="4" class="sepTable">&nbsp;</td>
	</tr>
	<tr class="tableTitle">
	  <td colspan="4">Car hire details</td>
	</tr>
	<tr>
	  <td class="leftTitle">Pick-up date</td>
	  <td><input type="text" name="pickupdate" id="pickupdate" value="<?=$getBookingDetail->Responses->BookingInfoRS->PickUpLoc['time'];?>" readonly="readonly" /></td>
	  <td class="leftTitle">Drop-off date</td>
	  <td><input type="text" name="dropoffdate" id="dropoffdate" value="<?=$getBookingDetail->Responses->BookingInfoRS->DropOffLoc['time'];?>" readonly="readonly" /></td>
	</tr>
	<tr>
	  <td class="leftTitle">Pick-up location</td>
	  <td colspan="3"><input type="text" name="pickuplocation" id="pickuplocation" value="<?=$getBookingDetail->Responses->BookingInfoRS->PickUpLoc;?>" readonly="readonly" /></td>
	</tr>
	<tr>
	  <td class="leftTitle">Drop-off location</td>
	  <td colspan="3"><input type="text" name="dropofflocation" id="dropofflocation" value="<?=$getBookingDetail->Responses->BookingInfoRS->DropOffLoc;?>" readonly="readonly" /></td>
	</tr>
	<tr>
	  <td class="leftTitle">Vehicle Registration Number</td>
	  <td colspan="3" class="inputValue"><input type="text" name="vehicleRegNo" id="vehicleRegNo" placeholder="please enter here" /></td>
	</tr>
	<tr>
	  <td class="leftTitle">Rental Agreement Number</td>
	  <td colspan="3" class="inputValue"><input type="text" name="rentalAgrNo" id="rentalAgrNo" placeholder="please enter here" /></td>
	</tr>
	<tr>
		<td colspan="4" class="sepTable"><input type="button" name="validateRenterDetails" id="validateRenterDetails" class="validateBtns" value="Is above correct?" style="width:250px;" /></td>
	</tr>	
    </table>
	

	<div id="incidentDetailHead" class="blockHeadTitle" style="display:none;">Incident details & Driver Information</div>
	<div id="incidentTableBlock" style="display:none;">
	<table id="incidentTable" class="incidentTable">
	<tr id="driverIncidentInfo">
	  <td class="leftTitle">Were you a driver at the time of an incident?</td>
	  <td colspan="3" class="inputValue"><input type="radio" name="driverInfo" id="driverInfo" value="Yes" /> Yes <input type="radio" name="driverInfo" value="No" /> No </td>
	</tr>
	</table>
	</div>
	
	<div id="driverInfoBlock" style="display:none;">
	<div id="requireMsg" style="display:none;" class="requireMsg">Please fill in require fields highlighted in red.</div>
	<table class="driverDetailTable" id="driverDetailTable2">
	<tr>
	  <td class="dleftTitle">Title, First name, Last name (driver)</td>
	  <td class="dinputValue"><input type="text" name="fullname" id="fullname_require" value="<?=$titleFirstName.'  '.(string)$getBookingDetail->Responses->BookingInfoRS->CustName->attributes()->lastName;?>" class="test" /></td>
	  <td class="dleftTitle">Age</td>
	  <td class="dinputValue"><input type="text" name="driverage" id="driverage_require" value="<?=$getBookingDetail->Responses->BookingInfoRS->DrvAge.'   (DOB: '.$getBookingDetail->Responses->BookingInfoRS->DateOfBirth.')';?>" /></td>
	</tr>
	<tr>
	  <td class="dleftTitle">Address</td>
	  <td colspan="3" class="dinputValue"><input type="text" name="driveraddress" id="driveraddress_require" value="<?=(string)$getBookingDetail->Responses->BookingInfoRS->Contact->attributes()->address;?>" /></td>
	</tr>
	<tr>
	  <td class="dleftTitle">Postcode</td>
	  <td class="dinputValue"><input type="text" name="driverpostcode" id="driverpostcode_require" value="<?=(string)$getBookingDetail->Responses->BookingInfoRS->Contact->attributes()->zip;?>" /></td>
	  <td class="dleftTitle">City</td>
	  <td class="dinputValue"><input type="text" name="drivercity" id="drivercity_require" value="<?=(string)$getBookingDetail->Responses->BookingInfoRS->Contact->attributes()->city?>" /></td>
	</tr>	
	<tr>
	  <td class="dleftTitle">Telephone Number</td>
	  <td colspan="3" class="dinputValue"><input type="text" name="drdivertelephone" id="drivertelephone_require" value="<?=(string)$getBookingDetail->Responses->BookingInfoRS->Contact->attributes()->phone;?>" /></td>
	</tr>
	<tr>
	  <td class="dleftTitle">E-mail address</td>
	  <td colspan="3" class="dinputValue"><input type="text" name="driveremail" id="driveremail_require" value="<?=(string)$getBookingDetail->Responses->BookingInfoRS->Contact->attributes()->email;?>" /></td>
	</tr>
	<tr>
	  <td class="dleftTitle">Driver's licence number</td>
	  <td colspan="3" class="inputValue"><input type="text" name="drLicenceNo" id="drLicenceNo_require" placeholder="Please enter here" /></td>
	</tr>
	<tr>	  
	  <td colspan="4" class="sepTable"><input type="button" name="validateIncidentDriver" id="validateIncidentDriver" class="validateBtns" value="Continue" style="width:250px;" onclick="checkIncidentFields();" /></td>	
	</table>	
	
	</tr>
	
	</div>
	
	<div id="requireMsgIncident" style="display:none;" class="requireMsg">Please fill in require fields highlighted in red.</div>
	<div id="incidentHead" class="blockHeadTitle" style="display:none;">Incident details</div>
	<div id="accidentDetail" style="display:none;">
	<table class="detailTable">	
	<tr class="leftTitle">
	  <td colspan="4">Please provide full details of any car rental accidents/claims in last 5 years.</td>
	</tr>
	<tr>
	  <td colspan="4" style="padding:0px;"><textarea rows="4" cols="108" class="detailTextArea" name="pastAccidentClaim"></textarea></td>
	</tr>
	<tr>
		<td colspan="3"  class="leftTitle">Any past and pending convictions (including penalties and endorsements)</td>				
		<td class="inputValue"><input type="radio" name="convictions" id="convictions" value="Yes" /> Yes <input type="radio" name="convictions" value="No" /> No </td>
			
	</tr>
	<tr id="convictionsInfoRow">
		<td class="leftTitle">If yes, please provide more information</td>
		<td class="inputValue" colspan="3"><textarea name="convictionsInfo" cols="65" rows="4"></textarea></td>
	</tr>
	<tr>
		<td colspan="3" class="leftTitle">Was the rental vehicle being used in compliance with the Rental agreement conditions</td>				
		<td class="inputValue" id="complianceRow"><input type="radio" name="compliance" id="compliance_require" value="Yes" /> Yes <input type="radio" name="compliance" value="No" /> No </td>			
	</tr>
	<tr id="complianceInfoRow">
		<td class="leftTitle">If no, please provide more information</td>
		<td class="inputValue" colspan="3"><textarea name="complianceInfo" id="compInfoTxtAr_require" cols="65" rows="4"></textarea></td>
	</tr>
	<tr>
	  <td class="leftTitle">Incident date and time</td>
	  <td colspan="3" class="inputValue"><input type="text" name="incidentDateTime" id="incidentDateTime_require" /></td>
	</tr>
	<tr>
	  <td colspan="1" class="leftTitle">Exact location of incident</td>
	  <td colspan="3" class="inputValue"><input type="text" name="incidentLoc" id="incidentLoc_require" /></td>
	</tr>
	<tr class="leftTitle">
	  <td colspan="4">Was the incident reported to the car rental provider? If so, when was it reported?</td>
	</tr>
	<tr>
	  <td colspan="4" style="padding:0px;"><textarea rows="4" cols="108" name="incReported" id="incReported_require" class="detailTextArea"></textarea></td>
	</tr>
	<tr>
	  <td class="leftTitle">If you are applying due to an accident, was the alcohol level test performed?</td>
	  <td colspan="3" class="inputValue" id="alocholInfoCheck"><input type="radio" name="alcoholInfo" id="alcoholInfo_require" value="Yes" /> Yes <input type="radio" name="alcoholInfo" value="No" /> No </td>
	</tr>
	<tr>
	  <td colspan="4">If no, please confirm below statement:</td>
	</tr>
	<tr class="leftTitle">
	  <td colspan="3">I hereby declare that I was not under the influence of alcohol/drugs/prescription medication at the time of the accident, nor had I taken alcohol/drugs/prescription medication before the accident</td>
	  <td id="alcoholCheckDeclare" class="inputValue">I confirm  <input type="checkbox" name="alcoholDeclare" id="alcoholDeclare_require" value="Yes" /></td>
	</tr>
	<tr class="leftTitle">
	  <td colspan="4">Reason(s) of incident (please answer ALL questions below):</td>
	</tr>
	<tr class="leftTitle">
	  <td colspan="4">a) What happened immediately before the accident (weather/road conditions, road topography, etc)?
	  <textarea rows="4" cols="108" name="reasonAbeforeInc" class="detailTextArea" id="reasonAbeforeInc_require" ></textarea></td>
	</tr>
	<tr class="leftTitle">
	  <td colspan="4">b) How did the incident happen (speed of the vehicle, position of vehicle, traffic signals, etc)?
	  <textarea rows="4" cols="108" name="reasonBHowInc" class="detailTextArea" id="reasonBHowInc_require" ></textarea></td>
	</tr>
	<tr class="leftTitle">
	  <td colspan="4">c) What happened after the accident?
	  <textarea rows="4" cols="108" name="reasonCAfterInc" class="detailTextArea" id="reasonCAfterInc_require" ></textarea></td>
	</tr>
	<tr>	  
	  <td colspan="4" class="sepTable"><input type="button" name="validateIncidentInfo" id="validateIncidentInfo" class="validateBtns" value="Continue" style="width:250px;" onclick="valIncidentInfoFields();" /></td>
	</tr>
	</table>
	</div>

	<div id="requireMsgThirdParty" style="display:none;" class="requireMsg">Please fill in require fields highlighted in red.</div>
	<div id="thirdPartyHead" class="blockHeadTitle" style="display:none;">3rd Party Involvement (accident / damage / loss)</div>
	<div id="thirdPartyInvolve" style="display:none;">
	<table class="detailTable">
	<!--tr class="tableTitle">
	  <td colspan="4">3rd Party Involvement (accident / damage / loss)</td>
	</tr-->
	<tr>
	  <td class="leftTitle">Is there was 3rd party involvement?</td>
	  <td colspan="3" class="inputValue"><input type="radio" name="thirdPartyInfo" id="thirdPartyInfo" value="Yes" /> Yes <input type="radio" name="thirdPartyInfo" value="No" /> No </td>
	</tr>
	</table>
	</div>

	<div id="thirdPartyInfoBlock" style="display:none;">
	<table class="driverDetailTable">	
	<tr>
	  <td class="dleftTitle">Address</td>
	  <td colspan="3" class="dinputValue"><input type="text" name="thirdPartyAddress" id="tpAddress_require" /></td>
	</tr>
	<tr>
	  <td class="dleftTitle">Postcode</td>
	  <td class="dinputValue"><input type="text" name="thirdPartypostcode" id="tpPostcode_require" /></td>
	  <td class="dleftTitle">City</td>
	  <td class="dinputValue"><input type="text" name="thirdPartycity" id="tpcity_require" /></td>
	</tr>	
	<tr>
	  <td class="dleftTitle">Telephone Number</td>
	  <td colspan="3" class="dinputValue"><input type="text" name="thirdPartytelephone" id="tptelephone_require" /></td>
	</tr>
	<tr>
	  <td class="dleftTitle">E-mail address</td>
	  <td colspan="3" class="dinputValue"><input type="text" name="thirdPartyemail" id="tpEmail_require" /></td>
	</tr>
	<tr>
	  <td class="dleftTitle">3rd Party insurance company's name</td>
	  <td colspan="3" class="inputValue"><input type="text" name="thirdPartyInsuranceName" id="tpCompanyName_require" placeholder="Please enter here" /></td>
	</tr>
	<tr>
	  <td class="dleftTitle">3rd Party insurance company's address</td>
	  <td colspan="3" class="inputValue"><input type="text" name="thirdPartyInsuranceAdd" id="tpCompAdd_require" placeholder="Please enter here" /></td>
	</tr>
	<tr>
	  <td class="dleftTitle">Policy number</td>
	  <td colspan="3" class="inputValue"><input type="text" name="thirdPartyPolicyNo" id="tpPolicyNo_require" placeholder="Please enter here" /></td>
	</tr>
	<tr>
	  <td class="dleftTitle">Insurance company contact person's name and position</td>
	  <td colspan="3" class="inputValue"><input type="text" name="thirdPartyContactName" id="tpContactName_require" placeholder="Please enter here" /></td>
	</tr>
	<tr>
	  <td class="dleftTitle">Telephone Number</td>
	  <td colspan="3" class="inputValue"><input type="text" name="thirdPartyTelephoneNo" id="tpTelNo_require" placeholder="Please enter here" /></td>
	</tr>
	<tr>
	  <td class="dleftTitle">E-mail address</td>
	  <td colspan="3" class="inputValue"><input type="text" name="thirdPartyComEmail" id="tpComEmail_require" placeholder="Please enter here" /></td>
	</tr>
	<tr>
	  <td colspan="2" class="dleftTitle">Was an accident report with the 3rd party obtained? If so, please provide a copy</td>
	  <td class="inputValue tpReportValue"><input type="radio" name="thirdPartyReport" id="tpReport_require" value="Yes" /> Yes</td>
	  <td class="inputValue tpReportValue"><input type="radio" name="thirdPartyReport" value="No" /> No</td>
	</tr>
	<tr>
	  <td colspan="2" class="dleftTitle">According to you, which party was responsible for the accident?</td>
	  <td colspan="2" class="inputValue"><input type="text" name="thirdPartyResAcccident" id="tpResAcccident_require" placeholder="Please enter here" /></td>
	</tr>
	<tr>
	  <td colspan="2" class="dleftTitle">Was there any witness (es)? If so, please provide their declaration</td>
	  <td class="inputValue tpWitnessDecl"><input type="radio" name="witnessDecl" id="tpwitnessDecl_require" value="Yes" /> Yes</td>
	  <td class="inputValue tpWitnessDecl"><input type="radio" name="witnessDecl" value="No" /> No</td>
	</tr>
	<!--tr id="thirdPartyBtn">	  
	  <td colspan="4" class="sepTable"><input type="button" name="validateThirdPartyInfo" id="validateThirdParty" class="validateBtns" value="Continue" style="width:250px;" onclick="valThirdInfoFields();" /></td>
	</tr-->
	</table>
	</div>

	<div id="thirdPartyBtn" style="display:none;">	  
	  <input type="button" name="validateThirdPartyInfo" id="validateThirdParty" class="validateBtns" value="Continue" onclick="valThirdInfoFields();" />
	</div>
	
	<div id="requireMsgPoliceInvolve" style="display:none;" class="requireMsg">Please fill in require fields highlighted in red.</div>
	<div id="policeInvolveHead" class="blockHeadTitle" style="display:none;">Police Involvement</div>
	<div id="driverDetailBlock" style="display:none;">
	<table class="detailTable">		
	<tr>
	  <td class="leftTitle">Is there was an involvement of police?</td>
	  <td colspan="3" class="inputValue"><input type="radio" name="policeInInfo" id="policeInInfo" value="Yes" /> Yes <input type="radio" name="policeInInfo" value="No" /> No </td>
	</tr>
	<tr id="policeConjuction" style="display:none;">
	  <td class="leftTitle">Is the police was involved in conjuction of third party claim?</td>
	  <td colspan="3" class="inputValue"><input type="radio" name="policeInThirdInfo" id="policeInThirdInfo" value="Yes" /> Yes <input type="radio" name="policeInThirdInfo" value="No" /> No </td>
	</tr>	
	</table>
	</div>
	

	<div id="policeInfoBlock" style="display:none;">
	<table class="driverDetailTable">		
	<tr>
	  <td class="dleftTitle">If yes, specify which authority name and location</td>
	  <td class="dinputValue"><input type="text" name="authNameLoc" id="authNameLoc_require" /></td>
	  <td class="dleftTitle">Name of the officer</td>
	  <td class="dinputValue"><input type="text" name="nameOfficer" id="authNameOfficer_require" /></td>
	</tr>	
	<tr>
	  <td class="dleftTitle">Incident report ref number</td>
	  <td colspan="3" class="dinputValue"><input type="text" name="reportRefNo" id="reportRefNo_require" /></td>
	</tr>		
	</table>
	</div>

	<div id="validatePolice" style="display:none;">
	<input type="button" name="validatePoliceInfo" id="validatePolice" class="validateBtns" value="Continue" onclick="valPoliceInfoFields();" />
	</div>
	
	<div id="requireMsgAccount" style="display:none;" class="requireMsg">Please fill in require fields highlighted in red.</div>
	<div id="accountHead" class="blockHeadTitle" style="display:none;">Particulars of Excess Claim Settlement</div>
	<div id="excessClaimSet" style="display:none;">
	<table class="driverDetailTable">	
	<tr>
	  <td>&nbsp;</td>
	  <td class="leftTitle">Local Currency/Amount</td>
	  <td class="leftTitle">Settlement Currency/Amount</td>	  
	</tr>	
	<tr>
	  <td class="leftTitle">Invoiced amount for excess/accident/damage/loss</td>
	  <td class="inputValue">
		<select name="locCurrency" id="locCur_require" width="180">
			<option value="aed">AED</option>
			<option value="amd">AMD</option>
			<option value="arp">ARP</option>
			<option value="ars">ARS</option>
			<option value="aud">AUD</option>
			<option value="bhd">BHD</option>
			<option value="brl">BRL</option>
			<option value="bsd">BSD</option>
			<option value="bwp">BWP</option>
			<option value="cad">CAD</option>
			<option value="chf">CHF</option>
			<option value="clp">CLP</option>
			<option value="cny">CNY</option>
			<option value="cop">COP</option>
			<option value="cve">CVE</option>
			<option value="cyp">CYP</option>
			<option value="czk">CZK</option>
			<option value="dkk">DKK</option>
			<option value="dkr">DKR</option>
			<option value="eek">EEK</option>
			<option value="eur">EUR</option>
			<option value="fjd">FJD</option>
			<option value="gbp">GBP</option>
			<option value="gel">GEL</option>
			<option value="ghc">GHC</option>
			<option value="hkd">HKD</option>
			<option value="hnl">HNL</option>
			<option value="hrk">HRK</option>
			<option value="huf">HUF</option>
			<option value="idr">IDR</option>
			<option value="ils">ILS</option>
			<option value="inr">INR</option>
			<option value="irr">IRR</option>
			<option value="isk">ISK</option>
			<option value="jod">JOD</option>
			<option value="jpy">JPY</option>
			<option value="krw">KRW</option>
			<option value="kwd">KWD</option>
			<option value="kzt">KZT</option>
			<option value="lkr">LKR</option>
			<option value="lsl">LSL</option>
			<option value="ltl">LTL</option>
			<option value="lvl">LVL</option>
			<option value="lyd">LYD</option>
			<option value="mad">MAD</option>
			<option value="mtl">MTL</option>
			<option value="mur">MUR</option>
			<option value="mxn">MXN</option>
			<option value="myr">MYR</option>
			<option value="nad">NAD</option>
			<option value="nok">NOK</option>
			<option value="nzd">NZD</option>
			<option value="omr">OMR</option>
			<option value="pab">PAB</option>
			<option value="pen">PEN</option>
			<option value="php">PHP</option>
			<option value="pkr">PKR</option>
			<option value="pln">PLN</option>
			<option value="qar">QAR</option>
			<option value="rub">RUB</option>
			<option value="sar">SAR</option>
			<option value="sek">SEK</option>
			<option value="sgd">SGD</option>
			<option value="thb">THB</option>
			<option value="tnd">TND</option>
			<option value="trl">TRL</option>
			<option value="ttd">TTD</option>
			<option value="twd">TWD</option>
			<option value="usd">USD</option>
			<option value="veb">VEB</option>
			<option value="vuv">VUV</option>
			<option value="xaf">XAF</option>
			<option value="xof">XOF</option>
			<option value="xpf">XPF</option>
			<option value="zar">ZAR</option>
		</select>
		<input type="text" name="excessInvAmtLocal" id="excessInvAmtLocal_require" style="width:70%;" />
	  </td>
	  <td class="inputValue">		
		<select name="setlCurrency" id="setCur_require" width="180">
			<option value="aed">AED</option>
			<option value="amd">AMD</option>
			<option value="arp">ARP</option>
			<option value="ars">ARS</option>
			<option value="aud">AUD</option>
			<option value="bhd">BHD</option>
			<option value="brl">BRL</option>
			<option value="bsd">BSD</option>
			<option value="bwp">BWP</option>
			<option value="cad">CAD</option>
			<option value="chf">CHF</option>
			<option value="clp">CLP</option>
			<option value="cny">CNY</option>
			<option value="cop">COP</option>
			<option value="cve">CVE</option>
			<option value="cyp">CYP</option>
			<option value="czk">CZK</option>
			<option value="dkk">DKK</option>
			<option value="dkr">DKR</option>
			<option value="eek">EEK</option>
			<option value="eur">EUR</option>
			<option value="fjd">FJD</option>
			<option value="gbp">GBP</option>
			<option value="gel">GEL</option>
			<option value="ghc">GHC</option>
			<option value="hkd">HKD</option>
			<option value="hnl">HNL</option>
			<option value="hrk">HRK</option>
			<option value="huf">HUF</option>
			<option value="idr">IDR</option>
			<option value="ils">ILS</option>
			<option value="inr">INR</option>
			<option value="irr">IRR</option>
			<option value="isk">ISK</option>
			<option value="jod">JOD</option>
			<option value="jpy">JPY</option>
			<option value="krw">KRW</option>
			<option value="kwd">KWD</option>
			<option value="kzt">KZT</option>
			<option value="lkr">LKR</option>
			<option value="lsl">LSL</option>
			<option value="ltl">LTL</option>
			<option value="lvl">LVL</option>
			<option value="lyd">LYD</option>
			<option value="mad">MAD</option>
			<option value="mtl">MTL</option>
			<option value="mur">MUR</option>
			<option value="mxn">MXN</option>
			<option value="myr">MYR</option>
			<option value="nad">NAD</option>
			<option value="nok">NOK</option>
			<option value="nzd">NZD</option>
			<option value="omr">OMR</option>
			<option value="pab">PAB</option>
			<option value="pen">PEN</option>
			<option value="php">PHP</option>
			<option value="pkr">PKR</option>
			<option value="pln">PLN</option>
			<option value="qar">QAR</option>
			<option value="rub">RUB</option>
			<option value="sar">SAR</option>
			<option value="sek">SEK</option>
			<option value="sgd">SGD</option>
			<option value="thb">THB</option>
			<option value="tnd">TND</option>
			<option value="trl">TRL</option>
			<option value="ttd">TTD</option>
			<option value="twd">TWD</option>
			<option value="usd">USD</option>
			<option value="veb">VEB</option>
			<option value="vuv">VUV</option>
			<option value="xaf">XAF</option>
			<option value="xof">XOF</option>
			<option value="xpf">XPF</option>
			<option value="zar">ZAR</option>
		</select>
		<input type="text" name="excessInvAmtGbp" id="excessSetAmt_require" style="width:70%;" />
	  </td>	  
	</tr>
	<tr>
		<td colspan="5" style="border:none;">&nbsp;</td>
	</tr>
	<tr>
	  <td colspan="5" class="leftTitle" >Bank details for Refund</td>
	</tr>
	<? $countryOfResidence= $getBookingDetail->Responses->BookingInfoRS->ResidenceCountry; ?>
	<input type="hidden" id="residenceCountry" name="residenceCountry" value="<?=strtolower($countryOfResidence);?>" />
	<tr>
	  <td class="leftTitle">Name of the Bank</td>
	  <td colspan="4" class="inputValue"><input type="text" name="bankNane" id="bankName_require" /></td>
	</tr>	
	<tr>
	  <td class="leftTitle">Name of the account holder(as per bank account)</td>
	  <td colspan="4" class="inputValue"><input type="text" name="accountHolder" id="accountHolder_require" />
	  </td>
	</tr>
	<? if(strtolower($countryOfResidence) == "gb"){ ?>
	<tr>
	  <td class="leftTitle">Account Number</td>
	  <td colspan="4" class="inputValue"><input type="text" name="accountNumber" id="accountNumber_require" /></td>
	</tr>
	<tr>
	  <td class="leftTitle">Sort Code</td>
	  <td colspan="4" class="inputValue"><input type="text" name="accountSortcode" id="accountSortcode_require" /></td>
	</tr>
	<? }
	else{ ?>
	<tr>
	  <td class="leftTitle">IBAN</td>
	  <td colspan="4" class="inputValue"><input type="text" name="accountIban" id="accountIban_require" /></td>
	</tr>	
	<tr>
	  <td class="leftTitle">BIC/Swift Code</td>
	  <td colspan="4" class="inputValue"><input type="text" name="accountSwiftcode" id="accountSwiftcode_require" /></td>
	</tr>
	<? } ?>
	<tr>
		<td colspan="5" style="border:none">&nbsp;</td>
	</tr>
	<tr>
	  <td colspan="5">I declare that the above statement is true and complete to the best of my knowledge and belief.</td>
	</tr>
	<tr>
	  <td>Date</td>
	  <td colspan="4" class="inputValue"><input type="text" name="declareDate" id="declareDate_require" placeholder="dd/mm/yyyy" /></td>
	</tr>
	<tr>
	  <td>Signature</td><!--input type="text" name="declareSign" id="declareSign" /-->
	  <td colspan="4" id="signature-pad" class="m-signature-pad">
		<!--canvas class="inputCanvas" id="signCanvas"></canvas-->

		<div class="m-signature-pad--body">   
		        <canvas id="signCanvas" tabindex="0" contentEditable="true" ></canvas>			
      		        <input type="button" class="button clear" id="signClear" value="Clear" />
	      		<input type="button" class="button save" id="signSave" value="Save" />
			<input id="sign"  name="sign" type="hidden" />
		</form>
		<!--input type="button" class="button clear" id="signClear" value="Clear" /-->
	      	<!--input type="button" class="button save" id="signSave" value="Save" /-->
		 <!--div id="signdisp"></div-->
		<!--textarea id="sign"  name="sign" style="display: none"></textarea-->
	  </td>
	</tr>
	<tr id="validateAccountInfo">
	<td colspan="4"><input type="button" name="accountInfo" id="vldAccountInfo" class="validateBtns" value="Continue" onclick="valAccountInfoFields();" /></td>
	</tr>
	</table>
	</div>
	
    <div id="fileUploadHead" class="blockHeadTitle" style="display:none;">Report Files Uploaded</div>
    <ul id="uploadFiles" style="display:none">
	<hr />
	
    	<li>
        	<label>Copy of the rental agreement<em>*</em></label>
            <input type="file" name="rentalagreement" id="uploadreports1" />
        </li>
        <li>
        	<label>Copy of the supplier accident report form<em>*</em></label>
            <input type="file" name="supplieraccidentrep" id="uploadreports2" />
        </li>
        <li>
        	<label>Copy of the driver’s photocard licence<em>*</em></label>
            <input type="file" name="driverlicence" id="uploadreports3" />
        </li>
        <li>
        	<label>Copy of vehicle check in & check out<em>*</em></label>
            <input type="file" name="vehiclecheckinout" id="uploadreports4" />
        </li>
        <li>
        	<label>Copy of the final invoice<em>*</em></label>
            <input type="file" name="finalinvoice" id="uploadreports5" />
        </li>
        <li>
        	<label>Copy of the repair invoice</label>
            <input type="file" name="repairinvoice" id="uploadreports6" />
        </li>  
	<li>
        	<label>Copy of charge receipt<em>*</em></label>
            <input type="file" name="chargereceipt" id="uploadreports8" />
        </li>
	<li>
        	<label>Evidence of payment (Credit card / Bank Statement showing debit of the damage amount)<em>*</em></label>
            <input type="file" name="evidenceofpayment" id="uploadreports10" />
        </li>            
        <li>
        	<label>Copy of police report if a 3rd party</label>
            <input type="file" name="thirdpartypolicerep" id="uploadreports7" />            
        </li>
	
	 <li>
        	<label>For 3rd Party claims, confirmation from the supplier that your claim has been finalised
and party identified for damage liability.</label>
            <input type="file" name="thirdpartyconf" id="uploadreports9" />
        </li>	
	   
    </ul>
    <p>Fields marked with a<em>*</em>are mandatory</p>
    <hr />

    <div id="fileempty-dialog" title="Error" style="display:none;">
	<p style="color:red;">Please upload files</p>
	 
    </div>
   
    <div id="fileSize-dialog">
	<p style="text-align:left;"><span id="totalFileSize"></span>&nbsp;MB exceeds the total file-upload limit of 15MB. Please resize your files.<br /><br />These file(s) are large and may be causing the issue: <br /><span id="sizeFileList"></span></p>
    </div>

   <input type="button" name="submitAllFiles" id="vldSubmitAllFiles" class="btn-orange" value="Upload All" onclick="valAllFiles();" style="display:none; width:250px; " />

    <input id="excessFormSubmit" type="submit" value="Submit form now" name="uploadreports" class="btn-orange" style="display:none" />
    <a href="#" class="btn-grey" id="excessCancelBtn" style="display:none">Cancel</a>
     </div> 
     <? } ?>  
</form>

    </div>
</div>

<? get_Footer() ?> 	

	
