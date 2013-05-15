<?php
namespace Mouf\MVC\BCE\Classes\Descriptors;
use Mouf\MVC\BCE\Classes\ValidationHandlers\JsValidationHandlerInterface;

use Mouf\MVC\BCE\BCEForm;

/**
 * Interface to be implemented by all Descriptors of a BCE form
 * @author Kevin
 *
 */
interface BCEFieldDescriptorInterface {
	
	/**
	 * Returns the name of the field as a unique identifier of that field
	 */
	public function getFieldName();

	/**
	 * Returns the name of the field as a unique identifier of that field
	 */
	public function getFieldLabel();
	
	/**
	 * Called when initializing the form (loading bean value into decsriptors, getting the validation rules, etc...)
	 * @param mixed $bean : the main bean of the form
	 * @param mixed $id : the idenfier of the form
	 * @param BCEForm $form : the form itself
	 */
	public function load($bean, $id = null, &$form = null);
	
	/**
	 * returns the specific JS for the field. 
	 * 	- This JS may come from the renderer if any (eg datepicker or slider, multiselect, etc..)
	 *  - Or also from the descriptor itself : eg a file upload callback function
	 *  - In case of a custom field, this may also be some validation script
	 *  
	 * @param bool $formMode the mode (edit ot view) of the form
	 */
	public function addJS(BCEForm & $form);
	
	/**
	 * Does all the operations before the main bean is saved. E.G:
	 *   - unformat value
	 *   - validate value
	 *   - set the value on the bean
	 *   ...
	 *   
	 * @param array $post The $_POST
	 * @param BCEForm $form the form instance
	 */
	public function preSave($post, BCEForm &$form);
	
	/**
	 * Does some operations after the main bean has been saved.
	 * Very important for M2M descriptors in order to perform their own persistance
	 * @param mixed $bean the saved bean
	 * @param mixed $beanId the id of the saved bean
	 */
	public function postSave($bean, $beanId, $postValues);
	
}