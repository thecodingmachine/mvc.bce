<?php
namespace Mouf\MVC\BCE\admin;

/**
 * These classes are simple stringifyed representations of the BCE elements.
 * 
 * It has no other use than providing autocompletion, in building the objects 
 * that will be used by the administration interface of BCE.
 * 
 * @author Kevin
 *
 */
class BeanFieldHelper {
	
	/**
	 * @var BeanMethodHelper
	 */
	public $getter;

	/**
	 * @var BeanMethodHelper
	 */
	public $setter;
	
	/**
	 * @var BaseFieldDescriptorBean
	 */
	public $asDescriptor;
	
	public $columnName;
	public $isPk = false;
	
	public $type = 'base';
	
}