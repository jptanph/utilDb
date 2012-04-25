/*!
 * 	Popup Plugin 1.0.1
 *
 *	Description: Create a popup box
 *
 * 	Author : Venstin John Q. Cenas
 *	Website : http://simplexi.com/eng/
 *	Copyright : Simplex Internet Inc.
 *	Since : Aug 22, 2011
 *	Update : Oct 18, 2011 13:50
 *
 *	Command
 *
 *	- width : 200 / 200px
 *	- close
 *	- remove
 *	- closeImage : url
 *	- appendTo : Selector
 *	- title : ""
 *	- contain : true
 *	- container : Selector
 *	- draggable : true
 *	- autoCenter : true
 *	- resize : true
 *	- delay : Value
 *	- scrollBar : false
 *	- onOpen : function(){}
 *	- onClose : function(){}
 */
 
;(function($){

    $.fn.popUp = function(options){
		var thistag = this.attr("id") ? "#" + this.attr("id") : "." + this.attr("class")
		if (options == "close"){
			$("[popup='" + thistag + "']").hide();
		}
		else if (options == "remove"){
			$("[popup='" + thistag + "']").remove();
		}
		else {
			var setPopUp = $.data(this, 'setPopUp');
			setPopUp = new $.setPopUp(options, this);
			$.data(this, 'setPopUp', setPopUp);
		}
    };

    $.setPopUp = function(options, form) {
        this.settings = $.extend($.setPopUp.defaults, options);
        this.currentForm = form;
        this.init();
    };

    $.extend($.setPopUp, {

        defaults: {
            appendTo : "body",
			close : false,
			width : 200,
			closeImage : "./images/ly_close_btn.gif",
			title : "",
			zIndex : 1000,
			contain : true,
			container : "body",
			draggable : true,
			autoCenter : true,
			resize : false,
			resizeDelay : 1000,
			delay : 100,
			scrollBar : false
        },
		
		cssArray: {
		
			layer : {
				"position" : "absolute",
				"z-index" : "1000",
				"margin" : "auto",
				"display" : "inline-block",
				"background" : "#e9e9e9",
				"color" : "#777",
				"border" : "1px solid #777"
			},
			
			body : {
				"display" : "inline-block",
				"width" : "",
				"word-wrap" : "break-word",
				"margin" : "25px",
				"overflow" : "auto"
			},
			
			close : {
				"position" : "absolute",
				"right" : "-1px",
				"top" : "0",
				"padding" : "0",
				"background" : "",
				"display" : "inline-block",
				"width" : "34px",
				"height" : "34px",
				"font-size" : "0",
				"cursor" : "pointer"
			},
			
			title : {
				"margin-bottom" : "25px",
				"font-family" : "Tahoma, verdana, arial, sans-serif",
				"font-weight" : "bold",
				"font-size" : "16px",
				"text-align" : "center",
				"color" : "#444",
				"line-height" : "16px",
				"padding-bottom" : "0",
				"cursor" : ""
			},
			
			handle : {
				"position" : "absolute",
				"bottom" : "-1px",
				"right" : "-1px",
				"cursor" : "nw-resize",
				"height" : "8px",
				"width" : "8px",
				"border" : "1px solid #777",
				"background" : "#777"
			}
		},
	
        prototype: {
		
			actionOwner : [],

            init: function(){

				var setPopUp = this;
				var tag = setPopUp.currentForm;

				if (setPopUp.settings.onOpen) {
					setPopUp.settings.onOpen.call(setPopUp, setPopUp.currentForm);
					delete setPopUp.settings.onOpen;
				}
				
				var thistag = tag.attr("id") ? "#" + tag.attr("id") : "." + tag.attr("class");
				var replaceClass = tag.parent().attr("class") ? (tag.parent().attr("class")).replace(/[0-9]/g,"") : "";

				if (replaceClass == "popup-dialog-content"){
					var parent = $("." + $("." + tag.parent().attr("class")).parent().attr("class")).parent();
					var currentIndex = setPopUp.settings.zIndex;
					var parentClass = $("." + $("." + tag.parent().attr("class")).parent().attr("class")).parent().attr("class");
					var parentNumber = parentClass.replace("popup-dialog", "");
					var title = setPopUp.settings.title ? setPopUp.settings.title : (tag.attr("title") != undefined ? tag.attr("title") : "");
					$(".popup-dialog-title" + parentNumber).text(title);
					
					if ($("[popup]:visible").length > 0){
						$("[popup]:visible").each(function(){
							if (parseInt($(this).css("z-index")) > currentIndex) currentIndex = parseInt($(this).css("z-index"));
						});
					}
					
					parent.css("z-index", (currentIndex + 1));

					if (setPopUp.settings.autoCenter == true){
						setTimeout(function(){
							setPopUp.marginLeft(parent);
							setPopUp.marginTop(parent);
							parent.show();
						}, setPopUp.settings.delay);
					}
					else {
						parent.show();
					}
				}
				else {
					$(document).find('[popup="' + thistag + '"]').remove();
					
					tag.show();
					var clone = tag.clone()
					tag.remove();

					var classnumber = $("[popup]").length + 1;
					var title = setPopUp.settings.title ? setPopUp.settings.title : (tag.attr("title") != undefined ? tag.attr("title") : "");
					var handle = setPopUp.settings.resize == true ? '<div class="popup-handle' + classnumber + '"></div>' : "";
					
					title = title != "" ? '<div class="popup-dialog-title' + classnumber + '">' + title + '</div>' : "";
					
					setPopUp.cssSetting();

					$(setPopUp.settings.appendTo).append('<div class="popup-dialog' + classnumber + '" popup="' + thistag + '"><span class="popup-close"></span><div class="popup-dialog-body' + classnumber + '">' + title + '<div class="popup-dialog-content' + classnumber + '"></div></div>' + handle + '</div>');
					$(".popup-dialog-content" + classnumber).empty();
					$(".popup-dialog-content" + classnumber).html(clone);
					$(".popup-dialog" + classnumber).css($.setPopUp.cssArray.layer);
					$(".popup-close").css($.setPopUp.cssArray.close);
					$(".popup-handle" + classnumber).css($.setPopUp.cssArray.handle);
					$(".popup-dialog-body" + classnumber).css($.setPopUp.cssArray.body);
					$(".popup-dialog-title" + classnumber).css($.setPopUp.cssArray.title);
					$(".popup-dialog" + classnumber).hide();
					this.marginLeft($(".popup-dialog" + classnumber));
					this.marginTop($(".popup-dialog" + classnumber));
					
					setTimeout(function(){
						setPopUp.marginTop($(".popup-dialog" + classnumber));
						$(".popup-dialog" + classnumber).show();
					}, setPopUp.settings.delay);
					
					if (setPopUp.settings.contain == true && setPopUp.settings.draggable == true){
						var $div = $(setPopUp.settings.container);
						$(".popup-dialog" + classnumber).draggable("start",function(e, dd){
							dd.limit = $div.offset();
							dd.limit.bottom = dd.limit.top + $div.outerHeight() - $(this).outerHeight();
							dd.limit.right = dd.limit.left + $div.outerWidth() - $(this).outerWidth();
						}).draggable(function(e, dd){
							$(this).css({
								top: Math.min(dd.limit.bottom, Math.max(dd.limit.top, dd.offsetY)),
								left: Math.min(dd.limit.right, Math.max(dd.limit.left, dd.offsetX))
							});   
						}, {handle: ".popup-dialog-title" + classnumber});
					}
					else if (setPopUp.settings.draggable == true){
						$(".popup-dialog" + classnumber).draggable(function(e, dd){
							$(this).css({
								top: dd.offsetY,
								left: dd.offsetX
							});
						}, {handle: ".popup-dialog-title" + classnumber});
					}
		
					if (setPopUp.settings.resize == true){
						setTimeout(function(){
							var height = $(".popup-dialog-body" + classnumber).height();
							var width = $(".popup-dialog-body" + classnumber).width();
							
							$(".popup-handle" + classnumber).draggable("start",function(e, dd){
								dd.width = $(".popup-dialog-body" + classnumber).width();
								dd.height = $(".popup-dialog-body" + classnumber).height();
							}).draggable(function(e, dd){
								$(".popup-dialog-body" + classnumber).css({		
									width: Math.max(width, dd.width + dd.deltaX),
									height: Math.max(height, dd.height + dd.deltaY)
								});   
							}, {handle: ".popup-handle" + classnumber});
						}, setPopUp.settings.resizeDelay);
					}
		
					$(document).unbind("keydown").keydown(function(e){
						var code = (e.keyCode ? e.keyCode : e.which);
						if (code == 27){
							var container = "";
							var currentIndex = 0;
							$("[popup]:visible").each(function(){
								if (parseInt($(this).css("z-index")) > currentIndex){
									container = this;
									currentIndex = parseInt($(this).css("z-index"));
								}
							});
							
							setPopUp.close(container);
						}
					});
					
					$(".popup-close").click(function(){
						setPopUp.close(this);
					});
					
					$(".popup-dialog" + classnumber).css("z-index", (setPopUp.settings.zIndex + $("[popup]:visible").length));
				}
				
				if (setPopUp.settings.onClose){
					setPopUp.actionOwner[$(tag).attr("id")] = setPopUp.settings.onClose;
					delete setPopUp.settings.onClose;
				}
				
				$(thistag).find("input[type='text']:first").focus();
            },
			
			cssSetting : function(){
				var setPopUp = this;
				$.setPopUp.cssArray.close.background = "url(" + setPopUp.settings.closeImage + ")"
				$.setPopUp.cssArray.body.width = setPopUp.settings.width;
				$.setPopUp.cssArray.body.overflow = setPopUp.settings.scrollBar == true ? "auto" : "none";
				$.setPopUp.cssArray.title.cursor = function(){
					return setPopUp.settings.draggable == true ? "move" : "auto";
				};
			},
			
			close : function(selector){
				var setPopUp = this;
				var parent = $(selector).attr("class") == "popup-close" ? $(selector).parents("[popup]").attr("popup") : $(selector).attr("popup");
			
				if (parent != undefined){
					parent = parent.replace("#", "");
					if (parent in setPopUp.actionOwner){
						setPopUp.actionOwner[parent].call(setPopUp, setPopUp.currentForm);
					}	
				}
				
				$(selector).attr("class") == "popup-close" ? $(selector).parents("[popup]").hide() : $(selector).hide();	
			},
			
			marginTop : function(selector){
				var body = document.body, html = document.documentElement;
				var documentHeight = Math.max(body.offsetHeight, html.clientHeight, html.offsetHeight);
				var marginTop = (documentHeight / 2) - (selector.height() / 2);
				return selector.css("top", (marginTop > 0 ? marginTop : 50) + "px");
			},
			
			marginLeft : function(selector){
				return selector.css("left", (($("body").width() / 2) - (parseInt(this.settings.width) / 2) - 25) + "px");
			},

            objectSize : function(obj){
                var size = 0, key;
                for (key in obj){
                    if (obj.hasOwnProperty(key)) size++;
                }
                return size;
            }
        }
    });

	$.fn.draggable = function(str, arg, opts){
		var type = typeof str == "string" ? str : "",
		fn = $.isFunction(str) ? str : $.isFunction(arg) ? arg : null;
		if (type.indexOf("drag") !== 0) type = "drag"+ type;

		opts = (str == fn ? arg : opts) || {};

		return fn ? this.bind(type, opts, fn) : this.trigger(type);
	};

	var $event = $.event, 
	$special = $event.special,
	drag = $special.drag = {
		
		defaults: {
			which: 1,
			distance: 0,
			not: ':input',
			handle: null,
			relative: false,
			drop: true,
			click: false
		},

		datakey: "dragdata",
		livekey: "livedrag",
		
		add: function(obj){ 
			var data = $.data(this, drag.datakey),

			opts = obj.data || {};
			data.related += 1;

			if (!data.live && obj.selector){
				data.live = true;
				$event.add(this, "draginit."+ drag.livekey, drag.delegate);
			}

			$.each(drag.defaults, function(key, def){
				if (opts[ key ] !== undefined)
					data[ key ] = opts[ key ];
			});
		},

		remove: function(){
			$.data(this, drag.datakey).related -= 1;
		},
		
		setup: function(){
			if ($.data(this, drag.datakey)) 
				return;

			var data = $.extend({ related:0 }, drag.defaults);
			$.data(this, drag.datakey, data);
			$event.add(this, "mousedown", drag.init, data);

			if (this.attachEvent) 
				this.attachEvent("ondragstart", drag.dontstart); 
		},
		
		teardown: function(){
			if ($.data(this, drag.datakey).related) 
				return;

			$.removeData(this, drag.datakey);
			$event.remove(this, "mousedown", drag.init);
			$event.remove(this, "draginit", drag.delegate);
			drag.textselect(true); 

			if (this.detachEvent) 
				this.detachEvent("ondragstart", drag.dontstart); 
		},

		init: function(event){
			var dd = event.data, results;

			if (dd.which > 0 && event.which != dd.which) 
				return; 

			if ($(event.target).is(dd.not)) 
				return;

			if (dd.handle && !$(event.target).closest(dd.handle, event.currentTarget).length) 
				return;

			dd.propagates = 1;
			dd.interactions = [ drag.interaction(this, dd) ];
			dd.target = event.target;
			dd.pageX = event.pageX;
			dd.pageY = event.pageY;
			dd.dragging = null;
			results = drag.hijack(event, "draginit", dd);

			if (!dd.propagates)
				return;

			results = drag.flatten(results);

			if (results && results.length){
				dd.interactions = [];
				$.each(results, function(){
					dd.interactions.push(drag.interaction(this, dd));
				});
			}

			dd.propagates = dd.interactions.length;

			if (dd.drop !== false && $special.drop) 
				$special.drop.handler(event, dd);

			drag.textselect(false); 
			$event.add(document, "mousemove mouseup", drag.handler, dd);

			return false;
		},	

		interaction: function(elem, dd){
			return {
				drag: elem, 
				callback: new drag.callback(), 
				droppable: [],
				offset: $(elem)[ dd.relative ? "position" : "offset" ]() || { top:0, left:0 }
			};
		},

		handler: function(event){ 
			var dd = event.data;
			switch (event.type){

				case !dd.dragging && 'mousemove': 

					if (Math.pow(event.pageX-dd.pageX, 2) + Math.pow(event.pageY-dd.pageY, 2) < Math.pow(dd.distance, 2)) 
						break; 
					event.target = dd.target; 
					drag.hijack(event, "dragstart", dd); 
					if (dd.propagates) 
						dd.dragging = true; 
				case 'mousemove': 
					if (dd.dragging){
						drag.hijack(event, "drag", dd);
						if (dd.propagates){
								if (dd.drop !== false && $special.drop)
								$special.drop.handler(event, dd); 
							break; 
						}
						event.type = "mouseup";
					}

				case 'mouseup': 
					$event.remove(document, "mousemove mouseup", drag.handler); 
					if (dd.dragging){
						if (dd.drop !== false && $special.drop) 
							$special.drop.handler(event, dd);
						drag.hijack(event, "dragend", dd);
						}
					drag.textselect(true);
					
					if (dd.click === false && dd.dragging){
						jQuery.event.triggered = true;
						setTimeout(function(){
							jQuery.event.triggered = false;
						}, 20);
					dd.dragging = false; 
					}
					break;
			}
		},

		delegate: function(event){
			var elems = [], target, 
			events = $.data(this, "events") || {};
			$.each(events.live || [], function(i, obj){
				if (obj.preType.indexOf("drag") !== 0)
					return;
				target = $(event.target).closest(obj.selector, event.currentTarget)[0];
				if (!target) 
					return;
				$event.add(target, obj.origType+'.'+drag.livekey, obj.origHandler, obj.data);
				if ($.inArray(target, elems) < 0)
					elems.push(target);		
			});
			if (!elems.length) 
				return false;	
			return $(elems).bind("dragend."+ drag.livekey, function(){
				$event.remove(this, "."+ drag.livekey);
			});
		},
		
		hijack: function(event, type, dd, x, elem){
			if (!dd) 
				return;
			var orig = { event:event.originalEvent, type: event.type },
			mode = type.indexOf("drop") ? "drag" : "drop",
			result, i = x || 0, ia, $elems, callback,
			len = !isNaN(x) ? x : dd.interactions.length;
			event.type = type;
			event.originalEvent = null;
			dd.results = [];
			do if (ia = dd.interactions[ i ]){
				if (type !== "dragend" && ia.cancelled)
					continue;
				callback = drag.properties(event, dd, ia);
				ia.results = [];
				$(elem || ia[ mode ] || dd.droppable).each(function(p, subject){
					callback.target = subject;
					result = subject ? $event.handle.call(subject, event, callback) : null;
					if (result === false){
						if (mode == "drag"){
							ia.cancelled = true;
							dd.propagates -= 1;
						}
						if (type == "drop"){
							ia[ mode ][p] = null;
						}
					}
					else if (type == "dropinit")
						ia.droppable.push(drag.element(result) || subject);
					if (type == "dragstart")
						ia.proxy = $(drag.element(result) || ia.drag)[0];
					ia.results.push(result);
					delete event.result;
					if (type !== "dropinit")
						return result;
				});	
				dd.results[ i ] = drag.flatten(ia.results);	
				if (type == "dropinit")
					ia.droppable = drag.flatten(ia.droppable);
				if (type == "dragstart" && !ia.cancelled)
					callback.update(); 
			}
			while (++i < len)
			event.type = orig.type;
			event.originalEvent = orig.event;
			return drag.flatten(dd.results);
		},

		properties: function(event, dd, ia){		
			var obj = ia.callback;
			obj.drag = ia.drag;
			obj.proxy = ia.proxy || ia.drag;
			obj.startX = dd.pageX;
			obj.startY = dd.pageY;
			obj.deltaX = event.pageX - dd.pageX;
			obj.deltaY = event.pageY - dd.pageY;
			obj.originalX = ia.offset.left;
			obj.originalY = ia.offset.top;
			obj.offsetX = event.pageX - (dd.pageX - obj.originalX);
			obj.offsetY = event.pageY - (dd.pageY - obj.originalY);
			obj.drop = drag.flatten((ia.drop || []).slice());
			obj.available = drag.flatten((ia.droppable || []).slice());
			return obj;	
		},
		
		element: function(arg){
			if (arg && (arg.jquery || arg.nodeType == 1))
				return arg;
		},

		flatten: function(arr){
			return $.map(arr, function(member){
				return member && member.jquery ? $.makeArray(member) : 
					member && member.length ? drag.flatten(member) : member;
			});
		},
		
		textselect: function(bool){ 
			$(document)[ bool ? "unbind" : "bind" ]("selectstart", drag.dontstart)
				.attr("unselectable", bool ? "off" : "on")
				.css("MozUserSelect", bool ? "" : "none");
		},

		dontstart: function(){ 
			return false; 
		},
		
		callback: function(){}
	};

	drag.callback.prototype = {
		update: function(){
			if ($special.drop && this.available.length)
				$.each(this.available, function(i){
					$special.drop.locate(this, i);
				});
		}
	};

	$special.draginit = $special.dragstart = $special.dragend = drag;

})(jQuery);