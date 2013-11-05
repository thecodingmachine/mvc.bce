<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;
use Mouf\Html\Widgets\Form\SelectField;
use Mouf\Html\Tags\Option;
use Mouf\MVC\BCE\Classes\ValidationHandlers\BCEValidationUtils;
use Mouf\Html\Widgets\Form\RadiosField;
use Mouf\Html\Widgets\Form\RadioField;


/**
 * A renderer class that ouputs a simple select box: it doesn't handle multiple selection
 * 
 * @Component
 * @ApplyTo {"type": ["fk"]}
 */
class SelectFieldRenderer extends DefaultViewFieldRenderer implements SingleFieldRendererInterface {
	
	/**
	 * Tells if the field should display a select box or a radio button group
	 * @Property
	 * @var bool
	 */
	public $radioMode = false;
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::render()
	 */
	public function renderEdit($descriptorInstance){
		/* @var $descriptorInstance FieldDescriptorInstance */
		$descriptor = $descriptorInstance->fieldDescriptor;
		$value = $descriptorInstance->getFieldValue();
		
		if (!$this->radioMode) {
			$selectField = new SelectField($descriptor->getFieldLabel(), $descriptorInstance->getFieldName(), $value);
			$selectField->getSelect()->setName($descriptorInstance->getFieldName());
			$selectField->getSelect()->setId($descriptorInstance->getFieldName());
			if($descriptorInstance->getValidator()) {
				$selectField->setSelectClasses($descriptorInstance->getValidator());
			}
			if(isset($descriptorInstance->attributes['styles'])) {
				$selectField->getSelect()->setStyles($descriptorInstance->attributes['styles']);
			}
			$selectField->getSelect()->setDisabled((!$descriptor->canEdit()) ? "disabled" : null);
			$selectField->setRequired(BCEValidationUtils::hasRequiredValidator($descriptor->getValidators()));
			
			$options = array();
			$data = $descriptor->getData();
			foreach ($data as $linkedBean) {
				$beanId = $descriptor->getRelatedBeanId($linkedBean);
				$beanLabel = $descriptor->getRelatedBeanLabel($linkedBean);
				$option = new Option();
				$option->setValue($beanId);
				$option->addText($beanLabel);
				if ($beanId == $value) {
					$option->setSelected('selected');
				}
				$options[] = $option;
			}
			$selectField->setOptions($options);

			ob_start();
			$selectField->toHtml();
			return ob_get_clean();
		}
		else {
			$radiosFields = new RadiosField($descriptor->getFieldLabel(), $descriptorInstance->getFieldName());
			$radiosFields->setRequired(BCEValidationUtils::hasRequiredValidator($descriptorInstance->fieldDescriptor->getValidators()));
			
			$radios = array();
			$data = $descriptor->getData();
			foreach ($data as $linkedBean) {
				$beanId = $descriptor->getRelatedBeanId($linkedBean);
				$beanLabel = $descriptor->getRelatedBeanLabel($linkedBean);
				$radioField = new RadioField($beanLabel, $descriptorInstance->getFieldName(), $beanId, ($beanId == $value)); 
				$radioField->getInput()->setReadonly((!$descriptor->canEdit()) ? "readonly" : null);
				if($descriptorInstance->getValidator()) {
					$radioField->setInputClasses($descriptorInstance->getValidator());
				}
				if(isset($descriptorInstance->attributes['styles'])) {
					$radioField->getInput()->setStyles($descriptorInstance->attributes['styles']);
				}
				
				$radios[] = $radioField;
			}
			$radiosFields->setRadios($radios);
			
			ob_start();
			$radiosFields->toHtml();
			return ob_get_clean();
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::getJS()
	 */
	public function getJSEdit($descriptor, $bean, $id){
		/* @var $descriptorInstance FieldDescriptorInstance */
		return array();
	}
	
}