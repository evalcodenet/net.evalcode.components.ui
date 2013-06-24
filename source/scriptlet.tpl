<!DOCTYPE html>
<html>
  <head>
    <title><?= $this->self->title; ?></title>
    <? /* FIXME (CSH) $this->printReferences();*/ ?>
  </head>
  <body>
    <?= $this->self->panel->display(); ?>
  </body>
</html>
