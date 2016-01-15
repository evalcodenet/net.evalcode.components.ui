<? namespace Components; ?>
<? /* @var $self \Components\Ui_Scriptlet */ ?>
<!DOCTYPE html>
<html>
  <head>
    <title><?= \html\strip($self->title); ?></title>
    <? if(Environment::isDev() || Runtime::isManagementAccess()): ?>
      <meta name="libstd.debug" content="<?= Debug::verbosity(); ?>"/>
    <? endif; ?>
    <script type="text/javascript">
      <? if(Environment::isEmbedded()): ?>
        window.ui_panel_route="<?= Environment::uriComponentsEmbedded('ui'); ?>";
        window.ui_panel_scope="<?= Http_Session::current()->getName(); ?>";
      <? endif; ?>
      window.ui_panel_tx="<?= Ui_Scriptlet::transactionId(); ?>";
    </script>
    <? $self->printReferences(); ?>
  </head>
  <body id="<?= $self->panel->id(); ?>">
    <? foreach($self->panel->panels() as $panel): ?>
      <? $panel->display(); ?>
    <? endforeach; ?>
  </body>
</html>
