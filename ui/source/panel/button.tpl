<input id="<?= $this->id; ?>" <?= $this->attributes(); ?><? if($this->hasCallbackJs()): ?> onclick="<?= $this->callbackJs(); ?>"<? elseif($this->hasCallbackAjax()): ?> onclick="<?= $this->callbackAjax(); ?>"<? endif; ?>/>