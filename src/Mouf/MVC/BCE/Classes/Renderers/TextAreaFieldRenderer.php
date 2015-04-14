<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

use Mouf\Html\Utils\WebLibraryManager\WebLibraryManager;
use Mouf\MVC\BCE\Classes\Descriptors\BCEFieldDescriptorInterface;
use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;
use Mouf\MVC\BCE\Classes\Descriptors\BaseFieldDescriptor;
use Mouf\Html\Widgets\Form\TextAreaField;
use Mouf\MVC\BCE\Classes\ValidationHandlers\BCEValidationUtils;
/**
 * Base class for rendering simple text area fields
 * @Component
 */
class TextAreaFieldRenderer extends BaseFieldRenderer implements SingleFieldRendererInterface, ViewFieldRendererInterface {

    /**
     * @var int $rows
     */
    private $rows;

	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::render()
	 */
	public function renderEdit($descriptorInstance){
		/* @var $descriptorInstance FieldDescriptorInstance */
		$fieldName = $descriptorInstance->getFieldName();
		$value = $descriptorInstance->getFieldValue();

		$textareaField = new TextAreaField($descriptorInstance->fieldDescriptor->getFieldLabel(), $descriptorInstance->getFieldName(), $descriptorInstance->getFieldValue());
		if($descriptorInstance->getValidator()) {
			$textareaField->setTextareaClasses($descriptorInstance->getValidator());
		}
		
		$textareaField->getTextarea()->setId($descriptorInstance->getFieldName());
		$textareaField->getTextarea()->setReadonly((!$descriptorInstance->fieldDescriptor->canEdit()) ? "readonly" : null);
		if(isset($descriptorInstance->attributes['styles'])) {
			$textareaField->getTextarea()->setStyles($descriptorInstance->attributes['styles']);
		}

        $textareaField->getTextarea()->setRows($this->rows);
		
		$textareaField->setHelpText($descriptorInstance->fieldDescriptor->getDescription());
		
		$textareaField->setRequired(BCEValidationUtils::hasRequiredValidator($descriptorInstance->fieldDescriptor->getValidators()));
		
		if ($textareaField->getLayout() == null){
			$textareaField->setLayout($descriptorInstance->form->getDefaultLayout());
		}
		
		ob_start();
		$textareaField->toHtml();
		return ob_get_clean();
/*
		$descriptorInstance->attributes['class'][] = 'form-control';
		return '<textarea '.$descriptorInstance->printAttributes().' name="'.$fieldName.'" id="'.$fieldName.'">'.htmlspecialchars($value, ENT_QUOTES).'</textarea>';
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
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\ViewFieldRendererInterface::renderView()
	 */
	public function renderView($descriptorInstance){
				/* @var $descriptorInstance FieldDescriptorInstance */
		$fieldName = $descriptorInstance->getFieldName();
		return "<div id='".$fieldName."' name='".$fieldName."'>". $descriptorInstance->getFieldValue() ."</div>";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\ViewFieldRendererInterface::getJSView()
	 */
	public function getJSView(BCEFieldDescriptorInterface $descriptor, $bean, $id, WebLibraryManager $webLibraryManager){
		/* @var $descriptorInstance FieldDescriptorInstance */
		return array();
	}

    /**
     * @param int $rows
     */
    public function setRows($rows){
        $this->rows = $rows;
    }
}