<? namespace Components; ?>
<? /* @var $self \Components\Ui_Scriptlet */ ?>
<? if(Environment::isDev() || Runtime::isManagementAccess()): ?>
  <meta name="libstd.debug" content="<?= Debug::verbosity(); ?>"/>
<? endif; ?>
<script type="text/javascript">
  window.ui_panel_route="<?= Environment::uriComponentsEmbedded('ui'); ?>";
  window.ui_panel_scope="<?= Http_Session::current()->getName(); ?>";
  window.ui_panel_tx="<?= Ui_Scriptlet::transactionId(); ?>";
</script>
