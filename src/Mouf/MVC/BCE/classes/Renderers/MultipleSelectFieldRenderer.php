<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

/**
 * A renderer class that ouputs multiple values field like checkboxes , multiselect list, ... fits for many to many relations
 * @Component
 */
class MultipleSelectFieldRenderer extends DefaultViewFieldRenderer implements MultiFieldRendererInterface {
	
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
	 * @see FieldRendererInterface::render()
	 */
	public function render($descriptor){
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
	 * @see FieldRendererInterface::getJS()
	 */
	public function getJS($descriptor){
		return array();
	}
	
}