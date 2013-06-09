<? namespace Components; ?>
<? $this->printErrors(); ?>
<? $countFiles=$this->countFiles(); ?>
<div class="ui_panel_upload_file_files">
  <? $this->display('key'); ?>
  <div class="ui_panel_disclosure_header">
    <h3 class="title">Files<? if(0<$countFiles): ?> (<?= $countFiles; ?>)<? endif; ?></h3>
    <a href="javascript:void(0);" rel="<?= $this->id; ?>-files" class="ui_panel_disclosure_toggle expanded">collapse</a>
  </div>
  <span class="clear"></span>
  <div id="<?= $this->id; ?>-files">
    <? $i=0; ?>
    <? if(0<$countFiles): ?>
      <? if(12<$countFiles): ?>
        <? $sizeIcon=Io_Mimetype::ICON_SIZE_16; ?>
      <? elseif(6<$countFiles): ?>
        <? $sizeIcon=Io_Mimetype::ICON_SIZE_32; ?>
      <? else: ?>
        <? $sizeIcon=Io_Mimetype::ICON_SIZE_64; ?>
      <? endif; ?>
      <ul class="files_<?= $sizeIcon; ?>">
        <? foreach($this->getFiles() as $subPath=>$file): ?>
          <li class="<? if(0===($i++%2)): ?>odd<? else: ?>even<? endif; ?>">
            <? $this->printFile($file, $subPath, $sizeIcon); ?>
          </li>
        <? endforeach; ?>
        <li class="clear"></li>
      </ul>
    <? endif; ?>
    <div id="<?= $this->id; ?>-progress" class="ui_panel_upload_file_progress"><div><div>&nbsp;</div></div></div>
    <input type="file" id="<?= $this->id; ?>-value" name="<?= $this->id; ?>" class="ui_panel_upload_file_chooser"<? if($this->hasCallbackJs()): ?> onchange="<?= $this->callbackJs(); ?>"<? elseif($this->hasCallbackAjax()): ?> onchange="<?= $this->callbackAjax(); ?>"<? endif; ?>/>
  </div>
</div>
