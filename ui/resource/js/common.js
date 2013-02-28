

  // PREDEFINED PROPERTIES
  var ROUTE_PANEL_ROOT="/components";
  var ROUTE_PANEL_HTML="/components/get.html";
  var ROUTE_PANEL_PLAIN="/components/get.txt";
  var ROUTE_PANEL_JSON="/components/get.json";
  var ROUTE_PANEL_XML="/components/get.xml";
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

    error(exception_.type, exception_.message, exception_);
  }

  function ui_panel_submit(panelIdSubmitted_, callback_, params_, trigger_, panelUploadedId_)
  {
    if(panelUploadedId_)
      log("ui/panel/common", "Continuing submission of ui/panel [panel: "+panelIdSubmitted_+"].");
    else
      log("ui/panel/common", "Initiating submission of ui/panel [panel: "+panelIdSubmitted_+"].");

    var submitted=jQuery("#"+panelIdSubmitted_);
    var forms=submitted.parents(".ui_panel_form");
    var form=null;

    if(0<forms.length)
    {
      form=forms[0];
    }
    else
    {
      log("ui/panel/common", "Unable to resolve enclosing form for submitted panel [submitted: "+panelIdSubmitted_+"].");

      return;
    }

    if(!panelUploadedId_)
    {
      var panelsUploadFile=jQuery("#"+form.id+" .ui_panel_upload_file input[type=file]");

      if(panelsUploadFile && 0<panelsUploadFile.length)
      {
        assert("ui/panel/common", "Missing referenced script [name: ui/upload/file].", "function"==typeof(ui_panel_upload_submit));

        for(var i=0; i<panelsUploadFile.length; i++)
        {
          if(0<panelsUploadFile[i].files.length)
            ui_panel_upload.unshift(panelsUploadFile[i]);
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

    var tmpForm=document.createElement("form");
    tmpForm.innerHTML=form.innerHTML;

    var parameters=jQuery(tmpForm).serializeArray();
    tmpForm=null;

    var formId=form.id;
    formId=formId.replace(/-container/ig, "");

    parameters.push({"name": "ui-panel-submitted", "value": panelIdSubmitted_});

    if(callback_)
      parameters.push({"name": "ui-panel-callback", "value": callback_[0]+"::"+callback_[1]});

    if("undefined"!=typeof(params_))
    {
      for(var param in params_)
        parameters.push({"name": panelIdSubmitted_+"-"+param, "value": params_[param]});
    }

    var request=jQuery.ajaxSetup({
      type: "POST",
      url: ROUTE_PANEL,
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

    log("ui/panel/common", "Submitting ui/panel [panel: "+panelIdSubmitted_+", form: "+formId+"].", parameters);

    var response=jQuery.ajax({
      data: parameters
    }).always(
      function()
      {
        log("ui/panel/common", "Received response for ui/panel submission [panel: "+panelIdSubmitted_+", form: "+formId+"].", response);

        ui_panel_dump_debug(response);

        var responseObject=null;
        var responseText=response.responseText;

        var start=responseText.indexOf("{");
        var end=responseText.lastIndexOf("}")+1;

        var responseJson="";
        if(-1<start && -1<end)
          responseJson=responseText.substring(start, end);

        try
        {
          var responseArray=eval("["+responseJson+"]");
          if(responseArray && 0<responseArray.length)
            responseObject=responseArray[0];
        }
        catch(e)
        {
          error("ui/panel/common", "Failed to decode response for ui/panel submission [panel: "+panelIdSubmitted_+", form: "+formId+"].", e);
        }

        if(400>response.status)
        {
          if(null==responseObject)
          {
            debug("ui/panel/common", "RAW Response", responseText);
          }
          else
          {
            if(responseObject.exception)
              ui_panel_raise_exception(responseObject.exception);

            ui_panel_redraw(responseObject);
          }
        }
        else
        {
          error("ui/panel/common", "Received error for ui/panel submission [panel: "+panelIdSubmitted_+", form: "+formId+"].", null==responseObject?responseText:responseObject);
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

    parameters.push({"name": "ui-panel-submitted", "value": panelIdSubmitable_});
    parameters.push({"name": "ui-panel-callback", "value": callbackType_+"::"+callbackMethod_});

    if("undefined"!=typeof(params_))
    {
      for(var param in params_)
        parameters.push({"name": panelIdSubmitable_+"-"+param, "value": params_[param]});
    }

    jQuery.ajaxSetup({
      type: "POST",
      url: ROUTE_PANEL,
      async: true
    });

    var response=jQuery.ajax({data: parameters}).always(
      function()
      {
        log("ui/panel/common", "Received response for static ui/panel callback submission [panel: "+panelIdSubmitable_+"].", response);
  
        ui_panel_dump_debug(response);
        callbackJsResponse_(response);
      }
    );
  }

  function ui_panel_request(uri_, params_, callbackJsResponse_)
  {
    log("ui/panel/common", "Initiating ui/panel request [uri: "+uri_+"].", params_);

    var parameters=new Array();

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

  function ui_panel_session_id()
  {
    if(!document.cookie)
      return null;

    var ldim=document.cookie.search(/PHPSESSID/);
    var sid=document.cookie;

    if(-1<ldim)
      sid=document.cookie.substring(ldim+10);

    ldim=sid.indexOf("adminhtml=")
    if(-1<ldim)
      sid=sid.substring(ldim+10);

    var rdim=sid.indexOf(";");

    if(0>rdim)
      return sid;

    return sid.substring(0, rdim);
  }

  ui_panel_disclosure_init();

