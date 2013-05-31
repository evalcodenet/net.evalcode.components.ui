<? namespace Components; ?>
<!DOCTYPE html>
<html>
  <head>
    <title><?= $this->title; ?></title>
    <script type="text/javascript" src="/ui/resource/js/jquery/jquery-1.9.1.js"></script>
    <? /* FIXME (CSH) $this->printReferences();*/ ?>
  </head>
  <body>
    <?= $this->panel->display(); ?>
  </body>
</html>
