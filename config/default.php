<?php


namespace Components;


  Ui_Scriptlet::serve();
  Ui_Scriptlet_Image::serve('image');

  if(Environment::isDev())
    Ui_Scriptlet_Test::serve('test');
?>
