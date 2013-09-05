<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;
use Mouf\MVC\BCE\Classes\Descriptors\BaseFieldDescriptor;
/**
 * Base class for rendering simple text area fields
 * @Component
 */
class TextAreaFieldRenderer extends BaseFieldRenderer implements SingleFieldRendererInterface, ViewFieldRendererInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::render()
	 */
	public function renderEdit($descriptorInstance){
		/* @var $descriptorInstance FieldDescriptorInstance */
		$fieldName = $descriptorInstance->getFieldName();
		$value = $descriptorInstance->getFieldValue();
		return '<textarea '.$descriptorInstance->printAttributes().' name="'.$fieldName.'" id="'.$fieldName.'">'.htmlspecialchars($value, ENT_QUOTES).'</textarea>';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::getJS()
	 */
	public function getJSEdit($descriptor, $bean, $id){
		/* @var $descriptorInstance FieldDescriptorInstance */
		return array();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\ViewFieldRendererInterface::renderView()
	 */
	public function renderView($descriptorInstance){
				/* @var $descriptorInstance FieldDescriptorInstance */
		$fieldName = $descriptorInstance->getFieldName();
		return "<div id='".$fieldName."' name='".$fieldName."'>". $descriptor->getFieldValue() ."</div>";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\ViewFieldRendererInterface::getJSView()
	 */
	public function getJSView($descriptor, $bean, $id){
		/* @var $descriptorInstance FieldDescriptorInstance */
		return array();
	}
	
}