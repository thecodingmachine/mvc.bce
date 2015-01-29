<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

use Mouf\MVC\BCE\Classes\ScriptManagers\ScriptManager;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;

use Mouf\MoufManager;
use Mouf;
use Mouf\Html\Widgets\Form\TextField;
use Mouf\MVC\BCE\Classes\ValidationHandlers\BCEValidationUtils;

/**
 * This renderer handles date / timestamp input fields with the jQuery DatePicker
 * @ApplyTo { "php" :[ "timestamp", "datetime", "date" ] }
 * @Component
 */
class DatePickerRenderer extends TextFieldRenderer implements SingleFieldRendererInterface {

	/**
	 * @Property
	 * @var string
	 * the JSON settings for the datpicker (see http://jqueryui.com/demos/datepicker/#options)
	 */
	public $settings;
	
	/**
	 * (non-PHPdoc)
	 * The datepicker depends on jQueryUI's datepicker widget, therefore load the library into the WebLibrary manager, and call the datepicker initialization on dom ready
	 * @see FieldRendererInterface::getJS()
	 */
	public function getJSEdit(Mouf\MVC\BCE\Classes\Descriptors\BCEFieldDescriptorInterface $descriptor, $bean, $id, Mouf\Html\Utils\WebLibraryManager\WebLibraryManager $webLibraryManager){
		/* @var $descriptor FieldDescriptorInstance */
		/* @var $libManager WebLibraryManager */
		$jQueryUI = MoufManager::getMoufManager()->getInstance('jQueryUiLibrary');
		$webLibraryManager->addLibrary($jQueryUI);
		
		$fieldName = $descriptor->getFieldName();
		
		$settings = "";
		if ($this->settings){
			if (!json_decode($this->settings)){
				throw new \Exception("Settings property of the DatePickerRenderer component is not a valid JSON string <pre>$this->settings</pre> given");
			}
			$settings = $this->settings;
		}
		
		return array(
			ScriptManager::SCOPE_READY => "$('#$fieldName').datepicker($settings);"
		);
	}
	
	
}