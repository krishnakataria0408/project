<? /* Template Name: Country Keyword  */ ?>
<? $microStart = microtime(true); ?>
<?
	// Remove Meta Keywords on Specific pages Added By Yoast WordPress SEO Plugin

	$countryRmkeywords = Array("spain_mainland");

	$beforehdr = ltrim($_SERVER['REQUEST_URI'],"/");
	$splithdrurl=explode('?',$beforehdr);
	$getkeyuri= $splithdrurl[0];
	$sepurl = array_filter((explode('/', $getkeyuri)));
	$countryselect = $sepurl[1];

	if(in_array(strtolower($countryselect),$countryRmkeywords)){		
		add_filter( 'wpseo_metakey', '__return_false' );
	}

	/** Meta Data 17072015 - krishna **/	
	
	function wpseo_cdn_filter( $title ) {
		//$getCountryName = getPageName();

		$url = explode('?',ltrim(str_replace("//","/",$_SERVER['REQUEST_URI']),"/"));		
		$rtrim_slashurl = array_filter((explode('/',rtrim($url[0],"/"))));
		
		if($rtrim_slashurl[2] == "k"){
			$getKeywordTitle = ucwords(str_replace('-',' ',$rtrim_slashurl[3]));
			$getCountryName = ucwords(str_replace('_',' ',$rtrim_slashurl[1]));						
			if(strpos($getKeywordTitle, $getCountryName)){
				$pageTitleCountry = $getKeywordTitle;
			}	
			else{
				$pageTitleCountry = $getKeywordTitle." ".$getCountryName;
			}			
			
			$pageTitleDis = $pageTitleCountry." - Best Price Guaranteed - Atlaschoice";
			
		}
		else
		{
			$getCountryName = ucwords(str_replace('_',' ',$rtrim_slashurl[1]));
			$pageTitleDis = "Car hire ".$getCountryName." - Best Price Guaranteed - Atlaschoice";
		}
		return str_replace( $title , $pageTitleDis, $title );
	}

	
	function replace_yoast_description() {
		$get_url = explode('?',ltrim(str_replace("//","/",$_SERVER['REQUEST_URI']),"/"));		
		$rtrim_slash_url = array_filter((explode('/',rtrim($get_url[0],"/"))));
		if($rtrim_slash_url[2] == "k"){
			$getKeywordTitle = ucwords(str_replace('-',' ',$rtrim_slash_url[3]));
			$getCountryName = ucwords(str_replace('_',' ',$rtrim_slash_url[1]));						
			if(strpos($getKeywordTitle, $getCountryName)){
				$pageTitleCountry = $getKeywordTitle;
			}	
			else{
				$pageTitleCountry = $getKeywordTitle." ".$getCountryName;
			}
			$getCountryName = $pageTitleCountry;
		}
		else{
			$getCountryName = "cheap car hire in ";	
			$getCountryName .= ucwords(str_replace('_',' ',$rtrim_slash_url[1]));
			
		}
	
		
		$metaDescription = "Find ".$getCountryName." with our fantastic range of suppliers. We are a leading car rental provider across ".ucwords($rtrim_slash_url[1]).", book today!";
		
		return $metaDescription;		
	}

	/** Replace Yoast SEO Page Description with Custom description **/
	add_filter( 'wpseo_title', 'wpseo_cdn_filter' );
	add_filter( 'wpseo_metadesc', 'replace_yoast_description', 10, 1 );
?>

<? get_header("country-keyword"); ?>
<? 
   	//require_once 'library/Oa/Date/Stopwatch.php';

	//Oa_Date_Stopwatch::start();
	// Init debud tools
	//require_once 'Zend/Debug.php';

	/* Word Limit Function for post */
	function string_limit_words($string, $word_limit)
	{
	  $words = explode(' ', $string, ($word_limit + 1));
	  if(count($words) > $word_limit)
	  array_pop($words);
	  return implode(' ', $words);
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
	do_ShortCode(sPrintF('[ManageAffiliates id="%s" noredir=1]', $params[1]));

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
	$myCID='';
}


// Get the last occurence of location
$url = explode('?',ltrim($_SERVER['REQUEST_URI'],"/"));
$link = array_filter((explode('/', $url[0])));

