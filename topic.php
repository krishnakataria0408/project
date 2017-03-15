<link href="<?=EdFaq::$url?>css/main.css" rel="stylesheet" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
    var edFaq = {};
    edFaq.topics = <?=json_Encode($topicNames)?>;
</script>
<script src="<?=EdFaq::$url?>js/topic.js" type="text/javascript"></script>

<div id="edfaq_container">
    <form id="edfaq_addTopicForm" method="post">
        <input name="action" type="hidden" value="addTopic" />
        <input autocomplete="off" id="edfaq_topicInput" maxlength="30" name="name" type="text" />
        <input disabled="disabled" id="edfaq_topicSubmit" type="submit" value="Add Topic" />
        <br/>
        <div id="edfaq_topicErr"></div>
    </form>
    
    <? if (count($topics)): ?>
        <div id="edfaq_topics">
        <? forEach ($topics as $topic): ?>
            <a href="plugins.php?page=<?=EdFaq::$faqPage?>&topicId=<?=$topic->id?>"> <? // Raman: Whats the wp way? ?>
                <div class="edfaq_topic">
                    <div class="edfaq_topicName"><?=$topic->name?></div>
                    <img class="edfaq_topicDelete" data-id="<?=$topic->id?>" src="<?=EdFaq::$url?>img/close-icon.png" title="Delete topic." />
					<? if ($topic->count): ?>
						<div class="edfaq_topicQuesCount"><?=$topic->count?> <?=($topic->count==1) ? 'Question' : 'Questions'?></div>
					<? endIf ?>
                    <div class="edfaq_topicShortCode" title="Click to select shortcode."><?=sPrintF('[%s id=&quot;%s&quot;]', EdFaq::$shortCode, $topic->id)?></div>
                </div>
            </a>
        <? endForEach ?>
        </div>
    <? endIf ?>
</div>