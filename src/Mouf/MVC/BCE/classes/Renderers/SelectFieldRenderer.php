<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;


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
		/* @var $descriptor ForeignKeyFieldDescriptor */
		$fieldName = $descriptorInstance->getFieldName();
		$data = $descriptor->getData();
		$value = $descriptorInstance->getFieldValue();
		$html = "";
		if (!$this->radioMode){
			$html = "<select ".$descriptorInstance->printAttributes()." name='$fieldName' id='$fieldName'>";
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
	public function getJSEdit($descriptorInstance){
		/* @var $descriptorInstance FieldDescriptorInstance */
		return array();
	}
	
}