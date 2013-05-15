<?php
namespace Mouf\MVC\BCE\Admin;
/**
 * These classes are simple stringifyed representations of the BCE elements.
 * 
 * It has no other use than providing autocompletion, in building the objects 
 * that will be used by the administration interface of BCE.
 * 
 * @author Kevin
 *
 */
class SubFormFieldDescriptorBean extends FieldDescriptorBean {
	
	public $fieldName;
	public $fieldLabel;
	public $form;
	public $beansGetter;
	
	public $fkSetter;
	public $fkGetter;
	
	public $editCondition;
	public $viewCondition;
	
	public $fieldWrapperRenderer;
	public $itemWrapperRenderer;
	
	public function __construct(){
		$this->type = 'subform';
	}
	
	
	
}