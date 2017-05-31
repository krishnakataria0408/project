<? /* Template Name: News Inner */ ?>
<?
	function string_limit_chars($string, $count){	  	
		$excerpt = strip_tags($string);
  		$excerpt = substr($excerpt, 0, $count);
	  	$excerpt = substr($excerpt, 0, strripos($excerpt, " "));
 	  return $excerpt."...";
	}	

	function replace_yoast_description() {
			global $post;
 			
			$str = $post->post_content;

				if(strlen($str) != 0)
				{
				$t = 'introNews';
				if($pos = strpos($str, $t))
				{
					$lent = strlen($t)+2;
					$posstart = $pos + $lent;
					$endpos=strpos($str,"</div>");
					$str1=substr($str, $posstart,$endpos);
					$strAbout = strstr($str1, '</div>', true);
					$metaDescription = string_limit_chars($strAbout,160);
										
				}
				}
			
		 return $metaDescription;
	}

	/** Replace Yoast SEO Page Description with Custom description **/
	add_filter( 'wpseo_metadesc', 'replace_yoast_description', 10, 1 );
?>
<? get_Header('blognews'); ?>
<script type="text/javascript">
$(document).ready(function(){
	$('#introNews').hide();
	$('#postedBy').hide();

	$('#commentform').submit(function(){
		var errEl = $('#commentErr');
		errEl.hide();

		var email = $('.comment-email').val();
		var author = $('#author').val();
		var captcha = $('#easy_captcha_captcha_simple').val();
		var comment = $('#comment').val();
		
		if(captcha == ''){			
			errEl.html("Please enter Captcha text.").fadeIn('fast');
			$('#easy_captcha_captcha_simple').focus();
			return false;
			
		}
	
		if(comment == ''){			
			errEl.html("Please enter your comment.").fadeIn('fast');
			$('#comment').focus();
			return false;
			
		}

		var emailRegex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	 	if (!emailRegex.test(email) || author == ''){
			errEl.html("Please check whether you have entered everything correctly.").fadeIn('fast');
	       		$('.comment-email').focus();
		}
		else{
			errEl.fadeOut();
			$('#commentform').submit();
		}
	   return false;
	});
});
</script>
<script src="//platform.twitter.com/widgets.js" type="text/javascript"></script>

    <div class="body_container01">
        <!--body container-->
	<span><a href="/news/" style="text-decoration:none; cursor:pointer; margin-left:10px;">Back to News Page</a></span>
        <div class="content-wrap">		
            <div class="content-panel newsInner">
		<? 
				if(strlen($post->post_content) != 0)
				{					
					$conSpanID = 'postedBy';	
					if($pos = strpos($post->post_content, $conSpanID))
					{
						$len = strlen($conSpanID)+2;
						$pos_start_span = $pos + $len;
						$end_span_pos=strpos($post->post_content,"</span>");
						$posted=substr($post->post_content, $pos_start_span,$end_span_pos);
						$strSpan = strstr($posted, '</span>', true);
						//$postedBy = string_limit_words($strSpan,40);
					}
				}
			$posted_date = date("F d,Y",strtotime($post->post_date));
			$author_link = str_replace(' ','-',strtolower($strSpan));
		?>
		<h1 style="font-size:26px;"><? echo $post->post_title; ?></h1>
		<span style="font-size:14px;" class="blogInnerSpan">Posted on <? echo $posted_date;?><? if($strSpan){ ?><? echo " by "; ?><a href="/authors/<?=$author_link;?>/"><?=$strSpan;?></a><? } ?></span>
		<? echo $post->post_content; ?>

	<?php 
		//$pagelist = get_pages('sort_column=menu_order&sort_order=asc&child_of=41203&post_type=page&post_status=publish'); 
		$pages = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE  post_parent='41203'AND post_type = 'page' AND post_status = 'publish' ORDER BY post_date DESC");			 
		
	
		/*$pages = array();
		foreach ($pagelist as $page) {
		   $pages[] += $page->ID;
		}*/
		
		$current = array_search(get_the_ID(), $pages); 
		$prevID = $pages[$current-1];
		$nextID = $pages[$current+1];
	?>

	<div class="navigation">
	<?php if (!empty($prevID)) { ?>
		<div class="alignleft">
			<a href="<?php echo get_permalink($prevID); ?>" title="<?php echo get_the_title($prevID); ?>"><?php echo '&laquo; &laquo;'?> Previous</a>
		</div>
	<?php }
	if (!empty($nextID)) { ?>
		<div class="alignright">
			<a href="<?php echo get_permalink($nextID); ?>" title="<?php echo get_the_title($nextID); ?>">Next <?php echo '&raquo; &raquo;'?></a>
		</div>
	<?php } ?>
	</div><!-- .navigation -->
	
	<div id="comments">
	<div id="commentErr"></div>
	
	<?
		$fields =  array(
		
		'author' => '<ul><li>' . '<label for="author">' . __( 'Name' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) .
		            '<input id="author" name="author" type="text"  class="comment-name" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /><em class="obligatory"></em></li>',
		'email'  => '<li><label for="email">' . __( 'Email' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) .
		            '<input id="email" name="email" type="text" class="comment-email" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /><em class="obligatory"></em></li></ul>',
		
		
		); 
	
		$comments_args = array(
			  'fields' =>  $fields,
			  'title_reply'=>'<strong>Post a Comment</strong>',
    			  'label_submit' => 'Post My Comment',
			   'comment_notes_before' => '',
			   'comment_notes_after' => '',
		);
 
		comment_form($comments_args);  ?>
		<small class="obligatory">Your email adress will not be published.Required fields are marked with<em></em></small>
	</div>
	
	<div>
	<ul class="comments">
	<?php   /*'post_id' =>$post_id[$current],*/
		//Gather comments for a specific page/post 
		$comments = get_comments(array(
			'post_id' =>$post->ID,
			'status' => 'approve' //Change this to the type of comments to be displayed
		));

		//Display the list of comments
		wp_list_comments(array(
			'per_page' => 5, //Allow comment pagination
			'reverse_top_level' => false //Show the latest comments at the top of the list
		), $comments);
	?>
	</ul>

	

	<?php //comments_template(); ?>
	</div>

	<div class="clear"></div>
	<div style="margin-top:10px;">
		<? 
		//twitter
		$tweetUrl = "http://www2.atlaschoice.com/blog/".$post->post_name; ?> 
		<a href="https://twitter.com/intent/tweet?text=<?=$post->post_title;?>&amp;url=<?=$tweetUrl;?>"  data-lang="en"><img alt="Twitter" src="<? blogInfo('template_directory') ?>/images/twitter_icon.gif" title="Twitter" /></a>
		
  		<? //facebook ?>
	        <a href="#"  onclick="
		    window.open(
		      'https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent(location.href)+'&amp;display=popup', 
		      'facebook-share-dialog', 
		      'width=650,height=415'); 
			return false;">
			<img alt="Facebook" src="<? blogInfo('template_directory') ?>/images/facebook.jpeg" title="Facebook" /></a>

                        <a href="https://plus.google.com/share?url=<?=$tweetUrl;?>" onclick="javascript:window.open(this.href,
  '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><img alt="Google Plus" src="<? blogInfo('template_directory') ?>/images/google-plus-icon.png" title="Google Plus" /></a>
	</div>
	<div class="clear"></div>

	<div style="margin-top:20px;">
	<h2 style="margin-top:20px">Recent news...</h2>
	<?php 
		$argsRelPages = array( 'post_type' => 'page', 'posts_per_page' => 5, 'post_parent' => '41203','sort_oreder' => 'DESC', 'sort_column'=>'post_date','post_status' => 'publish','post__not_in' => array($post->ID) );
		$wp_query = new WP_Query($argsRelPages);
		?>	 
		<ul id="realtedPages">
		<?
		while ( have_posts() ) : the_post(); ?>
			<li><a href="<?php the_permalink() ?>"><? the_title() ?></a></li>
		<? endwhile;
		?>
		</ul>

		<?
		/*$pages = wp_list_pages('title_li=&sort_column=post_date&sort_order=DESC&depth=1&echo=0&child_of=41203&exclude='.$post->ID.'&post_type=page&post_status=publish');

		preg_match_all('/(<li.*?>)(.*?)<\/li>/i', $pages, $matches);
		if (!empty($matches[0])) {
		  print '<ul id="realtedPages">' . implode("\n", array_slice($matches[0],0,5)) . '</ul>';
		}*/
	?></div>

		</div>
		
            <? get_Sidebar() ?>
        </div>

        <div class="clear"></div>
    </div>
<? get_Footer() ?>