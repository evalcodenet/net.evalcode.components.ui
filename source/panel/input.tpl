<input type="<?= $this->self->type; ?>" id="<?= $this->id; ?>"
  <? if($this->hasCallbackJs()): ?> onchange="<?= $this->callbackJs(); ?>"<? elseif($this->hasCallbackAjax()): ?> onchange="<?= $this->callbackAjax(); ?>"<? endif; ?>
  name="<?= $this->id; ?>" value="<?= \Components\String::escapeHtml($this->value()); ?>"<?= $this->attributes(); ?>/>