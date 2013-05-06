<?php


namespace Components;


  /**
   * Ui_Panel_Upload_File
   *
   * @package net.evalcode.components
   * @subpackage ui.panel.upload
   *
   * @author evalcode.net
   *
   * TODO Refactor to use Io_Path, Io_File..
   */
  class Ui_Panel_Upload_File extends Ui_Panel
  {
    // PROPERTIES
    public static $implArchives=array(
      'application/zip'=>'\ZipArchive'
    );

    public $fileExtensionsAllowed=array();
    public $fileExtensionsForbidden=array('php', 'php3', 'php4', 'php5', 'phtml', 'phps', 'js', 'css');

    public $mimeTypesAllowed=array();
    public $mimeTypesForbidden=array('application/x-php', 'application/x-php-source');

    /**
     * Extract archives and treat contained files
     * as if they were uploaded one by one.
     */
    public $extractArchives=false;
    //--------------------------------------------------------------------------


    // ACCESSORS
    public function countFiles()
    {
      return count($this->m_files);
    }

    public function getFiles()
    {
      return $this->m_files;
    }

    public function getFileRemoved()
    {
      return $this->getRequestParam('remove');
    }

    public function addFileActionJs($actionName_, $actionTitle_, array $applyActionCallback_)
    {
      $this->m_fileActionsJs[$actionName_]=array('title'=>$actionTitle_, 'callback'=>$applyActionCallback_);
    }

    public function getUploadSessionId()
    {
      return $this->key->getValue();
    }
    //--------------------------------------------------------------------------


    // INITIALIZATION
    protected function init()
    {
      parent::init();

      $this->addStylesheet('ui/upload/file');
      $this->addStylesheet('/io/resource/css/mimetype.css');

      $this->addScript('ui/upload/file');

      $this->setTemplate(__DIR__.'/file.tpl');

      $this->add(Ui_Panel_Input_Type::HIDDEN()->create('key', md5(uniqid(null, true))));
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private $m_files=array();
    private $m_fileActionsJs=array();
    //-----


    protected function initTemplateEngine(Ui_Template $engine_)
    {
      parent::initTemplateEngine($engine_);

      $engine_->countFiles=array($this, 'countFiles');
      $engine_->getFiles=array($this, 'getFiles');

      $engine_->printFile=array($this, 'printFile');
    }

    protected function onRetrieveValue()
    {
      parent::onRetrieveValue();

      if(false===$this->hasRequestParam('key'))
        return;

      $uploadSessionId=$this->getRequestParam('key');
      $uploadPathTmp=self::getTemporaryUploadPath($uploadSessionId);

      if($uploadPathTmp->isDirectory())
        $this->scanTemporaryUploadPath($uploadPathTmp);

      $uploadPath=self::getUploadPath($uploadSessionId);

      $this->m_files=array();
      $this->scanUploadPath($uploadPath, '', $this->m_files);

      if(($remove=$this->getRequestParam('remove')) && isset($this->m_files[$remove]))
      {
        $this->m_files[$remove]->delete();

        unset($this->m_files[$remove]);
      }
    }

    private function scanUploadPath(Io_Path $path_, $subPath_='', array &$files_)
    {
      if(false===String::isEmpty($subPath_)
        && false===String::endsWith($subPath_, '/'))
        $subPath_.='/';

      foreach($path_ as $entry)
      {
        if($entry->isDot())
          continue;

        if($entry->isFile())
        {
          $file=$entry->asFile();
          $files_[$subPath_.$file->getName()]=$file;
        }
        else if($entry->isDirectory())
        {
          $this->scanUploadPath($entry, $subPath_.$entry->getName(), $files_);
        }
      }
    }

    private function scanTemporaryUploadPath(Io_Path $path_, $subPath_='')
    {
      if(false===String::isEmpty($subPath_)
        && false===String::endsWith($subPath_, '/'))
        $subPath_.='/';

      foreach($path_ as $entry)
      {
        if($entry->isDot())
          continue;

        if($entry->isFile())
          $this->stashFile($entry->asFile(), $subPath_);
        else if($entry->isDirectory())
          $this->scanTemporaryUploadPath($entry, $subPath_.$entry->getName());
      }

      $path_->delete(true);
    }

    private function stashFile(Io_File $file_, $subPath_=null)
    {
      $mimeType=$file_->getMimeType();

      if(!$this->isValidMimeType($mimeType) || !$this->isValidFileExtension($file_->getExtension()))
      {
        $this->addError(translatef('ui/panel/upload/file/error/illegal_mimetype', $mimeType->title(), $file_->getName()));
        $file_->delete();

        return;
      }

      if($this->extractArchives && $mimeType->isArchive())
      {
        if(false===isset(self::$implArchives[$mimeType->name()]))
        {
          $this->addError(translatef('ui/panel/upload/file/error/unsupported_archive', $mimeType->name()));
          $file_->delete();

          return;
        }

        $archiveId=md5($file_->getPathAsString());
        $archiveImpl=self::$implArchives[$mimeType->name()];
        $archivePath=self::getUploadPath($this->getRequestParam('key'), $archiveId);

        // TODO Io_File_Archive, Io_File_Archive_Zip ...
        $archive=new $archiveImpl();
        $archive->open($file_->getPathAsString());
        $archive->extractTo($archivePath);

        $this->scanTemporaryUploadPath(Io::path($archivePath), $subPath_);

        $archive->close();
        $file_->delete();

        return;
      }

      $file=$file_->move(self::getUploadPath($this->getRequestParam('key'), $subPath_)->getFile($file_->getName()));
      $this->m_files[$subPath_.$file->getName()]=$file;
    }

    private function isValidMimeType(Io_MimeType $mimeType_)
    {
      if(in_array($mimeType_->name(), $this->mimeTypesForbidden))
        return false;

      return in_array($mimeType_->name(), $this->mimeTypesAllowed);
    }

    private function isValidFileExtension($fileExtension_)
    {
      if(in_array($fileExtension_, $this->fileExtensionsForbidden))
        return false;

      return in_array($fileExtension_, $this->fileExtensionsAllowed);
    }


    // TEMPLATE METHODS
    /*private*/ function printFile(Io_File $file_, $subPath_, $mimeTypeIconSize_=Io_MimeType::ICON_SIZE_64)
    {
      $actions=array();
      foreach($this->m_fileActionsJs as $name=>$action)
      {
        if(false===($callback=call_user_func_array($action['callback'], array($file_))))
          continue;

        $function=array_shift($callback);
        $actions[]=sprintf('<a href="javascript:void(0);" onclick="%3$s(%4$s);" class="%1$s">%2$s</a>',
          $name,
          $action['title'],
          $function,
          strtr(json_encode(array_merge(array($subPath_), $callback)), '"', "'")
        );
      }

      $actionRemove=sprintf('<a href="javascript:void(0);" onclick="%3$s" class="%1$s">%2$s</a>',
        'remove',
        'Remove',
        $this->callbackAjax(array('remove'=>$subPath_))
      );

      echo '<div class="file">';

      if($mimeTypeIconSize_>Io_MimeType::ICON_SIZE_16)
      {
        printf('
          <div class="icon">
            <img src="%1$s" alt="%2$s" title="%2$s"/>
          </div>
          <div class="info">
            <a href="javascript:void(0);" class="label">%2$s</a>',
            $file_->getMimeType()->icon($mimeTypeIconSize_),
            $subPath_
        );

        array_unshift($actions, $actionRemove);
      }
      else
      {
        printf('
          <div class="info">
            <img src="%1$s" alt="%2$s" title="%2$s"/>
            <a href="javascript:void(0);" class="label">%2$s</a>',
            $file_->getMimeType()->icon($mimeTypeIconSize_),
            $subPath_
        );

        array_push($actions, $actionRemove);
      }

      printf('
            <span class="type">%1$s</span>
            <span class="size">%2$s</span>
            <span class="links">%3$s</span>
          </div>
          <br class="clear"/>
        </div>',
          ucfirst($file_->getMimeType()->title()),
          $file_->getSize()->formatted(2),
          implode(' | ', $actions)
      );
    }


    // HELPERS
    /**
     * @param string $uploadSessionId_
     *
     * @return Io_Path
     */
    private static function getTemporaryUploadPath($uploadSessionId_)
    {
      $path=Io::tmpPath("$uploadSessionId_/upload", true);

      if(false===$path->exists())
        $path->create();

      return $path;
    }

    /**
     * @param string $uploadSessionId_
     * @param string $directory_
     * @param boolean $create_
     *
     * @return Io_Path
     */
    private static function getUploadPath($uploadSessionId_, $directory_=null, $create_=true)
    {
      if(null===$directory_)
        $path=Io::tmpPath($uploadSessionId_, true);
      else
        $path=Io::tmpPath("$uploadSessionId_/$directory_", true);

      if($create_)
        $path->create();

      return $path;
    }

    /**
     * @param string $uploadSessionId_
     * @param string $filename_
     *
     * @return Io_File
     */
    private static function getUploadedFile($uploadSessionId_, $filename_)
    {
      $segments=explode('/', $filename_);
      $filename=array_pop($segments);
      $directory=implode('/', $segments);

      return self::getUploadPath($uploadSessionId_, $directory)->getFile($filename);
    }

    /**
     * @param string $uploadSessionId_
     *
     * @return boolean
     */
    private static function removeUploadPath($uploadSessionId_, $directory_=null)
    {
      $path=self::getUploadPath($uploadSessionId_, $directory_);

      if($path->exists())
        return $path->delete(true);

      return true;
    }


    // STATTC AJAX CALLBACKS
    /*private*/ static function upload()
    {
      $uploadSessionId=$_REQUEST[Ui_Panel::getSubmittedPanelId().'-key'];

      $failed=array();
      foreach($_FILES as $file)
      {
        $target=self::getTemporaryUploadPath($uploadSessionId)->getFile($file['name']);
        if(false===move_uploaded_file($file['tmp_name'], $target->getPathAsString()))
          $failed[$file['name']]=$file['error'];
      }

      return $failed;
    }

    /*private*/ static function status()
    {
      return apc_fetch('upload_'.$_REQUEST[Ui_Panel::getSubmittedPanelId().'-file']);
    }

    /*private*/ static function cleanup($uploadSessionId_)
    {
      return self::removeUploadPath($uploadSessionId_);
    }
    //--------------------------------------------------------------------------
  }
?>
