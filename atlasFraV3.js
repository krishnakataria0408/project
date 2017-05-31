	var iframeFixTimeout = null;
	function initIWframeFix() {		
		var frame = document.getElementById('searchBoxFrame2');	
		var frameDOM2 = frame.contentWindow || frame.contentDocument;
		var jQe = frameDOM2.$;
		if(jQe == undefined || !jQe('#de').length) {
			iframeFixTimeout = setTimeout(function(){initIWframeFix()}, 1000)			
			return;
		} else {
			clearTimeout(iframeFixTimeout);
		}
		
		jQe('#de, #ds').closest(".field_panel, .ui-datepicker").click(function(e) {			
			expandFrame();
		});
		jQe('.external').click(function(e) {			
			var clicked = jQe(e.target);
			if(clicked.parent().hasClass('ui-datepicker-prev') || clicked.parent().hasClass('ui-datepicker-next')) {
				return;
			}
			contractFrame();			
		});
		window.onclick = function() {
			contractFrame();
		};
		function expandFrame() {
			if(jQe('.ui-datepicker:visible').length > 0) {
				document.getElementById('searchBoxFrame2').style.width  = "960px";
				document.getElementById('searchBoxFrame2').style.height = "750px";
            }
		}
		function contractFrame() {
			jQe('#de, #ds').datepicker('hide');
          	document.getElementById('searchBoxFrame2').style.width  = "530px";
			document.getElementById('searchBoxFrame2').style.height = "600px";
		}
	}
