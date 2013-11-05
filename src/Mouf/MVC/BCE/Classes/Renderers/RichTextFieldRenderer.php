<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

use Mouf\MVC\BCE\Classes\ScriptManagers\ScriptManager;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;
use Mouf\MVC\BCE\Classes\ValidationHandlers\BCEValidationUtils;
use Mouf\Html\Widgets\Form\TextAreaField;

/**
 * Base class for rendering simple text fields
 * @Component
 */
class RichTextFieldRenderer extends TextAreaFieldRenderer implements SingleFieldRendererInterface {
	
	/**
	 * Custom configuration file, relative to the ROOT_URL
	 * @var string
	 */
	public $custom_configFile = "vendor/mouf/cms.cms-controller/src/js/ck_full_config.js";
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::getJS()
	 */
	public function getJSEdit($descriptor, $bean, $id){
		/* @var $descriptor BCEFieldDescriptorInterface */
		$fieldName = $descriptor->getFieldName();
				
		return array(ScriptManager::SCOPE_READY => "CKEDITOR.replace( '$fieldName', {allowedContent: true, language: 'en', customConfig: '".ROOT_URL.$this->custom_configFile."'} );");
	}
	
}