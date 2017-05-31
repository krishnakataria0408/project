$ = jQuery;
var atlasChoice = {};
atlasChoice.errs = [];

atlasChoice.errs['invalidInput'] = 'Oops... an error has occurred. Please check whether you have entered everything correctly.';
atlasChoice.errs['loginFailed'] = 'Login failed.';
atlasChoice.errs['invalidEmail'] = 'Please enter your name and email address';
atlasChoice.errs['invalidName'] = 'Please enter your name';

$(document).ready(function(){
	//codeaddress();
    if ($('#wpadminbar').length) $('#fixedTop').css('top', $('#wpadminbar').css('height')); // If admin bar, move fixed header down.
    
    $('.accessBookingLink').click(function(){
        $('#blackOut').fadeIn('fast', function(){
            $('#loginPanel').fadeIn('fast');
            $('#loginPanelEmailInpt').focus();
        });
        return false;
    });
    
    $('.loginPanelClose').click(function(){
        $('#loginPanel').fadeOut('fast', function(){
            $('#blackOut').fadeOut('fast');
        });
    });
    
    $('#loginPanelSubmitButton').click(function(){
        $('#loginPanel form').submit();
    });
	
	$('#loginBookingSubmitButton').click(function(){
        $('#bookingLogin form').submit();
    });
	
	$('#Newsletter_Sign_up').click(function(){		
        	$('#blackOutNewsltr').fadeIn('fast', function(){
	        $('#formSignup').fadeIn('fast');
        });	
		return false;
    });
	
	$('.signup_closeButton').click(function(){
        $('#formSignup').fadeOut('fast', function(){
        	$('#blackOutNewsltr').fadeOut('fast');
        });
   	});
	
	$('.closeoption, #closeoption2').click(function(){
	      $("#blackOut").fadeOut('fast');
              $('#displayoption').fadeOut('fast');

		/*$('#displayoption').fadeOut('fast', function(){
        		$('#blackOut').fadeOut('fast');
	        });*/
    });
	
	$('.selectSiteClose').click(function(){
        $('#displayoption').fadeOut('fast', function(){
            $('#blackOut').fadeOut('fast');
        });
		return false;
    });
    
    $('#searchBoxFrame').load(function(){
        var frameEl = $(this);
        var searchBoxEl = $(this.contentWindow.document.body).find('#bigSearchBox').width(448); // Adjust width so the box fits nicely on the page.
        var searchBoxLoadedObj = window.setInterval(function(){ // Keep checking if search box has loaded.
            if (window.searchBoxFrame.searchbox.loaded) // Once the search box is loaded.
            {
                window.clearInterval(searchBoxLoadedObj); // Stop checking.
                searchBoxEl.find('.reselect_select').css('min-width', ''); // Adjust for drop down selectors.
                // alert('a');
            }
        }, 100);
        // alert(window.searchBoxFrame.searchbox);
    });
    
	$('#searchBoxFrame2').load(function(){
        var frameE2 = $(this);
        var searchBoxE2 = $(this.contentWindow.document.body).find('#bigSearchBox').width(448); // Adjust width so the box fits nicely on the page.
        var searchBoxLoadedObj2 = window.setInterval(function(){ // Keep checking if search box has loaded.
            if (window.searchBoxFrame2.searchbox.loaded) // Once the search box is loaded.
            {
                window.clearInterval(searchBoxLoadedObj2); // Stop checking.
                searchBoxE2.find('.reselect_select').css('min-width', ''); // Adjust for drop down selectors.
                // alert('a');
            }
        }, 100);
        // alert(window.searchBoxFrame.searchbox);
    });
    
    $('.wpcf7 form p br').remove();
    $('.wpcf7 form p').contents().filter(function(){return this.nodeType===3 && $.trim(this.nodeValue)!=''}).wrap('<label/>');
	
	
	
	/** Tabs **/
	 $(".tabContent").hide(); //Hide all content
	 $("ul.innerPageTabDiv li:first").addClass("selected").show(); //Activate first tab
	 $(".tabContent:first").show(); //Show first tab content
 
	 //On Click Event
	 $("ul.innerPageTabDiv li").click(function() {
 	 $("ul.innerPageTabDiv li").removeClass("selected"); //Remove any "active" class
	 $("ul.innerPageTabDiv li").unbind("mouseenter mouseleave");
	 $(this).addClass("selected"); //Add "active" class to selected tab
	 $(".tabContent").hide(); //Hide all tab content
				
	var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
		$(activeTab).show(); //Fade in the active ID content
	  return false;
	});
	/** End Tabs **/
	
	
});

function displayOption(){
	$('#blackOut').fadeIn('fast', function(){
	            $('#displayoption').fadeIn('fast');
        });
	
}

atlasChoice.login = function(){
    var errEl = $('#loginPanelErr');
    errEl.hide();
    
    var panelEl = $('#loginPanel');
    var email = $.trim(panelEl.find('input[name=email]').val());
    var password = $.trim(panelEl.find('input[name=pwd]').val());
    
    var emailRegex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (!emailRegex.test(email) || password == '')
    {
        errEl.html(atlasChoice.errs['invalidInput']).fadeIn('fast');
        $('#loginPanelEmailInpt').focus();
    }
    else
    {
        errEl.fadeOut();
         //errEl.html(atlasChoice.errs['loginFailed']).fadeIn('fast');
      location.href = 'http://2014.atlaschoice.com/selfservice/login/'+password+'/'+email;
	//location.href = 'http://cars.atlaschoice.com/selfservice/login/';

    }
    
    return false;
};

atlasChoice.signup = function(){
    var errEl = $('#singupErr');
    errEl.hide();
    
    var email = $('#signupEmailInpt').val();
    var name = $('#signupName').val();
    
    var emailRegex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (!emailRegex.test(email) || name == '')
    {
        errEl.html(atlasChoice.errs['invalidEmail']).fadeIn('fast');
        $('#signupEmailInpt').focus();
    }
    else
    {
        errEl.fadeOut();
		$('#loginPanelNewsltr" form').submit();
    }
    	
    return false;
};

atlasChoice.loginbooking = function(){
    var errEl = $('#loginBookingErr');
    errEl.hide();
    
    
    var email = $("#loginBookingEmailInpt").val();
    var password = $("#loginBookingPwd").val();
   
    var emailRegex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (!emailRegex.test(email) || password == '')
    {
        errEl.html(atlasChoice.errs['invalidInput']).fadeIn('fast');
        $('#loginBookingEmailInpt').focus();
    }
    else
    {
        errEl.fadeOut();
		location.href = 'http://2014.atlaschoice.com/selfservice/login/'+password+'/'+email;
    }
    
    return false;
};
