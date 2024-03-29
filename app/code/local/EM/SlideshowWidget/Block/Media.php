<?php
class EM_SlideshowWidget_Block_Media  extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct($arguments=array())
    {
       parent::__construct($arguments);
	   $this->setTemplate('slideshowwidget/grid.phtml');
        $this->setUseAjax(true);
    }
    protected $_filesCollection;

    public function getFiles()
    {
		$path =Mage::getBaseDir('media').DS.'slideshow';
		if(file_exists($path))
		{
				if (! $this->_filesCollection) {
					$this->_filesCollection = Mage::getSingleton('cms/wysiwyg_images_storage')->getFilesCollection(Mage::getBaseDir('media') . DS . 'slideshow','image');
				}
		}
        return $this->_filesCollection;
    }
      public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
    	 $pagesGrid = $this->getLayout()->createBlock('slideshowwidget/media', '', array(
            'id' => $uniqId,
        ));
        $uniqId = Mage::helper('core')->uniqHash($element->getId());
        $sourceUrl = $this->getUrl('slideshowwidget/admin_chooser/chooser', array('uniq_id' => $uniqId));

        $chooser = $this->getLayout()->createBlock('slideshowwidget/widget')
            ->setElement($element)
            ->setTranslationHelper($this->getTranslationHelper())
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);
        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }   
    public function getRowClickCallback()
    {
        $chooserJsObject = $this->getId();
        $js = '
            function (grid, event) {
                var trElement = Event.findElement(event, "tr");
                var pageTitle = trElement.down("td").next().innerHTML;
                var pageId = trElement.down("td").innerHTML.replace(/^\s+|\s+$/g,"");
                '.$chooserJsObject.'.setElementValue(pageId);
                '.$chooserJsObject.'.setElementLabel(pageTitle);
                '.$chooserJsObject.'.close();
            }
        ';
        return $js;
    }
    
    protected function _prepareCollection()
    {
        $collection = $this->getFiles();
        
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }
    protected function _prepareColumns()
    {
        $this->addColumn('chooser_id', array(
            'header'    => Mage::helper('slideshowwidget')->__('File Base Name'),
            'align'     => 'left',
            'index'     => 'basename',
        ));

        $this->addColumn('chooser_title', array(
            'header'    => Mage::helper('slideshowwidget')->__('File Name'),
            'align'     => 'left',
            'index'     => 'filename',
        ));
    }
    public function getGridUrl()
    {
        return $this->getUrl('slideshowwidget/admin_chooser/chooser', array('_current' => true));
    }
    
    
}
?>