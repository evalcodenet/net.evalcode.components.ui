

  // PREDEFINED PROPERTIES
  var ROUTE_PANEL_HTML=ui_panel_route+".html";
  var ROUTE_PANEL_PLAIN=ui_panel_route+".txt";
  var ROUTE_PANEL_JSON=ui_panel_route+".json";
  var ROUTE_PANEL_XML=ui_panel_route+".xml";
  var ROUTE_PANEL=ROUTE_PANEL_JSON;

  var TRIGGER_ON_SUBMIT=1; 
  var TRIGGER_ON_RESPONSE=2;


  // PROPERTIES
  var ui_panel_upload=new Array();
  var ui_panel_disclosure=new Array();


  // IMPLEMENTATION
  function ui_panel_dump_debug(response_)
  {
    var dump=response_.getResponseHeader("Components-Debug");

    if(dump && 0<dump.length)
      debug("ui/panel/common", "Components Debug Output", dump);
  }

  function ui_panel_raise_exception(exception_)
  {
    if("string"==typeof(exception_))
      exception_=eval("["+exception_+"]")[0];

    error(exception_.type, exception_.message);
  }

  function ui_panel_form_name(anyFormElement_)
  {
    var submittedForm=null;

    if("string"==typeof(anyFormElement_))
      submittedForm=jQuery(anyFormElement_+"[class*=ui_panel_form]");
    else if(-1<jQuery(anyFormElement_).attr("class").indexOf("ui_panel_form"))
      submittedForm=jQuery(anyFormElement_);

    if(null==submittedForm || 1>submittedForm.length)
      submittedForm=jQuery(anyFormElement_).parents("[class*=ui_panel_form]");

    if(null==submittedForm || 1>submittedForm.length)
      return "ui_panel_root";

    var submittedFormName=jQuery(submittedForm).attr("class");
    var submittedFormNameIdx=0;

    if(-1<(submittedFormNameIdx=submittedFormName.indexOf("ui_panel_form_")))
    {
      submittedFormName=submittedFormName.substring(submittedFormNameIdx);
      if(-1<submittedFormName.indexOf(" "))
        submittedFormName=submittedFormName.substring(0, submittedFormName.indexOf(" "));
    }

    return submittedFormName;
  }

  function ui_panel_form_elements(formName_)
  {
    return jQuery("[class~="+formName_+"] :input[type!=file]").not("[class*=ui_panel_form][class!="+formName_+"]");
  }

  function ui_panel_form_elements_file(formName_)
  {
    return jQuery("[class~="+formName_+"] :file").not("[class*=ui_panel_form][class!="+formName_+"]");
  }

  function ui_panel_submit(panelIdSubmitted_, callback_, params_, trigger_, panelUploadedId_)
  {
    if(panelUploadedId_)
      log("ui/panel/common", "Continuing submission of ui/panel [panel: "+panelIdSubmitted_+"].");
    else
      log("ui/panel/common", "Initiating submission of ui/panel [panel: "+panelIdSubmitted_+"].");

    var submittedPanel=jQuery("#"+panelIdSubmitted_);
    var submittedFormName=ui_panel_form_name(submittedPanel);

    if(!panelUploadedId_)
    {
      var submittedFormElementsFile=ui_panel_form_elements_file(submittedFormName);

      if(0<submittedFormElementsFile.length)
      {
        assert("ui/panel/common", "Missing referenced script [name: ui/upload/file].", "function"==typeof(ui_panel_upload_submit));

        for(var i=0; i<submittedFormElementsFile.length; i++)
        {
          if(0<submittedFormElementsFile[i].files.length)
            ui_panel_upload.unshift(submittedFormElementsFile[i]);
        }
      }
    }

    if(0<ui_panel_upload.length)
    {
      var nextPanelUpload=ui_panel_upload.pop();
      var nextPanelUploadId=nextPanelUpload.id;

      log("ui/panel/common", "Interrupting submission of ui/panel for file upload [panel: "+panelIdSubmitted_+", panel-upload: "+nextPanelUploadId+"].");

      ui_panel_upload_submit(nextPanelUpload, function() {
        ui_panel_submit(panelIdSubmitted_, callback_, params_, trigger_, nextPanelUploadId);
      });

      return;
    }

    var submittedFormElements=ui_panel_form_elements(submittedFormName);
    var parameters=submittedFormElements.serializeArray();

    parameters.push({"name": "ui-panel-form", "value": ui_panel_forms[0][submittedFormName]});
    parameters.push({"name": "ui-panel-submitted", "value": panelIdSubmitted_});

    if("undefined"!=typeof(ui_panel_transfer_sid))
      parameters.push({"name": "ui-panel-sid", "value": ui_panel_transfer_sid});
    if(callback_)
      parameters.push({"name": "ui-panel-callback", "value": callback_[0]+"::"+callback_[1]});

    if("undefined"!=typeof(params_))
    {
      for(var param in params_)
        parameters.push({"name": panelIdSubmitted_+"-"+param, "value": params_[param]});
    }

    var request=jQuery.ajaxSetup({
      type: "POST",
      url: ui_panel_get_route(),
      async: true
    });

    if(trigger_ && trigger_[TRIGGER_ON_SUBMIT])
    {
      var triggerMethod=trigger_[TRIGGER_ON_SUBMIT].method;
      var triggerArgs=trigger_[TRIGGER_ON_SUBMIT].args;

      log("ui/panel/common", "Trigger onSubmit [panel: "+panelIdSubmitted_+", trigger: "+triggerMethod+"].");

      if(false==eval(triggerMethod+"(request, parameters, triggerArgs);"))
      {
        log("ui/panel/common", "Submission of ui/panel canceled by trigger [panel: "+panelIdSubmitted_+", trigger: "+triggerMethod+"].");

        return;
      }
    }

    log("ui/panel/common", "Submitting ui/panel [panel: "+panelIdSubmitted_+", form: "+submittedFormName+"].", parameters);

    var response=jQuery.ajax({
      data: parameters
    }).always(
      function()
      {
        log("ui/panel/common", "Received response for ui/panel submission [panel: "+panelIdSubmitted_+", form: "+submittedFormName+"].", response);

        ui_panel_dump_debug(response);

        var responseText=response.responseText;

        try
        {
          var responseObject=eval(responseText);
        }
        catch(e)
        {
          error("ui/panel/common", responseText);
        }

        if(responseObject && responseObject[0])
          responseObject=responseObject[0];

        if(responseObject)
        {
          if(responseObject.exception)
            ui_panel_raise_exception(responseObject.exception);
          
          ui_panel_redraw(responseObject);
        }

        if(trigger_ && trigger_[TRIGGER_ON_RESPONSE])
        {
          var triggerMethod=trigger_[TRIGGER_ON_RESPONSE].method;
          var triggerArgs=trigger_[TRIGGER_ON_RESPONSE].args;

          log("ui/panel/common", "Trigger onResponse [panel: "+panelIdSubmitted_+", trigger: "+triggerMethod+"].");

          eval(triggerMethod+"(response, triggerArgs);");
        }
      }
    );
  }

  function ui_panel_submit_static(panelIdSubmitable_, callbackType_, callbackMethod_, params_, callbackJsResponse_)
  {
    log("ui/panel/common", "Submitting static ui/panel callback [panel: "+panelIdSubmitable_+"].", params_);

    var parameters=new Array();

    if("undefined"!=typeof(ui_panel_transfer_sid))
      parameters.push({"name": "ui-panel-sid", "value": ui_panel_transfer_sid});

    parameters.push({"name": "ui-panel-submitted", "value": panelIdSubmitable_});
    parameters.push({"name": "ui-panel-callback", "value": callbackType_+"::"+callbackMethod_});

    if("undefined"!=typeof(params_))
    {
      for(var param in params_)
        parameters.push({"name": panelIdSubmitable_+"-"+param, "value": params_[param]});
    }

    jQuery.ajaxSetup({
      type: "POST",
      url: ui_panel_get_route(),
      async: true
    });

    var response=jQuery.ajax({data: parameters}).always(
      function()
      {
        log("ui/panel/common", "Received response for static ui/panel callback submission [panel: "+panelIdSubmitable_+"].", response);

        var responseText=response.responseText;

        try
        {
          var responseObject=eval(responseText);
        }
        catch(e)
        {
          error("ui/panel/common", responseText);
        }

        if(responseObject && responseObject[0])
          responseObject=responseObject[0];

        if(responseObject)
        {
          if(responseObject.exception)
            ui_panel_raise_exception(responseObject.exception);

          callbackJsResponse_(responseObject);
        }
        else
        {
          callbackJsResponse_(responseText);
        }
      }
    );
  }

  function ui_panel_request(uri_, params_, callbackJsResponse_)
  {
    log("ui/panel/common", "Initiating ui/panel request [uri: "+uri_+"].", params_);

    var parameters=new Array();

    if("undefined"!=typeof(ui_panel_transfer_sid))
      parameters.push({"name": "ui-panel-sid", "value": ui_panel_transfer_sid});

    if("undefined"!=typeof(params_))
    {
      for(var param in params_)
        parameters.push({"name": param, "value": params_[param]});
    }

    jQuery.ajaxSetup({
      type: "POST",
      url: uri_,
      async: true
    });

    var response=jQuery.ajax({data: parameters}).always(
      function()
      {
        log("ui/panel/common", "Received response for ui/panel request [uri: "+uri_+"].", response);
  
        ui_panel_dump_debug(response);
        callbackJsResponse_(response);
      }
    );
  }

  function ui_panel_redraw(response_)
  {
    if(response_.redraw && 0<response_.redraw.length && response_.content && 0<response_.content.length)
    {
      var scripts=ui_panel_tag_extract(response_.content, "script");

      var element=document.createElement("div");
      element.innerHTML=ui_panel_tag_strip(response_.content, "script");

      var nodes=new Array();
      for(var i=0; i<element.childNodes.length; i++)
      {
        if(3!=element.childNodes[i].nodeType && 8!=element.childNodes[i].nodeType)
          nodes.push(element.childNodes[i]);
      }

      for(var i=0; i<nodes.length; i++)
      {
        log("ui/panel/common", "Redrawing ui/panel [panel: "+nodes[i].id+"].");

        document.getElementById(nodes[i].id).parentNode.replaceChild(nodes[i], document.getElementById(nodes[i].id));
      }

      for(var i=0; i<scripts.length; i++)
        eval(scripts[i]);
    }

    if(response_ && response_.js && 0<response_.js.length)
      eval(response_.js);

    ui_panel_disclosure_init();
  }

  function ui_panel_tag_extract(html_, tag_)
  {
    var results=[];
    var code=(html_.match(new RegExp("(?:<"+tag_+".*?>)((\n|\r|.)*?)(?:<\/"+tag_+">)", "img")) || []);

    for (var i=0; i<code.length; i++)
      results.push((code[i].toString().match(new RegExp("(?:<"+tag_+".*?>)((\n|\r|.)*?)(?:<\/"+tag_+">)", "im")) || ['', ''])[1].replace(/\<\!\-\-/, '').replace(/\-\-\>/, ''));

    return results;
  }

  function ui_panel_tag_strip(html_, tag_)
  {
    return html_.replace(new RegExp("(?:<"+tag_+".*?>)((\n|\r|.)*?)(?:<\/"+tag_+">)", "img"), "");
  }

  function ui_panel_disclosure_init()
  {
    var panelsDisclosureToggle=jQuery(".ui_panel_disclosure_toggle");

    for(var i=0; i<panelsDisclosureToggle.length; i++)
    {
      var panelDisclosureToggle=jQuery(panelsDisclosureToggle[i]);

      if(!panelDisclosureToggle.hasClass("expanded"))
      {
        jQuery("#"+panelDisclosureToggle.context.rel).hide();
        panelDisclosureToggle.text("expand");
      }

      panelDisclosureToggle.attr("onclick", "ui_panel_disclosure_toggle(this);");
    }
  }

  function ui_panel_disclosure_toggle(panelDisclosureToggle_)
  {
    var width=jQuery('#'+panelDisclosureToggle_.rel).width();

    jQuery('#'+panelDisclosureToggle_.rel).slideToggle(300,
      function()
      {
        if("expand"==panelDisclosureToggle_.innerHTML)
          panelDisclosureToggle_.innerHTML="collapse";
        else
          panelDisclosureToggle_.innerHTML="expand";

        jQuery(this).width(width);
      }
    );
  }

  function ui_panel_xdebug_profile_enabled()
  {
    if(-1<location.href.indexOf("XDEBUG_PROFILE"))
      return true;

    return false;
  }

  function ui_panel_get_route()
  {
    if(ui_panel_xdebug_profile_enabled())
      return ROUTE_PANEL+"?XDEBUG_PROFILE=1";

    return ROUTE_PANEL;
  }


  // INTERNAL
  function ui_panel_initialize()
  {
    if("undefined"==typeof(jQuery) || null==document.getElementById(ui_panel_root_id+"-loaded"))
    {
      setTimeout(ui_panel_initialize, 10);

      return;
    }

    ui_panel_disclosure_init();

    log("ui/panel", "Root panel loaded [id: "+ui_panel_root_id+"].");
  }


  ui_panel_initialize();

