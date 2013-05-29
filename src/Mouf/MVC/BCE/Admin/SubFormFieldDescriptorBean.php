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
	public $label;
	public $description;
	public $form;
	public $beansGetter;
	
	public $fkSetter;
	public $fkGetter;
	
	public $wrapperRenderer;
	public $itemWrapperRenderer;
	
	public $daoData;
	
	public function __construct(){
		$this->type = 'subform';
	}
	
	
	
}