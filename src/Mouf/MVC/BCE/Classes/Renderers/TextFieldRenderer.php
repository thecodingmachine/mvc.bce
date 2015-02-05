<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

use Mouf\Html\Utils\WebLibraryManager\WebLibraryManager;
use Mouf\Html\Widgets\Form\Styles\LayoutStyle;

use Mouf\Html\Tags\Style;

use Mouf\MVC\BCE\Classes\Descriptors\BCEFieldDescriptorInterface;
use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;
use Mouf\Html\Widgets\Form;
use Mouf\Html\Widgets\Form\TextField;
use Mouf\MVC\BCE\Classes\ValidationHandlers\BCEValidationUtils;
use Mouf\Html\Tags\Span;

/**
 * Base class for rendering simple text fields
 * @Component
 * @ApplyTo { "php" :[ "string", "int", "number"] }
 */
class TextFieldRenderer extends DefaultViewFieldRenderer implements SingleFieldRendererInterface {

	/**
	 * Boolean, true if the input is required, else false 
	 * @var bool
	 */
	protected $isRequired = false;
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::render()
	 */
	public function renderEdit($descriptorInstance){
		/* @var $descriptorInstance FieldDescriptorInstance */

		$textField = new TextField($descriptorInstance->fieldDescriptor->getFieldLabel(), $descriptorInstance->getFieldName(), $descriptorInstance->getFieldValue());
		if($descriptorInstance->getValidator()) {
			$textField->setInputClasses($descriptorInstance->getValidator());
		}
		
		$textField->getInput()->setId($descriptorInstance->getFieldName());
		$textField->getInput()->setReadonly((!$descriptorInstance->fieldDescriptor->canEdit()) ? "readonly" : null);
		if(isset($descriptorInstance->attributes['styles'])) {
			$textField->getInput()->setStyles($descriptorInstance->attributes['styles']);
		}

		$textField->setHelpText($descriptorInstance->fieldDescriptor->getDescription());
		
		$textField->setRequired(BCEValidationUtils::hasRequiredValidator($descriptorInstance->fieldDescriptor->getValidators()));
		
		if ($this->getLayout() == null){
			$textField->setLayout($descriptorInstance->form->getDefaultLayout());
		}
		
		ob_start();
		$textField->toHtml();
		return ob_get_clean();
		/*
		$fieldName = $descriptorInstance->getFieldName();
		$value = $descriptorInstance->getFieldValue();
		$strReadonly = ! $descriptorInstance->fieldDescriptor->canEdit() ? "readonly='readonly'" : "";
		
		$descriptorInstance->attributes['class'][] = 'form-control';
		
		return "<input type='text' value='".htmlspecialchars($value, ENT_QUOTES)."' name='".$fieldName."' id='".$fieldName."' $strReadonly ".$descriptorInstance->printAttributes()."/>";
		*/
	}
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::getJS()
	 */
	public function getJSEdit(BCEFieldDescriptorInterface $descriptor, $bean, $id, WebLibraryManager $webLibraryManager){
		/* @var $descriptorInstance FieldDescriptorInstance */
		return array();
	}
	
}