<? namespace Components; ?>
<div id="<?= $this->id(); ?>">
  <? /* TODO Initialize date/time picker & localize... */ ?>
  <input id="<?= $this->id(); ?>-date" name="<?= $this->id(); ?>-date" type="text" value="<?= $this->value->format(I18n::translate('common/date/pattern/short')); ?>" />
  <input id="<?= $this->id(); ?>-time" name="<?= $this->id(); ?>-time" type="text" value="<?= $this->value->format('H:i'); ?>" />
</div>
