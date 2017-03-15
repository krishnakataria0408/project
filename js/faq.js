edFaq.quesWatermarkValue = 'Add a new question here.';
edFaq.wysiwygEditObj = null;

$(document).ready(function(){
	$('.edfaq_faq').click(function(){
		var el = $(this);
		$.post(ajaxurl,{
			action: 'edFaqGetAnswer',
			id: el.attr('data-id')
		}, function(response){
			$('.edfaq_editAnswrContainer').empty();
			var editorContainerEl = $('.edfaq_editFaq').clone().appendTo(el.find('.edfaq_editAnswrContainer'));
			editorContainerEl.find('input[name=id]').val(el.attr('data-id'));
			editorContainerEl.find('.edfaq_answrEdit').wysiwyg({controls: {insertImage: {visible: false}}}).wysiwyg('setContent', response);
			editorContainerEl.show();
		});
	});
	
	$('.edfaq_right').click(function(){
		return false;
	});
	
	$('#edfaq_faqs').sortable({stop: function(event, ui){
		$.get(ajaxurl,{
			action: 'edFaqSortQues',
			id: ui.item.attr('data-id'),
			pos: ui.item.index()+1, // Coz zero based.
			topicId: edFaq.topicId
		});
	}});
	
    $('#edfaq_faqs .edfaq_faqDelete').click(function(){
        if (window.confirm("Delete the question?"))
        {
            var parent = $(this).closest('.edfaq_faq');
            $.get(ajaxurl, {
                action: 'edFaqDeleteQues',
                id: parent.attr('data-id'),
                topicId: edFaq.topicId
            });
            
            parent.fadeOut('slow', function(){
                $(this).remove();
            });
        }
        return false;
    });
    
	$('#edfaq_faqs .edfaq_faqPublish').iphoneStyle({onChange: function(el, isChecked){
		$.post(ajaxurl,{
			action: 'edFaqPublishFaq',
			id: $(el).closest('.edfaq_faq').attr('data-id'),
			status: +isChecked // Boolean to int.
		});
	}}).show();
	
    $('#edfaq_quesInput')
    .blur(function(){
        if ($.trim(this.value) == '') this.value = edFaq.quesWatermarkValue;
    })
    .focus(function(){
        if (this.value == edFaq.quesWatermarkValue) this.value = '';
        $('#edfaq_answrInputContainer').slideDown();
    })
    .keyup(function(){
        var val = $.trim($(this).val());
        var submitEl = $('#edfaq_addFaq .edfaq_quesSubmit').attr('disabled', 'disabled');
        if (val != '') submitEl.removeAttr('disabled');
    })
    .val(edFaq.quesWatermarkValue);
    
    $('#edfaq_addFaq .edfaq_quesSubmit').click(function(){
        $('#edfaq_addFaq input[name=published]').val($(this).attr('data-published'));
		$('#edfaq_addFaq').submit();
    });
    
    $('#edfaq_answrInput').wysiwyg({controls: {insertImage: {visible: false}}});
});