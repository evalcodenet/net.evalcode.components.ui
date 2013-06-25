<select id="<?= $this->id; ?>" name="<?= $this->id; ?>"<? if($this->hasCallbackJs()): ?> onchange="<?= $this->callbackJs(); ?>"<? elseif($this->hasCallbackAjax()): ?> onchange="<?= $this->callbackAjax(); ?>"<? endif; ?> <?= $this->attributes(); ?>>
  <? if(null!==$this->self->emptyOptionTitle): ?>
    <option value=""<? if(null===$this->value()): ?> selected="selected"<? endif; ?>><?= \Components\String::escapeHtml($this->self->emptyOptionTitle); ?></option>
  <? endif; ?>
  <? foreach($this->params->options as $value=>$title): ?>
    <option value="<?= $value; ?>"<? if($this->value()==$value): ?> selected="selected"<? endif; ?>><?= \Components\String::escapeHtml($title); ?></option>
  <? endforeach; ?>
</select>
