;(function($){

	/*!
	 * 	Iframe Resize Plugin 1.0.0
	 *
	 *	Description: This plugin resize the parent iframe which contains the setup and content
	 *
	 * 	Author: Venstin John Q. Cenas
	 *	Website: http://simplexi.com/eng/
	 *	Copyright: Simplex Internet Inc.
	 *	Since: Fri Oct 14, 2011
	 *	Update : Mon Dec 19, 2011 08:41
	 */

	$.fn.resizeIframe = function(object, callback){
		var settings = {
			action : "auto",
			delay : 0,
			height : 0,
			iframe : "plugin_contents"
		}
	
		if (typeof object == "object") settings = $.extend(settings, object);
	
		var selector = $(this);
		var resizeIframe = {
			init : function(){
				if (settings.action == "auto"){
					setTimeout(function(){
						if (parent.document.getElementById(settings.iframe) != null){
							documentHeight = Math.max(parent.document.getElementById(settings.iframe).scrollHeight, parent.document.getElementById(settings.iframe).clientHeight)
							parent.iframeHeight(parent.document.getElementById(settings.iframe), (selector.height() - documentHeight) + parseInt(settings.height));
						}
					}, settings.delay);	
				}
				else {
					var body = document.body, html = document.documentElement;
					var documentHeight = Math.max(body.offsetHeight, html.clientHeight, html.offsetHeight);
					if (settings.action == "add"){
						setTimeout(function(){
							if (parent.document.getElementById(settings.iframe) != null) parent.iframeHeight(parent.document.getElementById(settings.iframe), ((documentHeight + parseInt(settings.height)) - documentHeight)); 
						}, settings.delay);
					}
					else if (settings.action == "deduct"){
						setTimeout(function(){
							if (parent.document.getElementById(settings.iframe) != null) parent.iframeHeight(parent.document.getElementById(settings.iframe), (documentHeight - (documentHeight + ((parseInt(settings.height) * 2) - 10)))); 
						}, settings.delay);
					}
				}
			}
		};
		
		resizeIframe.init();
		if (settings.action == "auto") resizeIframe.init(); //This fixes some bugs on height adjustment
		
		setTimeout(function(){
			if (typeof callback == "function") callback.call();
			if (typeof object == "function") object.call();
		}, settings.delay);
	};
	
	/*!
	 * 	Show Message Plugin 1.0.0
	 *
	 *	Description: This plugin show message on the top most part of the setup or content
	 *
	 * 	Author: Venstin John Q. Cenas
	 *	Website: http://simplexi.com/eng/
	 *	Copyright: Simplex Internet Inc.
	 *	Since: Wed Oct 26, 2011
	 *	Update : Mon Dec 19, 2011 08:41
	 */

	$.fn.showMessage = function(object, callback){
		var settings = {
			message : {},
			successStyle : "msg_suc_box",
			failedStyle : "msg_suc_box",
			errorStyle : "msg_warn_box cle_l",
			delay : 3000,
			hide : true,
			resizeIframe : true,
			resize : "body",
			type : "success",
			disable : "",
			scrollTop : true,
			scrollSpeed : "fast"
		};
		
		if (typeof object == "object") settings = $.extend(settings, object);
				
		var defaultmessage = {
			success : "Saved successfully.",
			failed : "No data changed.",
			error : "Server error."
		};
		
		var selector = $(this);
		
		var showMessage = {
			init : function(){
				var message = "";
				var cssClass = "";

				if (settings.type == "failed"){
					cssClass = settings.failedStyle;
					message = settings.message.failed ? settings.message.failed : defaultmessage.failed;
				}
				else if (settings.type == "success"){
					cssClass = settings.successStyle;
					message = settings.message.success ? settings.message.success : defaultmessage.success;
				}
				else {
					cssClass = settings.errorStyle;
					message = settings.message.error ? settings.message.error : defaultmessage.error;
				}
			
				selector.empty();
				$.browser.msie ? selector.prop("class", cssClass) : selector.attr("class", cssClass);
				selector.html('<p><span>' + message + '</span></p>');
				
				if (settings.disable != "") $.browser.msie ? $(settings.disable).prop("disabled", "disabled") : $(settings.disable).attr("disabled", "disabled");

				if (settings.hide === true){
					selector.show(0, function(){
						if (settings.resizeIframe === true) $(settings.resize).resizeIframe();
					}).delay(settings.delay).fadeOut("slow", function(){
						$(settings.resize).resizeIframe();
						if (settings.disable != "") $.browser.msie ? $(settings.disable).removeProp("disabled", "disabled") : $(settings.disable).removeAttr("disabled", "disabled");
						showMessage.callback();
					});
				}
				else {
					selector.show(0, function(){
						if (settings.resizeIframe === true) $(settings.resize).resizeIframe();
						showMessage.callback();
					});
				}
				
				if (settings.scrollTop == true){
					var iframe = parent.document.getElementById("plugin_contents");
					iframe ? $(iframe).parents("html, body").animate({ scrollTop: 0 }, settings.scrollSpeed) : $("html, body").animate({ scrollTop: 0 }, settings.scrollSpeed);
				}
			},
			
			callback : function(){
				if (callback) callback.call();
			}
		};
		
		showMessage.init();
	};
	
	$.extend({
	
		/*!
			Get URL Parameters
			
			Description: Get URL parameters & values
			Source: http://jquery-howto.blogspot.com/2009/09/get-url-parameters-values-with-jquery.html
		*/
	
		getUrlVars : function(){
			var vars = [], hash;
			var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
			for(var i = 0; i < hashes.length; i++){
				hash = hashes[i].split('=');
				vars.push(hash[0]);
				vars[hash[0]] = hash[1];
			}
			return vars;
		},
		
		getUrlVar : function(name){
			return $.getUrlVars()[name];
		}
	});
	
})(jQuery);