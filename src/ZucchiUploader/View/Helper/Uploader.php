<?php
namespace ZucchiUploader\View\Helper;

use Zend\View\Helper\AbstractHtmlElement;
use Zend\Form\Element\Button;
use ZucchiUploader\Options\UploaderOptions;

class Uploader extends AbstractHtmlElement
{
    protected $options;
    
    public function __invoke($url, $attribs = array(), $options = array())
    {
        $view = $this->getView();
        
        $config= $this->getOptions();
        if (!empty($options)) { 
            $config->setFromArray($options);
        }
        $config->setUrl($url);
        
        $view->inlineScript()->appendFile($config->getPath() . 'plupload.js');
        
        $runtimes = explode(',', $config->getRuntimes());
        foreach ($runtimes as $runtime) {
            $view->inlineScript()->appendFile($config->getPath() . 'plupload.' . $runtime . '.js');
        }
        
        $settings = json_encode(
             $config, 
             JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
         );
        
        $view->inlineScript()
             ->appendScript(
                 "var uploader = new plupload.Uploader(" . $settings .");" . PHP_EOL .
                 "uploader.init();" . PHP_EOL .
                 "uploader.bind('FilesAdded', function(up, files) { console.log('FileAdded');console.log(up);console.log(files); uploader.start(); });" . PHP_EOL .
                 "uploader.bind('Error', function(up, args){ console.log('Error');console.log(up);console.log(args); });" . PHP_EOL .
                 "uploader.bind('FileUploaded', function(up, file, info){ console.log('FileUploaded');console.log(up);console.log(file);console.log(info); });"
             );
        
        $html = '<div id="' . $config->getContainer() . '">';
        
        $button = new Button($config->getBrowse_button());
        $button->setLabel('Upload');
        $button->setAttribute('id', $config->getBrowse_button());
        $button->setAttributes($attribs);
        
        $html .= $view->formButton($button);
        
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * 
     * @param UploaderOptions $options
     * @return \ZucchiUploader\View\Helper\Uploader
     */
    public function setOptions(UploaderOptions $options)
    {
        $this->options = $options;
        return $this;
    }
    
    /**
     * 
     * @return UploaderOptions
     */
    public function getOptions()
    {
        return $this->options;
    }
}