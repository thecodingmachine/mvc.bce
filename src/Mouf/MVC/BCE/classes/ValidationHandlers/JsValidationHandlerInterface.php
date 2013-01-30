<?php
namespace Mouf\MVC\BCE\Classes\ValidationHandlers;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptor;

interface JsValidationHandlerInterface {
	
	/**
	 * Builds the validation script of a form depending on it's field descriptors and their validators
	 * A validator will have JS impacts only if
	 *   - it implements the JsValidatorInterface
	 *   or
	 *   - if the related PhpValidator (Server Side) has "PHP Fall Back" property activated
	 *   
	 * @param FieldDescriptor $fieldDescriptor
	 * @param string $formId
	 */
	public function buildValidationScript(FieldDescriptor $descriptor, $formId);

	/**
	 * Returns the Javascript Code that will handle from's validation: methods and rules
	 * @param $formId the html 'id' attribute of the form
	 */
	public function getValidationJs($formId);	
	
	/**
	 * Returns the JS library needed for validation handling
	 */
	public function getJsLibrary();
}