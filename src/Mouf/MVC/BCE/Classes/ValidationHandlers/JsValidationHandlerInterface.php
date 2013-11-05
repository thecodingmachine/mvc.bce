<?php
namespace Mouf\MVC\BCE\Classes\ValidationHandlers;

use Mouf\MVC\BCE\BCEForm;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptor;

interface JsValidationHandlerInterface {
	
	/**
	 * Returns the Javascript Code that will handle from's validation: methods and rules
	 * @param $formId the html 'id' attribute of the form
	 */
	public function addJS(BCEForm $form);
	
	/**
	 * Returns the JS library needed for validation handling
	 */
	public function getJsLibrary();
	
	/**
	 * Add a validation data to the form
	 * @param JSValidationData $data
	 * @return string Css class to add it on html element
	 */
	public function addValidationData(JSValidationData $data);
}