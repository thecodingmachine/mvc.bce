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
		"Mouf\\MVC\\BCE\\Classes\\Renderers\\HiddenRenderer",
		"Mouf\\MVC\\BCE\\Classes\\Renderers\\MultipleSelectFieldRenderer{\"mode\":\"chbx\"}",
		"Mouf\\MVC\\BCE\\Classes\\Renderers\\DatePickerRenderer",
		"Mouf\\MVC\\BCE\\Classes\\Renderers\\ColorPickerRenderer",
		"Mouf\\MVC\\BCE\\Classes\\Renderers\\SelectFieldRenderer",
		"Mouf\\MVC\\BCE\\Classes\\Renderers\\BooleanFieldRenderer",
		"Mouf\\MVC\\BCE\\Classes\\Renderers\\TextFieldRenderer",
		"Mouf\\MVC\\BCE\\Classes\\Renderers\\TextAreaFieldRenderer",
		"Mouf\\MVC\\BCE\\Classes\\Renderers\\PasswordFieldRenderer",
		"Mouf\\MVC\\BCE\\FormRenderers\\Bootstrap\\Wrappers\\DefaultFieldWrapperRenderer",
		"Mouf\\MVC\\BCE\\FormRenderers\\Bootstrap\\Wrappers\\NoWrapFieldWrapperRenderer"
);
InstallUtils::massCreate($classes, $moufManager);

$baseRendererInstance = $moufManager->createInstance("Mouf\\MVC\\BCE\\FormRenderers\\Bootstrap\\BootstrapFormRenderer");
$baseRendererInstanceName = InstallUtils::getInstanceName("BaseRenderer", $moufManager);
$baseRendererInstance->setName($baseRendererInstanceName);
$baseRendererInstance->getProperty("skin")->setValue($moufManager->getInstanceDescriptor("javascript.bootstrap"));

/* JQueryValidateHandler */
$jQValidateInstance = $moufManager->createInstance("Mouf\\MVC\\BCE\\Classes\\ValidationHandlers\\JQueryValidateHandler");
$jQValidateInstanceName = InstallUtils::getInstanceName("JQueryValidateHandler", $moufManager);
$jQValidateInstance->setName($jQValidateInstanceName);
$jQValidateInstance->getProperty('jsLib')->setValue($moufManager->getInstanceDescriptor("jQueryValidateLibrary"));

// Let's rewrite the MoufComponents.php file to save the component
$moufManager->rewriteMouf();

// Finally, let's continue the install
InstallUtils::continueInstall();