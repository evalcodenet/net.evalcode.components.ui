

  // PREDEFINED PROPERTIES
  var TYPE_PANEL_UPLOAD_FILE="\\Components\\Ui_Panel_Upload_File";
  var METHOD_PANEL_UPLOAD_FILE_STATUS="status";
  var METHOD_PANEL_UPLOAD_FILE_UPLOAD="upload";
  var PARAM_PANEL_UPLOAD_FILE_PROGRESS="APC_UPLOAD_PROGRESS";


  // IMPLEMENTATION
  function ui_panel_upload_submit(panelUpload_, callback_)
  {
    var panelIdUpload=panelUpload_.id;
    panelIdUpload=panelIdUpload.substring(0, panelIdUpload.length-6);

    var panelUpload=jQuery(panelUpload_);
    var panelUploadParent=panelUpload.parent();
    var panelUploadProgress=jQuery("#"+panelIdUpload+"-progress");
    var panelUploadProgressInner=panelUploadProgress.children("div");
    var panelUploadProgressLabel=panelUploadProgressInner.children("div");

    var fileName=panelUpload.context.files[0].name;

    var container=document.createElement("div");

    var frame=document.createElement("iframe");
    frame.id=panelUpload.context.name+"-frame";
    frame.name=frame.id;
    container.appendChild(frame);

    var form=document.createElement("form");
    form.method="POST";
    form.action=ui.route;
    form.enctype="multipart/form-data";
    form.target=frame.id;
    container.appendChild(form);

    var apcUploadProgress=document.createElement("input");
    apcUploadProgress.type="hidden";
    apcUploadProgress.name=PARAM_PANEL_UPLOAD_FILE_PROGRESS;
    apcUploadProgress.value=jQuery.now()
    form.appendChild(apcUploadProgress);

    var uiPanelSubmitted=document.createElement("input");
    uiPanelSubmitted.type="hidden";
    uiPanelSubmitted.name="ui-panel-submitted";
    uiPanelSubmitted.value=panelIdUpload;
    form.appendChild(uiPanelSubmitted);

    var uiPanelCallback=document.createElement("input");
    uiPanelCallback.type="hidden";
    uiPanelCallback.name="ui-panel-callback";
    uiPanelCallback.value=TYPE_PANEL_UPLOAD_FILE+"::"+METHOD_PANEL_UPLOAD_FILE_UPLOAD;
    form.appendChild(uiPanelCallback);

    var uiPanelScope=document.createElement("input");
    uiPanelScope.type="hidden";
    uiPanelScope.name="ui-panel-scope";
    uiPanelScope.value=ui_panel_scope;
    form.appendChild(uiPanelScope);

    var uiPanelTx=document.createElement("input");
    uiPanelTx.type="hidden";
    uiPanelTx.name="ui-panel-tx";
    uiPanelTx.value=ui_panel_tx;
    form.appendChild(uiPanelTx);

    form.appendChild(panelUpload.context);

    panelUpload.fadeOut(200);
    document.body.appendChild(container);

    panelUploadProgressDisplay=true;
    var panelUploadProgressUpdate=function()
    {
      if(!panelUploadProgressDisplay)
        return;

      ui.submitStatic(panelIdUpload, [TYPE_PANEL_UPLOAD_FILE, METHOD_PANEL_UPLOAD_FILE_STATUS], {"file": apcUploadProgress.value}, function(response_)
      {
        if(response_ && response_.content)
        {
          var data=response_.content;
          var percent=Math.round((100/data.total)*data.current, 2);

          panelUploadProgressInner.width(percent+"%");
          panelUploadProgressInner.fadeIn(1000);

          if(10<percent)
            panelUploadProgressLabel.fadeIn(1000);

          if(10<percent)
          {
            panelUploadProgressLabel.width("100%");
            panelUploadProgressLabel.text(percent+" %");
          }
        }

        setTimeout(function() {panelUploadProgressUpdate();}, 100);
      });
    };

    jQuery(frame).bind(
    {
      load: function(event_)
      {
        panelUploadProgressDisplay=false;

        panelUpload.context.value=null;
        panelUpload.context.files.length=0;

        panelUploadParent.append(panelUpload.context);
        document.body.removeChild(container);

        panelUploadProgress.fadeOut(200, function() {
          panelUpload.fadeIn(200);
          panelUploadProgressInner.hide();
          panelUploadProgressLabel.hide();
        });

        if(callback_)
          callback_();
      }
    });

    panelUploadProgress.fadeIn(200);

    form.submit();
    panelUploadProgressUpdate();
  }
