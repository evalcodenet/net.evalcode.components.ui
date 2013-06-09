

  // IMPLEMENTATION
  function ui_panel_html_init(panelIdHtml_)
  {
    if("undefined"==typeof(tinyMCE))
      jQuery.getScript("/js/tinymce/tiny_mce.js", function() {ui_panel_html_init_tinymce(panelIdHtml_);});
    else
      ui_panel_html_init_tinymce(panelIdHtml_);
  }

  function ui_panel_html_init_tinymce(panelIdHtml_)
  {
    tinyMCE.baseURL="/js/tinymce";
    tinyMCE.baseURI=new tinymce.util.URI(document.location.protocol+"//"+document.location.hostname+"/js/tinymce");

    tinyMCE.init({

      mode: "exact",
      elements: panelIdHtml_,

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
  }

  function ui_panel_html_value(panelIdHtml_)
  {
    return tinyMCE.get(panelIdHtml_).getContent();
  }
