<? namespace Components; ?>
<? /* @var $self \Components\Ui_Panel_Upload_File */ ?>
<? if($self->multiFileUpload): ?>
  <? $self->printErrors(); ?>
  <? $count=$self->count(); ?>
  <div class="ui_panel_disclosure_header">
    <h3 class="title">Files<? if(0<$count): ?> (<?= $count; ?>)<? endif; ?></h3>
    <a href="javascript:void(0);" rel="<?= $self->id(); ?>-files" class="ui_panel_disclosure_toggle expanded">collapse</a>
  </div>
  <span class="clear"></span>
<? endif; ?>
<div id="<?= $self->id(); ?>-files" class="ui_panel_upload_file content">
  <? if($self->multiFileUpload): ?>
      <? $i=0; ?>
      <? if(0<$count): ?>
        <? if(12<$count): ?>
          <? $sizeIcon=Io_Mimetype::ICON_SIZE_16; ?>
        <? elseif(6<$count): ?>
          <? $sizeIcon=Io_Mimetype::ICON_SIZE_32; ?>
        <? else: ?>
          <? $sizeIcon=Io_Mimetype::ICON_SIZE_64; ?>
        <? endif; ?>
        <ul class="files_<?= $sizeIcon; ?>">
          <? foreach($self->files() as $subPath=>$file): ?>
            <li class="<? if(0===($i++%2)): ?>odd<? else: ?>even<? endif; ?>">
              <? $self->printFile($file, $subPath, $sizeIcon); ?>
            </li>
          <? endforeach; ?>
          <li class="clear"></li>
        </ul>
    <? endif; ?>
  <? endif; ?>
  <div id="<?= $self->id(); ?>-progress" class="ui_panel_upload_file progress"><div><div>&nbsp;</div></div></div>
  <input type="file" id="<?= $self->id(); ?>-value" name="<?= $self->id(); ?>" class="ui_panel_upload_file upload"<? if($self->callback): ?> onchange="<?= $self->callback(); ?>"<? endif; ?>/>
</div>
