<?php /* Template Name: Location SEO */ ?>
<?php
	get_Header('locationseo');
	
?>
<?php
	// Initiate Titanium library
	initTitanium();
	$templatepath = TEMPLATEPATH.'/';
	// Init debud tools
	//require_once($templatepath.'libraray/Zend/Debug.php');
	require_once($templatepath.'library/App/Request.php');
	require_once($templatepath.'library/App/Area.php');
	require_once($templatepath.'library/Acriss.php');

	// Initiate request object
	$request = App_Request::getInstance();

	// Leading rates & Hot Chilli deals collecting date
	$futureDate = date('Y-m-d', strtotime('+60 day'));

	// Pre-load leading rates
	$leadingPricesData = null;
	$leadingPricesUri = sprintf('http://mx2.atlaschoice.com:8080/serviceZ/price/getLeading/locationId/%s/pickUpDate/%s/hireDays/7/forceDow/2', $request->getLocationId(), $futureDate);
	 //echo sprintf('<!-- %s -->', $leadingPricesUri); 

	if($request->getLocationId())
	{
		$leadingPricesData = file_get_contents($leadingPricesUri);
		if(($leadingPricesData = json_decode($leadingPricesData)))
		{
			if($leadingPricesData->isError || !$leadingPricesData->isFinished)
			{
				$leadingPricesData = null;
			}
			elseif($leadingPricesData->isFinished)
			{
				$leadingPricesData = $leadingPricesData->data;
			}
		}
	}

	// Pre-load hot chilli deals if leading rate are not available
	$hotChilliDealsData = null;
	$hotChilliDealsLocations = array(
		2733,	// Portugal, Faro, Faro Airport
		19807,	// Spain - Mainland, Malaga, Malaga Airport
		3888,	// USA Florida, Orlando, Orlando Airport
		2068,	// Ireland, Dublin, Dublin Airport
		27553,	// Greece, Corfu (Corfu Island), Corfu Airport
		1390,	// Germany, Berlin, Berlin Schoenefeld Airport
		23504,	// United Kingdom, London Heathrow Airport, London Heathrow Airport
		72886,	// France, Nice, Nice Airport
		19125,	// Italy, Milan, Milan Linate Airport
	);
	$hotChilliDealsUri = sprintf('http://mx2.atlaschoice.com:8080/serviceZ/price/getLeading/locationId/%s/pickUpDate/%s/hireDays/7/forceDow/2', implode(',', $hotChilliDealsLocations), $futureDate);
	//echo sprintf('<!-- Hot Chilli Uri: %s -->', $hotChilliDealsUri);
	if(empty($leadingPricesData))
	{
		$hotChilliDealsData = file_get_contents($hotChilliDealsUri);
		if(($hotChilliDealsData = json_decode($hotChilliDealsData)))
		{
			if($hotChilliDealsData->isError || !$hotChilliDealsData->isFinished)
			{
				$hotChilliDealsData = null;
			}
			elseif($hotChilliDealsData->isFinished)
			{
				$hotChilliDealsData = $hotChilliDealsData->data;
			}
		}
	}

	// Pre-load airport info
	$airportInfo = null;
	if(stripos($request->getLocation(), 'airport') !== false)
	{
		$airport = new Airport();
		$airportInfo = $airport->getInfo($request->getLocationId());
		$airportWebsite = $wpdb->get_col(sprintf("SELECT `official_website` FROM `airports_website` WHERE `location_id_titanium` = %s", mysql_escape_string($request->getLocationId())));
	}

	// Prepare title
	if(($area = ($area = $request->getLocation()) ? $area : (($area = $request->getCity()) ? $area : (($area = $request->getCountry()) ? $area : null))))
	{
		$title = getTitle($request->getPathPart(0), $area);
	}
	$area = ucwords($area);

	// Prepare branch data
	$branchModel = new Branch();
	$branchInfoData = null;
	$branchTermsData = null;
	/*if($request->getLocationId() && ($branches = $branchModel->getList($request->getLocationId())))
	{	
		// Pre-load info
		$branchInfoData = file_get_contents(($branchInfoUrl = sprintf('http://mx2.atlaschoice.com:8080/serviceZ/branch/getInfo/branchId/%s,', implode(',', $branches))));
		
		$branchInfoData = json_decode($branchInfoData);

		// Pre-load tems
		$branchTermsData = file_get_contents(($branchTermsUrl = sprintf('http://mx2.atlaschoice.com:8080/serviceZ/branch/getTerms/branchId/%s,', implode(',', $branches))));
		$branchTermsData = json_decode($branchTermsData);
	}*/
	

	if($request->getLocationId() && ($branches = $branchModel->getList($request->getLocationId())))
	{  
	 // Pre-load info
 		$branchInfoData = file_get_contents(($branchInfoUrl = sprintf('http://mx2.atlaschoice.com:8080/serviceZ/branch/getInfo/branchId/%s,', implode(',', $branches))));
		
		//echo sprintf('<!-- %s -->', $branchInfoUrl); 
		/* if(($branchInfoData = json_decode($branchInfoData)) && isset($branchInfoData->responseId))
 		 {
			  // Reset response cache
			 file_get_contents(sprintf('http://mx2.atlaschoice.com:8080/serviceZ/branch/reset/responseId/%s', $branchInfoData->responseId));
 		 }
		 $branchInfoData = file_get_contents(($branchInfoUrl = sprintf('http://mx2.atlaschoice.com:8080/serviceZ/branch/getInfo/branchId/%s,', implode(',', $branches))));*/
		
		$branchInfoData = json_decode($branchInfoData);
		
 	
	// Pre-load tems
		 $branchTermsData = file_get_contents(($branchTermsUrl = sprintf('http://mx2.atlaschoice.com:8080/serviceZ/branch/getTerms/branchId/%s,', implode(',', $branches))));
		 /*if(($branchTermsData = json_decode($branchTermsData)) && isset($branchTermsData->responseId))
 		{
		  // Reset response cache
		  file_get_contents(sprintf('http://mx2.atlaschoice.com:8080/serviceZ/branch/reset/responseId/%s', $branchTermsData->responseId));
 		}
		 $branchTermsData = file_get_contents(($branchTermsUrl = sprintf('http://mx2.atlaschoice.com:8080/serviceZ/branch/getTerms/branchId/%s,', implode(',', $branches))));*/
		$branchTermsData = json_decode($branchTermsData);
	}
	
	
	// Extra functions - MOVE OUTSIDE
	function getCustomItem($myKey)
	{
		$myKey_values = get_post_custom_values($myKey);

		return $myKey_values[0];
	}

	function theImage()
	{
		$upload_dir = wp_upload_dir();
		$default = $upload_dir['baseurl']."/default.jpg";
		$myCustom = getCustomItem('imgItem');
		if(strlen($myCustom) != 0) 
		{
			return $myCustom;
		}
		else
		{
			return $default;
		}
	}

	function wordLimit($string, $limit)
	{
		$words = explode(' ', $string, ($limit + 1));
		if(count($words) > $limit) array_pop($words);

		return implode(' ', $words);
	}

	function getTitle($context, $area)
	{
		$title = new stdClass();

		switch($context)
		{
			case 'cheapcarhire':
					$title->prefix		= $area;
					$title->span		= ' Cheap Car Hire';
					$title->suffix		= '';
					$title->before		= 'Cheap Car Hire in';
					$title->after		= 'With Excellent Service ';
					$title->followUrl	= 'cheapcarhire';
			break;
			case 'discountcarhire':
					$title->prefix		= $area;
					$title->span		= ' - Discount Car Hire ';
					$title->suffix		= $area;
					$title->before		= 'Discount Car Hire in';
					$title->after		= 'With Excellent Service ';
					$title->followUrl	= 'discountcarhire';
			break;
			case 'compare-car-hire':
					$title->prefix		= $area;
					$title->span		= ' - Compare Car Hire in ';
					$title->suffix		= $area;
					$title->before		= 'Compare Car Hire in';
					$title->after		= 'and Find Your Perfect Car';
					$title->followUrl	= 'compare-car-hire';
			break;
			case 'car-hire':
					$title->prefix		= $area;
					$title->span		= ' - Car Hire ';
					$title->suffix		= $area;
					$title->before		= 'Car Hire';
					$title->after		= 'With Excellent Service';
					$title->followUrl	= 'car-hire';
			break;
			case 'car-hire-in':
					$title->prefix		= $area;
					$title->span		= ' - Car Hire in ';
					$title->suffix		= $area;
					$title->before		= 'Car Hire in';
					$title->after		= 'With Excellent Service';
					$title->followUrl	= 'car-hire-in';
			break;
			case 'cheap-car-hire':
					$title->prefix		= $area;
					$title->span		= ' - Cheap Car Hire ';
					$title->suffix		= $area;
					$title->before		= 'Cheap Car Hire ';
					$title->after		= 'Lowest Price Guaranteed';
					$title->followUrl	= 'cheap-car-hire';
			break;
			case 'cheap-car-hire-in':
					$title->prefix		= $area;
					$title->span		= ' - Cheap Car Hire in ';
					$title->suffix		= $area;
					$title->before		= 'Cheap Car Hire in';
					$title->after		= 'Lowest Price Guaranteed';
					$title->followUrl	= 'cheap-car-hire-in';
			break;
			case 'hire-a-car':
					$title->prefix		= $area;
					$title->span		= ' - Hire a Car in ';
					$title->suffix		= $area;
					$title->before		= 'Hire a Car';
					$title->after		= 'With Excellent Service';
					$title->followUrl	= 'hire-a-car';
			break;
			case 'carhire':
					$title->prefix		= $area;
					$title->span		= ' Car Hire ';
					$title->suffix		= '';
					$title->before		= 'Make Huge Savings on';
					$title->after		= 'Car Hire';
					$title->followUrl	= 'carhire';
			break;	
			default:
					$title->prefix		= $area;
					$title->span		= ' Car Hire ';
					$title->suffix		= '';
					$title->before		= 'Car Hire in';
					$title->after		= 'With Excellent Service ';
					$title->followUrl	= 'carhire';
			break;
		}

		return $title;
	}

	function stringToPath($string)
	{
		$replace = array(',', ' ');
		$string = trim(str_replace($replace, ' ', $string));
		$string = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $string);
		$string = strtolower(str_replace(' ', '-', $string));

		return $string;
	}

	function areaToPath($country, $city, $location)
	{
		$replace = array("(", ")", "/", "_", "car hire", ".", "php", "htm", "html", "-", "%20", "´");

		$country = trim(str_replace($replace, ' ', $country));
		$country = str_replace("'", '', $country);
		$country = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $country);
		$country = strtolower(str_replace(' ', '_', $country));
		$country = str_replace('usa_', '', $country);

		$city = trim(str_replace($replace, ' ', $city));
		$city = str_replace("'", '', $city);
		$city = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $city);
		$city = strtolower(str_replace(' ', '_', $city));
		$city = str_replace('usa_', '', $city);		

		$location = strtolower(str_replace(' ', '_', $location));

		$area = array_filter(array_map('trim', array($country, $city, $location)));

		return implode('/', $area);
	}