if($link[2] == "k"){
	$getKeyword = $link[3];		
	$spaceReplaceKeyword = str_replace(" ","-",$getKeyword);	
	$keyword_check = do_ShortCode(sPrintF('[ManageKeywords keyword="%s"]', $spaceReplaceKeyword));

	if($keyword_check == 1){ $keyword = str_replace("-"," ",$getKeyword); }
}

function getCustomItem($myKey) {
  
  $myKey_values = get_post_custom_values($myKey);
  return $myKey_values[0];
}

function theImage() {
   $upload_dir = wp_upload_dir();
   $default = $upload_dir['baseurl']."/default-car.jpg";
   $myCustom = getCustomItem('imgItem');
   if (strlen($myCustom)!=0) 
   {
	return $myCustom;
   }
   else
   {
	return $default;
   }

}

	// Initiate Titanium library
	initTitanium();
	
	global $defaultCountry, $pickCities, $pickCountryId, $wpdb;

	$country = new Country();
	$city = new City();
	$airport = new Airport();



	$defaultCountry = ucwords($link[1]);
	$getCountry = $link[1];
		
	$pickCountries = $pickCities = Array();
	$pickCountryId = false;

	$usaCountries = Array('alabama','alaska','arizona','arkansas','california','colorado','connecticut','delaware','florida',
		'georgia','hawaii','idaho','illinois','indiana','iowa','kansas','kentucky','louisiana','maine',
		'maryland','massachusetts','michigan','minnesota','mississippi','missouri','montana','nebraska',
		'nevada','new hampshire','new jersey','new mexico','new york','north carolina','north dakota',
		'ohio','oklahoma','oregon','pennsylvania','rhode island','south carolina','south dakota',
		'tennessee','texas','utah','vermont','virginia','washington','washington dc','west virginia',
		'wisconsin','wyoming');

	
	$defaultCountry = str_replace('_',' ',$defaultCountry);
	$defaultCountry = ucwords($defaultCountry);

	if(in_array(strtolower($defaultCountry),$usaCountries)){
		$coun = "USA ".$defaultCountry;
		$defaultCountry = $coun;
	}	
	else{
		$defaultCountry;
	}

	$pageTitle = ucwords((isset($keyword)) ? $keyword : $defaultCountry." Car Hire");
	
	if($keyword){
		$pos = strpos(strtolower($pageTitle), strtolower($defaultCountry));
		if($pos !== false){	
		$pageTitleIncCountry = $pageTitle;
	}
	else{
		$pageTitleIncCountry = $pageTitle." ".$defaultCountry;
	}
	}
	else{
		$pageTitleIncCountry = $pageTitle;
	}
	

	/** Get Country ID @pickCountryId **/
	$pickCountryId = $country->getId($defaultCountry);

	$replacers = array("(",")","/","_","car hire",".","php","htm","html","-","%20","´","–");
	$replaceCountryName = trim(str_replace($replacers,' ',$defaultCountry));
	$replaceCountryCut = str_replace(array(".",")","`","'"),'',$replaceCountryName); 
	$replace = str_replace("'",'',$replaceCountryName);
	$strippedCountry = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $replaceCountryCut);
	$country_link = strtolower(str_replace(' ','_',$strippedCountry));
	$countryUrl = str_replace('usa_','',$country_link);


	$post_id_data = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE  post_title LIKE 'About $getCountry' AND post_type = 'post' AND post_status = 'publish' ORDER BY ID DESC");			
		
					
	$post_id = $post_id_data[0];	
	foreach($post_id_data as $test)
	{
		$countryMeta = get_post_meta( $test, 'country_name', true );
		if(isset($countryMeta))
		{
			if($countryMeta == strtolower($get_country))
			{
				  $post_id = $test;
			}
		}
	}
	//echo $post_id;

	if ($post_id) {
		$args=array(
			'p' => $post_id,
			'title' => "About+".$getCountry,
			'tag' => "about=".$getCountry,
			'posts_per_page' => 1,
			'caller_get_posts'=> 1,
		);
							  
	$my_query = null;
	$my_query = new WP_Query($args);

	if( $my_query->have_posts() ) {
		while ($my_query->have_posts()) : $my_query->the_post(); 
			
			$postContent = $post->post_content;
			$tags = get_the_tags(); 
		foreach ($tags as $tag)
	    	{
		$tag_split=split('=',$tag->name);
		switch($tag_split[0])
		{
			case "language":
				$language = $tag_split[1];
				break;
			case "currency":
				$currency = $tag_split[1];
				break;
			case "electrical":
				$electrical = $tag_split[1];
				break;
			case "dialingcode":
				$dialingcode = $tag_split[1];
				break;
			case "emergencyno":
				$emergencyno = $tag_split[1];
				break;
			case "country":
				$countryName = $tag_split[1];
				break;									
			case "city":
				$cityName = $tag_split[1];
				break;									
			case "imagename":
				$imageName = $tag_split[1];
				break;
			case "about":
				$tagname = $tag_split[1];
				break;
		}
	    }
	    endwhile;
	    }
		wp_reset_query();  // Restore global post data stomped by the_post().
	}
	else
	{
		echo "";
	}	
					

	/* when $pickCountryId null than match tag=country and get that country id */
	if(empty($pickCountryId))
	{						
		$pickCountryId = getIdCountry($countryName);
	}
	else
	{
		 $pickCountryId;
	}	
	$pickCities = $city->getList($pickCountryId);


	$getContinetsList = $country->getContinentList();	
	foreach($getContinetsList as $id=>$name)
	{
		$countryListContinent = $country->getList($name[attr][id]);
		if(in_array($defaultCountry,$countryListContinent)){
			$displayContinent = $name[value];
			if($displayContinent == "Australia And Oceania")
			{
				$displayContinent = "Australia";
			}
			if($displayContinent == "The Caribbean")
			{
				$displayContinent = "Caribbean";
			}
		}
		
	}
	$continentUrlLink = strtolower(str_replace(' ','_',$displayContinent));
	$continentUrl = $continentUrlLink."_car_hire";

