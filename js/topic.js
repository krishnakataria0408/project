edFaq.showTopics;
edFaq.topicFadeDuration = 600;
edFaq.topicEls;

$(document).ready(function(){
    edFaq.topicEls = $('#edfaq_topics .edfaq_topic');
    edFaq.showTopics(0);
    
    $('#edfaq_topicInput').keyup(function(){
        var errEl = $('#edfaq_topicErr').empty();
        var submitEl = $('#edfaq_topicSubmit').attr('disabled', 'disabled');
        var val = $.trim($(this).val());
        
        if (val != '')
        {
            if (val.toLowerCase() in edFaq.topics) errEl.html('Topic name already exists.');
            else
            {
                errEl.empty();
                submitEl.removeAttr('disabled');
            }
        }
    }).focus();
    
    $('#edfaq_topics .edfaq_topicDelete').click(function(){
        var me = $(this);
        var name = me.closest('.edfaq_topic').find('.edfaq_topicName').html();
        if (window.confirm("Delete topic '"+name+"' and all related data?"))
        {
            $.post(ajaxurl, {
                action: 'edFaqDeleteTopic',
                id: me.attr('data-id')
            });
            
            me.closest('.edfaq_topic').fadeOut('slow', function(){
                delete edFaq.topics[name];
                $(this).remove();
            });
        }
        return false;
    });
    
    $('#edfaq_topics .edfaq_topicShortCode').click(function(event){
        var text = this;
		if ($.browser.msie)
        {
			var range = document.body.createTextRange();
			range.moveToElementText(text);
			range.select();
		}
        else if ($.browser.mozilla || $.browser.opera)
        {
			var selection = window.getSelection();
			var range = document.createRange();
			range.selectNodeContents(text);
			selection.removeAllRanges();
			selection.addRange(range);
		}
        else if ($.browser.safari)
        {
			var selection = window.getSelection();
			selection.setBaseAndExtent(text, 0, text, 1);
		}
        return false;
    });
});

edFaq.showTopics = function(index){
	edFaq.topicEls.eq(index).fadeIn(edFaq.topicFadeDuration);
    if (++index <= edFaq.topicEls.length)
    {
        window.setTimeout(function(){
            edFaq.showTopics(index);
        }, edFaq.topicFadeDuration/2);
    }
};