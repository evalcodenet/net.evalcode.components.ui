

    /**
     * Pager
     *
     * @package net.evalcode.components.ui
     * @subpackage panel
     * 
     * @author evalcode.net
     */
    ui.panel.Pager=function(panel_, config_)
    {
      ui.panel.Abstract.call(this, panel_, config_);

      // PROPERTIES
      if(!this.config.classNext)
        this.config.classNext="next";
      if(!this.config.classPrev)
        this.config.classPrev="prev";

      if(!this.config.page)
        this.config.page=0;
      if(!this.config.pages)
        this.config.pages=0;

      if("undefined"==typeof(this.config.swipe))
        this.config.swipe=true;
    };

    ui.panel.Pager.prototype=new ui.panel.Abstract();
    ui.panel.Pager.prototype.constructor=ui.panel.Pager;


    // OVERRIDES/IMPLEMENTS
    ui.panel.Pager.prototype.type=function()
    {
      return "ui/panel/pager";
    };

    ui.panel.Pager.prototype.init=function()
    {
      this.info("Initialize pager.", this.config);

      if(this.config.swipe)
      {
        var pager=this;

        if(!this.config.swipeElement && this.config.pageable)
          this.config.swipeElement=this.config.pageable.panel;

        if(this.config.swipeElement)
          this.config.swipeElement.on("swipe", {panel: this}, this.onSwipe);
        else
          this.warn("Swiping can not be initialized without a swipe element [config: swipeElement].");

        this.config.elementNext=this.panel.children("."+this.config.classNext).first();
        this.config.elementPrev=this.panel.children("."+this.config.classPrev).first();

        this.config.elementNext.on("click", function() {
          pager.onNext();
        });

        this.config.elementPrev.on("click", function() {
          pager.onPrev();
        });
      }
    };

    ui.panel.Pager.prototype.render=function()
    {
      this.info("Render pager.");

      if(this.config.pageable)
      {
        if(this.config.pageable.hasNext())
          this.config.elementNext.removeClass("disabled");
        else
          this.config.elementNext.addClass("disabled");

        if(this.config.pageable.hasPrev())
          this.config.elementPrev.removeClass("disabled");
        else
          this.config.elementPrev.addClass("disabled");
      }
      else
      {
        this.warn("Pager requires an instance of ui/panel/pageable to render correctly [config: pageable].");
      }
    };


    // ACCESSORS/MUTATORS
    ui.panel.Pager.prototype.onSwipe=function(event_)
    {
      if(event_.swipestart.coords[0]<event_.swipestop.coords[0])
        event_.data.panel.onPrev();
      else
        event_.data.panel.onNext();
    };

    ui.panel.Pager.prototype.onPrev=function()
    {
      this.info("onPrev", this.config);

      if(this.config.pageable && this.config.pageable.hasPrev())
        this.config.pageable.prev();

      this.render();
    };

    ui.panel.Pager.prototype.onNext=function()
    {
      this.info("onNext", this.config);

      if(this.config.pageable && this.config.pageable.hasNext())
        this.config.pageable.next();

      this.render();
    };
    //--------------------------------------------------------------------------


    /**
     * Pageable
     *
     * @package net.evalcode.components.ui
     * @subpackage panel
     * 
     * @author evalcode.net
     */
    ui.panel.Pageable=function(panel_, config_)
    {
      ui.panel.Abstract.call(this, panel_, config_);
    }

    ui.panel.Pageable.prototype=new ui.panel.Abstract();
    ui.panel.Pageable.prototype.constructor=ui.panel.Pageable;


    // OVERRIDES/IMPLEMENTS
    ui.panel.Pageable.prototype.type=function()
    {
      return "ui/panel/pageable";
    };


    // ACCESSORS/MUTATORS
    ui.panel.Pageable.prototype.next=function() {};
    ui.panel.Pageable.prototype.prev=function() {};

    ui.panel.Pageable.prototype.hasNext=function() {};
    ui.panel.Pageable.prototype.hasPrev=function() {};
    //--------------------------------------------------------------------------
