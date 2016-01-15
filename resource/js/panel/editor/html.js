

    // NAMESPACE
    if(!ui.panel.editor)
      ui.panel.editor={};


    /**
     * Html
     *
     * @package net.evalcode.components.ui
     * @subpackage panel.editor
     * 
     * @author evalcode.net
     */
    ui.panel.editor.Html=function(panel_, config_)
    {
      ui.panel.Abstract.call(this, panel_, config_);

      // PROPERTIES
      this.rendered=false;
      this.tinymceLoaded=false;
    };

    ui.panel.editor.Html.prototype=new ui.panel.Abstract();
    ui.panel.editor.Html.prototype.constructor=ui.panel.editor.Html;


    // OVERRIDES/IMPLEMENTS
    ui.panel.editor.Html.prototype.type=function()
    {
      return "ui/panel/editor/html";
    };

    ui.panel.editor.Html.prototype.init=function()
    {
      this.info("Initialize HTML editor.", this.config);

      this.tinymceLoaded="undefined"!=typeof(tinyMCE);

      var instance=this;

      if(!this.tinymceLoaded)
      {
        std.include("/js/tinymce/tiny_mce.js", function() {
          instance.tinymceLoaded=true;
          instance.render();
        });
      }
    };

    ui.panel.editor.Html.prototype.render=function()
    {
      if(!this.tinymceLoaded || this.rendered)
        return;

      this.info("Render HTML editor.");

      var panelId=this.id();

      tinyMCE.baseURL="/js/tinymce";
      tinyMCE.baseURI=new tinymce.util.URI(
        document.location.protocol+"//"+document.location.hostname+"/js/tinymce"
      );

      // TODO Utilize ui.panel.Abstract.config (ui/panel attribute data-ui-args respectivly).
      tinyMCE.init({

        mode: "exact",
        elements: panelId,

        width: "800",
        height: "600",

        theme: "advanced",
        theme_advanced_buttons1: "fontselect,fontsizeselect,bold,italic,underline,strikethrough,separator,forecolor,backcolor,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,redo,undo,separator,visualaid,search,help",
        theme_advanced_buttons2: "formatselect,styleprops,bullist,numlist,separator,indent,outdent,separator,table,tablecontrols,row_props,cell_props,separator,removeformat,code",
        theme_advanced_buttons3: "image,separator,link,unlink,anchor,separator,sub,sup,cite,abbr,acronym,separator,insertdate,inserttime,charmap,emotions,hr",
        theme_advanced_blockformats: "h1,h2,h3,h4,h5,h6,p,pre,dt,dd",
        theme_advanced_resizing: true,
        theme_advanced_resizing_use_cookie: true,

        theme_advanced_fonts:
          "Arial=arial,sans-serif;"+
          "Courier New=courier new,courier;"+
          "Helvetica=helvetica;"+
          "Tahoma=tahoma,sans-serif;"+
          "Times New Roman=times new roman,times;"+
          "Verdana=verdana",

        theme_advanced_font_sizes:
           "7=  7px,"+
           "9=  9px,"+
          "10= 10px,"+
          "11= 11px,"+
          "13= 13px,"+
          "15= 15px,"+
          "17= 17px,"+
          "19= 19px,"+
          "21= 21px,"+
          "23= 23px,"+
          "25= 25px,"+
          "27= 27px,"+
          "29= 29px",

        plugins: "contextmenu,inlinepopups,insertdatetime,media,nonbreaking,paste,searchreplace,style,table,template,visualchars,xhtmlxtras",

        // editor settings
        force_br_newlines: true,
        force_p_newlines: true,
        forced_root_block: "",
        relative_urls: false
      });

      ui.set(panelId, "value", function() {
        return tinyMCE.get(panelId).getContent();
      });

      this.rendered=true;
    };
    //--------------------------------------------------------------------------
