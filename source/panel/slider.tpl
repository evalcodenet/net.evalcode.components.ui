<? namespace Components; ?>
<? /* @var $self \Components\Ui_Panel_Slider */ ?>
<? if($self->title): ?>
  <h3><?= \html\strip($self->title); ?></h3>
<? endif; ?>
<ul id="<?= $self->id(); ?>-pager" class="ui_panel_pager">
  <li class="prev"><a>Previous</a></li>
  <li class="next"><a>Next</a></li>
</ul>
<div class="viewport">
  <ul class="stage">
    <? foreach($self->panels() as $panel): ?>
      <li class="item">
        <? $panel->display(); ?>
      </li>
    <? endforeach; ?>
  </ul>
</div>
