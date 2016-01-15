<? namespace Components; ?>
<? /* @var $self \Components\Ui_Panel_Disclosure */ ?>
<details id="<?= $self->id(); ?>" <?= $self->attributes(); ?>>
  <summary><?= \html\escape($self->title); ?></summary>
  <div class="ui_panel_disclosure details">
    <? foreach($self->panels() as $panel): ?>
      <? $panel->display(); ?>
    <? endforeach; ?>
  </div>
</details>
