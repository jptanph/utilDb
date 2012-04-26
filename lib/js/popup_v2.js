/**
 * 관리자에서 사용하는 팝업을 띄워줍니다.
 * 
 * 
 *  1) window popup 띄우기
 *  <code>
    popup.open().window({
        'name' : 'naver',
        'url' : 'http://www.naver.com',
        'width' : '600',
        'height' : '600',
        'scrollbars' : 'yes',
        'resizable' : 'yes'
    });
 *  </code>
 *   
 *  2) 관리자 스킨을 사용하여 팝업을 호출
 *  <code>
    popup.load('id_popup').skin('admin').layer({
        'title' : '팝업제목',
        'url' : '/admin/sub/?module=MenueditPageIndex',
        'width': '500'
    });
 *  </code>
 *  
 *  3) 관리자 스킨을 사용하여 팝업을 호출 (url 미지정)
 *  <code>
    popup.load('id_popup').skin('admin').layer({
        'title' : '팝업제목',
        'width': '500'
    });
 *  </code>
 *  
 *  4) 스킨없이 display:none되어있는 레이어를 띄워준다.
 *  <code>
    popup.load('id_popup').skin(false).layer();
 *  </code>
 *  
 *  5) 빈레이어를 감싸서 띄워준다.
 *  <code>
    popup.load('id_popup').skin('empty').layer({
        'url' : '/basic/1/popup.html'
    });
 *  </code>
 *  
 *  6) 빈레이어를 감싸서 띄워준다. (url 미지정)
 *  <code>
    popup.load('id_popup').skin('empty').layer();
 *  </code>
 *  
 *  7) 팝업닫기
 *  <code>
    popup.close('id_popup');
 *  </code>
 */

