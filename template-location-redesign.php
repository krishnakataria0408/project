<?php /* Template Name: Location Redesign SEO */ ?>
<?
	/** Meta Data 17072015 - krishna **/
	function getPageName(){
		$get_url = explode('?',ltrim(str_replace("//","/",$_SERVER['REQUEST_URI']),"/"));		
		$rtrim_slash_url = array_filter((explode('/',rtrim($get_url[0],"/"))));	
		$pageTitle = ucwords(str_replace('_',' ',$rtrim_slash_url[3]));
		return $pageTitle;
	}
	
	function wpseo_cdn_filter( $title ) {
		$getLocationName = getPageName();
		$pageTitle = "Car hire ".$getLocationName." - Compare Rental Prices - Atlaschoice";

		return str_replace( $title , $pageTitle, $title );
	}


	function replace_yoast_description() {
		$getLocationName = getPageName();
		$metaDescription = "Find cheap car hire in ".$getLocationName." with our fantastic range of suppliers. We are a leading car rental provider across ".$getLocationName.", book today!";
		return $metaDescription;		
	}

	/** Replace Yoast SEO Page Description with Custom description **/
	add_filter( 'wpseo_title', 'wpseo_cdn_filter' );
	add_filter( 'wpseo_metadesc', 'replace_yoast_description', 10, 1 );
