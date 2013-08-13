<?php
namespace Mouf\MVC\BCE\Classes\Renderers;
use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;
use Mouf\MVC\BCE\Classes\Descriptors\Many2ManyFieldDescriptor;
/**
 * A renderer class that ouputs multiple values field like checkboxes , multiselect list, ... fits for many to many relations
 * @Component
 */
class MultipleSelectFieldRenderer extends BaseFieldRenderer implements MultiFieldRendererInterface, ViewFieldRendererInterface {
	
	/**
	 * Tells if the field should display 
	 * <ul>
	 * 	<li>a set of checkboxes,</li> 
	 *  <li>a multiselect list,</li>
	 *  <li>a multiselect widjet (TODO),</li>
	 *  <li>maybe a sortable dnd list (TODO)</li>
	 *  </ul>
	 * @OneOf("chbx", "multiselect")
	 * @OneOfText("Checkboxes", "Multiselect List")
	 * @Property
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
			break;
			
			case 'chbx':
				foreach ($data as $bean) {
					$beanId = $descriptor->getRelatedBeanId($bean);
					$beanLabel = $descriptor->getRelatedBeanLabel($bean);
					if($this->defaultTradMode == true){
						$beanLabel = iMsg($beanLabel);
					}
					$checked = (array_search($beanId, $selectIds)!==false) ? "checked='checked'" : "";
					$html .= "
					<label class='checkbox inline' for='$fieldName"."-"."$beanId'>
						<input type='checkbox' $checked value='$beanId' id='$fieldName"."-"."$beanId' name='".$fieldName."[]' ".$descriptorInstance->printAttributes().">
						$beanLabel
					</label>";
				}
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