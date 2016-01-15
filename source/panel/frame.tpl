<? namespace Components; ?>
<? /* @var $self \Components\Ui_Panel_Frame */ ?>
<iframe id="<?= $self->id(); ?>" src="<?= ($value=$self->value())?$value:'/resource/ui/html/frame.html'; ?>" <?= $self->attributes(); ?>></iframe>
