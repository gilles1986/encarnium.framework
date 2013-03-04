$(document).ready(function() {
    if( siteLoaderObject == undefined ) {
        var siteLoaderObjectConf = {
           "linkSelector" : ".dynLink",
           "loadElements" : {
             "default" : "#content",
             "html" : "html",
             "nav" : "#navigation"
           },
           "debug" : "warn"
        };
    
        var siteLoaderObject = new SiteLoader(siteLoaderObjectConf);
    }

    var ajaxFormObject = new AjaxFormHandler({
       "linkSelector" : ".ajaxForm",
       "contentArea" : $("#content"),
       "siteLoaderObject" : siteLoaderObject,
       "debug" : "warn"
    });
    siteLoaderObject.onSiteLoad(ajaxFormObject.setEventHandlers);
});