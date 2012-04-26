/**
 * PLUGIN Emulation
 */
var PLUGIN = {
    /**
     * event check methods
     */
    aSubmitMethod : {},
    /**
     * Submit Event is added method
     *
     * @param string    Content Code
     * @param function  Callback Method
     */
    addSubmitEvent: function(sCode, fMethod) {
        PLUGIN.aSubmitMethod[sCode] =fMethod;
    },

    /**
     *  Page Move Function
     *  Target : Anchor Tag Move [Link] , Page Move [location.href]
     */
    goUrl: function(pNode, sUrl) {        
        window.location.href = sUrl;
    },

    post : function (pNode, aParam , sNormal , sReturnType, callbackFunc){
        // aParam.url => Fixed Url , aParam => Data Array        
        if(sNormal == "" || sNormal == "normal" || sNormal == undefined){
            $.post(aParam.url, aParam, function(sHtml) {$(pNode).html(sHtml);},"html");
        } else {
            $.post(aParam.url, aParam, callbackFunc , sReturnType);            
        }
    },
    get : function (pNode, sParam , sNormal , sReturnType , callbackFunc){
        // sParam => Fixed Url + Parameta       
        if(sNormal == "" || sNormal == "normal" || sNormal == undefined){
            $.get(sParam, function(sHtml) {$(pNode).html(sHtml);},"html");
        } else {
            $.get(sParam, callbackFunc , sReturnType);            
        }                     
    },
    eventHandler : function (node){
        return;
    }    
};


$(document).ready(function(){
    $('form').submit(function(event) {
        var bFormCheck = true;
		var oForm = this;
        var nodeName = '';

        for(k in PLUGIN.aSubmitMethod) {
            nodeName = k == 'body' ? k : '#'+k;
	        var findNode = $(this).attr('id') == k ? $(this) : $(this).parents(nodeName);

            if(findNode.attr('id') == k || (k=='body')) {
                bFormCheck = PLUGIN.aSubmitMethod[k](oForm);
            }
        }
        return bFormCheck;
    });

});