?>
<?php
	get_Header('location');
	
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
	if($request->getLocationId() && ($branches = $branchModel->getList($request->getLocationId())))
	{	
		if(empty($branchInfoData))
		{
		 	while(true)
			{
				$attempt = isset($attempt) ? ++$attempt : 1;

				if($attempt > 20) break;

				// Pre-load info
				$branchInfoData = file_get_contents(($branchInfoUrl = sprintf('http://mx2.atlaschoice.com:8080/serviceZ/branch/getInfo/branchId/%s,', implode(',', $branches))));
				$branchInfoData = json_decode($branchInfoData);
			
				// Wait 250ms
				//usleep(250000);
			}
		}

		

		foreach($branches AS $branchId) { 
			if(!isset($branchInfoData->data->{"$branchId"})) continue; 
			$_branchInfoData = $branchInfoData->data->{"$branchId"};
			$_branchInfoData = $_branchInfoData->data;
			$_branchInfoDataResult[] = $_branchInfoData;
		}

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
		$default = $upload_dir['baseurl']."/bg-default.jpg";
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
			
		$title->prefix		= $area;
		$title->span		= ' Car Hire ';
		$title->suffix		= '';
		$title->before		= 'Make Huge Savings on';
		$title->after		= 'Car Hire';
		$title->followUrl	= 'carhire';
			
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
	$myCID='&src=atlaschoice.com';
	//old was: $myCID='';
}
?>
<div class="body_container03">
	<div class="content-wrap2"> 
		<div id="imageLocationTop" style="background-image:url(<?=theImage()?>); " class="image-top">
			<div class="searchFrameLoc" id="searchFrame">
				<iframe class="searchBoxFrame2" id="searchBoxFrame2" name="searchBoxFrame2" width="530" scrolling="no" title="Atlaschoice Car Hire" src="<?=site_Url('/cars/searchbox/external?transparent&amp;country='. $request->getCountryId() .'&amp;city='. $request->getCityId() .'&amp;location='. $request->getLocationId())?>&amp;residence=<?=($request->getCountryOfResidence())?><?=$myCID?>" onload="this.style.width='830px'">Car Hire Search</iframe>
			</div>
							
		</div>
		
		<div class="promoCodeBoxLoc">
			<h2><?=($title->before)?></h2>
				<h1><? echo $area; ?> Car Hire</h1> 
				<p>Search now!
				<span style="font-size:16px; float:right; margin-right:10px; margin-top:10px; font-family:OpenSans Light,Arial,sans-serif">Extra discount today! Enter promo code <span style="font-size:18px;">  GOG121</span></span></p>
		</div>	

	<div style="margin-top: 10px;" id="chooseUs">
		<h3><span class="toChoose"></span>Over 20 Million Clients Trusted Atlas</h3>
		<h3><span class="toChoose"></span>Best Rates Guarantee</h3>
		<h3><span class="toChoose"></span>Inclusive Deals</h3>
		<h3><span class="toChoose"></span>Choice of Best Cars</h3>
		<h3><span class="toChoose"></span>No Booking Fee</h3>
		<h3><span class="toChoose"></span>No Hidden Charges</h3>
		<h3><span class="toChoose"></span>25 Years of Excellence and Quality Service</h3>
	</div>
		
	</div> 
	</div> <? /** End of content-wrap and body_container **/ ?>		

	
	<div class="slideshow">
	
		<div class="slide">
		<div class="creviews-grey">
				<span class="ui-5star"></span>				
				<p>"As easy as 123.Really impressed from start to finish.I'll be using Atlaschoice again this summer.10/10 Doesn't get any better than that."</p>
				<p class="customerNameBox">M.Drewery</p>
		</div>
								
		<div class="creviews-grey">
				<span class="ui-5star"></span>
				<p>“Easy,Supportive, and good access to pick up and drop.I have passed the details onto my wife and friends for the up coming Rugby World Cup next year.”</p>
				<p class="customerNameBox">auskeltonma</p>
		</div>
		<div class="creviews-grey">
				<span class="ui-5star"></span>
				<p>“Car hire with a hassle free experience.This car hire experience was one of the easiest I have encountered with an excellent service.”</p>
				<p class="customerNameBox">Torchy497</p>
		</div>
		</div> <? /** end of slide **/ ?>
		<div class="slide">
		<div class="creviews-grey">
				<span class="ui-5star"></span>
				<p>"I love this site.I spend a lot of time looking on many sites.I am happy to say I find not just better deals but awesome deals with Atlaschoice.com"</p>
				<p class="customerNameBox">carrentalguy488</p>
		</div>
								
		<div class="creviews-grey">
				<span class="ui-5star"></span>
				<p>“smooth, simple, and so cheap! I paid ~1/3 what I would have paid using other sites. I'm thrilled and will use it again no question.”</p>
				<p class="customerNameBox">YJ12</p>
		</div>
		<div class="creviews-grey">
				<span class="ui-5star"></span>
				<p>“I was very skeptical because the pricing was so much lower than the top rated competitors.I am glad I did, the service was excellent from start to finish.”</p>
				<p class="customerNameBox">lmarino</p>
		</div>
		</div> <? /** end of slide **/ ?>
		
		<div class="slide">
		<div class="creviews-grey">
				<span class="ui-5star"></span>
				<p style="text-align:left !important;">"your so reliable.there is only one choice for me because of their customer service and and reliability,in a word !! ATLASCHOICE.”</p>
				<p class="customerNameBox">victor1958</p>
		</div>
								
		<div class="creviews-grey">
				<span class="ui-5star"></span>
				<p>“Great surprise. Atlas choice proved to be a reliable source of information and way of booking a vehicle at the lowest available price.”</p>
				<p class="customerNameBox">eduardoseijo</p>
		</div>
		<div class="creviews-grey">
				<span class="ui-5star"></span>
				<p>“No problems, at all! I would definately use Atlas again and I would definately reccommend Atlas to anyone else contemplaing a rental!”</p>
				<p class="customerNameBox">lmarino</p>
		</div>
		</div> <? /** end of slide **/ ?>

		<div class="slide">
		<div class="creviews-grey">
				<span class="ui-5star"></span>
				<p style="text-align:left !important;">"Atlas makes car rental easy at the lowest cost.It was a totally hassle free pickup and return of the vehicle with no hidden fees or additions just as promised.”</p>
				<p class="customerNameBox">B.A.S.</p>
		</div>
								
		<div class="creviews-grey">
				<span class="ui-5star"></span>
				<p>“I only paid 50% of what I have paid in the past.The website is really easy to use too. I would definitely recommend atlaschoice for anyone thinking of booking a hire car.”</p>
				<p class="customerNameBox">Emmaward13</p>
		</div>
		<div class="creviews-grey">
				<span class="ui-5star"></span>
				<p>“Atlas choice found me the best deal for a least a hundred less if not more. I will/would definitely use them next time I'm looking for anything related to travel.”</p>
				<p class="customerNameBox">lobogirl13</p>
		</div>
		</div> <? /** end of slide **/ ?>
		
	</div>	<? /** End of customerreview **/ ?>

	<div id="locationDescription" class="locationDescription">
		<div id="locationSubMain" class="locationSubMain" style="border-top-left-radius:10px; background-color:#fff;">
			<div itemscope itemtype="http://data-vocabulary.org/Breadcrumb" class="urlDirection" style="width:600px !important;">
			<?php
				// Prepare breadcrumbs
				$breadcrumbs = array();
				if($request->getCountry())
				{
					$breadcrumbs[1] = array(
						'href'	=> sprintf('/worldwide_car_hire/%s_car_hire/', strtolower(str_replace(' ', '_', $request->getContinent()))),
						'text'	=> sprintf('%s', $request->getContinent())
					);

					$breadcrumbs[] = array( 
						'href'	=> ($href = sprintf('/carhire/%s', $request->getRawCountry())),
						'text'	=> sprintf('%s Car Hire', $request->getCountry())
					);

					if($request->getCity())
					{
						$breadcrumbs[] = array(
							'href'	=> ($href = sprintf('%s/%s', $href, $request->getRawCity())),
							'text'	=> sprintf('%s', $request->getCity())
						);

						if($request->getLocation())
						{
							$breadcrumbs[] = array(
								'href'	=> sprintf('%s/%s', $href, $request->getRawLocation()),
								'text'	=> sprintf('%s', $request->getLocation())
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
								$url			= sprintf('http://cars.atlaschoice.com/vehicles/?residence=gb&age=auto&country=%s&return=same&drop-country=0&city=%s&drop-city=0&location=%s&drop-location=0&start=%s&end=%s&promo=%s', $request->getCountryId(), $request->getCityId(), $request->getLocationId(), $pickUpDate, $dropOffDate,$myCID);
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
										<p><?=($price)?></p><span>based on <?=($days)?> days</span></h3>
									</div>
								<?php } ?>
							</div>
							<a href="/worldwide_car_hire/" class="locationDealLink">See more Car Hire</a>
						
					<?php  } ?>
				</div>

				<!-- Branch info -->
				<div class="boxseparator"></div>
		
		<? /** About Location **/ ?>
		<div style="height:auto; width:100%; background-color:#fff; padding:10px 0px 10px 0px;">
					<div class="locationMatchText" style=" margin:20px;">
						<h3>About <?=($area)?> Car Hire</h3>

						<?php 
							 $id = get_the_ID();							
 						 	$content = '';
							$post = get_page($id);
							if ($post){
							 $content = $post->post_content;
							}

							if(preg_match("(downtown|resort|railway station|city centre)", strtolower($area), $matches)) {
							 if(strlen($content) != 0){
								$text = $content;
							 }
							 else{
							$text = "					
								<p>Car Hire at $area is made a lot easier with AtlasChoice. Lowest prices guaranteed, great friendly service, easy to find Car Hire stations as well as everything you require in hiring a car, AtlasChoice will be the perfect Car Hire company for you.</p>
								<p>AtlasChoice wants to make sure you have everything you need for your Car Hire in $area so please remember to bring your driver’s licence, a valid credit card as well as additional proof of identity (such as your passport when renting abroad) with all these documents we can make sure you have a speedy pick up and you can make the most of you hire car.</p>
								<p>AtlasChoice Green ethos and Carbon Footprint – AtlasChoice use the latest technology in order to be environmentally friendly and to achieve our neutrality goals. As a responsible Car Hire Company we are offering low emission vehicles in $area ensuring our effects on the planet are kept to a minimum and knowing the fact you have made the right choice in booking your Car Hire with Atlas Choice.</p>
							";
							}
							$chunk = wordLimit($text, 60);
						?>
						<div id="allLocationsText" style="display: block;" class="textReadMore"><?=($text)?></div>										
						<?php } else { 
							if(strlen($content) != 0){
								$text = $content;
							}
							else{
							$text =	"
								<p>AtlasChoice offer $area Car Hire, this is a quick way for you to get off and enjoy your trip. Located worldwide AtlasChoice have made Car Hire a lot easier with our airport pick-up and drop-off.</p>
								<p>AtlasChoice offer you great service, new vehicles as well as the lowest prices guaranteed. $area Car Hire stations are very easy to find making your $area Car Hire a hassle free one.</p>
								<p>Whether you're looking for an economy Car Hire, luxury Car Hire or an eco-friendly Car Hire AtlasChoice has the right car for you, so book your $area Car Hire with AtlasChoice today.</p>
								<p>AtlasChoice Green ethos and Carbon Footprint – AtlasChoice use the latest technology in order to be environmentally friendly and to achieve our neutrality goals. As a responsible Car Hire company we are offering low emission vehicles in {$request->getCountry()} ensuring our effects on the planet are kept to a minimum and knowing the fact you have made the right choice in booking your Car Hire with Atlas Choice.</p>
							";
							}
							$chunk = wordLimit($text, 60);
						?>
						<div id="allLocationsText" style="display: block;" class="textReadMore"><?=($text)?></div>										
						<?php } ?>

					</div>
		</div>
			<? /** end about location **/ ?>				
				

			</div>
			<div id="aboutSubSidePanel" class="aboutSidePanel" style="padding-bottom:15px;border-top-right-radius:10px; ">
				<div style="height:250px; border-bottom:1px solid #303030">
					<div id="map_worldwide" style="height: 250px;"></div>
							
					<?php 
					
					if($_branchInfoDataResult){
					foreach($_branchInfoDataResult AS $branchDescId) { 

					$branchAddress = json_encode($_branchInfoData->Address->value); 
					
					//echo $branchAddress."<br />";
					echo '<script type="text/javascript">addMarker('.$branchAddress.');</script>';   
					

						
					} 
					} //if end ?>

				</div>
			
				<h2 style="padding:15px 0 0 10px; color:#1579D3;">Pickup Points and Locations</h2>
			
				<? /** address listing **/ ?>
			<?php if(!empty($branches) && $branchInfoData) { ?>
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
		<div style="width:600px; height: auto; margin:20px 10px 0 0;">
					<p id="locationName" style="display: none; color: #0099CC; font-size: 20px; font-weight: bold; padding: 0;"><?=ucwords(($title))?></p>				
					<div style="width: 320px; display: block; height: 540px; overflow-y: auto;" id="mainAddress">
					<? 

					if($_branchInfoDataResult){
					   foreach($_branchInfoDataResult As $branchDesc) { 

							$branchAddress = $branchDesc->Address->value;
							$branchTel = $branchDesc->Telephone->value;
							$branchFax = $branchDesc->Fax->value;
							
							
						?>

						
					<div itemscope itemtype="http://schema.org/Organization" class="addressList">						
							<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
								<h2><span itemprop="streetAddress"><?=($branchAddress)?></span></h2>
							</div>
							<h3><img src="<?=(blogInfo('template_url'))?>/images/phone.png" alt="AtlasChoice Car Hire"/><span itemprop="telephone"><?=($branchTel)?></span></h3>
							<h3><img src="<?=(blogInfo('template_url'))?>/images/fax.png" alt="AtlasChoice Car Hire" /><span itemprop="faxNumber"><?=($branchFax)?></span></h3>						
						</div>
						
						
						<div class="addressSeparator"></div>
					<?php } 
					      } //if end ?>
					</div>
				</div>					
				
				<div class="clear"></div>

				<?php } ?>
				<? /** End address listing **/ ?>		
				
			</div>
		</div>

		<div class="clear"></div>
		
		<div style="height:auto; width:100%; background-color:#fff; padding:5px 0px 5px 0px;">
			<div class="aggencies">
                <div class="agencies_text">Atlaschoice <span>partners</span></div>
                <div class="part">
                    <?=do_ShortCode('[BookingPartners limit="7"]')?>
                </div>
           </div>
		</div>	
		
		<div class="feature-bg">
			<?=do_ShortCode('[MainFeatures]')?> <br />	
		</div>
	
	<div class="clear"></div>
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