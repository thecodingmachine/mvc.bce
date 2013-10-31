<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;
use Mouf\Html\Widgets\Form\SelectField;
use Mouf\Html\Tags\Option;
use Mouf\MVC\BCE\Classes\ValidationHandlers\BCEValidationUtils;
use Mouf\Html\Widgets\Form\RadiosField;


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
			if(isset($descriptorInstance->attributes['classes'])) {
				$selectField->setSelectClasses($descriptorInstance->attributes['classes']);
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
				$option->setLabel($beanLabel);
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
			$radiosFields = new RadiosField($descriptor->getFieldLabel(), $descriptorInstance->getFieldName(), $value);
			
			foreach ($data as $linkedBean) {
				$beanId = $descriptor->getRelatedBeanId($linkedBean);
				$beanLabel = $descriptor->getRelatedBeanLabel($linkedBean);
				
				$checkedStr = ($beanId == $value) ? "checked='checked'" : "";
			
			
				$html.="
				<label class='radio inline' for='$fieldName"."-"."$beanId'>
				<input type='radio' value='$beanId' $checkedStr id='$fieldName"."-"."$beanId' name='$fieldName' $readonlyStr> $beanLabel
				</label>
				";
			}
		}
		/*
		$descriptor = $descriptorInstance->fieldDescriptor;
		/* @var $descriptor ForeignKeyFieldDescriptor 
		$fieldName = $descriptorInstance->getFieldName();
		$data = $descriptor->getData();
		$value = $descriptorInstance->getFieldValue();
		$html = "";
		
		$readonlyStr = $descriptorInstance->fieldDescriptor->canEdit() ? "" : "disabled='disabled'";
		
		if (!$this->radioMode){
			$html = "<select  class='form-control' ".$descriptorInstance->printAttributes()." name='$fieldName' id='$fieldName' $readonlyStr>";
			foreach ($data as $linkedBean) {
				$beanId = $descriptor->getRelatedBeanId($linkedBean);
				$beanLabel = $descriptor->getRelatedBeanLabel($linkedBean);
				if ($beanId == $value) $selectStr = "selected = 'selected'";
				else $selectStr = "";
				$html .= "<option value='$beanId' $selectStr>$beanLabel</option>";
			}
			$html .= "</select>";
		}else{
			foreach ($data as $linkedBean) {
				$beanId = $descriptor->getRelatedBeanId($linkedBean);
				$beanLabel = $descriptor->getRelatedBeanLabel($linkedBean);
				$checkedStr = ($beanId == $value) ? "checked='checked'" : "";
				
				
				$html.="
					<label class='radio inline' for='$fieldName"."-"."$beanId'>
						<input type='radio' value='$beanId' $checkedStr id='$fieldName"."-"."$beanId' name='$fieldName' $readonlyStr> $beanLabel
					</label>
				";
			}
		}
		*/
		/*
		 * 
		$textField = new TextField($descriptorInstance->fieldDescriptor->getFieldLabel(), $descriptorInstance->getFieldName(), $descriptorInstance->getFieldValue());
		if(isset($descriptorInstance->attributes['classes'])) {
			$textField->setInputClasses($descriptorInstance->attributes['classes']);
		}

		$textField->getInput()->setType('text');
		
		$textField->getInput()->setId($descriptorInstance->getFieldName());
		$textField->getInput()->setReadonly((!$descriptorInstance->fieldDescriptor->canEdit()) ? "readonly" : null);
		if(isset($descriptorInstance->attributes['styles'])) {
			$textField->getInput()->setStyles($descriptorInstance->attributes['styles']);
		}

		$textField->setHelpText($descriptorInstance->fieldDescriptor->getDescription());
		
		$textField->setRequired(BCEValidationUtils::hasRequiredValidator($descriptorInstance->fieldDescriptor->getValidators()));
		
		ob_start();
		$textField->toHtml();
		return ob_get_clean();
		 */
		return $html;
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