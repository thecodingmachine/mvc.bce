<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

use Mouf\MoufManager;
use Mouf;

/**
 * This renderer handles date / timestamp input fields with the jQuery DatePicker
 * @ApplyTo { "php" :[ "timestamp", "datetime", "date" ] }
 * @Component
 */
class DatePickerRenderer implements SingleFieldRendererInterface {

	/**
	 * @Property
	 * @var string $settings
	 * the JSON settings for the datpicker (see http://jqueryui.com/demos/datepicker/#options)
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
		return "<input type='text' value='".$value."' name='".$fieldName."' id='".$fieldName."'/>";
	}
	
	/**
	 * (non-PHPdoc)
	 * The datepicker depends on jQueryUI's datepicker widget, therefore load the library into the WebLibrary manager, and call the datepicker initialization on dom ready
	 * @see FieldRendererInterface::getJS()
	 */
	public function getJS($descriptor){
		/* @var $libManager WebLibraryManager */
		$jQueryUI = MoufManager::getMoufManager()->getInstance('jQueryUiLibrary');
		Mouf::getDefaultWebLibraryManager()->addLibrary($jQueryUI);
		
		$fieldName = $descriptor->getFieldName();
		
		$settings = "";
		if ($this->settings){
			if (!json_decode($this->settings)){
				throw new \Exception("Settings property of the DatePickerRenderer component is not a valid JSON string <pre>$this->settings</pre> given");
			}
			$settings = $this->settings;
		}
		
		return array(
			"ready" => "$('#$fieldName').datepicker($settings);"
		);
	}
	
	
}