?>



<?php


/*  GET AFFILIATE COOKIE IF SET - PS 25022012 
	cid cookie is set by the ManageAffiliates plug-in
	in www2.atlaschoice.com the url is formed like www2.atlaschoice.com/one?[affiliate_id]
*/

$params = explode('?', str_replace('//', '/', $_SERVER['REQUEST_URI']));
if(isset($params[1]))
{
	// Set cookie
	do_ShortCode(sPrintF('[ManageAffiliates id="%s"]', $params[1]));

	// Redirect
	$_url = $params[0];
//	echo sprintf('<!-- RedirectMe: %s -->', print_r($_url, true));

	header(sprintf('HTTP/1.1 302 Found %s', $_url));
	header(sprintf('Refresh:0; %s', $_url));
	header(sprintf('Location: %s', $_url));
	exit;
}

if (isset($_COOKIE["cid"]) )
{
	$myCID = '&amp;src='.$_COOKIE["cid"];
}

else
{
	 /*default for AC_COM*/
        $myCID='&amp;src=atlaschoice.com';
	//old was: $myCID='';
}
?>
<div class="body_container03">
	<div class="content-wrap2"> 
		<div id="imageLocationTop" style="background-image:url(<?=theImage()?>); " class="image-top">
			<div class="searchFrame" id="searchFrame">
				<iframe class="searchBoxFrame2" id="searchBoxFrame2" name="searchBoxFrame2" width="530" scrolling="no" title="Atlaschoice Car Hire" src="<?=site_Url('/cars/searchbox/external?transparent&amp;country='. $request->getCountryId() .'&amp;city='. $request->getCityId() .'&amp;location='. $request->getLocationId())?>&amp;residence=<?=($request->getCountryOfResidence())?><?=$myCID?>" onload="this.style.width='830px'">Car Hire Search</iframe>
			</div>
							
		</div>
		
		<div class="promoCodeBox">
		<h2><?=($title->before)?></h2>
			<h1><? echo $area; ?> Car Hire</h1> 
			<p>Search now!
	      <span class="promoSpan"><img src="<?=(blogInfo('template_url'))?>/images/blue_arrow.png" class="blueArrow" alt="arrow" />Extra discount today! Enter promo code <span style="font-size:18px;">  GOG121</span></span></p>
	</div>	

	</div>
	</div> <? /** End of content-wrap and body_container **/ ?>		

		<div class="customer-block2">
		<div class="creviews-grey">
			<div class="customerNameGrey"><span class="ui-4star"></span></div>
				<p>"will very definitely use Atlas again. Thank you very much for all your help."</p>
				<p class="customerNameBox">I. Smith</p>
		</div>
								
		<div class="creviews-grey">
			<div class="customerNameGrey"><span class="ui-5star"></span></div>
				<p>“I am impressed by your service I hope we shall be able to book with you in future.”</p>
				<p class="customerNameBox">R. Mitchell</p>
		</div>
		<div class="creviews-grey">
			<div class="customerNameGrey"><span class="ui-5star"></span></div>
				<p>“An excellent car hire experience to which I would give a strong recommendation.”</p>
				<p class="customerNameBox">shannant</p>
		</div>
	

	<div class="aggencies">
                <div class="agencies_text">Atlaschoice <span>partners</span></div>
                <div class="part">
                    <?=do_ShortCode('[BookingPartners]')?>
                </div>
            </div>

	</div>	


			


		<div id="locationDescription" class="locationDescription">
			<div id="locationSubMain" class="locationSubMain" style="border-top-left-radius:10px; background-color:#fff;">
				<div itemscope itemtype="http://data-vocabulary.org/Breadcrumb" class="urlDirection" style="width:600px !important;">
					<?php
						// Prepare breadcrumbs
						$breadcrumbs = array();
						if($request->getCountry())
						{
							$breadcrumbs[1] = array(
								'href'	=> sprintf('/worldwide_car_hire/%s_car_hire', strtolower(str_replace(' ', '_', $request->getContinent()))),
								'text'	=> sprintf('%s Car Hire', $request->getContinent())
							);

							$breadcrumbs[] = array( 
								'href'	=> ($href = sprintf('/carhire/%s', $request->getRawCountry())),
								'text'	=> sprintf('%s Car Hire', $request->getCountry())
							);

							if($request->getCity())
							{
								$breadcrumbs[] = array(
									'href'	=> ($href = sprintf('%s/%s', $href, $request->getRawCity())),
									'text'	=> sprintf('%s Car Hire', $request->getCity())
								);

								if($request->getLocation())
								{
									$breadcrumbs[] = array(
										'href'	=> sprintf('%s/%s', $href, $request->getRawLocation()),
										'text'	=> sprintf('%s Car Hire', $request->getLocation())
									);
								}
							}
						}

				
					?>
					
					<a href="<?=(get_option('home'))?>" itemprop="url"><span itemprop="title">Home</span></a><span>>> </span>
					<?php foreach($breadcrumbs AS $i => $part) { $style = 'color: #0099CC; font-weight: bold;'; ?>
					<div itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" style="display:inline"><a  href="<?=($part['href'])?>" itemprop="url"><span itemprop="title" style="<?=($i == count($breadcrumbs) ? $style : '')?>"><?=(ucwords($part['text']))?></span></a><?=($i < count($breadcrumbs) ? '<span>>> </span>' : '')?>
					<?php } ?></div></div></div></div>
					
				</div>
				<h3 class="locTitleText"><?=($title->prefix)?> <span><?=($title->span)?></span><?=($title->suffix)?></h3>
				<h2 class="offerLocation">Special Offer   <span style="margin:2px 0px 0px 5px;"> </span><span class="ui-5star"></span><br /><br /></h2>
				<p style="margin: -20px 0px 0px 20px;"><span style="font-family: OpenSans, Arial, sans-serif; color: #000; font-size: 14px;  ">* Below are just few cars based on recent searches. Complete your search to find your best deal.</span></p>

				<?php
					// Load leading prices
					if(empty($leadingPricesData))
					{
						while(true)
						{
							$attempt = isset($attempt) ? ++$attempt : 1;

							if($attempt > 20) break;

							$leadingPricesData = file_get_contents($leadingPricesUri);
							if(($leadingPricesData = json_decode($leadingPricesData)))
							{
								if($leadingPricesData->isError)
								{
									$leadingPricesData = null;
									break;
								}
								elseif($leadingPricesData->isFinished)
								{
									$leadingPricesData = empty($leadingPricesData->data) ? null : $leadingPricesData->data;
									break;
								}
							}

							// Wait 250ms
							usleep(250000);
						}
					}

					// Load hot chilli deals
					if(empty($leadingPricesData) && empty($hotChilliDealsData))
					{
						while(true)
						{
							$attempt = isset($attempt) ? ++$attempt : 1;

							if($attempt > 20) break;

							$hotChilliDealsData = file_get_contents($hotChilliDealsUri);
							if(($hotChilliDealsData = json_decode($hotChilliDealsData)))
							{
								if($hotChilliDealsData->isError)
								{
									$hotChilliDealsData = null;
									break;
								}
								elseif($hotChilliDealsData->isFinished)
								{
									$hotChilliDealsData = empty($hotChilliDealsData->data) ? null : $hotChilliDealsData->data;
									break;
								}
							}

							// Wait 250ms
							usleep(250000);
						}
					}
				?>

				<!-- Prices -->
				<div>
					<?php if($leadingPricesData) { ?>
						<?php
							$cars = array();
							foreach($leadingPricesData AS $car)
							{
								$acriss = new Acriss($car->description[2]);

								if(($diff = array_diff($acriss->carCategory, array_keys($cars))))
								{
									$carCategory = array_shift($diff);
									$cars[$carCategory] = $car;
								}
							}
						?>
						<?php foreach($cars AS $car) { ?>
							<?php
								$id				= $car->id;
								$modified		= $car->modified;
								$acriss			= $car->description[2];
								$category		= ucwords($car->category);	
								$name			= $car->description[0];
								$image			= sprintf('http://cars.atlaschoice.com/crbmsfiles/%s', rawurlencode($car->description[1]));
								$date			= $car->date;
								$price			= $car->price;
								$pickUpDate		= date_create($car->date)->format('U');
								$days			= $car->days; if(!$days) continue; // Continue if days was not set
								$dropOffDate	= date_create($car->date)->add(new DateInterval(sprintf('P%sD', $car->days)))->format('U');
								$daily			= number_format($price / $days, 2);
								$url			= sprintf('http://cars.atlaschoice.com/vehicles/?residence=gb&amp;age=auto&amp;country=%s&amp;return=same&amp;drop-country=0&amp;city=%s&amp;drop-city=0&amp;location=%s&amp;drop-location=0&amp;start=%s&amp;end=%s&amp;promo=%s', $request->getCountryId(), $request->getCityId(), $request->getLocationId(), $pickUpDate, $dropOffDate,$myCID);
								$bookingUrl = $url; //link for go-ahead
							?>
							<ul class="cars-list">    
								<li>
									<a href="<?=($url)?>" class="cars-list-img"><img src="<?=($image)?>" alt="Atlaschoice <?=($area)?> Car Hire" /></a>
									<a href="<?=($url)?>" class="cars-list-days"><strong>Based on <?=($days)?> days</strong></a>
									<a href="<?=($url)?>" class="cars-list-price"><p><?echo '£ ';?><?=($daily)?>/day</p></a>
									<a href="<?=($url)?>" class="cars-list-model"><h3><?=($name)?></h3></a>
									<a href="<?=($url)?>" class="chooseCar">Choose</a>
									<a href="<?=($url)?>" class="cars-list-class"><h3><?=($category)?> <br />Car Hire</h3></a>
								</li>
							</ul>
						<?php } ?>
					<?php } else {  ?>
							<div class="locationHotChilliDeals">
								<h3 style="font-size: 28px; color: #1579D3; padding: 20px;">Hot Chilli Deals</h3>
								<?php foreach($hotChilliDealsData AS $locationId => $row) { ?>
									<?php
										$response = file_get_contents(sprintf('http://mx2.atlaschoice.com:8080/serviceZ/area/getLocation/locationId/%s', $locationId));
										$response = json_decode($response);

										$country = $response->data->country;
										$city = $response->data->city;
										$location = $response->data->location;
										
										$price = $row->data[0]->price;
										$days = $row->data[0]->days;
										$path = areaToPath($country, $city, $location);
									?>
									<div class="locationDealSub">
										<h3><a href="http://www2.atlaschoice.com/carhire/<?=($path)?>"><?=($location)?></a>
										<span class="priceValue"><?=($price)?></span><span>based on <?=($days)?> days</span></h3>
									</div>
								<?php } ?>
							</div>
							<a href="/worldwide_car_hire/" class="locationDealLink">See more Car Hire</a>
						
					<?php  } ?>
				</div>

				<!-- Branch info -->
				<div class="separator"></div>
				
				<?php if(!empty($branches) && $branchInfoData && $branchTermsData) { ?>
					<?php 
						if(preg_match("(downtown|resort|railway station|city centre)", strtolower($area), $matches))
						{
							$title = sprintf("%s Car Hire Address List - (Downtown, City Centre, City Railway Station, Resort)", trim(preg_replace('(downtown|resort|railway station|city centre)', '', strtolower($area))));
						}
						else
						{
							$title = sprintf("%s Car Hire Address List", $area);
						}
					
					?>

				<p id="addressList" class="linkAddressList"><?=(ucwords($title))?></p>

				<div style="width:600px; height: auto; margin: 20px;">
					<p id="locationName" style="display: none; color: #0099CC; font-size: 20px; font-weight: bold; padding: 0;"><?=ucwords(($title))?></p>				
					<div style="width: 300px; display: none; height: 480px; overflow: auto;" id="mainAddress">
					<?php foreach($branches AS $branchId) { if(!isset($branchInfoData->data->{"$branchId"}) || !isset($branchTermsData->data->{"$branchId"})) continue; ?>
						<?php
							$_branchInfoData = $branchInfoData->data->{"$branchId"};
							if(!$_branchInfoData->isFinished || $_branchInfoData->isError) continue;
							$_branchInfoData = $_branchInfoData->data;

							$_branchTermsData = $branchTermsData->data->{"$branchId"};
							if(!$_branchTermsData->isFinished || $_branchTermsData->isError) continue;
							$_branchTermsData = $_branchTermsData->data;

							$branchAddress = $_branchInfoData->Address->value;
							$branchTel = $_branchInfoData->Telephone->value;
							$branchFax = $_branchInfoData->Fax->value;
							$branchUrl = sprintf('http://www2.atlaschoice.com/%s/address/%s/%s', $request->getPath(), $branchId, stringToPath($branchAddress));
	
							$_branchTermsData = array_merge(array(
								'Inclusive'			=> null,
								'General'			=> null,
								'Extras'			=> null,
								'RentalConditions'	=> null,
								'PersonalTouch'		=> null,
								'PaymentInfo'		=> null,
								'ContactInfo'		=> null
							), (array) $_branchTermsData);
						?>

						<a href="<?=($branchUrl)?>" style="cursor: pointer; text-decoration: none;">
						<div class="addressListing" id="addressListing">						
							<h2><?=($branchAddress)?></h2>
							<h3><img src="<?=(blogInfo('template_url'))?>/images/phone.png" alt="AtlasChoice Car Hire"/><?=($branchTel)?></h3>
							<h3><img src="<?=(blogInfo('template_url'))?>/images/fax.png" alt="AtlasChoice Car Hire" /><?=($branchFax)?></h3>						
						</div>
						</a>
						<div style="display: inline-block; width: 300px;">
							<a href="<?=$url?>" class="viewAll" style="width: 50px; margin: 5px 0px 5px 10px; padding: 2px 0px; font-size: 15px; float: left;">Book</a>
							<a onclick="openTermsBox()" style="float:right; margin-right: 40px; cursor: pointer;">T&C</a>
						</div>

						<!-- Terms & Conditions -->
						<div class="blackOut" id="blackOut"></div>
						<div class="termsConditionsLoc" id="termsConditions" style="display: none;">
							<a class="terms_closeButton" title="Close"></a>
							<h2>Terms and Conditions</h2>

							<?php foreach($_branchTermsData AS $data) { ?>
								<?php foreach($data->Caption AS $key => $caption) { ?>
									<?php
										//$caption = is_string($caption) ? $caption : (isset($caption->value) ? $caption->value : null);
										//$body = isset($data->Body->{"$key"}) ? $data->Body->{"$key"} : (isset($data->Body[$key]) ? (is_string($data->Body[$key]) ? $data->Body[$key] : (isset($data->Body[$key]->value) ? $data->Body[$key]->value : null)) : null);


										if(is_string($key) && $key != NULL && $key != attr){
											$captionTitle = $caption;
										}
										else{
											$captionTitle = $caption->value;
										}
										
										if(is_array($data->Body) && $data->Body != NULL){ 
											$body = $data->Body[$key]->value; 
										}
										else{ 
											$body = $data->Body->value;
										}
									?>
									<h2><?=(trim($captionTitle))?></h2>
									<h3><?=(trim($body))?></h3>
								<?php } ?>
							<?php } ?>
						</div>

						<div class="addressSeparator"></div>
					<?php } ?>
					</div>
				</div>
					
				<a class="viewAll" id="addressListLess" style="display: none; width: 60px; margin:0px 0px 5px 20px; padding: 2px 0px; ">Less</a>
				<div class="clear"></div>

				<?php } ?>

				<!-- -->
				

			</div>
			<div id="aboutSubSidePanel" class="aboutSubSidePanel" style="padding-bottom:205px;border-top-right-radius:10px;">
				<h2 style="color: #DB5200; font-size: 22px; margin: 70px 20px 0px 20px;">Why choose Atlaschoice?</h2>			
				<div style="margin-top: 20px;">
					<span class="aboutCompany">&nbsp;</span><h3>Over 20 Million Clients Trusted Atlas</h3><div class="sepratoreAbout"></div>
					<span class="aboutCompany">&nbsp;</span><h3>Best Rates Guarantee</h3><div class="sepratoreAbout"></div>
					<span class="aboutCompany">&nbsp;</span><h3>Inclusive Deals</h3><div class="sepratoreAbout"></div>
					<span class="aboutCompany">&nbsp;</span><h3>Choice of Best Cars</h3><div class="sepratoreAbout"></div>
					<span class="aboutCompany">&nbsp;</span><h3>No Booking Fee</h3><div class="sepratoreAbout"></div>
					<span class="aboutCompany">&nbsp;</span><h3>No Hidden Charges</h3><div class="sepratoreAbout"></div>
					<span class="aboutCompany">&nbsp;</span><h3>25 Years of Excellence and Quality Service</h3>
				</div>

				

				
				
			</div>
		</div>

		<div class="clear"></div>
				<? /** About and Fast fact **/ ?>
		<div style="height:auto; width:100%; background-color:#fff; padding:10px 0px 10px 0px;">
				<!--div style="display:inline-block; width:480px"-->
					<div class="locationMatchText" id="locationContentTab" style=" margin:20px;">
						<h3>About <?=($area)?> Car Hire</h3>

						<?      $id = get_the_ID();							
 						 	$content = '';
							$post = get_page($id);
							if ($post){
							 $content = $post->post_content;
							}
							//echo $content;
							
											
						 ?>

						<?php 

							

							if(preg_match("(downtown|resort|railway station|city centre)", strtolower($area), $matches)) {
							 if(strlen($content) != 0){
								//$text = $content;

								$showTotalChar = 450;
								$strAboutLength = strlen($content);
								$chunk1 = substr($content,0,$showTotalChar);  
								$chunk2 = substr($content, $showTotalChar, $strAboutLength - $showTotalChar);
							}
							else{
							
							$chunk1 = "					
								<p>Car Hire at $area is made a lot easier with AtlasChoice. Lowest prices guaranteed, great friendly service, easy to find Car Hire stations as well as everything you require in hiring a car, AtlasChoice will be the perfect Car Hire company for you.</p>
								<p>AtlasChoice wants to make sure you have everything you need for your Car Hire in $area so please remember to bring your driver’s licence, a valid credit card as well as additional proof of identity (such as your passport when renting abroad) with all these documents we can make sure you have a speedy pick up and you can make the most of you hire car.</p>";

							$chunk2 = "<p>AtlasChoice Green ethos and Carbon Footprint – AtlasChoice use the latest technology in order to be environmentally friendly and to achieve our neutrality goals. As a responsible Car Hire Company we are offering low emission vehicles in $area ensuring our effects on the planet are kept to a minimum and knowing the fact you have made the right choice in booking your Car Hire with Atlas Choice.</p>";
							}
							$chunk = wordLimit($text, 60);
						?>

						<div class="location-content"><?=$chunk1;?></div>
						<span class="read-more-toggle" style="margin:0px 10px 0px 20px;">Read More</span>
						<div class="read-more-content"><?=$chunk2;?></div>
						<span class="hide-more-toggle" style="display:none; margin:0px 10px 0px 20px;">Less</span>			
					
						<?php } else {
							if(strlen($content) != 0){
								//$text = $content;

								$showTotalChar = 483;
								$strAboutLength = strlen($content);
								$chunk1 = substr($content,0,$showTotalChar);  
								$chunk2 = substr($content, $showTotalChar, $strAboutLength - $showTotalChar);
							}
							else{ 
						
	
							$chunk1 = "
								<p>AtlasChoice offer $area Car Hire, this is a quick way for you to get off and enjoy your trip. Located worldwide AtlasChoice have made Car Hire a lot easier with our airport pick-up and drop-off.</p>
								<p>AtlasChoice offer you great service, new vehicles as well as the lowest prices guaranteed. $area Car Hire stations are very easy to find making your $area Car Hire a hassle free one.</p>";

							$chunk2 = "<p>Whether you're looking for an economy Car Hire, luxury Car Hire or an eco-friendly Car Hire AtlasChoice has the right car for you, so book your $area Car Hire with AtlasChoice today.</p>
								<p>AtlasChoice Green ethos and Carbon Footprint – AtlasChoice use the latest technology in order to be environmentally friendly and to achieve our neutrality goals. As a responsible Car Hire company we are offering low emission vehicles in {$request->getCountry()} ensuring our effects on the planet are kept to a minimum and knowing the fact you have made the right choice in booking your Car Hire with Atlas Choice.</p>";
							}
							

							$chunk = wordLimit($text, 60);
						?>

						<div class="location-content"><?=$chunk1;?></div>
						<span class="read-more-toggle" style="margin:0px 10px 0px 20px;">Read More</span>
						<div class="read-more-content"><?=$chunk2;?></div>
						<span class="hide-more-toggle" style="display:none; margin:0px 10px 0px 20px;">Less</span>

				    <?php } ?>

					</div>
				<!--/div-->
				
				<!-- Airport info -->
				<!--div style="width:480px; display:inline-block;"-->
					<?php if($airportInfo && $airportInfo['isFinished'] && !$airportInfo['isError']) { ?>
						<div id="airportInfoTab" class="locationMatchText" style="margin:20px;">
							<h3>Fast Fact About <?=($request->getLocation())?> Car Hire </h3>
							<div class="citiesFieldTitle" style="width: 900px !important; margin-left:20px;">
								<h2 style="margin:0px 10px 10px 0px">About:</h2>
								<?php if(isset($airportInfo['data']['About']) && strlen(($chunk = wordLimit($airportInfo['data']['About'], 40))) != 0) { ?>

			<?
			$airportContent = $airportInfo['data']['About'];
			$showTotalChar = 400;
			$strAboutLength = strlen($airportContent);
			$airportContent_new = substr($airportContent,0,$showTotalChar);  
			$airportContent_remain = substr($airportContent, $showTotalChar, $strAboutLength - $showTotalChar);	
			?>					

			<div class="read-less-content customtext"><?=$airportContent_new;?></div>
			<span class="read-more-toggle" style="display:block; margin:0px 10px 0px 0px;">Read More</span>
			<div class="read-more-content"><?=$airportContent_remain;?>


			<div class="locationInfoText">
				<h2>Airport: <span><?=($airportInfo['data']['Title'])?></span></h2> 
				<h2>Address: <span><?=($airportInfo['data']['Address'])?></span></h2>
				<h2>Phone: <span><?=($airportInfo['data']['Contacts']['formattedPhone'])?></span></h2>
				<h2>twitter: <span><?=($airportInfo['data']['Contacts']['twitter'])?></span></h2>
				<h2>Airport Type: <span><?=($airportInfo['data']['Summary']['Airport type'])?></span></h2>
				<h2>Owner: <span><?=($airportInfo['data']['Summary']['Owner'])?></span></h2>
				<h2>Operator: <span><?=($airportInfo['data']['Summary']['Operator'])?></span></h2>
				<h2>Serves: <span><?=($airportInfo['data']['Summary']['Serves'])?></span></h2>
				<h2>Location: <span><?=($airportInfo['data']['Summary']['Location'])?></span></h2>
				<h2>Hub for: <span><?=($airportInfo['data']['Summary']['Hub for'])?></span></h2>
				<h2>Coordinates: <span><?=($airportInfo['data']['Summary']['Coordinates'])?></span></h2>
				<? if($airportWebsite[0] != ''){ ?>
					<h2>Website: <span><a href="<?=($airportWebsite[0])?>"><?=($airportWebsite[0])?></a></span></h2>
				<? } ?>
				<h2>Passengers: <span><?=($airportInfo['data']['Statistics (2012)']['Passengers'])?></span></h2>
			</div>

		<?php } ?>

			</div><!--end of read-more-content-->
			<span class="hide-more-toggle" style="display:none; margin:0px 10px 0px 0px;">Less</span>
								
							</div>
						</div>
					<?php } ?>
				<!--/div-->

			</div>
				<? /** end about and fast fact **/ ?>
		<div class="clear"></div>
		<div class="feature-bg">
			<?=do_ShortCode('[MainFeatures]')?> <br />	
		</div>
	<!--/div--> <?php /* End Content-wrap2 */ ?>

	<div class="clear"></div>
<!--/div--> <?php /* End body_container03 */ ?>

	<?php require_once 'tagCloud.php'; ?>

	<div class="clear"></div>
	<label for="acwebsite" style="display:none;">Other AtlasChoice Websites</label>
	<select name="otherWebsites" id="acwebsite">
		<option value="">Other AtlasChoice Websites</option>
		<option value="http://www2.atlaschoice.com">AtlasChoice</option>
		<option value="http://www.atlaschoice.co.uk">United Kingdom</option>
		<option value="http://www.atlaschoice.us">United States</option>
		<option value="http://www.atlaschoice.co.nz">New Zealand</option>
		<option value="http://www.atlaschoice.com.au">Australia</option>
		<option value="http://www.atlaschoice.co">AtlasChoice International</option>
		<option value="http://www.atlaschoice.eu">Europe</option>
		<option value="http://www.atlaschoice.co.za">South Africa</option>
	</select>
	<button name="acwebsite" value="Go" onclick="openAcwebsite()">Go</button>
	<div style="margin-top:30px;"></div>
<?php get_Footer('seo') ?>
