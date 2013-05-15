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
class ForeignKeyFieldDescriptorBean extends BaseFieldDescriptorBean {
	
	/**
	 * The name of the "foreign key" linked DAO
	 * @var string
	 */
	public $daoName;
	
	/**
	 * The method of the dao that will retrieve the list of available values
	 * @var string
	 */
	public $dataMethod;
	public $linkedIdGetter;
	public $linkedLabelGetter;
	
	/**
	 * @var DaoDescriptorBean
	 */
	public $daoData;
	
	public function __construct(){
		$this->type = 'fk';
	}
	
}