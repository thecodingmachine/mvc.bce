<?php
namespace Mouf\MVC\BCE\Classes\Renderers;
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
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\EditFieldRendererInterface::renderEdit()
	 */
	public function renderEdit($descriptor){
		/* @var $descriptor Many2ManyFieldDescriptor */
		$fieldName = $descriptor->getFieldName();
		$values = $descriptor->getBeanValues();
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
				$html = "<select name='$fieldName' id='$fieldName' multiple='multiple'>";
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
					$checked = (array_search($beanId, $selectIds)!==false) ? "checked='checked'" : "";
					$html .= "<label class='checkbox inline' for='$fieldName"."-"."$beanId'>
						<input type='checkbox' $checked value='$beanId' id='$fieldName"."-"."$beanId' name='".$fieldName."[]'> $beanLabel
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
	public function getJSEdit($descriptor){
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
	public function getJSView($descriptor){
		return array();
	}
	
}