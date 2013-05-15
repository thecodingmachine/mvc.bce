<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;

/**
 * This renderer create a select with static values
 * @Component
 */
class StaticSelectRenderer extends DefaultViewFieldRenderer implements SingleFieldRendererInterface {

	/**
	 * @Property
	 * @var array<string>
	 * the static values
	 */
	public $settings;
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::render()
	 */
	public function renderEdit($descriptorInstance){
		/* @var $descriptorInstance FieldDescriptorInstance */
		$fieldName = $descriptorInstance->getFieldName();
		$value = $descriptorInstance->getFieldValue();
		$html = "<select ".$descriptorInstance->printAttributes()." name='".$fieldName."' id='".$fieldName."'>";
		foreach($this->settings as $option) {
			if ($option == $value) {
				$html .= "<option value=".$option." selected >".$option."</option>";
			} else {
				$html  .= "<option value=".$option." >".$option."</option>";
			}
		}
		$html .= "</select>";
		return $html;
	}
	
	/**
	 * (non-PHPdoc)
	 * NO JS for the moment
	 * @see FieldRendererInterface::getJS()
	 */
	public function getJSEdit($descriptorInstance){
		/* @var $descriptorInstance FieldDescriptorInstance */
		return array();
	}
	
	
}