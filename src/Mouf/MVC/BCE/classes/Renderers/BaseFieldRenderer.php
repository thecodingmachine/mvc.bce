<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptor;
use Mouf\MVC\BCE\Classes\BCEException;

abstract class BaseFieldRenderer implements FieldRendererInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\FieldRendererInterface::render()
	 */
	public function render($descriptor, $formMode){
		/* @var $descriptor FieldDescriptor */
		if ($formMode == 'edit'){
			if ($this instanceof EditFieldRendererInterface){
				return $this->renderEdit($descriptor);
			}else{
				throw new BCEException("Descriptor for field '$descriptor->getFieldName()' does not implement the 'edit' rendering mode.");
			}
		}else if ($formMode == 'view'){
			if ($this instanceof ViewFieldRendererInterface){
				return $this->renderView($descriptor);
			}else{
				throw new BCEException("Descriptor for field '$descriptor->getFieldName()' does not implement the 'view' rendering mode.");
			}
		}else{
			throw new BCEException("Form mode '$formMode' is not valid");
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\FieldRendererInterface::getJS()
	 */
	public function getJS($descriptor, $formMode){
	/* @var $descriptor FieldDescriptor */
		if ($formMode == 'edit'){
			if ($this instanceof EditFieldRendererInterface){
				return $this->getJSEdit($descriptor);
			}else{
				throw new BCEException("Descriptor for field '$descriptor->getFieldName()' does not implement the 'edit' rendering mode.");
			}
		}else if ($formMode == 'view'){
			if ($this instanceof ViewFieldRendererInterface){
				return $this->getJSView($descriptor);
			}else{
				throw new BCEException("Descriptor for field '$descriptor->getFieldName()' does not implement the 'view' rendering mode.");
			}
		}else{
			throw new BCEException("Form mode '$formMode' is not valid");
		}
	}
}	