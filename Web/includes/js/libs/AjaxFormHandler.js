/**
 * Da die Seite immer dynamisch Inhalte nachlädt, m
 * @param optionArray
 */
function AjaxFormHandler(optionArray) {
  // Attribute
  var options = [];
  options.debug = 0;
  options.linkSelector = null;
  options.contentArea = null;
  options.siteLoaderObject = null;


  /**
   * Konstruktor.
   *
   * @param {String}
   *            argOptions
   */
  var __construct = function(argOptions) {

    var configs = [
          {
           "confName" : "linkSelector",
           "type" : "string",
           "mandatory" : "true"
          },
          {
            "confName" : "contentArea",
            "type" : "object",
            "mandatory" : "true"
          },
          {
            "confName" : "siteLoaderObject",
            "type" : "object",
            "mandatory" : "true"
           }
    ];

    if(argOptions !== undefined) setDebug(argOptions['debug']);
    // Config überprüfen
    for ( var i = 0; i < configs.length; i++) {
      setConfig(configs[i].confName, argOptions[configs[i].confName],
          configs[i].type, configs[i].mandatory,
          configs[i].elementType, configs[i].jquery, configs[i].extend);
    }


    // Sobald das Dokument beim ersten mal geladen wurde werden die Links mit Event-Handlern ausgestattet
    log.debug("AjaxFormHandler::__construct");
    I.setEventHandlers();

  };


  /**
   * Setzt Event-Handler für die dynamischen Forms, sodass diese bei einem Klick dynamisch nachgeladen werden
   */
  this.setEventHandlers = function() {
    log.debug("AjaxFormHandler::setEventHandlers");
    // Alle Klick-Events aufheben, falls welche existieren
    $(options.linkSelector).unbind("submit",I.sendAjaxRequestEvent);
    $(options.linkSelector).submit(I.sendAjaxRequestEvent);
    
    log.debug("AjaxFormHandler::setEventHandlers Number of Matchings (" + options.linkSelector + "): " + $(options.linkSelector).length);
  };
  
  /**
   * Setzt Ajax Request ab und läd Antwort in Content Area
   * Event-Methode! (this => element)
   */
  this.sendAjaxRequestEvent = function(event) {
    log.debug("AjaxFormHandler::sendAjaxRequestEvent");
    method = $(this).attr('method');
    if (method === ""){
        method = "GET";
    }
    action = $(this).attr('action');
    saveData = $(this).serialize();

    $.ajax({
       type: method,
       url: action,
       data: saveData,
       success: function(msg){
         if (msg){
            $(options.contentArea).html(msg);
            options.siteLoaderObject.setEventHandlers("false");
         }
       }
     });
     I.setEventHandlers();
    return false;
  };



  /************************************************************************
   * ====================================================================
   * DO NOT TOUCH BELOW
   * ====================================================================
   ************************************************************************/
  var setDebug = function(debugging) {
    if(debugging !== undefined && (debugging == "info" || debugging == "debug" || debugging == "warning" || debugging == "error" )) {
      options.debug = debugging;
      switch(debugging) {
      case "info": log.level = 1; break;
      case "debug": log.level = 2; break;
      case "warning": log.level = 3; break;
      case "error": log.level = 4; break;
      }
    } else {
      options.debug = false;
      log.level = 0;
    }
  };

  /**
   * Logging
   */
  var log = {};
  log.info = function(message) {if (options.debug !== false) {log.write(message,1);}};
  log.debug = function(message) {if (options.debug !== false) {log.write(message,2);}};
  log.warning = function(message) {if (options.debug !== false) {log.write(message,3);}};
  log.error = function(message) {if (options.debug !== false) {log.write(message,4);}};
  log.write = function(message, logLevel) {
    if (options.debug !== false && logLevel >= log.level) {
      switch (logLevel) {
      case 1: try{console.info(message);}catch(e){} break;
      case 2: try{console.debug(message);}catch(e){} break;
      case 3: try{console.warning(message);}catch(e){} break;
      case 4: try{console.error(message);}catch(e){} break;
      }

    }
  };

  /**
   * Setzt ein Config Element und prüft es auf Typ und ob es Pflicht ist
   *
   * @param {String}
   *            confElement
   * @param {Mixed}
   *            value
   * @param {String}
   *            type
   * @param {Boolean}
   *            mandatory
   * @param {String}
   *            elementType
   */
  var setConfig = function(confElement, value, type, mandatory, elementType,
      jquery, extend) {
    // Ob leer
    if (value == null || value == undefined) {
      if (mandatory == "true") {
        throw new Error("Config Element " + confElement
            + " ist leer und darf nicht leer sein");
      }
    } else {
      // Typprüfung
      if (type != undefined) {
        if (type == "element") {
          var jQueryObject = false;
          // Auf JQuery Objekt prüfen
          if (value.context != undefined) {
            jQueryObject = true;
          }

          // Prüfen ob es ein JQuery Objekt sein muss
          if (jQueryObject == false && jquery == "true") {
            throw new Error("Config Element " + confElement
                + " soll jQuery Element sein, ist aber nicht");
          }

          var elements = [];
          // Wenn mehrere Elemente übergeben werden können müssen auch
          // alle validiert werden
          if (jQueryObject == true) {
            // Prüfen, ob Elemente im JQuery Objekt sind
            if (value.length > 0) {
              // Alle Elemente Validieren
              for ( var i = 0; i < value.length; i++) {
                var jElement = value.get(i);
                if (jElement.tagName != undefined) {
                  if (elementType != undefined) {
                    if (typeof elementType == "string")
                      elementType = [ elementType ];
                    var validElement = false;
                    for ( var j = 0; j < elementType.length; j++) {
                      if (elementType[j] == jElement.tagName
                          .toString().toLowerCase()) {
                        validElement = true;
                      }
                    }
                    if (validElement == false) {
                      var stringElemTypes = elementType[0];
                      for (j = 1; j < elementType.length; j++) {
                        stringElemTypes += ", "
                            + elementType[j];
                      }
                      throw new Error(
                          "Config Element "
                              + confElement
                              + " Nr."
                              + (i + 1)
                              + " ist nicht HTML Element Typ \""
                              + stringElemTypes
                              + "\" sondern ist \""
                              + jElement.tagName
                                  .toString()
                                  .toLowerCase()
                              + "\"");
                    }
                  }
                } else {
                  throw new Error(
                      "Config Element "
                          + confElement
                          + " hat ein Unterobjekt, dass kein HTML Element ist");
                }
              }
            } else {
              if (mandatory == "true") {
                throw new Error(
                    "Config Element "
                        + confElement
                        + "  soll HTML Element sein, ist aber leeres JQuery Objekt");
              }
            }
          } else {
            // Kein JQuery Element
            if (value.tagName != undefined) {
              if (elementType != undefined) {
                var jElement = value;
                if (typeof elementType == "string")
                  elementType = [ elementType ];
                var validElement = false;
                for ( var j = 0; j < elementType.length; j++) {
                  if (elementType[j] == jElement.tagName
                      .toString().toLowerCase()) {
                    validElement = true;
                  }
                }
                if (!validElement) {
                  var stringElemTypes = elementType[0];
                  for (j = 1; j < elementType.length; j++) {
                    stringElemTypes += ", "
                        + elementType[j];
                  }
                  throw new Error("Config Element "
                      + confElement
                      + " ist nicht HTML Element Typ \""
                      + stringElemTypes
                      + "\" sondern ist \""
                      + jElement.tagName.toString()
                          .toLowerCase() + "\"");
                }
              }
            } else {
              if (mandatory == "true") {
                throw new Error(
                    "Config Element "
                        + confElement
                        + " soll HTML Element sein, ist aber vom Typ \""
                        + (typeof value) + "\"");
              }
            }
          }

        } else if (type == "array") {
          if (typeof value == "object" && (value instanceof Array)) {
          } else {
            throw new Error("Config Element " + confElement
                + " ist nicht nicht vom Typ \"" + elementType
                + "\" sondern ist \""
                + value.tagName.toString().toLowerCase() + "\"");
          }
        } else if ((typeof value) != type) {
          if (mandatory == "true") {
            throw new Error("Config Element " + confElement
                + " ist vom Typ \"" + (typeof value)
                + "\" und nicht vom Typ \"" + type + "\"");
          }
        }
      }
      if(extend != null && extend != undefined && extend != ""  && extend != false && typeof options[confElement] == "object" && ((options[confElement] instanceof Array) || (options[confElement] instanceof Object))) {
        // Deep Merge?
        var deepMerge = (extend == "deep") ? {} : false;
        options[confElement] =(deepMerge !== false) ? $.extend(deepMerge,options[confElement], value) : $.extend(options[confElement], value);
      } else {
        options[confElement] = value;
      }


    }
    return true;
  };


  // Konstruktor nur aufrufen, wenn Klasse mit new Klasse aufgerufen wurde.
  var I = this;

  if (self != this) {
    __construct(optionArray);
  } else {
    var funcName = arguments.callee.toString();
    funcName = funcName.substr('function '.length);
    funcName = funcName.substr(0, funcName.indexOf('('));

    throw new Error("Called Function instead of Object in  " + funcName);
  }
}