<?php
/**
 * Plugin for the template engine factory that makes a decision of whether to activate debugging hints or not
 */
namespace Yong\Magento2DebugBar\Framework\View\Model\Layout;

use Magento\Framework\View\Model\Layout\Merge as Base;
use Magento\Framework\Filesystem\File\ReadFactory;
use Yong\Magento2DebugBar\Block\Magento2Debugbar;

class Merge extends Base {
   private $updateFiles;
   private $theme;
   private $fileSource;
   private $pageLayoutFileSource;

public function __construct(
        \Magento\Framework\View\DesignInterface $design,
        \Magento\Framework\Url\ScopeResolverInterface $scopeResolver,
        \Magento\Framework\View\File\CollectorInterface $fileSource,
        \Magento\Framework\View\File\CollectorInterface $pageLayoutFileSource,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\Cache\FrontendInterface $cache,
        \Magento\Framework\View\Model\Layout\Update\Validator $validator,
        \Psr\Log\LoggerInterface $logger,
        ReadFactory $readFactory,
        \Magento\Framework\View\Design\ThemeInterface $theme = null,
        $cacheSuffix = ''
    ) {
        $this->theme = $theme ?: $design->getDesignTheme();
 	$this->fileSource = $fileSource;
	$this->pageLayoutFileSource = $pageLayoutFileSource;

	parent::__construct($design,
         $scopeResolver,
         $fileSource,
         $pageLayoutFileSource,
         $appState,
         $cache,
         $validator,
         $logger,
         $readFactory,
         $theme,
         $cacheSuffix);
    }

   public function addHandle1($handleName) {
	if (is_array($handleName)) {
            foreach ($handleName as $name) {
		$this->getLayoutFile($name);
            }
        } else {
		$this->getLayoutFile($handleName);
        }
	return parent::addHandle($handleName);
    }


    public function getHandles()
    {
        $result = parent::getHandles();
	foreach($result as $handleName) {
		$this->getLayoutFile($handleName);
	}
	return $result;
    }

    private function getLayoutUpdatesFile() {
	if (empty($this->updateFiles)) {
		$theme = $this->_getPhysicalTheme($this->theme);
		$this->updateFiles = $this->fileSource->getFiles($theme, '*.xml');
		$this->updateFiles = array_merge($this->updateFiles, $this->pageLayoutFileSource->getFiles($theme, '*.xml'));
	}
	return $this->updateFiles;
    }

    private function getLayoutFile($handlename) {
	$updateFiles = $this->getLayoutUpdatesFile();
	$files = [];
	foreach($updateFiles as $file) {
		$basename = basename($file->getFilename(), '.xml');
		if ($handlename == $basename) {
			$files[] = $file->getFilename();
		}
	}
	Magento2Debugbar::getInstance()->addLayout($handlename, $files);
    }
}
