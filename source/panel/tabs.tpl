<? if($this->self->alwaysShowTabBar || 1<count($tabs=$this->panels)): ?>
  <ul class="ui_panel_tab_labels">
    <? $i=0; ?>
    <? foreach($tabs as $tab): ?>
      <? $idx=$this->tabIndex($tab); ?>
      <li id="<?= $this->id; ?>-label-<?= $idx; ?>" class="ui_panel_tab_label ui_panel_tab_label_<?= $idx; ?> ui_panel_tab_label_<? if(0===$i%2): ?>even<? else: ?>odd<? endif; ?><? if($this->isActiveTab($tab)): ?> active<? endif; ?>">
        <a href="javascript:void(0);" onclick="ui_panel_tab_activate('<?= $this->id; ?>', <?= $idx; ?>, <? if($this->hasCallbackJs()): ?><?= strtr(json_encode($this->self->getCallbackJs()), '"', "'"); ?><? else: ?>null<? endif; ?>);"><?= $tab->getTitle(); ?></a>
      </li>
      <? $i++; ?>
    <? endforeach; ?>
    <li class="clear"></li>
  </ul>
<? endif; ?>
<ul class="ui_panel_tab_contents">
  <? $j=0; ?>
  <? foreach($tabs as $tab): ?>
    <? $idx=$this->tabIndex($tab); ?>
    <li id="<?= $this->id; ?>-content-<?= $idx; ?>" class="ui_panel_tab_content ui_panel_tab_content_<?= $idx; ?> ui_panel_tab_content_<? if(0===$j%2): ?>even<? else: ?>odd<? endif; ?><? if($this->isActiveTab($tab)): ?> active<? endif; ?>">
      <?= $tab->display(); ?>
    </li>
    <? $j++; ?>
  <? endforeach; ?>
  <li class="clear"></li>
</ul>
<input type="hidden" value="<?= (null===($value=$this->value()))?0:$value; ?>" name="<?= $this->id; ?>" id="<?= $this->id; ?>-value"/>
