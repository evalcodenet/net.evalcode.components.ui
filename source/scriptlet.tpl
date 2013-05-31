<? namespace Components; ?>
<!DOCTYPE html>
<html>
  <head>
    <title><?= $this->title; ?></title>
    <? /* FIXME (CSH) $this->printReferences();*/ ?>
  </head>
  <body>
    <?= $this->panel->display(); ?>
  </body>
</html>
