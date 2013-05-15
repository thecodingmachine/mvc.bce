<?php
namespace Mouf\MVC\BCE\Classes\ValidationHandlers;

use Mouf\Reflection\MoufReflectionProxy;

class JSValidationData {
	
	public $fieldIdentifier;
	public $ruleScript;
	public $ruleName;
	public $ruleMessage;
	public $ruleArguments;
	
	
	public function __construct($fieldIdentifier, $script, $name, $message, $args) {
		$this->fieldIdentifier = $fieldIdentifier;
		$this->ruleScript = $script;
		$this->ruleName = $name;
		$this->ruleMessage = $message;
		$this->ruleArguments = $args;
	}
	
}