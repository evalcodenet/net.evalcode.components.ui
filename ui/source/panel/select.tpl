<select id="<?= $this->id; ?>" name="<?= $this->id; ?>"<? if($this->hasCallbackJs()): ?> onchange="<?= $this->callbackJs(); ?>"<? elseif($this->hasCallbackAjax()): ?> onchange="<?= $this->callbackAjax(); ?>"<? endif; ?>>
  <? foreach($this->params->options as $value=>$title): ?>
    <option value="<?= $value; ?>"><?= \Components\String::escapeHtml($title); ?></option>
  <? endforeach; ?>
</select>
