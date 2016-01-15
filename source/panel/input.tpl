<? namespace Components; ?>
<? /* @var $self \Components\Ui_Panel_Input */ ?>
<input type="<?= $self->type; ?>" id="<?= $self->id(); ?>"<? if($self->callback): ?> onchange="<?= $self->callback(); ?>"<? endif; ?> name="<?= $self->id(); ?>" value="<?= \html\escape($self->value()); ?>"<?= $self->attributes(); ?>/>
