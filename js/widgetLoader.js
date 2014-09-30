/* 
 * widgetLoader.js
 * 
 * Various functions for loading widgets
 * Must allow for the following:
 * -insert widget into specified element
 * -append widget into a widget container
 * -change widget settings
 * 
 * Widgets must all have the following handlers:
 * -drag
 * -set active
 * -settings (includes removal)
 * 
 * Must use standardized class names for compatibility with library-specific css
 * Css inclusion must be enforced
 * All js dependencies must be loaded
 * 
 * Must have handlers for animation
 * ..every n microseconds, call the animate action
 */

function uiWidget(id){
    this.settings = {
        id: id, //this is the widget id in the database
        container_class: "widget",
        content_class: "content",
        animation_callback: "test_animation",
        animation_speed: 5
    };
    
    this.setAttribute = function(attribute, value){
        this.settings[attribute] = value;
    };
    
    this.getAttribute = function(attribute){
        return this.settings[attribute];
    };
}

/**
 * Handle various widget actions
 */
function widgetController(){
    /**
     * Load up the widget
     */
    this.init = function(widget, load_function){
        var widget_container = document.createElement('div');
        widget_container.setAttribute('id',widget.getAttribute('id'));
        widget_container.className = widget.getAttribute('container_class');
        
        var content_wrapper = document.createElement('div');
        content_wrapper.className = widget.getAttribute('content_class');
        
        /**
         * @TODO: process positioning data (parent id)
         */
        widget_container.appendChild(content_wrapper);
        document.body.appendChild(widget_container);
        
        /**
         * Now load up the data
         * Use the api to return widget data
         * Perform auth with a token set in cookies
         */
        load_function(widget);
    };
    
    /**
     * Add some texture
     */
    this.skin = function(widget, callback){
        callback(widget);
    };
    
    /**
     * Set the content
     */
    this.setContent = function(widget, content){
        document.getElementById(widget.settings.id).firstChild.innerHTML = content;
    }
    
    /**
     * Preform frame-based recursion
     */
    this.animate = function(widget){
        
    };
    
    /**
     * Load up a menu
     */
    this.menu = function(widget){
        
    };
    
    /**
     * Load up a settings dialogue
     */
    this.configure = function(widget){
        
    };
}

function xmlhttp_request(method, url, params, callback){
    var xmlhttp;
    
    if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else{// code for IE6, IE5
        alert('You should seriously upgrade your internet browser');
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    xmlhttp.onreadystatechange = function(){
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
            callback(xmlhttp.responseText);
        }
    };
    
    var query_string = serialize(params);
    switch(method){
        case 'GET':
            xmlhttp.open(method,url+'?'+query_string,true);
            xmlhttp.send();
            break;
        default:
            xmlhttp.open(method,url,true);
            xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            xmlhttp.send(query_string);
            break;
    }
    
    
}

/**
 * From http://stackoverflow.com/questions/1714786/querystring-encoding-of-a-javascript-object
 */
serialize = function(obj, prefix) {
  var str = [];
  for(var p in obj) {
    var k = prefix ? prefix + "[" + p + "]" : p, v = obj[p];
    str.push(typeof v == "object" ?
      serialize(v, k) :
      encodeURIComponent(k) + "=" + encodeURIComponent(v));
  }
  return str.join("&");
};