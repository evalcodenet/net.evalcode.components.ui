<? namespace Components; ?>
<? /* @var $self \Components\Ui_Panel_Tabs */ ?>
<ul class="ui_panel_tabs labels<? if(false===$self->alwaysShowTabBar && 1>count($tabs=$self->panels())): ?> gone<? endif; ?>">
  <? foreach($tabs as $tab): ?>
    <? $idx=$self->index($tab); ?>
    <li class="ui_panel_tabs label idx_<?= $idx; ?> <? if(0===$idx%2): ?>even<? else: ?>odd<? endif; ?><? if($self->isActive($tab)): ?> active<? endif; ?>">
      <a rel="<?= $idx; ?>"><?= $tab->title; ?></a>
    </li>
  <? endforeach; ?>
  <li class="clear"></li>
</ul>
<ul class="ui_panel_tabs contents">
  <? foreach($tabs as $tab): ?>
    <? $idx=$self->index($tab); ?>
    <li id="<?= $self->id(); ?>-<?= $idx; ?>" class="ui_panel_tabs content idx_<?= $idx; ?> <? if(0===$idx%2): ?>even<? else: ?>odd<? endif; ?><? if($self->isActive($tab)): ?> active<? endif; ?>">
      <?= $tab->display(); ?>
    </li>
  <? endforeach; ?>
  <li class="clear"></li>
</ul>
<input type="hidden" id="<?= $self->id(); ?>-value" name="<?= $self->id(); ?>" value="<?= $self->value(); ?>"/>
