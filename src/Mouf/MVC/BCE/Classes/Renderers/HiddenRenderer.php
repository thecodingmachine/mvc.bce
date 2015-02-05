<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

use Mouf\Html\Utils\WebLibraryManager\WebLibraryManager;
use Mouf\MVC\BCE\Classes\Descriptors\BCEFieldDescriptorInterface;
use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;

use Mouf\MVC\BCE\Classes\Descriptors\BaseFieldDescriptor;

/**
 * This renderer handles hidden input fields
 * @ApplyTo { "pk" : [ "pk" ] }
 * @Component
 */
class HiddenRenderer extends BaseFieldRenderer implements SingleFieldRendererInterface, ViewFieldRendererInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::render()
	 */
	public function renderEdit($descriptorInstance){
		/* @var $descriptorInstance FieldDescriptorInstance */
		$fieldName = $descriptorInstance->getFieldName();
		$value = $descriptorInstance->getFieldValue();
		return "<input class='".implode(' ', $descriptorInstance->getValidator())."' type='hidden' value='".htmlspecialchars($value, ENT_QUOTES)."' name='".$fieldName."' id='".$fieldName."'/>";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::getJS()
	 */
	public function getJSEdit(BCEFieldDescriptorInterface $descriptor, $bean, $id, WebLibraryManager $webLibraryManager){
		/* @var $descriptorInstance FieldDescriptorInstance */
		return array();
	}

	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\ViewFieldRendererInterface::renderView()
	 */
	public function renderView($descriptorInstance){
		/* @var $descriptorInstance FieldDescriptorInstance */
		return $this->renderEdit($descriptorInstance);
	}

	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\ViewFieldRendererInterface::getJSView()
	 */
	public function getJSView(BCEFieldDescriptorInterface $descriptor, $bean, $id, WebLibraryManager $webLibraryManager){
		/* @var $descriptorInstance FieldDescriptorInstance */
		return $this->getJSEdit($descriptor, $bean, $id, $webLibraryManager);
	}

}