?>

    <div id="page-title">
	<img src="<?=theImage()?>"  alt="<? single_post_title() ?>" width="1583" height="238" />
	<div class="innerPageTitle">	
	        <h1><?=$pageTitleIncCountry;?></h1>
	

	<? //echo (theImage());

	$mySubtitle = "Find best prices on ".$pageTitleIncCountry;
	if (strlen($mySubtitle) != 0) {
	?>     	<h2><?=$mySubtitle?></h2>
	<? } ?>
	</div>
    </div>

 <div class="content page">	
      
        <div class="search-wrap">
            <iframe class="searchBoxFrame" id="searchBoxFrame" name="searchBoxFrame" width="530" scrolling="no" title="Atlaschoice Car Hire" src="<?=site_Url('/cars/searchbox/external?country='.$pickCountryId.'&amp;lang='.$setlanguage.'')?><?=$myCID?>"   onload="this.style.width='600px'; initIframeFix();" >Car Hire Search</iframe>

	<div class="whyChoose">
	<div><img src="http://www2.atlaschoice.com/wp-content/uploads/service-stamp.png" width="145" height="175" alt="25 Years of Excellence and Quality Service" style="float:right; margin-top:30px; padding:0px;"></div>
	<h2>Why choose Atlaschoice?</h2>
	<ul>
		<li><span>Over 10 Million Clients Trusted Atlas</span></li>
		<li><span>Best Rated Guarantee</span></li>
		<li><span>Inclusive Deals</span></li>
		<li><span>Choice of Best Cars</span></li>
		<li><span>No Booking Fee</span></li>
		<li><span>No Hidden Charges</span></li>
		<li><span>20 Years of Excellence and Quality Service</span></li>
	</ul>
	</div>

        </div>

		

	<div class="clear"></div>
	<? /** Car Hire Partners **/?>
	<div class="partners">
		<h2>Car Hire Partners</h2>
		 <?=do_ShortCode('[BookingPartners limit="9"]')?>
	</div>


	<div id="map_country" style="width: 980px; height: 190px; margin-bottom:20px;"></div>
	<div id="pickcities" style="display:none;">
	<?
	foreach($pickCities as $address){		
		echo'<script type="text/javascript">addMarker("'.$address.'")</script>';   
	}?>
	</div>

	<? /** Breadcrumbs **/ ?>
	<div class="clear"></div>
	<div itemscope itemtype="http://data-vocabulary.org/Breadcrumb" class="urlDirection">
	  <a href="<?=bloginfo('url')?>" itemprop="url">
		<span itemprop="title" style="border:none; padding:10px 10px 10px 0px;">Home</span></a>
	  	
		<div itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" style="display:inline;">		
		<a itemprop="url" href="/worldwide_car_hire/<?=$continentUrl;?>/"><span itemprop="title" class="urlItem"><?=$displayContinent;?> Car Hire</span></a>	
	
		<div itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" style="display:inline;">
		  <? if($keyword){ ?>
		     <a itemprop="url" href="/carhire/<?=$country_link;?>/"><span itemprop="title" class="urlItem"><? echo $defaultCountry; ?> Car Hire</span></a>
		  <? } 
		     else{ ?>
		  	<span itemprop="title" style="color:#0099CC; font-weight:bold;" class="urlItem"><? echo $defaultCountry; ?> Car Hire</span>
		  <? } ?>
		</div>
		</div>

	     <? /** Keyword is independent (not in hierarchy tree) **/ ?>
	     <?	if($keyword){ ?>
			 <div itemscope itemtype="http://schema.org/BreadcrumbList" style="display:inline;">
			  <span itemprop="itemListElement" itemscope  itemtype="http://schema.org/ListItem">
 			     <span itemprop="name" style="color:#0099CC; font-weight:bold;" class="urlItem"><? echo ucwords($pageTitleIncCountry); ?></span>
			  </span>
			</div>
	     <?	}	  
	     ?>
	</div>

	 

	<div class="innerNewPage">
		<div class="titlePage">
		<img src="<? blogInfo('template_url') ?>/images/arrow-circle.png" width="34" height="32" alt="Atlaschoice Car Hire" style="float:left; padding:0px 10px 0px 0px; margin-left:-5px;" /><h3 class="boxTitle"><?php echo $pageTitleIncCountry; ?></h3>		
		</div>
		<?
		if($keyword){
			$strAbout="<p>When it comes to visiting Latvia let Atlaschoice take the hassle out of hiring a car. We have been arranging ".$pageTitleIncCountry." for over a decade. With many convenient locations such as airports, railway stations, city centres, downtowns and Latvian resorts to choose from you will be on your way  at no time. Simply select and pre-book your car from the wide range of categories on offer to hire in ".$defaultCountry." including mini, compact, full size, premium, luxury etc. – all at a very competitive price.</p><p>We have stunning new ".$pageTitle." in ".$defaultCountry." at affordable prices whatever the occasion might be. Luxurious SUVs, including the Range Rover, are perfect  for those who want to explore ".$defaultCountry.". luxury saloons cars , Mercedes S-CLASS, BMW 7 Series, Audi A8 are among the best luxury cars when it comes to travelling in style and making an impression for that business meeting. Luxury people carriers, like the Mercedes Vito, will provide ample space whether you doing business or simply partying in Latvia. Our customer support is always available to help you to ensure you a wonderful experience when it comes to hiring your ".$pageTitleIncCountry.".</p>";

		}
		else{
		$str = $postContent; 
		if(strlen($str) != 0)
		{
			$t = 'about';
			if($pos = strpos($postContent, $t))
			{
				/** Get the text within about div **/
				$lent = strlen($t)+2;
				$posstart = $pos + $lent;
				$endpos=strpos($postContent,"</div>");
				$str1=substr($postContent, $posstart,$endpos);
				$strAbout = strstr($str1, '</div>', true);							
							
			}
		}
		}
		?>
		<?=$strAbout?>
	</div>

	<div class="box">
	<ul class="innerTabDiv">
		<li class="add"><a href="#innerTab1">Popular Locations in <?=$defaultCountry?></a></li>		
		<li class="add"><a href="#innerTab2">Airports in <?=$defaultCountry?></a></li>
		<li class="add"><a href="#innerTab3">Reviews</a></li>		
		<li class="add"><a href="#innerTab4">Suppliers</a></li>
		<li class="add"><a href="#innerTab5">Fast Fact about <?=$defaultCountry?></a></li>						
	</ul>
	</div>
	
	<div class="pane">
		<div id="innerTab1" class="tab_content">
		 
		   <ul class="listCities">
			<? forEach ($pickCities as $name): ?>
			<li>
			<? 	$nameLower = strtolower(str_replace(' ','_',$name)); 
				//$replacers = array("(",")","/","_","car hire",".","php","htm","html","%20","´","-","–");
				$replaceName = trim(str_replace($replacers,' ',$name));
				$replaceNameJoint = str_replace(array("'","`"),'',$replaceName);
				$stripped = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $replaceNameJoint);
				$linkCityName = strtolower(str_replace(' ','_',$stripped));

			?>
				<a href="/carhire/<?=$countryUrl;?>/<?=$linkCityName?>/"><?=$name?></a>
				<div class="sepratoretxt"></div>
			</li>
			<? endForEach ?>
		  </ul>
		 
		</div>

		<div id="innerTab2" class="tab_content">
		<?
			$airportList = $airport->getList($pickCountryId);
			sort($airportList);
			//print_r($airportList[0][Data]);
		?>
		<ul class="listCities">
		<?	
			forEach ($airportList as $aiportDetail){
			foreach($aiportDetail[Data] as $detail){ 
				$nameLower = strtolower(str_replace(' ','_',$detail[city])); 
				$replaceName = trim(str_replace($replacers,' ',$detail[city]));
				$replaceNameJoint = str_replace("'",'',$replaceName);
				$stripped = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $replaceNameJoint);
				$linkAirportCityName = strtolower(str_replace(' ','_',$stripped));

				$replaceLocationName = trim(str_replace($replacers,' ',$detail[location]));
				$replaceLocationNameJoint = str_replace("'",'',$replaceLocationName);
				$strippedLocation = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $replaceLocationNameJoint);
				$linkLocationName = strtolower(str_replace(' ','_',$strippedLocation));
		
			?>		
                	<li>
			 <a href="/carhire/<?=$countryUrl?>/<?=$linkAirportCityName?>/<?=$linkLocationName?>/"><?=$detail[location]?> Car Hire</a>
				    <div class="sepratoretxt"></div>
			</li>
			<? } } ?>
		</ul>

		</div>

		<div id="innerTab3" class="tab_content">
			<? /** Cusotmer Reviews **/ ?>
			<? global $wpdb;
				$reviewResults = $wpdb->get_Results('
					SELECT *
					FROM wp_mngreview_main
					ORDER BY ordr DESC');
			?>
				
			<div class="clear"></div>
			<strong style="font-size:14px; color:#666;"><?=$defaultCountry?> Car Hire Reviews</strong>
			<div class="reviewSlide">
			<div class="slideshow" style="overflow:hidden;">	
				<? forEach($reviewResults as $review){ ?>
				<div class="customer-reviews">
						<strong><?=$review->reviewtitle?></strong>
						<span class="ui-<?=$review->reviewStars?>star"></span>
						<? 
						$resCountDesc = explode(' ', $review->description, (45 + 1));
						if(count($resCountDesc) > 45){
							$description = string_limit_words($review->description, 45)." ...... ";
						}
						else{
							$description = $review->description;
						}
						
						?>
						<p><?=$description;?></p>
						<p class="customerNameBox">
							<small><? echo $review->customername." via ".$review->reviewVia; ?>
								<a href="<?=$review->link?>" id="<?=$review->id?>"><?=$review->linkname?></a>
							</small>
						</p>
				</div>
				<? } ?>	
			 </div>
			 </div> <? /** End reviewSlide div **/ ?>
		</div>

		<div id="innerTab4" class="tab_content">
			<?=do_ShortCode("[BookingPartners partnername='1' levelname='$defaultCountry' ]");?>
			
		</div>
		<div id="innerTab5" class="tab_content">
					<div class="fastFactTitle">
						<h2>Fast facts <span>about <?php echo $defaultCountry; ?></span></h2>
					</div>
													
					<div class="citiesFieldTitle">
						<h2>Country: <span><? echo $defaultCountry; ?></span></h2> 
						<h2>Time Zone: <span><? echo $timezone; ?></span></h2>
						<h2>Language: <span><? echo $language; ?></span></h2>
						<h2>Currency: <span><? echo $currency; ?></span></h2>
						<h2>Electrical Plug:<span><? echo $electrical; ?></span></h2>
						<h2>Country Dialing Code:<span><? echo $dialingcode; ?></span></h2>
						<h2>Emergency Number: <span><? echo $emergencyno; ?></span></h2>
					</div>
		</div>
	</div>


	


     <div class="clear"></div>
     	
<div class="clear"></div>
<? $microEnd = microtime(true);
   //echo "page load: ".($microEnd - $microStart);
	 //Zend_Debug::dump(Oa_Date_Stopwatch::stop(), 'Page processing time:'); 
?>
	

    </div> <? /** End of content div **/ ?>

<? get_footer("country-keyword"); ?>