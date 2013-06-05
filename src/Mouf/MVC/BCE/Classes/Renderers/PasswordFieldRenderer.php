<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;

/**
 * Base class for rendering simple text fields
 * @Component
 */
class PasswordFieldRenderer extends BaseFieldRenderer implements SingleFieldRendererInterface, ViewFieldRendererInterface {
	
	/**
	 * Autocomplete the field in the form.
	 * 
	 * @Property
	 * @var boolean
	 */
	public $autocomplete = false;
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::render()
	 */
	public function renderEdit($descriptorInstance){
		/* @var $descriptorInstance FieldDescriptorInstance */
		$fieldName = $descriptorInstance->getFieldName();
		$value = $descriptorInstance->getFieldValue();
		return "<input ".$descriptorInstance->printAttributes()." type='password' ".($this->autocomplete ? "autocomplete='on'" : "autocomplete='off'")." value='' name='".$fieldName."' id='".$fieldName."'/>";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::getJS()
	 */
	public function getJSEdit($descriptor, $bean, $id){
		/* @var $descriptorInstance FieldDescriptorInstance */
		return array();
	}
	
	public function renderView($descriptorInstance){
			/* @var $descriptorInstance FieldDescriptorInstance */
		return false;
	}
	
	public function getJSView($descriptor, $bean, $id){
			/* @var $descriptorInstance FieldDescriptorInstance */
		return array();
	}
	
}