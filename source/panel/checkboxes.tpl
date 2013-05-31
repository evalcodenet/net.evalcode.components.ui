<? foreach($this->params->options as $value=>$title): ?>
  <input type="checkbox" id="<?= $this->id; ?>-<?= $value; ?>"
    name="<?= $this->id; ?>[]"<? if($this->hasCallbackJs()): ?> onchange="<?= $this->callbackJs(); ?>"<? elseif($this->hasCallbackAjax()): ?> onchange="<?= $this->callbackAjax(); ?>"<? endif; ?>
    value="<?= $value; ?>" <? if(in_array($value, $this->value())): ?>checked="checked"<? endif; ?>/>
  <label for="<?= $this->id; ?>-<?= $value; ?>"><?= \Components\String::escapeHtml($title); ?></label>
<? endforeach; ?>
