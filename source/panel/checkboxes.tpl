<? namespace Components; ?>
<? /* @var $self \Components\Ui_Panel_Checkboxes */ ?>
<? foreach($self->options as $value=>$title): ?>
  <input type="checkbox" id="<?= $self->id(); ?>-<?= $value; ?>"
    name="<?= $self->id(); ?>[]"<? if($self->callback): ?> onchange="<?= $self->callback(); ?>"<? endif; ?>
    value="<?= $value; ?>"<? if(in_array($value, $self->value())): ?> checked="checked"<? endif; ?>/>
  <label for="<?= $self->id(); ?>-<?= $value; ?>"><?= \html\escape($title); ?></label>
<? endforeach; ?>
