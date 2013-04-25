

  // PREDEFINED PROPERTIES
  var TYPE_PANEL_UPLOAD_FILE="\\Components\\Ui_Panel_Upload_File";
  var METHOD_PANEL_UPLOAD_FILE_STATUS="status";
  var METHOD_PANEL_UPLOAD_FILE_UPLOAD="upload";
  var PARAM_PANEL_UPLOAD_FILE_PROGRESS="APC_UPLOAD_PROGRESS";


  // IMPLEMENTATION
  function ui_panel_upload_submit(panelUpload_, callback_)
  {
    var panelIdUpload=panelUpload_.id;

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
    form.action=ROUTE_PANEL;
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

    var uiPanelUploadKey=document.createElement("input");
    uiPanelUploadKey.type="hidden";
    uiPanelUploadKey.name=panelIdUpload+"-key";
    uiPanelUploadKey.value=jQuery("#"+panelIdUpload+"-key").val();
    form.appendChild(uiPanelUploadKey);

    form.appendChild(panelUpload.context);

    panelUpload.fadeOut(200);
    document.body.appendChild(container);

    panelUploadProgressDisplay=true;
    var panelUploadProgressUpdate=function()
    {
      if(!panelUploadProgressDisplay)
        return;

      ui_panel_submit_static(panelIdUpload, TYPE_PANEL_UPLOAD_FILE, METHOD_PANEL_UPLOAD_FILE_STATUS, {"file": apcUploadProgress.value}, function(response_)
      {
        var response=eval(response_.responseText);

        if(response && response[0] && response[0].exception)
          ui_panel_raise_exception(response[0].exception);

        if(response && response[0] && "false"!=response[0].content)
        {
          var data=response[0].content;
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
