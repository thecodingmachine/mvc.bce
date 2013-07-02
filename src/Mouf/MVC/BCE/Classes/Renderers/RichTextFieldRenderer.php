<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

use Mouf\MVC\BCE\Classes\ScriptManagers\ScriptManager;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;

/**
 * Base class for rendering simple text fields
 * @Component
 */
class RichTextFieldRenderer extends DefaultViewFieldRenderer implements SingleFieldRendererInterface {
	
	/**
	 * Custom configuration file, relative to the ROOT_URL
	 * @var string
	 */
	public $custom_configFile = "vendor/mouf/cms.cms-controller/src/js/ck_full_config.js";
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::render()
	 */
	public function renderEdit($descriptorInstance){
		/* @var $descriptorInstance FieldDescriptorInstance */
		$fieldName = $descriptorInstance->getFieldName();
		$value = $descriptorInstance->getFieldValue();
		$strReadonly = ! $descriptorInstance->fieldDescriptor->canEdit() ? "readonly='readonly'" : "";
		return "<textarea name='".$fieldName."' id='".$fieldName."' $strReadonly ".$descriptorInstance->printAttributes().">$value</textarea>";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::getJS()
	 */
	public function getJSEdit($descriptor, $bean, $id){
		/* @var $descriptor BCEFieldDescriptorInterface */
		$fieldName = $descriptor->getFieldName();
				
		return array(ScriptManager::SCOPE_READY => "CKEDITOR.replace( '$fieldName', {allowedContent: true, customConfig: '".ROOT_URL.$this->custom_configFile."'} );");
	}
	
}