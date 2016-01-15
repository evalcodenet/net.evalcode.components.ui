

  if('undefined'==typeof(ui))
  {
    ui_panel_declare=function()
    {
      /**
       * UI
       *
       * @package net.evalcode.components.ui
       *
       * @author evalcode.net
       */
      ui_panel=function()
      {
        ui_panel.TRIGGER_ON_SUBMIT=4; 
        ui_panel.TRIGGER_ON_RESPONSE=8;


        // PROPERTIES
        this.initialized=false,
        this.secure=true,

        this.script={},
        this.panel={},
        this.panelLoader=null,

        this.route=function() {
          var routeParams={};

          if(-1<std.DEBUG)
            routeParams["debug"]=std.DEBUG;
          if(-1<location.href.indexOf("XDEBUG_PROFILE"))
            routeParams["XDEBUG_PROFILE"]=1;

          var route="/";

          if("undefined"==typeof(ui_panel_route))
          {
            var path=window.location.pathname;
            var idxExt=path.lastIndexOf(".");

            if(-1<idxExt)
            {
              if("json"!=path.substring(idxExt+1).toLowerCase())
                path=path.substring(0, idxExt)+".json";
            }
            else
            {
              path+=".json";
            }

            route=window.location.origin+path;
          }
          else
          {
            route=ui_panel_route+".json";
          }

          if(routeParams=jQuery.param(routeParams))
          {
            if(-1<route.indexOf("?"))
              route+="&"+routeParams;
            else
              route+="?"+routeParams;
          }

          return route;
        }();

        this.data={},
        this.upload=[],
        // FIXME Remove / implement as regular panel.
        this.disclosure=[],


        // ACCESSORS/MUTATORS
        this.create=function(panel_, type_, args_, callback_)
        {
          var panel=jQuery(panel_);
          var type=std.typeForName(type_);

          if(type)
          {
	          var panelId=panel.attr("id");

            std.info("ui/create", "Instantiate panel [id: "+panelId+", type: "+type_+"].", args_);

            var instance=new type(panel, args_);
            instance.config.id=panelId;

            panel.get(0).uiPanel(instance);

            if(callback_)
            {
              callback_(instance);
            }
            else
            {
              instance.init();
              instance.render();
            }
          }
          else
          {
            if(null==this.panelLoader)
            {
              this.panelLoader=new ui.panel.Loader();

              std.schedule("ui/panel/loader", ui.panelLoader);
            }

            this.panelLoader.add(type_, panel_, args_, callback_);
          }
        },

        this.resolve=function(panel_, callback_)
        {
          std.run(function()
          {
            var panel=jQuery(panel_);
            var panelId=panel.attr("id");

            var type=panel.attr("ui-panel");

            var properties={};
            var propertiesValue=panel.attr("ui-panel-properties");

            try
            {
              if(propertiesValue)
                properties=jQuery.parseJSON(propertiesValue);
            }
            catch(e)
            {
              std.error("ui/resolve", "Unable to parse panel properties.", e);
            }

            var args={};

            if(properties)
            {
              jQuery.each(properties,
                function(idx_, property_)
                {
                  var propertyValue=panel.attr(property_);

                  if(propertyValue)
                  {
                    if(-1<propertyValue.indexOf("[") || -1<propertyValue.indexOf("{"))
                      args[property_]=jQuery.parseJSON(propertyValue);
                    else
                      args[property_]=propertyValue;
                  }
                }
              );

              args.properties=properties;
            }

            if(type)
            {
              try
              {
                ui.create(panel, type, args, callback_);
              }
              catch(e)
              {
                std.error("ui/resolve", null, e);
              }
            }
          });
        },

        this.init=function(parent_)
        {
          if(!parent_)
            parent_=document.body;

          jQuery(parent_).find("*[ui-panel]").each(function() {
            ui.resolve(this);
          });
        },

        this.form=function(element_)
        {
          element_=jQuery(element_);

          if(element_.attr("ui-panel-form"))
            return element_;

          var elements=element_.parents("[ui-panel-form]");

          if(1>elements.length)
            return null;

          return elements[0];
        },

        this.formPanels=function(form_)
        {
          return jQuery(form_).find(":input[type!=file]");
        },

        this.formPanelsUpload=function(form_)
        {
          return jQuery(form_).find(":file");
        },

        this.has=function(panelId_, attribute_)
        {
          return this.data[panelId_] && this.data[panelId_][attribute_];
        },

        this.get=function(panelId_, attribute_)
        {
          if(!this.data[panelId_])
            return null;

          if("function"==typeof(this.data[panelId_][attribute_]))
            return this.data[panelId_][attribute_]();

          return this.data[panelId_][attribute_];
        },

        this.set=function(panelId_, attribute_, value_)
        {
          if(!this.data[panelId_])
            this.data[panelId_]={id: panelId_};

          this.data[panelId_][attribute_]=value_;
        },

        this.request=function(uri_, params_, callbackJsResponse_)
        {
          std.info("ui/request", "Initialize ui/panel request [uri: "+uri_+"].", params_);

          var parameters=[];

          if("undefined"!=typeof(ui_panel_scope))
            parameters.push({"name": "ui-panel-scope", "value": ui_panel_scope});
          if("undefined"!=typeof(ui_panel_tx))
            parameters.push({"name": "ui-panel-tx", "value": ui_panel_tx});

          if("undefined"!=typeof(params_))
          {
            jQuery.each(params_, function(key_, value_) {
              parameters.push({"name": key_, "value": value_});
            });
          }

          if(-1<std.DEBUG)
            parameters.push({"name": "debug", "value": std.DEBUG});

          jQuery.ajaxSetup({
            async: true,
            type: "POST",
            url: uri_
          });

          var response=jQuery.ajax({data: parameters}).always(function()
          {
            std.info("ui/request", "Received response for ui/panel request [uri: "+uri_+"].", response);

            ui.dumpHeaders(response);

            callbackJsResponse_(response);
          });
        },

        this.redrawPanel=function(id_, html_)
        {
          var stddom=std("dom");
          var scripts=stddom.extractScriptTags(html_);

          var element=document.createElement("div");
          element.innerHTML=stddom.stripScriptTags(html_);

          var nodes=[];

          jQuery.each(element.childNodes, function(key_, node_) {
            if(3!=node_.nodeType && 8!=node_.nodeType)
              nodes.push(node_);
          });

          jQuery.each(nodes, function(key_, node_) {
            std.info("ui/panel", "Redrawing ui/panel [panel: "+node_.id+"].");

            var region=document.getElementById(node_.id).parentNode;
            region.replaceChild(node_, document.getElementById(node_.id));

            ui.init(region);
          });

          jQuery.each(scripts, function(key_, script_) {
            eval(script_);
          });
        }

        this.redrawPanels=function(panels_)
        {
          jQuery.each(panels_, function(id_, panel_) {
            ui.redrawPanel(id_, panel_);
          });
        }

        this.submit=function(panel_, callback_, params_, panelUploadedId_)
        {
          panel_=jQuery(panel_);

          var panelId=panel_.attr("id");
          var form=jQuery(ui.form(panel_));

          var panelPath=null;

          var formId=null;
          var formAction=null;
          var formCharset=null;
          var formEncoding=null;
          var formMethod=null;

          var parameters=[];

          if(form)
          {
            formId=form.attr("id");

            if(panelUploadedId_)
              std.info("ui/panel", "Continue submission of form [id: "+formId+", id-submittable: "+panelId+"].");
            else
              std.info("ui/panel", "Initialize submission of form [id: "+formId+", id-submittable: "+panelId+"].");

            // FIXME Probably breaks if multiple forms submit or user submits the same form multiple times simultaneously.
            // TODO Support upload panel without form.
            if(!panelUploadedId_)
            {
              var formPanelsUpload=ui.formPanelsUpload(form);

              if(0<formPanelsUpload.length)
              {
                jQuery.each(formPanelsUpload, function(key_, formPanelUpload_) {
                  if(0<formPanelUpload_.files.length)
                    ui.upload.unshift(formPanelUpload_);
                });
              }
            }

            // TODO Async.
            if(0<ui.upload.length)
            {
              var nextPanelUpload=ui.upload.pop();
              var nextPanelUploadId=nextPanelUpload.id;

              std.info("ui/panel", "Interrupt submission of form for file upload [id: "+formId+", id-upload: "+nextPanelUploadId+"].");

              ui_panel_upload_submit(nextPanelUpload, function() {
                ui.submit(panel_, callback_, params_, nextPanelUploadId);
              });

              return;
            }

            formAction=form.attr("action");
            formCharset=form.attr("accept-charset");
            formEncoding=form.attr("enctype");
            formMethod=form.attr("method");

            panelPath=form.attr("ui-panel-path");

            var formPanels=ui.formPanels(form);
            parameters=formPanels.serializeArray();

            jQuery.each(parameters, function(key_, property_) {
              if(ui.has(property_.name, "value"))
                parameters[key_].value=ui.get(property_.name, "value");
            });

            form.find("[ui-panel]").each(function() {
              if(!this.uiPanel)
                return;

              var panel=this.uiPanel();

              if(!panel)
                return;

              jQuery.each(panel.config.properties, function(key_, property_) {
                if("object"==typeof(panel.config[property_]))
                  parameters.push({name: panel.id()+"-"+property_, value: JSON.stringify(panel.config[property_])});
                else
                  parameters.push({name: panel.id()+"-"+property_, value: panel.config[property_]});
              });
            });
          }
          else
          {
            std.info("ui/panel", "Initialize submission of panel [id: "+panelId+"].");
          }

          parameters.push({name: "ui-panel-submitted", value: panelId});

          if("undefined"!=typeof(ui_panel_scope))
            parameters.push({name: "ui-panel-scope", value: ui_panel_scope});
          if("undefined"!=typeof(ui_panel_tx))
            parameters.push({name: "ui-panel-tx", value: ui_panel_tx});

          if(panelPath)
            parameters.push({name: "ui-panel-path", value: panelPath});
          if(callback_)
            parameters.push({name: "ui-panel-callback", value: callback_[0]+"::"+callback_[1]});

          if(params_)
          {
            jQuery.each(params_, function(key_, value_) {
              parameters.push({name: panelId+"-"+key_, value: value_});
            });
          }

          if(-1<std.DEBUG)
            parameters.push({name: "debug", value: std.DEBUG});

          if(!formAction)
            formAction=ui.route;
          if(!formCharset)
            formCharset="utf-8";
          if(!formEncoding)
            formEncoding="application/x-www-form-urlencoded";
          if(!formMethod)
            formMethod="POST";

          var config={
            async: true,
            type: formMethod,
            contentType: formEncoding+"; charset="+formCharset,
            dataType: "json",
            url: formAction
          };

          var request=jQuery.ajaxSetup(config);


// FIXME use jQuery.ajaxSetup({beforeSend})
//          if(trigger_ && trigger_[ui_panel.TRIGGER_ON_SUBMIT])
//          {
//            var triggerMethod=trigger_[ui_panel.TRIGGER_ON_SUBMIT].method;
//            var triggerArgs=trigger_[ui_panel.TRIGGER_ON_SUBMIT].args;
//
//            std.info("ui/panel", "Trigger onSubmit [panel: "+panelIdSubmitted_+", trigger: "+triggerMethod+"].");
//
//            if(false==eval(triggerMethod+"(request, parameters, triggerArgs);"))
//            {
//              std.info("ui/panel", "Submission of ui/panel canceled by trigger [panel: "+panelIdSubmitted_+", trigger: "+triggerMethod+"].");
//
//              return;
//            }
//          }

          if(form)
            std.info("ui/panel", "Execute submission of form [id: "+formId+", id-submittable: "+panelId+"].", parameters);
          else
            std.info("ui/panel", "Execute submission of panel [id: "+panelId+"].", parameters);

          var response=jQuery.ajax({data: parameters}).always(function()
          {
            if(form)
              std.info("ui/panel", "Received response for submission of form [id: "+formId+", id-submittable: "+panelId+"].", response);
            else
              std.info("ui/panel", "Received response for submission of panel [id: "+panelId+"].", response);

            ui.dumpHeaders(response);

            if(response.responseJSON && response.responseJSON.p)
              ui.redrawPanels(response.responseJSON.p);

//              if(trigger_ && trigger_[ui_panel.TRIGGER_ON_RESPONSE])
//              {
//                var triggerMethod=trigger_[ui_panel.TRIGGER_ON_RESPONSE].method;
//                var triggerArgs=trigger_[ui_panel.TRIGGER_ON_RESPONSE].args;
//
//                std.info("ui/panel", "Trigger onResponse [panel: "+panelIdSubmitted_+", trigger: "+triggerMethod+"].");
//
//                eval(triggerMethod+"(response, triggerArgs);");
//              }
          });
        }

        this.submitStatic=function(panel_, callback_, params_, callbackJsResponse_)
        {
          panel_=jQuery(panel_);

          var panelId=panel_.attr("id");

          std.info("ui/panel", "Initialize submission of static panel callback [id: "+panelId+"].");

          var parameters=[];

          parameters.push({name: "ui-panel-submitted", value: panelId});

          if("undefined"!=typeof(ui_panel_scope))
            parameters.push({name: "ui-panel-scope", value: ui_panel_scope});
          if("undefined"!=typeof(ui_panel_tx))
            parameters.push({name: "ui-panel-tx", value: ui_panel_tx});

          if(callback_)
            parameters.push({name: "ui-panel-callback", value: callback_[0]+"::"+callback_[1]});

          if(params_)
          {
            jQuery.each(params_, function(key_, value_) {
              parameters.push({name: panelId+"-"+key_, value: value_});
            });
          }

          var config={
            async: true,
            type: "POST",
            dataType: "json",
            url: ui.route
          };

          var request=jQuery.ajaxSetup(config);

          // TODO Integrate triggers.

          var response=jQuery.ajax({data: parameters}).always(function()
          {
            std.info("ui/panel", "Received response for submission of panel [id: "+panelId+"].", response);

            ui.dumpHeaders(response);

            if(callbackJsResponse_)
              callbackJsResponse_(response.responseJSON);

//                if(trigger_ && trigger_[ui_panel.TRIGGER_ON_RESPONSE])
//                {
//                  var triggerMethod=trigger_[ui_panel.TRIGGER_ON_RESPONSE].method;
//                  var triggerArgs=trigger_[ui_panel.TRIGGER_ON_RESPONSE].args;
//
//                  std.info("ui/panel", "Trigger onResponse [panel: "+panelIdSubmitted_+", trigger: "+triggerMethod+"].");
//
//                  eval(triggerMethod+"(response, triggerArgs);");
//                }
          });
        }

        this.dumpHeaders=function(response_)
        {
          var headers=response_.getAllResponseHeaders().split("\n");

          for(idx=0; idx<headers.length; idx++)
          {
            if(-1<headers[idx].indexOf("Components-Debug-")
              || -1<headers[idx].indexOf("Components-Exception-"))
              libstd_components.dumpHeader(headers[idx]);
          }
        }
      };


      Element.prototype.m_panel=null;

      Element.prototype.uiPanel=function(panel_)
      {
        if(panel_)
          this.m_panel=panel_;

        return this.m_panel;
      };


      // INITIALIZATION
      ui=new ui_panel();

      jQuery(window).load(function() {
        std.info("ui/init", "load", ui);
        ui.init();
      });
      //--------------------------------------------------------------------------


      // FIXME Integrate std.Loader.
      /**
       * UI Panel/Loader
       *
       * @package net.evalcode.components.ui
       * @subpackage panel
       *
       * @author evalcode.net
       */
      // CONSTRUCTION
      ui.panel.Loader=function()
      {
        std.Runnable.call(this);

        this.queue=[];
        this.waiting={};
      };

      ui.panel.Loader.prototype=new std.Runnable();
      ui.panel.Loader.prototype.constructor=ui.panel.Loader;


      // ACCESSORS/MUTATORS
      ui.panel.Loader.prototype.add=function(type_, panel_, args_, callback_)
      {
        this.queue.push({
          type: type_,
          panel: panel_,
          args: args_,
          callback: callback_
        });
      };


      // OVERRIDES/IMPLEMENTS
      ui.panel.Loader.prototype.type=function()
      {
        return "ui/panel/loader";
      };

      ui.panel.Loader.prototype.run=function()
      {
        var next=this.queue.pop();

        if(next)
        {
          var type=next.type;

          if(!this.waiting[type])
          {
            this.waiting[type]=[];

            std.run(function() {
              var chunks=type.split("/");
              var ns=chunks.shift();
              jQuery.getScript("/resource/"+ns+"/js/"+chunks.join("/")+".js");
            });
          }

          this.waiting[next.type].push(next);
        }

        jQuery.each(this.waiting, function(type_, panels_) {

          var type=std.typeForName(type_);

          if(type)
          {
            var panel=null;

            while(panel=panels_.pop())
              ui.create(panel.panel, type_, panel.args, panel.callback);
          }
        });
      };


      /**
       * UI Panel/Abstract
       *
       * @package net.evalcode.components.ui
       * @subpackage panel
       * 
       * @author evalcode.net
       */
      // CONSTRUCTION
      ui.panel.Abstract=function(panel_, config_)
      {
        std.Object.call(this);


        // PROPERTIES
        this.panel=panel_;
        this.config=config_;
      };

      ui.panel.Abstract.prototype=new std.Object();
      ui.panel.Abstract.prototype.constructor=ui.panel.Abstract;


      // ACCESSORS/MUTATORS
      ui.panel.Abstract.prototype.init=function() {};
      ui.panel.Abstract.prototype.render=function() {};

      ui.panel.Abstract.prototype.schedule=function(name_, closure_)
      {
        std.schedule(this.config.id+"-"+name_, closure_);
      };

      ui.panel.Abstract.prototype.unschedule=function(name_)
      {
        std.unschedule(this.config.id+"-"+name_);
      };

      ui.panel.Abstract.prototype.id=function()
      {
        if(!this.config.id)
        {
          if("undefined"==typeof(panel_))
            return undefined;

          if("undefined"==typeof(panel_.id))
            this.config.id=panel_.attr("id");
          else
            this.config.id=panel_.id;
        }

        return this.config.id;
      };
      //--------------------------------------------------------------------------


      // OVERRIDES/IMPLEMENTS
      ui.panel.Abstract.prototype.type=function()
      {
        return "ui/panel/abstract";
      };

      ui.panel.Abstract.prototype.toString=function()
      {
        return this.clazz()+"{"+this.id()+"}";
      };
      //--------------------------------------------------------------------------


      /**
       * UI Panel/Disclosure
       *
       * @package net.evalcode.components.ui
       * @subpackage panel
       *
       * @author evalcode.net
       */
      // CONSTRUCTION
      ui.panel.Disclosure=function(panel_, config_)
      {
        ui.panel.Abstract.call(this, panel_, config_);
      };

      ui.panel.Disclosure.prototype=new ui.panel.Abstract();
      ui.panel.Disclosure.prototype.constructor=ui.panel.Disclosure;


      // OVERRIDES/IMPLEMENTS
      ui.panel.Disclosure.prototype.init=function()
      {
        this.info("Initialize disclosure.", this.config);

        var element=document.createElement("details");
        this.config.supported=element && "undefined"!=typeof(element.open);

        var instance=this;

        if(this.config.supported)
        {
          this.panel.addClass("native");

          jQuery(this.panel).on("toggle",
            function()
            {
              if(jQuery(this).attr("open"))
                instance.config.open=true;
              else
                instance.config.open=false;
            }
          );
        }
        else
        {
          this.panel.addClass("emulated");

          if(this.config.open)
            this.panel.addClass("open");

          jQuery(this.panel).on("click",
            function()
            {
              var panel=jQuery(this);

              if(jQuery(this).attr("open"))
              {
                instance.config.open=false;

                panel.removeAttr("open");
                panel.removeClass("open");
              }
              else
              {
                instance.config.open=true;

                panel.attr("open", "true");
                panel.addClass("open");
              }
            }
          );
        }
      };

      ui.panel.Disclosure.prototype.type=function()
      {
        return "ui/panel/disclosure";
      };
      //--------------------------------------------------------------------------
    }


    var ui_panel_init=function() {
      if("undefined"==typeof(std))
        setTimeout(ui_panel_init, 10);
      else
        ui_panel_declare();
    }();
  }
