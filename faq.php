<link href="<?=EdFaq::$url?>css/iphone-style-checkboxes.css" rel="stylesheet" />
<link href="<?=EdFaq::$url?>css/jquery.wysiwyg.css" rel="stylesheet" />
<link href="<?=EdFaq::$url?>css/main.css" rel="stylesheet" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js" type="text/javascript"></script>
<script src="<?=EdFaq::$url?>js/iphone-style-checkboxes.js" type="text/javascript"></script>
<script src="<?=EdFaq::$url?>js/jquery.wysiwyg.js" type="text/javascript"></script>
<script type="text/javascript">
    var edFaq = {};
    edFaq.topicId = <?=$_GET['topicId']?>;
</script>
<script src="<?=EdFaq::$url?>js/faq.js" type="text/javascript"></script>

<div id="edfaq_container">
	<div id="edfaq_back">
		<div class="edfaq_left">
			<a href="plugins.php?page=<?=EdFaq::$topicPage?>" title="Back to topics.">
				<img src="<?=EdFaq::$url?>img/back-icon.png" />
			</a>
		</div>
		<div class="edfaq_left" id="edfaq_faqTopic"><?=$topicName?></div>
		<div class="edfaq_clear"></div>
	</div>
	
	<form id="edfaq_addFaq" method="post" tabindex="100">
		<input name="action" type="hidden" value="addQues" />
		<input name="published" type="hidden" value="0" />
		<input autocomplete="off" name="ques" id="edfaq_quesInput" maxlength="255" type="text" />
        <div id="edfaq_answrInputContainer">
            <textarea class="edfaq_rte" id="edfaq_answrInput" name="answr"></textarea>
			<div style="text-align:right">
				<input class="edfaq_quesSubmit" data-published="0" disabled="disabled" type="button" value="Just Add" />
				<input class="edfaq_quesSubmit" data-published="1" disabled="disabled" type="submit" value="Add & Publish" />
			</div>
        </div>
	</form>
	
	<!--ul id="edfaq_faqs">
		<? forEach ($faqs as $faq): ?>
			<li class="edfaq_faq" data-id="<?=$faq->id?>">
                <img class="edfaq_faqDelete" src="<?=EdFaq::$url?>img/close-icon.png" title="Delete question." />
				<div class="edfaq_left edfaq_quesAnswr">
					<div class="edfaq_faqQues"><?=$faq->ques?></div>
				</div>
                <div class="edfaq_right">
					<input <?if($faq->published):?>checked="checked"<?endIf?> class="edfaq_none edfaq_faqPublish" type="checkbox" />
				</div>
				<div class="edfaq_clear"></div>
			</li>
		<? endForEach ?>
	</ul-->
</div>