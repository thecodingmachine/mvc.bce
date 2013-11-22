<?php
namespace Mouf\MVC\BCE\Classes\Renderers;
use Mouf\Html\Widgets\Form\MultiSelectField;

use Mouf\MVC\BCE\Classes\ScriptManagers\ScriptManager;

use Mouf\Html\Widgets\Form\SelectField;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;
use Mouf\MVC\BCE\Classes\Descriptors\Many2ManyFieldDescriptor;
use Mouf\Html\Widgets\Form\CheckboxesField;
use Mouf\Html\Widgets\Form\CheckboxField;
use Mouf\Html\Widgets\Form\SelectMultipleField;
use Mouf\Html\Tags\Option;
use Mouf\MVC\BCE\Classes\ValidationHandlers\BCEValidationUtils;
use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptor;
/**
 * A renderer class that ouputs multiple values field like checkboxes , multiselect list, ... fits for many to many relations
 * @Component
 */
class MultiSelectListsRenderer extends BaseFieldRenderer implements MultiFieldRendererInterface, ViewFieldRendererInterface {
	
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
		$data = $descriptor->getData();
		$selectIds = array();
		if ($values){
			foreach ($values as $bean) {
				$id = $descriptor->getMappingRightKey($bean);
				$selectIds[] = $id;
			}
		}
		$valuesSelectBoxes = array();
		$isFirst = true;
		
		foreach ($data as $bean) {
			$beanId = $descriptor->getRelatedBeanId($bean);
			$beanLabel = $descriptor->getRelatedBeanLabel($bean);
				
			$option = new Option();
			$option->setValue($beanId);
			$option->addText($beanLabel);
			$options[] = $option;
		}

		$required = false;
		if($descriptorInstance->fieldDescriptor instanceof FieldDescriptor && $descriptorInstance->fieldDescriptor->getValidators()) {
			foreach ($descriptorInstance->fieldDescriptor->getValidators() as $validator){
				if ($validator instanceof RequiredValidator) {
					$required = true;
				}
			}
		}
		$selectBoxes = new MultiSelectField($descriptor->getFieldLabel(), $fieldName, $selectIds, $options);
		$selectBoxes->setRequired($required);
		
		ob_start();
		$selectBoxes->toHtml();
		return ob_get_clean();
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
	 * @param bool $tradMode
	 * @see \Mouf\MVC\BCE\Classes\Renderers\MultiFieldRendererInterface::seti18nUtilisation()
	 */
	public function seti18nUtilisation($tradMode){
		$this->defaultTradMode = $tradMode;
	}
	
}