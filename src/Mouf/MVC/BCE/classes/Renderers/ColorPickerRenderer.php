<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

/**
 * This renderer handles text input fields with the jQuery MiniColor
 * @Component
 */
class ColorPickerRenderer extends BaseFieldRenderer implements SingleFieldRendererInterface, ViewFieldRendererInterface  {
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\EditFieldRendererInterface::renderEdit()
	 */
	public function renderEdit($descriptor){
		/* @var $descriptor BaseFieldDescriptor */
		$fieldName = $descriptor->getFieldName();
		$value = $descriptor->getFieldValue();
		return "<input type='text' value='".$value."' name='".$fieldName."' id='".$fieldName."' class='color-picker'/>";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\EditFieldRendererInterface::getJSEdit()
	 */
	public function getJSEdit($descriptor){
		$fieldName = $descriptor->getFieldName();
		return array(
			"ready" => "
				function init() {
					// Enabling miniColors
					$('.color-picker[name=$fieldName]').miniColors({
						change: function(hex, rgb) {
						},
						open: function(hex, rgb) {
						},
						close: function(hex, rgb) {
						}
					});
				}
				init();
			"
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\ViewFieldRendererInterface::renderView()
	 */
	public function renderView($descriptor){
		return "<input type='text' value='".$value."' name='".$fieldName."' id='".$fieldName."' class='color-picker' disabled='disabled'/>";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\ViewFieldRendererInterface::getJSView()
	 */
	public function getJSView($descriptor){
		return $this->getJSEdit($descriptor);
	}
	
	
}