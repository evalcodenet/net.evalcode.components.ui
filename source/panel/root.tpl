<? namespace Components; ?>
<script type="text/javascript">
  if("undefined"==typeof(ui_panel_root_id))
  {
    <? if(Ui_Scriptlet::$transferSessionId): ?>
      var ui_panel_transfer_sid="<?= session_id(); ?>";
    <? endif; ?>
    <? if(Ui_Scriptlet::$embedded): ?>
      var ui_panel_route="<?= Environment::uriComponentsEmbedded('ui'); ?>";
    <? else: ?>
      var ui_panel_route="<?= Environment::uriComponents('ui'); ?>";
    <? endif; ?>
    var ui_panel_debug=<? if(Debug::active()): ?>true<? else: ?>false<? endif; ?>;
    var ui_panel_forms=new Array();
    var ui_panel_scripts=new Array();
    var ui_panel_stylesheets=new Array();
    var ui_panel_root_id="<?= $this->id(); ?>";

    var ui_panel_resource_callbacks=new Array();
    var ui_panel_resource_callbacks_pending=new Array();


    ui_panel_resource_callbacks_invoke=function()
    {
      var next=null;

      if(next=ui_panel_resource_callbacks_pending.pop())
      {
        if("string"==typeof(next))
        {
          try
          {
            eval(next);
          }
          catch(e)
          {
            ui_panel_resource_callbacks_pending.push(next);
          }
        }
        else if("function"==typeof(next))
        {
          next();
        }

        setTimeout("ui_panel_resource_callbacks_invoke()", 11);
      }
    }

    ui_panel_resource_callback_add=function(name_, callback_)
    {
      ui_panel_resource_callbacks_pending.push(callback_);
      ui_panel_resource_callbacks_invoke();
    }


    if("undefined"==typeof(console))
    {
      var assert=function(namespace_, message_, assertion_) {return assertion_;}
      var error=function(namespace_, message_, exception_) {}
      var profile_begin=function() {}
      var profile_end=function() {}
    }
    else
    {
      <? if(Debug::active()): ?>
        var log=function(namespace_, message_, arg_)
        {
          if("undefined"==typeof(arg_))
            console.log("["+namespace_+"] "+message_);
          else
            console.log("["+namespace_+"] "+message_, arg_);
        }

        var debug=function(namespace_, message_, arg_)
        {
          if("undefined"==typeof(arg_))
            console.warn("["+namespace_+"] "+message_);
          else
            console.warn("["+namespace_+"] "+message_, arg_);
        }

        var profile_begin=function()
        {
          console.profile();
        }

        var profile_end=function()
        {
          console.profileEnd();
        }
      <? else: ?>
        var log=function(namespace_, message_, arg_) {}
        var debug=function(namespace_, message_, arg_) {}
        var profile_begin=function() {}
        var profile_end=function() {}
      <? endif; ?>

      var assert=function(namespace_, message_, assertion_)
      {
        console.assert(assertion_, "["+namespace_+"] "+message_);
      }

      var error=function(namespace_, message_, exception_)
      {
        if("undefined"==typeof(exception_))
          console.error("["+namespace_+"] "+message_);
        else
          console.error("["+namespace_+"] "+message_, exception_.stack?exception_.stack:exception_);
      }
    }

    ui_panel_script_add=function(name_, async_, condition_, callback_, timestampLastModification_)
    {
      if(condition_ && !eval(condition_))
        return;

      ui_panel_resource_callback_add(name_, callback_);

      if(ui_panel_scripts[name_])
        return;

      ui_panel_scripts[name_]=name_;

      if("boolean"!=typeof(async_))
        async_=true;
      else
        async_=async_?true:false;

      log("ui/panel/root", "Loading script [name: "+name_+", modified: "+timestampLastModification_+", async: "+async_+", callback: "+callback_+"].");

      var elementsHead=document.getElementsByTagName("head");
      var elementScript=document.createElement("script");
      elementScript.type="text/javascript";
      elementScript.async=async_;
      elementScript.src=name_+"?"+timestampLastModification_;

      elementsHead[0].appendChild(elementScript);
    }

    ui_panel_stylesheet_add=function(name_, async_, media_, timestampLastModification_)
    {
      if(ui_panel_stylesheets && ui_panel_stylesheets[name_])
        return;

      ui_panel_stylesheets[name_]=name_;

      log("ui/panel/root", "Loading stylesheet [name: "+name_+", modified: "+timestampLastModification_+"].");

      var elementsHead=document.getElementsByTagName("head");
      var elementLink=document.createElement("link");
      elementLink.rel="stylesheet";
      elementLink.type="text/css";
      elementLink.media=media_;
      elementLink.href=name_+"?"+timestampLastModification_;

      elementsHead[0].appendChild(elementLink);
    }

    ui_panel_form_add=function(name_, path_)
    {
      ui_panel_forms.push(path_);
    }
  }
</script>
<div id="<?= $this->id; ?>" <?= $this->attributes(); ?>>
  <? foreach($this->panels as $panel): ?>
    <? $panel->display(); ?>
  <? endforeach; ?>
</div>
<var id="<?= $this->id; ?>-loaded" class="ui_panel_loaded"></var>
