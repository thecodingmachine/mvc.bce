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
class Many2ManyFieldDescriptorBean extends FieldDescriptorBean {
	
	public $mappingDaoName;
	/**
	 * @var DaoDescriptorBean
	 */
	public $mappingDaoData;
	public $beanValuesMethod;
	public $mappingIdGetter;
	public $mappingLeftKeySetter;
	public $mappingRightKeyGetter;
	public $mappingRightKeySetter;
	
	public $linkedDaoName;
	
	/**
	 * @var DaoDescriptorBean
	 */
	public $linkedDaoData;
	public $linkedIdGetter;
	public $linkedLabelGetter;
	public $dataMethod;
	
	public function __construct(){
		$this->type = 'm2m';
	}
	
}