var popup = {
    defaults : {
        'drag_header' : 'pop_header',
        'title' : null,
        'name' : null,
        'url' : null,
        'width' : null,
        'height' : null,
        'top' : null,
        'left' : null,
        'scrollbars' : 'no',
        'resizable ' : 'no',
        'position' : null,
        'classname' : 'ly_set',
        'addclass' : null , 
        'openCallback' : null,
        'closeCallback' : null
    },
    
    iIndex : 10000,
    sPopupId : null,
    mSkin : null,
    oPopUp : null,
    opts : null,
    sContentClass : null,
    
    load : function(sPopupId)
    {
        this.sPopupId = sPopupId;
        
        return this;
    },
    
    skin : function(mSkin)
    {
        this.mSkin = mSkin;
        
        return this;
    },
    
    open : function()
    {
        return this;
    },
    
    // ??? ????.
    layer : function(options)
    {
        options = options == null ? {} : options;

        // ?? ?? ??
        this.setValidate(options);
        
        // ?? ???? ??
        if(this.mSkin != false) this.close(this.sPopupId);
        
        // url ?? ??? ?? html? ????.
        if(this.mSkin != false){
            switch(this.mSkin){
                case "admin" :
                    this.loadAdminPopupHtml();
                break;
                case "empty" :
                    this.loadEmptyPopupHtml();
                break;
            }
        }
        
        // ?? (oPopUp) ????
        this.oPopUp = $("#"+this.sPopupId);
            
        // url ?? ??? ?? ajax???? ???? ????.
        if(this.mSkin != false){
            if(this.opts.url != null) this.callAjaxPopUpData();
            else this.callDivPopUpData();
        }
        
        // ?? top / left? ?? ?? center ? ??? ????.
        this.centerPosition();
        
        // ??? ????.
        this.show(this.oPopUp);

        // ???? ?? ??? ????.
        this.setPopUpAction();
    },
    
    // ??? ????.
    show : function(oPopUp)
    {
        oPopUp.show();
    },
    
    // ?? ??? ????.
    setPopUpAction : function ()
    {
        // esc ? ??? ??
//        $(document).keypress(function(e){
//            if(e.keyCode==27) popup.close(popup.sPopupId, true);
//        });
        
        $("#"+this.opts.drag_header).mouseover(function(){
            $("#"+popup.opts.drag_header).css("cursor", "move");
        });
        
        //???? ??? ??
        if(this.opts.drag_header && jQuery.ui) {
            var dragOpts = {
                cursor: "move",
                handle: "#"+popup.opts.drag_header
            };
            $("#"+popup.sPopupId).draggable(dragOpts);
        }
        
        if(this.opts.openCallback) this.opts.openCallback();
    },
    
    // ?? ?? ??
    setValidate : function(options)
    {
        this.iIndex++;
        this.opts  = $.extend(this.defaults, options);
        this.sContentClass = "admin_popup_contents";
        
        if (!options.title) this.opts.title = null;
        if (!options.height) this.opts.height = null;
        if (!options.top)  this.opts.top  = null;
        if (!options.left) this.opts.left = null;
        if (!options.scrollbars)  this.opts.scrollbars  = null;
        if (!options.addclass)  this.opts.addclass  = null;
        if (!options.openCallback)  this.opts.openCallback  = null;
        if (!options.closeCallback)  this.opts.closeCallback  = null;
        if (!options.url){
            this.opts.url = null;
        }else{
            var sPattern = /admin/;
            if(sPattern.test(options.url) == false) this.opts.position = "Front";
            else this.opts.position = "Admin";
        }
    },
    
    // ?? html? ????.
    loadAdminPopupHtml : function()
    {
        var popLayer = $("<div></div>").attr('id', this.sPopupId).attr("class", this.opts.classname+" "+this.opts.addclass);
        if(this.opts.width != null) popLayer.css("width", this.opts.width+"px");
        popLayer.css("height", (this.opts.height != null ? this.opts.height+"px" : "")).css("display", "none");
        
        var popLayerHeader = $("<h3>"+this.opts.title+"</h3>").attr("id", this.opts.drag_header);
        var popLayerContent = $("<div></div>").attr("class", this.sContentClass);
        var popLayerClose = "<a href=\"#none\" class=\"clse\" title=\"popup close\" onclick=\"popup.close('"+this.sPopupId+"', true)\">X</a>";

        popLayer.append(popLayerHeader).append(popLayerContent).append(popLayerClose);
        
        $('body').append(popLayer);
    },
    
    // ?? html? ????.
    loadEmptyPopupHtml : function()
    {
        var popLayer = $("<div></div>").attr('id', this.sPopupId).css('position', 'absolute');
        //popLayer.css('border', '1px solid #abadb3');
        
        var popLayerContent = $("<div></div>").attr("class", this.sContentClass);

        popLayer.append(popLayerContent);

        $('body').append(popLayer);
    },
    
    // ?? ???? ????.
    callAjaxPopUpData : function()
    {
        var sPattern = /.+\?+./;
        if(sPattern.test(this.opts.url) == true) sDelimiter = "&";
        else sDelimiter = "?";
        
        var responseText = $.ajax({ 
            type: "GET", 
            contentType: 'text/html; charset=utf-8', 
            cache : false,
            url: this.opts.url+sDelimiter+"contentonly=1", 
            async: false
        }).responseText;
        this.oPopUp.find("."+this.sContentClass).html(responseText);
    },
    
    // ?? ???? ????.
    callDivPopUpData : function()
    {
        var sContent = $('#' + this.sPopupId + "_contents").html();
        $('#' + this.sPopupId + "_contents").remove();
        this.oPopUp.find("."+this.sContentClass).html(sContent);
    },
    
    // ?? center ? ??? ????.
    centerPosition : function()
    {
        if(this.opts.left == null) var left = (($(window).width() - $("#"+this.sPopupId).width()) / 2).toFixed(0);
        else var left = this.opts.left;
        
        var iscrollTop = document.documentElement.scrollTop;
        iscrollTop =  iscrollTop == 0 ? document.body.scrollTop : iscrollTop;
        
        if(this.opts.top == null) var top = ((($(window).height() - $("#"+this.sPopupId).height()) / 2) + iscrollTop-20).toFixed(0);
        else var top = this.opts.top;
        top = top < 0 ? 0 : top;

        this.oPopUp.css("top", top+"px").css("left", left+"px");
        this.oPopUp.css({zIndex : this.iIndex});
    },
    
    // ????
    close : function(sPopupId, bCallBack)
    {
        if(this.mSkin != false){
            var sContent = $('#' + sPopupId).find("."+this.sContentClass).html();

            if(sContent && this.opts.url == null){
                var popContentsLayer = $("<div></div>").attr('id', sPopupId + "_contents").css('display', 'none').html(sContent);
                $('body').append(popContentsLayer);
            }
            $('#' + sPopupId).remove();
        }else{
            $('#' + sPopupId).hide();
        }
        
        if(bCallBack == true){
            if(this.opts.closeCallback) this.opts.closeCallback();
        }
    },
    
    // ??? ???? ??. 
    window : function(options)
    {
        this.opts  = $.extend(this.defaults, options);

        var top = (this.opts.top == null ? ($(window).height() - this.opts.height) / 2 : this.opts.top);
        var left = (this.opts.left == null ? ($(window).width() - this.opts.width) / 2 : this.opts.left);

        var popup = window.open(this.opts.url, this.opts.name, 'width='+this.opts.width+', height='+this.opts.height+', scrollbars='+this.opts.scrollbars+', resizable='+this.opts.resizable+', top='+top+', left='+left);
        
        if(popup) {
            popup.focus();
        }
    }
};