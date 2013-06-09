<? namespace Components; ?>
<? /* TODO Initialize date/time picker & localize... */ ?>
<input id="<?= $this->id(); ?>-date" name="<?= $this->id(); ?>-date" type="text" value="<?= $this->value()->formatLocalized('common/date/pattern/short'); ?>" />
<input id="<?= $this->id(); ?>-time" name="<?= $this->id(); ?>-time" type="text" value="<?= $this->value()->formatLocalized('common/time/pattern/short'); ?>" />
