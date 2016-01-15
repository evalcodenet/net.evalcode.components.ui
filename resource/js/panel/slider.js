

    /**
     * Slider
     *
     * @package net.evalcode.components.ui
     * @subpackage panel
     * 
     * @author evalcode.net
     */
    ui.panel.Slider=function(panel_, config_)
    {
      ui.panel.Abstract.call(this, panel_, config_);


      // PREDEFINED PROPERTIES
      this.ORIENTATION_HORIZONTAL=1;
      this.ORIENTATION_VERTICAL=2;


      // PROPERTIES
      this.env=std("env");

      this.config.page=parseInt(this.config.page);
      this.config.orientation=parseInt(this.config.orientation);

      if(!this.config.classViewport)
        this.config.classViewport="viewport";
      if(!this.config.classStage)
        this.config.classStage="stage";

      this.pager=null;

      if(this.config.idPager)
      {
        this.pagerElement=jQuery("#"+this.config.idPager);
      }
      else
      {
        this.pagerElement=this.panel.children(".ui_panel_pager").first();
        this.config.idPager=this.pagerElement.attr("id");
      }

      this.viewport=this.panel.children("."+this.config.classViewport).first();
      this.stage=this.viewport.children("."+this.config.classStage).first();
      this.items=this.stage.children();

      if(!this.config.sizeItem)
        this.config.sizeItem=this.items.first().width();

      this.config.countItem=this.items.length;
    };

    ui.panel.Slider.prototype=new ui.panel.Abstract();
    ui.panel.Slider.prototype.constructor=ui.panel.Slider;


    // OVERRIDES/IMPLEMENTS
    ui.panel.Slider.prototype.type=function()
    {
      return "ui/panel/slider";
    };

    ui.panel.Slider.prototype.init=function()
    {
      this.info("Initialize slider.", this.config);

      var slider=this;

      this.updateDimensions();

      if(this.ORIENTATION_VERTICAL==this.config.orientation)
      {
        // TODO Implement.
      }
      else
      {
        this.schedule("responsive", function() {
          if(slider.config.sizeViewport!=slider.panel.width())
          {
            slider.updateDimensions();
            slider.render();
          }
        });
      }

      var offset=0;
      var limit=this.config.countPageItem;

      if(0<this.config.page)
        offset=(this.config.page-1)*this.config.countPageItem;

      this.loadItems(offset, limit);

      var configPager={
        pageable: this, 
        onPrev: this.prev,
        onNext: this.next
      };

      ui.create(this.pagerElement, "ui/panel/pager", configPager, function(pager_) {
        slider.pager=pager_;
        slider.pager.init();
        slider.pager.render();
      });
    };

    ui.panel.Slider.prototype.render=function()
    {
      this.info("Render slider.");

      var slider=this;

      if(this.ORIENTATION_VERTICAL==this.config.orientation)
      {
        this.panel.addClass("vertical");

        // TODO Implement.
      }
      else
      {
        this.panel.removeClass("vertical");
        this.stage.width(this.config.sizeStage);

        this.items.each(function() {
          jQuery(this).width(slider.config.sizeItemHandle);
        });
      }
    };

    ui.panel.Slider.prototype.hasPrev=function()
    {
      return 0<this.config.page;
    };

    ui.panel.Slider.prototype.hasNext=function()
    {
      return this.config.page<(this.config.countPage-1) && 1<this.config.countPage;
    };

    ui.panel.Slider.prototype.prev=function()
    {
      this.info("Invoke ui/panel/slider#prev");

      if(!this.hasPrev())
        return;

      this.config.page--;

      if(0<this.config.page)
      {
        var offset=(this.config.page-1)*this.config.countPageItem;
        this.loadItems(offset, this.config.countPageItem);
      }

      this.scroll();
      this.render();
    };

    ui.panel.Slider.prototype.next=function()
    {
      this.info("Invoke ui/panel/slider#next");

      if(!this.hasNext())
        return;

      if(0==this.config.page)
      {
        var offset=(1+this.config.page)*this.config.countPageItem;
        this.loadItems(offset, this.config.countPageItem);
      }

      this.config.page++;

      var offset=(1+this.config.page)*this.config.countPageItem;
      this.loadItems(offset, this.config.countPageItem);

      this.scroll();
      this.render();
    };

    ui.panel.Slider.prototype.scroll=function()
    {
      if(this.ORIENTATION_VERTICAL==this.config.orientation)
        this.scrollToY(this.offsetY(this.config.page));
      else
        this.scrollToX(this.offsetX(this.config.page));
    };

    ui.panel.Slider.prototype.scrollTo=function(offset_)
    {
      if(this.ORIENTATION_VERTICAL==this.config.orientation)
        this.scrollToY(offset_);
      else
        this.scrollToX(offset_);
    };

    ui.panel.Slider.prototype.scrollToX=function(offset_)
    {
      this.info("Invoke ui/panel/slider#scrollToX("+offset_+")");

      if(0>offset_)
        offset_=0;
      if(this.config.sizeStage<offset_)
        offset_=this.config.sizeStage;

      if(this.env.USER_AGENT_ENGINE.MSIE==this.env.userAgentEngine && 9>this.env.userAgentEngineVersion)
        this.stage.animate({left: "-"+offset_+"px"});
      else
        this.stage.css("transform", "translateX(-"+offset_+"px)");
    };

    ui.panel.Slider.prototype.scrollToY=function(offset_)
    {
      this.info("Invoke ui/panel/slider#scrollToY("+offset_+")");

      if(0>offset_)
        offset_=0;
      if(this.config.sizeStage<offset_)
        offset_=this.config.sizeStage;

      if(this.env.USER_AGENT_ENGINE.MSIE==this.env.userAgentEngine && 9>this.env.userAgentEngineVersion)
        this.stage.animate({top: "-"+offset_+"px"});
      else
        this.stage.css("transform", "translateY(-"+offset_+"px)");
    };


    // IMPLEMENTATION
    ui.panel.Slider.prototype.offsetX=function(page_)
    {
      this.config.offset=page_*this.config.countPageItem*this.config.sizeItemHandle;

      return this.config.offset;
    };

    ui.panel.Slider.prototype.offsetY=function(page_)
    {
      this.config.offset=page_*this.config.countPageItem*this.config.sizeItemHandle;

      return this.config.offset;
    };

    // TODO Optimize: Resize only visible elements - do full resize on pagination. 
    ui.panel.Slider.prototype.updateDimensions=function()
    {
      if(this.ORIENTATION_VERTICAL==this.config.orientation)
      {
        // TODO Implement.
      }
      else
      {
        this.config.sizeViewport=this.panel.width();
        this.config.countPageItem=parseInt(this.config.sizeViewport/this.config.sizeItem);
        this.config.sizeItemHandle=this.config.sizeViewport/this.config.countPageItem;
        this.config.sizeStage=this.config.sizeItemHandle*this.config.countItem;
        this.config.countPage=this.config.countItem/this.config.countPageItem;
      }

      this.info("Updated dimensions.", this.config);
    };

    ui.panel.Slider.prototype.loadItems=function(offset_, limit_)
    {
      var last=offset_+limit_;

      if(last>this.config.countItem)
        last=this.config.countItem;

      if(1>(last-offset_))
        return;

      var tmp={};

      this.info("Load items [first: "+offset_+", last: "+limit_+"].");

      for(var i=offset_; i<last; i++)
      {
        var item=jQuery(this.items[i].children[0]);
        var itemId=item.attr("data-ui-src");

        if(itemId)
          tmp[itemId]=item;
      }

      var itemIds=Object.keys(tmp);

      if(0<itemIds.length)
      {
        this.info("Load items [first: "+offset_+", last: "+last+"].");

        var slider=this;

        ui.submitStatic(
          this.panel, 
          this.config.callbackLazy,
          {items: itemIds},
          function(response_)
          {
            jQuery.each(response_, function(id_, html_) {
              if(tmp[id_])
              {
                tmp[id_].removeAttr("data-ui-src");
                tmp[id_].html(html_);
              }
            });

            if(slider.config.selectorsEqualize)
            {
              var size=0;
              var property="height";

              if(slider.ORIENTATION_VERTICAL==slider.config.orientation)
                property="width";

              slider.config.selectorsEqualize.each(function(selector_) {
                std("dom").equal[property]("#"+slider.id()+" "+selector_);
              });
            }
          }
        );
      }
    };
    //--------------------------------------------------------------------------
