

    /**
     * Tabs
     *
     * @package net.evalcode.components.ui
     * @subpackage panel
     * 
     * @author evalcode.net
     */
    ui.panel.Tabs=function(panel_, config_)
    {
      ui.panel.Abstract.call(this, panel_, config_);


      // PREDEFINED PROPERTIES
      this.ORIENTATION_HORIZONTAL=1;
      this.ORIENTATION_VERTICAL=2;
    };

    ui.panel.Tabs.prototype=new ui.panel.Abstract();
    ui.panel.Tabs.prototype.constructor=ui.panel.Tabs;


    // OVERRIDES/IMPLEMENTS
    ui.panel.Tabs.prototype.type=function()
    {
      return "ui/panel/tabs";
    };

    ui.panel.Tabs.prototype.init=function()
    {
      this.info("Initialize tabs.", this.config);

      if(this.ORIENTATION_VERTICAL==this.config.orientation)
        this.panel.addClass("vertical");
      else
        this.panel.removeClass("vertical");

      var instance=this;

      this.panel.find(".label a").click(function() {

        var label=jQuery(this);
        var value=label.attr("rel");

        jQuery("#"+instance.id()+"-value").val(value);
        var content=jQuery("#"+instance.id()+"-"+value);

        instance.panel.find(".label").removeClass("active");
        label.parents(".label").first().addClass("active");

        instance.panel.find(".content").removeClass("active");
        content.addClass("active");
      });
    };
    //--------------------------------------------------------------------------
