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
				if(get_class($value) == 'Mouf\\Utils\\Common\\Validators\\RequiredValidator') {
					return true;
				}
			}
		}
		return false;
	}
}