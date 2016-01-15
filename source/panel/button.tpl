<? namespace Components; ?>
<? /* @var $self \Components\Ui_Panel_Button */ ?>
<input id="<?= $self->id(); ?>" <?= $self->attributes(); ?><? if($self->callback): ?> onclick="<?= $self->callback(); ?>"<? endif; ?>/>
