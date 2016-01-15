<? namespace Components; ?>
<? /* @var $self \Components\Ui_Panel_Select */ ?>
<select id="<?= $self->id(); ?>" name="<?= $self->id(); ?>"<? if($self->callback): ?> onchange="<?= $self->callback(); ?>"<? endif; ?> <?= $self->attributes(); ?>>
  <? if(null!==$self->emptyOptionTitle): ?>
    <option value=""<? if(null===$self->value()): ?> selected="selected"<? endif; ?>><?= \html\escape($self->emptyOptionTitle); ?></option>
  <? endif; ?>
  <? foreach($self->options as $value=>$title): ?>
    <option value="<?= $value; ?>"<? if($self->value()==$value): ?> selected="selected"<? endif; ?>><?= \html\escape($title); ?></option>
  <? endforeach; ?>
</select>
