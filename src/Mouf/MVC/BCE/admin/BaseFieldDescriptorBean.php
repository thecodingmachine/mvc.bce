<?php
namespace Mouf\MVC\BCE\Admin;

class BaseFieldDescriptorBean extends FieldDescriptorBean {
	/**
	* @var BeanMethodHelper
	*/
	public $getter;
	
	/**
	 * @var BeanMethodHelper
	 */
	public $setter;
	
	public function __construct(){
		$this->type = 'base';
	}
}