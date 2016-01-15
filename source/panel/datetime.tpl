<? namespace Components; ?>
<? /* @var $self \Components\Ui_Panel_Datetime */ ?>
<? /* TODO Initialize date/time picker & localize... */ ?>
<input id="<?= $self->id(); ?>-date" name="<?= $self->id(); ?>-date" type="text" value="<?= $self->value()->formatLocalized('common/date/pattern/short'); ?>" class="date"/>
<input id="<?= $self->id(); ?>-time" name="<?= $self->id(); ?>-time" type="text" value="<?= $self->value()->formatLocalized('common/time/pattern/short'); ?>" class="time"/>
