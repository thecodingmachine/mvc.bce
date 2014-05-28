<?php
namespace Mouf\MVC\BCE\Classes\Renderers;
use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;
use Mouf\MVC\BCE\Classes\Descriptors\Many2ManyFieldDescriptor;
use Mouf\Html\Widgets\Form\CheckboxesField;
use Mouf\Html\Widgets\Form\CheckboxField;
use Mouf\Html\Widgets\Form\SelectMultipleField;
use Mouf\Html\Tags\Option;
use Mouf\MVC\BCE\Classes\ValidationHandlers\BCEValidationUtils;
use Mouf\Html\Widgets\Form\RadiosField;
use Mouf\Html\Widgets\Form\RadioField;
/**
 * A renderer class that ouputs multiple values field like checkboxes , multiselect list, ... fits for many to many relations
 */
class MultipleSelectFieldRenderer extends BaseFieldRenderer implements MultiFieldRendererInterface, ViewFieldRendererInterface {
	
	/**
	 * Tells if the field should display 
	 * <ul>
	 * 	<li>a set of checkboxes ("chbx"),</li> 
	 *  <li>a multiselect list ("multiselect"),</li>
	 *  <li>a multiselect widget (TODO),</li>
	 *  <li>maybe a sortable dnd list (TODO)</li>
	 *  <li>radio ("radio")</li>
	 *  </ul>
	 * @var string
	 */
	public $mode = 'checkbox';
	
