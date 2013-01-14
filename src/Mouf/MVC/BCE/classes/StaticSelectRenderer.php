<?php
namespace Mouf\MVC\BCE\classes;

/**
 * This renderer create a select with static values
 * @Component
 */
class StaticSelectRenderer implements SingleFieldRendererInterface {

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
	public function render($descriptor){
		/* @var $descriptor BaseFieldDescriptor */
		$fieldName = $descriptor->getFieldName();
		$value = $descriptor->getFieldValue();
		$html = "<select name='".$fieldName."' id='".$fieldName."'>";
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
	public function getJS($descriptor){
		return array();
	}
	
	
}