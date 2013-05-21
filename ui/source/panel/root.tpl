<? namespace Components; ?>
<script type="text/javascript">
  var ui_panel_route="<?= Environment::uriComponents('ui'); ?>";
  var ui_panel_debug=<? if(Debug::active()): ?>true<? else: ?>false<? endif; ?>;
  var ui_panel_scripts=[];
  var ui_panel_stylesheets=[];

  if("undefined"!=typeof(console))
  {
    <? if(Debug::active()): ?>
      var log=function(namespace_, message_, arg_)
      {
        if("undefined"==typeof(arg_))
          arg_="";

        console.log("["+namespace_+"] "+message_, arg_);
      }
      var debug=function(namespace_, message_, arg_)
      {
        if("undefined"==typeof(arg_))
          arg_="";

        console.warn("["+namespace_+"] "+message_, arg_);
      }
    <? else: ?>
      var log=function(namespace_, message_, arg_) {}
      var debug=function(namespace_, message_, arg_) {}
    <? endif; ?>

    var assert=function(namespace_, message_, assertion_)
    {
      console.assert(assertion_, "["+namespace_+"] "+message_);
    }
    var error=function(namespace_, message_, exception_)
    {
      if("undefined"==typeof(exception_))
        exception_="";

      console.error("["+namespace_+"] "+message_, exception_);
    }
  }
  else
  {
    var assert=function(namespace_, message_, assertion_) {return assertion_;}
    var error=function(namespace_, message_, exception_) {}
  }

  ui_panel_script_add=function(name_, async_, condition_, callback_, timestampLastModification_)
  {
    if(condition_ && !eval(condition_))
      return;

    if(ui_panel_scripts && ui_panel_scripts[name_])
    {
      if("function"==typeof(callback_))
      {
        log("ui/panel/common", "Executing callback for loaded script [name: "+name_+", callback: "+callback_+"].");
        callback_();
      }
      else if("string"==typeof(callback_))
      {
        log("ui/panel/common", "Executing callback for loaded script [name: "+name_+", callback: "+callback_+"].");
        eval(callback_);
      }

      return;
    }

    ui_panel_scripts[name_]=name_;

    log("ui/panel/common", "Loading script [name: "+name_+", modified: "+timestampLastModification_+"].");

    if("boolean"!=typeof(async_))
      async_=true;
    else
      async_=async_?true:false;

    if("undefined"==typeof(jQuery))
    {
      var elementsHead=document.getElementsByTagName("head");
      var elementScript=document.createElement("script");
      elementScript.type="text/javascript";
      elementScript.async=async_;
      elementScript.src=name_+"?"+timestampLastModification_;

      elementsHead[0].appendChild(elementScript);
    }
    else
    {
      jQuery.getScript(name_,
        function()
        {
          if("function"==typeof(callback_))
          {
            log("ui/panel/common", "Executing callback for loaded script [name: "+name_+", callback: "+callback_+"].");
            callback_();
          }
          else if("string"==typeof(callback_))
          {
            log("ui/panel/common", "Executing callback for loaded script [name: "+name_+", callback: "+callback_+"].");
            eval(callback_);
          }
        }
      );
    }
  }

  ui_panel_stylesheet_add=function(name_, async_, media_, timestampLastModification_)
  {
    if(ui_panel_stylesheets && ui_panel_stylesheets[name_])
      return;

    ui_panel_stylesheets[name_]=name_;

    log("ui/panel/common", "Loading stylesheet [name: "+name_+", modified: "+timestampLastModification_+"].");

    var elementsHead=document.getElementsByTagName("head");
    var elementLink=document.createElement("link");
    elementLink.rel="stylesheet";
    elementLink.type="text/css";
    elementLink.media=media_;
    elementLink.href=name_+"?"+timestampLastModification_;

    elementsHead[0].appendChild(elementLink);
  }
</script>
<div id="<?= $this->id; ?>">
  <? foreach($this->panels as $panel): ?>
    <? $panel->display(); ?>
  <? endforeach; ?>
</div>
<var id="<?= $this->id; ?>-loaded" class="ui_panel_loaded"></var>
