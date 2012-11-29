<?php
// First, let's request the install utilities
require_once __DIR__."/../../../autoload.php";

use Mouf\Actions\InstallUtils;
use Mouf\MoufManager;

// Let's init Mouf
InstallUtils::init(InstallUtils::$INIT_APP);

// Let's create the instance
$moufManager = MoufManager::getMoufManager();

//Let's automatically create validators for the components that are not parametized (eg : don't create a MinMaxRangeValidator)...
$classes = array(
		"Mouf\\MVC\\BCE\\classes\\HiddenRenderer",
		"Mouf\\MVC\\BCE\\classes\\MultipleSelectFieldRenderer{\"mode\":\"chbx\"}",
		"Mouf\\MVC\\BCE\\classes\\DatePickerRenderer",
		"Mouf\\MVC\\BCE\\classes\\ColorPickerRenderer",
		"Mouf\\MVC\\BCE\\classes\\SelectFieldRenderer",
		"Mouf\\MVC\\BCE\\classes\\BooleanFieldRenderer",
		"Mouf\\MVC\\BCE\\classes\\TextFieldRenderer",
		"Mouf\\MVC\\BCE\\classes\\TextAreaFieldRenderer",
		"Mouf\\MVC\\BCE\\classes\\PasswordFieldRenderer"
);
InstallUtils::massCreate($classes, $moufManager);
//now create default renderer skin
$baseSkinLib = $moufManager->createInstance("Mouf\\Html\\Utils\\WebLibraryManager\\WebLibrary");
$baseSkinLibName = InstallUtils::getInstanceName("bceBaseSkin", $moufManager);
$baseSkinLib->setName($baseSkinLibName);
$baseSkinLib->getProperty("cssFiles")->setValue(array(
	"plugins/mvc/bce/1.0-alpha/form_renderer/base/basic/css/basic.css"
));
$baseSkinLib->getProperty("renderer")->setValue($moufManager->getInstanceDescriptor("defaultWebLibraryRenderer"));

$baseRendererInstance = $moufManager->createInstance("Mouf\\MVC\\BCE\\form_renderer\\base\\BaseRenderer");
$baseRendererInstanceName = InstallUtils::getInstanceName("BaseRenderer", $moufManager);
$baseRendererInstance->setName($baseRendererInstanceName);
$baseRendererInstance->getProperty("skin")->setValue($moufManager->getInstanceDescriptor($baseSkinLibName));

/* JQueryValidateHandler */
$jQValidateInstance = $moufManager->createInstance("Mouf\\MVC\\BCE\\classes\\validators\\JQueryValidateHandler");
$jQValidateInstanceName = InstallUtils::getInstanceName("JQueryValidateHandler", $moufManager);
$jQValidateInstance->setName($jQValidateInstanceName);
$jQValidateInstance->getProperty('jsLib')->setValue($moufManager->getInstanceDescriptor("jQueryValidateLibrary"));

// Let's rewrite the MoufComponents.php file to save the component
$moufManager->rewriteMouf();

// Finally, let's continue the install
InstallUtils::continueInstall();