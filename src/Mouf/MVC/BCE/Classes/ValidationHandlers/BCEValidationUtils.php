<?php
namespace Mouf\MVC\BCE\Classes\ValidationHandlers;

use Mouf\Utils\Common\Validators\ValidatorInterface;

class BCEValidationUtils {
	/**
	 * Check if the validators list has the RequiredValidator
	 * 
	 * @param ValidatorInterface[]|null $validators
	 * @return bool
	 */
	public static function hasRequiredValidator($validators) {
		if($validators) {
			foreach ($validators as $value) {
				$class = get_class($value);
				if($class == 'Mouf\\Utils\\Common\\Validators\\RequiredValidator'
					|| $class == 'Mouf\\Utils\\Common\\Validators\\FileUploaderRequiredValidator') {
					return true;
				}
			}
		}
		return false;
	}
}