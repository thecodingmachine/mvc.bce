<?php
namespace Mouf\MVC\BCE\Classes\Descriptors;

use Mouf\MVC\BCE\Classes\ValidationHandlers\JsValidationHandlerInterface;

interface FieldDescriptorInstanceInterface {

	public function addValidationData(JsValidationHandlerInterface &$handler);
	
	public function toHTML($formMode);
	
}