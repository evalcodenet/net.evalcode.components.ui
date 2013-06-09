

  // IMPLEMENTATION
  function ui_panel_tab_activate(panelIdTabs_, idxTab_, callback_)
  {
    if(null!=callback_ && "undefined"!=typeof(callback_))
    {
      var interrupt=false;

      if("object"==typeof(callback_))
      {
        var callbackMethod=callback_[0];
        var callbackArgs=callback_[1];

        callbackArgs.push(panelIdTabs_);
        callbackArgs.push(idxTab_);

        interrupt=!eval(callbackMethod+"(callbackArgs)");
      }
      else if("function"==typeof(callback_))
      {
        interrupt=!callback_(panelIdTabs_, idxTab_);
      }
      else if("string"==typeof(callback_))
      {
        interrupt=!eval(callback_+"('"+panelIdTabs_+"', "+idxTab_+");");
      }

      if(interrupt)
        return;
    }

    jQuery("#"+panelIdTabs_+" .ui_panel_tab_content").hide();

    jQuery("#"+panelIdTabs_+" .ui_panel_tab_label").removeClass("active");
    jQuery("#"+panelIdTabs_+"-label-"+idxTab_).addClass("active");

    jQuery("#"+panelIdTabs_+"-content-"+idxTab_).show();

    document.getElementById(panelIdTabs_+"-value").value=idxTab_;

    return;
  }

  function ui_panel_tabs_init(panelIdTabs_)
  {
    jQuery("#"+panelIdTabs_+" .ui_panel_tab_content").hide();
    jQuery("#"+panelIdTabs_+" .ui_panel_tab_content.active").show();
  }