	/**
	 * 
	 * @var bool
	 */
	private $defaultTradMode = false;
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\EditFieldRendererInterface::renderEdit()
	 */
	public function renderEdit($descriptorInstance){
		/* @var $descriptorInstance FieldDescriptorInstance */
		$descriptor = $descriptorInstance->fieldDescriptor;
		/* @var $descriptor Many2ManyFieldDescriptor */
		$fieldName = $descriptorInstance->getFieldName();
		$values = $descriptorInstance->getFieldValue();
		$html = "";
		$data = $descriptor->getData();
		$selectIds = array();
		if ($values){
			foreach ($values as $bean) {
				$id = $descriptor->getMappingRightKey($bean);
				$selectIds[] = $id;
			}
		}
		switch ($this->mode) {
			case 'multiselect':
				$selectMultipleField = new SelectMultipleField($descriptor->getFieldLabel(), $fieldName);
				if($descriptorInstance->getValidator()) {
					$selectMultipleField->setSelectClasses($descriptorInstance->getValidator());
				}
				if(isset($descriptorInstance->attributes['styles'])) {
					$selectMultipleField->getSelect()->setStyles($descriptorInstance->attributes['styles']);
				}
				$options = array();
				foreach ($data as $bean) {
					$beanId = $descriptor->getRelatedBeanId($bean);
					$beanLabel = $descriptor->getRelatedBeanLabel($bean);
					
					$option = new Option();
					$option->setValue($beanId);
					$option->addText($beanLabel);
					if (array_search($beanId, $selectIds) !== false) {
						$option->setSelected('selected');
					}
					$options[] = $option;
				}
				$selectMultipleField->setOptions($options);

				ob_start();
				$selectMultipleField->toHtml();
				return ob_get_clean();
				/*
				$html = "<select ".$descriptorInstance->printAttributes()." name='$fieldName' id='$fieldName' multiple='multiple'>";
				foreach ($data as $bean) {
					$beanId = $descriptor->getRelatedBeanId($bean);
					$beanLabel = $descriptor->getRelatedBeanLabel($bean);
					//TODO here :: change to check array search and select subset
					if (array_search($beanId, $selectIds)!==false) $selectStr = "selected = 'selected'";
					else $selectStr = "";
					$html .= "<option value='$beanId' $selectStr>$beanLabel</option>";
				}
				$html .= "</select>";
				*/
			break;
			
			case 'chbx':
				$checkboxesField = new CheckboxesField($descriptor->getFieldLabel(), $fieldName);
				if($descriptorInstance->getValidator()) {
					$checkboxesField->setRequired(BCEValidationUtils::hasRequiredValidator($descriptorInstance->fieldDescriptor->getValidators()));
				}
				$checkboxes = array();
				foreach ($data as $bean) {
					$beanId = $descriptor->getRelatedBeanId($bean);
					$beanLabel = $descriptor->getRelatedBeanLabel($bean);
					if($this->defaultTradMode == true){
						$beanLabel = iMsg($beanLabel);
					}
					
					$checkboxField = new CheckboxField($beanLabel, null, $beanId);
					$checkboxField->getInput()->setId($fieldName.'-'.$beanId);
					$checkboxField->setChecked((array_search($beanId, $selectIds)!==false));
					if($descriptorInstance->getValidator()) {
						$checkboxField->setInputClasses($descriptorInstance->getValidator());
					}
					if(isset($descriptorInstance->attributes['styles'])) {
						$checkboxField->getInput()->setStyles($descriptorInstance->attributes['styles']);
					}
					
					$checkboxes[] = $checkboxField;
					/*
					$checked = (array_search($beanId, $selectIds)!==false) ? "checked='checked'" : "";
					$html .= "
					<label class='checkbox inline' for='$fieldName"."-"."$beanId'>
						<input type='checkbox' $checked value='$beanId' id='$fieldName"."-"."$beanId' name='".$fieldName."[]' ".$descriptorInstance->printAttributes().">
						$beanLabel
					</label>";
					*/
				}
				$checkboxesField->setCheckboxes($checkboxes);
				
				if ($this->getLayout() == null){
					$checkboxesField->setLayout($descriptorInstance->form->getDefaultLayout());
				}

				ob_start();
				$checkboxesField->toHtml();
				return ob_get_clean();
			break;

			case 'radio':
				$radiosField = new RadiosField($descriptor->getFieldLabel(), $fieldName);
				if($descriptorInstance->getValidator()) {
					$checkboxesField->setRequired(BCEValidationUtils::hasRequiredValidator($descriptorInstance->fieldDescriptor->getValidators()));
				}
				$radios = array();
				foreach ($data as $bean) {
					$beanId = $descriptor->getRelatedBeanId($bean);
					$beanLabel = $descriptor->getRelatedBeanLabel($bean);
					if($this->defaultTradMode == true){
						$beanLabel = iMsg($beanLabel);
					}
						
					$radioField = new RadioField($beanLabel, null, $beanId);
					$radioField->getInput()->setId($fieldName.'-'.$beanId);
					$radioField->setChecked((array_search($beanId, $selectIds)!==false));
					if($descriptorInstance->getValidator()) {
						$radioField->setInputClasses($descriptorInstance->getValidator());
					}
					if(isset($descriptorInstance->attributes['styles'])) {
						$radioField->getInput()->setStyles($descriptorInstance->attributes['styles']);
					}
						
					$radios[] = $radioField;
					/*
					 $checked = (array_search($beanId, $selectIds)!==false) ? "checked='checked'" : "";
					$html .= "
					<label class='checkbox inline' for='$fieldName"."-"."$beanId'>
					<input type='checkbox' $checked value='$beanId' id='$fieldName"."-"."$beanId' name='".$fieldName."[]' ".$descriptorInstance->printAttributes().">
					$beanLabel
					</label>";
					*/
				}
				$radiosField->setRadios($radios);
			
				ob_start();
				$radiosField->toHtml();
				return ob_get_clean();
				break;
		}
		return $html;
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\EditFieldRendererInterface::getJSEdit()
	 */
	public function getJSEdit($descriptor, $bean, $id){
		return array();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\DefaultViewFieldRenderer::renderView()
	 */
	public function renderView($descriptor){
		/* @var $descriptor Many2ManyFieldDescriptor */
		$values = $descriptor->getBeanValues();
		foreach ($values as $bean){
			$label = $descriptor->getRelatedBeanLabel($bean);
			$labels[] = $label;
		}
		return count($labels) ? "<ul id='".$descriptor->getFieldName()."-view-field'><li>" . implode("</li><li>", $labels) . "</li><ul>" : "";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\DefaultViewFieldRenderer::getJSView()
	 */
	public function getJSView($descriptor, $bean, $id){
		return array();
	}
	
	/**
	 *
	 *
	 */
	public function seti18nUtilisation($tradMode){
		$this->defaultTradMode = $tradMode;
	}
	
}