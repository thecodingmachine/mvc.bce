<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

/**
 * A renderer class that ouputs a simple select box: it doesn't handle multiple selection
 * 
 * @Component
 * @ApplyTo {"type": ["fk"]}
 */
class SelectFieldRenderer implements SingleFieldRendererInterface {
	
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
	public function render($descriptor){
		/* @var $descriptor ForeignKeyFieldDescriptor */
		$fieldName = $descriptor->getFieldName();
		$data = $descriptor->getData();
		$value = $descriptor->getFieldValue();
		$html = "";
		if (!$this->radioMode){
			$html = "<select name='$fieldName' id='$fieldName'>";
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
						<input type='radio' value='$beanId' $checkedStr id='$fieldName"."-"."$beanId' name='$fieldName'> $beanLabel
					</label>
				";
			}
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