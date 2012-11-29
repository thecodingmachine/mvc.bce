<?php
use Mouf\Moufspector;

use Mouf\Reflection\MoufReflectionClass;
use Mouf\MoufManager;
use Mouf\MVC\BCE\admin\CustomFieldDescriptorBean;
use Mouf\MVC\BCE\admin\Many2ManyFieldDescriptorBean;
use Mouf\MVC\BCE\admin\BaseFieldDescriptorBean;
use Mouf\MVC\BCE\admin\ForeignKeyFieldDescriptorBean;
use Mouf\MVC\BCE\admin\ForeignKeyDataBean;
use Mouf\MVC\BCE\admin\BeanFieldHelper;
use Mouf\MVC\BCE\admin\BeanMethodHelper;
use Mouf\MVC\BCE\admin\DaoDescriptorBean;
use Mouf\MVC\BCE\admin\BCEFormInstanceBean;

/**
 * ----------------------------------------------------------------
 * ------------------------- Head's UP!!!--------------------------
 * ----------------------------------------------------------------
 * 
 * This is one of the moste important file of the Form configuration. bce_utils is an AJAX helper that handles:
 *   - get data about an existing instance
 *   - get data about a DAO (can be used either for the main dao or any secondary dao used in FK, M2M, ... descriptors)
 *
 *   bce_utils.php has it's client side JavaScript mirror : bceConfig.js. Basically, bce_utils loads BCE objects into well formatted data,
 *   which will be treated and displayed by bceConfig.js
 *   
 *   
 *   Here are some explanations on how the form configuration works :
 *   - First, if the main dao isn't set, then the only choice is to select one in the dropdown list
 *   
 *   - Once the DAO is defined, bce_utils will be called to get instance data
 *   
 *   - Of course, the instance data will load the form configuration (id, name, class, validation handler, etc...,
 *     but also the descriptors. 
 *     Even more! bce_utils will also return a set of descriptors for each getter / setter of the main bean (the one handled by the main dao)
 *     
 *   - Finally, bce_utils also retuns DAO data, which are used when a secondary dao is required (FK or M2M daos for example)
 */

session_start();
require_once '../../../../../mouf/Mouf.php';

$query = $_GET['q'];
$inputName = $_GET['n'];

$utils = new BCEUtils();

//depending on the $query parameter, return instance or dao data
switch ($query) {
	case 'daoData':
		echo json_encode($utils->getDaoDataFromInstance($inputName));
	break;
	
	case 'instanceData':
		echo json_encode($utils->getInstanceData($inputName));
	break;
}

class BCEUtils{
	
	/**
	 * List of all objects that are used by the descriptors : 
	 *  - validators
	 *  - renderers
	 *  - formatters
	 *  - daos
	 */
	private $validators = array();
	private $renderers = array();
	private $formatters = array();
	private $daos = array();
	
	/**
	 * The @ApplyTo annotations in the validators, renderers and formatters 
	 * are used to define when to apply those instances to some descriptors.
	 * 
	 * For example, a DatePickerRenderer is applyied to Date, Timestamp, and Datetime PHP types
	 * 
	 * The $handleOrder variable defines the priority of the @ApplyTo annotations : 
	 * For example, 'pk' @ApplyTo annotation prevails on 'type' ones...
	 */
	public $handleOrder = array("pk", "type", "php", "db");
	
	public function __construct(){
		//Simply initialize collections (used for drop downs)
		$this->initValidators();
		$this->initRenderers();
		$this->initFormatters();
		$this->initDaos();
	}
	
	/**
	 * Get the list of all suitable DAOs, and put them into an associative array 
	 * Key is table name, which will help suggesting the right dao for a FK descriptor, see _fitDaoByTableName function
	 * @return array
	 */
	private function initDaos(){
		$daos = Moufspector::getComponentsList("Mouf\\Database\\DAOInterface");
		foreach ($daos as $className) {
			$descriptor = new MoufReflectionClass($className);
			$table = $descriptor->getAnnotations("dbTable");
			$table = $table[0];
			$daoForClass = MoufManager::getMoufManager()->findInstances($className);
			$daoForClass = $daoForClass[0];
			$this->daos[$table] = $daoForClass;
		}
	}
	
	/**
	 * Gets the list of instances of classes that implement the interface parameter.
	 * The returned array has 2 dimensions, first one is the type (see $handleOrder variable), and the second is the value of the type
	 * For example $array['php']['number']
	 * 
	 * This will help the configurator to suggest adapted validators, renderers, etc for new descriptors
	 * 
	 * @param string $interface
	 */
	private function initHandler($interface){
		$handlers = array();
		$instances = MoufManager::getMoufManager()->findInstances($interface);
		foreach ($instances as $instance) {
			$className = MoufManager::getMoufManager()->getInstanceType($instance);
			$classDesc = new MoufReflectionClass($className);
			$types = $classDesc->getAnnotations('ApplyTo');
			if (count($types)){
				$types = $types[0];
				$types = json_decode($types);
				foreach ($types as $criteria => $values) {
					foreach ($values as $value) {
						$handlers[$criteria][$value][] = $instance;
					}
				}
			}
		}
		return $handlers;
	}
	
	private function initValidators(){
		$this->validators = $this->initHandler('Mouf\\Utils\\Common\\Validators\\ValidatorInterface');
	}
	
	private function initRenderers(){
		$this->renderers = $this->initHandler('Mouf\\MVC\\BCE\\classes\\FieldRendererInterface');
	}
	
	private function initFormatters(){
		$this->formatters = $this->initHandler('Mouf\\Utils\\Common\\Formatters\\FormatterInterface');
	}
	
	/**
	 * Shortcut for getting daoData from dao instancename, rather then from className
	 * @param string $daoInstanceName
	 */
	public function getDaoDataFromInstance($daoInstanceName){
		$desc = MoufManager::getMoufManager()->getInstanceDescriptor($daoInstanceName);
		$daoClass = $desc->getClassName();
		return $this->getDaoData($daoClass);
	}
	
	/**
	 * The DAO data will return the method descriptors for the dao itself and the bean that is handled by this dao.
	 * For example, 
	 *  - userDao has save, create, getById, etc.. methods
	 *  - userBean has getId, getName, getEmail, etc...
	 *  
	 *  The bean's methods can by used to suggest descriptors (the get / set Name methods) will suggest to create a nameDescriptor.
	 *  Moreover, those bean methods will be used for FK & M2M descriptors (like the linkedIdGetter property)
	 *  
	 *  The DAO's methods will be used for FK and M2M descriptors (like the dataMethod property)
	 * 
	 * @param string $daoClass
	 */
	private function getDaoData($daoClass){
		$daoDescripror = new DaoDescriptorBean();
		
		$class = new MoufReflectionClass($daoClass);
		$method = $class->getMethod("getById");
		$returnClass = $method->getAnnotations('return');
		
		list($fields, $table) = $this->getBeanMethods($returnClass[0]);
		$daoDescripror->beanClassFields = $fields;
		$daoDescripror->beanTableName = $table;
		$daoDescripror->daoMethods = $this->getDaoMethods($daoClass);
		
		return $daoDescripror;
	}
	
	/**
	 * Get the methods of a dao
	 * @param array<string> $daoClass
	 */
	private function getDaoMethods($daoClass){
		$daoMethodNames = array();
		$daoClassReflexion = new MoufReflectionClass($daoClass);
		$daoMethods = $daoClassReflexion->getMethods();
		foreach ($daoMethods as $method) {
			$daoMethodNames[] = $method->getName();
		}
		return $daoMethodNames;
	}
	
	/**
	 * Retrieve the field helpers of a bean class.
	 * This 
	 * In this function only Base of FK fieldDescriptors can be treated (because they are related to a bean property)
	 * @param string $beanClassName
	 * @return array<BeanFieldHelper>
	 */
	private function getBeanMethods($beanClassName){
		$beanClass = new MoufReflectionClass($beanClassName);
		
		//The table name will be used to the DB model data as primary key or foreign keys
		$tableName = $beanClass->getAnnotations("dbTable");
		if (empty($tableName)){
			$baseBeanClass = $beanClass->getParentClass();
			$tableName = $baseBeanClass->getAnnotations("dbTable");
		}
		
		//Get parent class in order to distinguish the bean classe's methods from it's parents' ones
		$parentBeanClass = $beanClass->getParentClass()->getParentClass();
		$methods = $beanClass->getMethodsByPattern("^[gs]et");
		$methodsParent = $parentBeanClass->getMethodsByPattern("^[gs]et");
		$finalMethods = array();

		$connection = Mouf::getTdbmService()->getConnection();
		//Primary keys will be used to suggest the idFieldDescriptor
		$primaryKeys = $connection->getPrimaryKey($tableName[0]);
		
		foreach ($methods as $method) {
			/* @var $method  MoufReflectionMethod */
			if (!array_key_exists($method->getName(), $methodsParent)){//Only take the bean's methods, not the parent's ones
				
				$methodObj = new BeanMethodHelper();
				$methodObj->name = $method->getName();
				
				//Will help to suggest appropriate validators, formatters and rederers
				$returnAnnotation = $method->getAnnotations('dbType');
				$columnName = $method->getAnnotations('dbColumn');//Get column name to suggest descriptor name
				$columnName = $columnName[0];
				
				//If there is no column name, the method is not a getter or a setter, and therefore cannot be mapped to a decriptor
				if (!$columnName){
					continue;
				}
				
				$fieldIndex = self::toCamelCase($columnName, true)."Desc";
				
				/* This script has to get getter AND setter for each property,
				 * so the method descriptor is in fact linked to 2 methods : the getter and the setter */
				$fieldDataObj = isset($finalMethods[$fieldIndex]) ? $finalMethods[$fieldIndex] : new BeanFieldHelper();
				
				/* @var $fieldDataObj BeanFieldHelper */
				$fieldDataObj->columnName = $columnName;
				
				/* If the current column is the primary key of the table, the set the pk attribute which will 
				 * suggest teh descripor to be teh idFieldDescriptor
				 */
				foreach ($primaryKeys as $key){
					if ($key->name == $columnName){
						$fieldDataObj->isPk = true;
						break;
					}
				}
				 
				/*
				 * Like the primary key test, foreign keys will suggest the field to be a FK descriptor
				 * The fkData will tell which table is linked and so which dao should be the linked dao
				 * 
				 */
				$referencedTables = $connection->getConstraintsOnTable($tableName[0], $columnName);
				if (!empty($referencedTables)){
					//TODO : linked column could be used to rather then regex match
					$ref = $referencedTables[0];
					$foreignKeyData = new ForeignKeyDataBean();
					$foreignKeyData->refTable = $ref['table2'];
					$foreignKeyData->refColumn = $ref['col2'];
					$methodObj->fkData = $foreignKeyData;
					$fieldDataObj->type = 'fk';
				}

				/* Set the type declared by the getter (db type is transleted into php type) */
				if (count($returnAnnotation)){
					$returnType = $returnAnnotation[0];
					$returnType = explode(" ", $returnType);
					$returnType = $returnType[0];
					$methodObj->dbType = $returnType;
					$phpType = $connection->getUnderlyingType($returnType);
					$methodObj->phpType = $phpType;
					$fieldDataObj->getter = $methodObj;
				}else{
					$fieldDataObj->setter = $methodObj;
				}
				$finalMethods[$fieldIndex] = $fieldDataObj;
			}
		}
		
		/* each  beanFieldHelper have to be converted to a FieldDeescriptorBean 
		 * in order to have same properties than the existing descriptors */
		foreach ($finalMethods as $columnName => $fieldData) {
			$fieldData->asDescriptor = $this->beanHelperConvert2Descriptor($fieldData);
		}
		
		return array($finalMethods, $tableName[0]);
	}
	
	/**
	 * Quite simple function that returns a FieldDecriptorBean from a BeanFieldHelper
	 * @param BeanFieldHelper $beanField
	 */
	private function beanHelperConvert2Descriptor(BeanFieldHelper $beanField){
		$descriptorBean = null;
		if (isset($beanField->getter->fkData)){//if getter is related to a foreign key, then instanciate a FK field decriptor
			$convertBean = new ForeignKeyFieldDescriptorBean();
		}else{
			$convertBean = new BaseFieldDescriptorBean();
		}
		
		//Dummy data mapping
		$convertBean->fieldName = $beanField->columnName;
		$convertBean->getter = $beanField->getter->name;
		$convertBean->setter = $beanField->setter->name;
		$convertBean->label = $this->getLabelFromFieldName($beanField->columnName);
		$convertBean->name = self::toCamelCase($beanField->columnName, true)."Desc";
		$convertBean->isPk = $beanField->isPk;
		$convertBean->active = true;
		$convertBean->is_new = true;
		
		if (isset($beanField->getter->fkData)){
			/* @var $convertBean ForeignKeyFieldDescriptorBean */
			
			//Just find the dao that is handling the referenced table, then load the dao's data
			$convertBean->daoName = $this->_fitDaoByTableName($beanField->getter->fkData->refTable);
			$convertBean->daoData = $this->getDaoDataFromInstance($convertBean->daoName);
			
			//By default, we are looking for a "getList" method for the dataMethod attribute. If none exists, no preselection is made clientside
			$convertBean->dataMethod = array_search("getList", $convertBean->daoData->daoMethods) !== false ? "getList" : $convertBean->daoData->daoMethods[0];
			
			//Same thing with linkedIdGetter/linkedLabelGetter properties (getter on the 'id' and 'label' fields if exists)
			$convertBean->linkedIdGetter =  isset($convertBean->daoData->beanClassFields['id']) ? $convertBean->daoData->beanClassFields['id']->getter->name : "";
			$convertBean->linkedLabelGetter = isset($convertBean->daoData->beanClassFields['label']) ? $convertBean->daoData->beanClassFields['label']->getter->name : ""; 
		}
		
		/* Find the best matching instances to apply... */
		$convertBean->renderer = $this->_match($beanField, $this->renderers);
		$convertBean->formatter = $this->_match($beanField, $this->formatters);
		$convertBean->validators = $this->_match($beanField, $this->validators, true);
		
		return $convertBean;
	}
	
	/** 
	 * As explained above, the initHandler calls have built associative arrays with 2 dimensions [type][type_value].
	 * For example, the validators arrray might look like this :
	 * array
		['php'] =>
			['boolean'] => 
				0 => 'booleanFieldRenderer'
			['timestamp'] => 
				0 => 'datePickerRenderer'
			['datetime'] => 
				0 => 'datePickerRenderer'
			['date'] => 
				0 => 'datePickerRenderer'
			['string'] => 
				0 => 'colorPickerRenderer'
			['string'] => 
				0 => 'textFieldRenderer'
			['string'] => 
				0 => 'passwordFieldRenderer'
			['int'] => 
				0 => 'textFieldRenderer'
			['number'] => 
				0 => 'textFieldRenderer'
		['pk'] =>
			['pk'] => 
				0 => 'hiddenRenderer'
		['type'] =>
			['fk'] => 
				0 => 'selectFieldRenderer'
		 * 
		 * More over the $handleOrder variable defines the priority of the categories, which means, for instance, 
		 * that the pk matches will hit before the php ones. 
		 * 
	 * Once a match has been done for an attibute, other instances cannot be 
	 * suggested unless the $isMultiple flag is passed to the _match function
	 * 
	 *  @param $beanField : the BaseFieldDescriptorBean
	 *  @param $instances : the arrociative array of validatrs, formatters or renderers
	 *  @param $isMultiple (optional) : tells if the function may retu one or several instance names
	 *  
	 *  @return mixed a string (or null) if the $isMultiple flag is not set or set to false, else an array 
	* */
	private function _match($beanField, $instances, $isMultiple = false){
		$matches = array();
		foreach ($this->handleOrder as $criteria) {//follow the priority
			switch ($criteria) {
				case 'pk':
					if (isset($instances[$criteria]) && isset($instances[$criteria]["pk"]) && $beanField->isPk){
						foreach ($instances[$criteria]["pk"] as $instance) {
							if (array_search($instance, $matches) === false){
								$matches[] = $instance;
							}
						}
					}
				;
				case 'type':
					if (isset($instances[$criteria]) && isset($instances[$criteria][$beanField->type])){
						foreach ($instances[$criteria][$beanField->type] as $instance) {
							if (array_search($instance, $matches) === false){
								$matches[] = $instance;
							}
						}
					}
				break;
				case 'php':
					if (isset($instances[$criteria]) && isset($instances[$criteria][$beanField->getter->phpType])){
						foreach ($instances[$criteria][$beanField->getter->phpType] as $instance) {
							if (array_search($instance, $matches) === false){
								$matches[] = $instance;
							}
						}
					}
				;
				break;
				case 'db':
					if (isset($instances[$criteria]) && isset($instances[$criteria][$beanField->getter->dbType])){
						foreach ($instances[$criteria][$beanField->getter->dbType] as $instance) {
							if (array_search($instance, $matches) === false){
								$matches[] = $instance;
							}
						}
					}
				;
				break;
			}
		}
		$return = $isMultiple ? $matches : (count($matches) ? $matches[0] : null);
		return $return;
	}
	
	private function _fitDaoByTableName($table){
		return $this->daos[$table];
	}
	
	private function getLabelFromFieldName($fieldName){
		return str_replace(" id", "", str_replace("_", " ", ucfirst($fieldName)));
	}
	
	/**
	 * Transforms a string to camelCase (except the first letter will be uppercase too).
	 * Underscores and spaces are removed and the first letter after the underscore is uppercased.
	 * 
	 * This method has been "stolen" from Davids TDBM dao generator ... may be it could be set into a "mouf admintool" package ?
	 * 
	 * @param $str string
	 * @return string
	 */
	private static function toCamelCase($str, $fisrtLower = false) {
		if (!$fisrtLower){
			$str = strtoupper(substr($str,0,1)).substr($str,1);
		}
		while (true) {
			if (strpos($str, "_") === false && strpos($str, " ") === false)
				break;
				
			$pos = strpos($str, "_");
			if ($pos === false) {
				$pos = strpos($str, " ");
			}
			$before = substr($str,0,$pos);
			$after = substr($str,$pos+1);
			$str = $before.strtoupper(substr($after,0,1)).substr($after,1);
		}
		return $str;
	}
	
	/**
	 * Second more important function for the BCEUtils helper :
	 * get the information about a BCEInstance.
	 * 
	 * Of course, the Form is mainly composed of field descriptors, and there for there will be DAO data returned,
	 * But there also is all the other properties of the form :
	 *   - all form tag's attributes (id, name, etc...)
	 *   - JS validation handler
	 *   - Form renderer
	 * 
	 * @param string $instanceName the name of the instance which's information should be returned
	 */
	public function getInstanceData($instanceName){
		$obj = new BCEFormInstanceBean();
		
		/* set the main dao property, and the associated table name that will help suggesting the right properties for FK & M2M descriptors */
		$desc = MoufManager::getMoufManager()->getInstanceDescriptor($instanceName);
		$prop = $desc->getProperty('mainDAO');
		$val = $prop->getValue();
		$obj->daoData = $this->getDaoData($val->getClassName());
		$obj->mainBeanTableName = $obj->daoData->beanTableName;
		
		/* Building the fieldDescriptors list (id Descriptor is made separately) */
		$fieldDescs = array();
		
		$baseFiedDescriptors = $desc->getProperty('idFieldDescriptor');
		$val = $baseFiedDescriptors->getValue();
		$fieldData = $this->getFieldDescriptorBean($val);
		$obj->idFieldDescriptor = $fieldData;
		
		$baseFiedDescriptors = $desc->getProperty('fieldDescriptors');
		$val = $baseFiedDescriptors->getValue();
		if ($val){
			foreach ($val as $descriptor) {
				$fieldData = $this->getFieldDescriptorBean($descriptor);
				$fieldDescs[] = $fieldData;
			}
		}
		
		$obj->descriptors = $fieldDescs;
		
		//Load form's configuration data
		$obj->action = $desc->getProperty('action')->getValue() ? $desc->getProperty('action')->getValue() : "save";
		$obj->method = $desc->getProperty('method')->getValue() ? $desc->getProperty('method')->getValue() : "POST";
		$obj->saveLabel = $desc->getProperty('saveLabel')->getValue() ? $desc->getProperty('saveLabel')->getValue() : "Save changes";
		$obj->cancelLabel = $desc->getProperty('cancelLabel')->getValue() ? $desc->getProperty('cancelLabel')->getValue() : "Cancel";
		
		
		$attrs = array(
			"id" => "default_id",
			"name" => "default_name",
			"accept-charset" => "UTF-8",
			"class" => "",
			"enctype" => "application/x-www-form-urlencoded"
		);
		
		$obj->attributes = $desc->getProperty('attributes')->getValue();
		foreach ($attrs as $k=>$attr){
			if (!isset($obj->attributes[$k])){
				$obj->attributes[$k] = $attrs[$k];
			}
		}
		
		$rendererDesc = $desc->getProperty('renderer')->getValue();
		if ($rendererDesc) $obj->renderer = $rendererDesc->getName();

		$validateHandlerDesc = $desc->getProperty('validationHandler')->getValue();
		if ($rendererDesc) $obj->validationHandler= $validateHandlerDesc->getName();
		
		return $obj;
	}
	
	/**
	 * Intantiate a FieldDescriptorBean from a FieldDescriptor
	 * @param BCEFieldDescriptorInterface $descriptor
	 */
	private function getFieldDescriptorBean($descriptor){
		if (!$descriptor) return null;
		
		//Custom Field Descriptors should be treated very simply : just set the name for displaying purpose and that's it
		$isCustom = false;
		
		//Load the instance from which the data will be extracted
		$instance = MoufManager::getMoufManager()->getInstanceDescriptor($descriptor->getName());
		
		//Instanciate the bean with a class that matches the descriptor instance's class
		if ($descriptor->getClassName() == 'Mouf\\MVC\\BCE\\classes\\ForeignKeyFieldDescriptor'){
			$fieldData = new ForeignKeyFieldDescriptorBean();
		}else if ($descriptor->getClassName() == 'Mouf\\MVC\\BCE\\classes\\BaseFieldDescriptor'){
			$fieldData = new BaseFieldDescriptorBean();
		}else if ($descriptor->getClassName() == 'Mouf\\MVC\\BCE\\classes\\Many2ManyFieldDescriptor'){
			$fieldData = new Many2ManyFieldDescriptorBean();
		}else{
			$isCustom = true;
			$fieldData = new CustomFieldDescriptorBean();
		}
		
		if ($isCustom){
			$fieldData->name = $descriptor->getName();
		}else{
			
			/* 
			 * In any case the descriptor extends the fieldDecriptor class,
			 * so getter, setter, name, label, formatter, etc... are loaded here
			 */
			$this->loadBaseValues($fieldData, $descriptor, $instance);
			
			/*
			 * Load BaseFieldDescriptor data
			 * TODO : find a better way than comparing class names, use instance of, is_a or is_subclass... but one that works :( 
			 */
			if ($descriptor->getClassName() != 'Many2ManyFieldDescriptor'){
				$fieldData->getter = $instance->getProperty('getter')->getValue();
				$fieldData->setter = $instance->getProperty('setter')->getValue();
			}
			
			/* load FK descriptor specific attributes */
			if ($descriptor->getClassName() == 'ForeignKeyFieldDescriptor'){
				$this->loadFKDescriptorValues($fieldData, $instance);
			}
			/* load M2M descriptor specific attributes */
			else if ($descriptor->getClassName() == 'Many2ManyFieldDescriptor'){
				$this->loadM2MDescriptorValues($fieldData, $instance);
			}
		}
		
		
		return $fieldData;
	}

	/**
	 * The FieldDescriptor abscract class defines a set of properties that are common to all (none custom) FieldDescriptors
	 * This function is responsible for loading those properties' values into the fieldDescriptorBean.
	 * In fact, this is quite a simple mapping between the FieldDescriptor ans the FieldDescriptorBean classes...
	 * 
	 * @param FieldDescriptorBean $bean the bean to be loaded 
	 * @param MoufInstanceDescriptor $descriptor : the InstanceDescriptor that will provide the data
	 * @param FieldDescriptor $instance : the fieldDescriptor that will provide the data
	 */
	private function loadBaseValues(&$bean, $descriptor, $instance){
		/* @var $bean FieldDescriptorBean */
		$bean->name = $descriptor->getName();
		if ($instance->getProperty('renderer')->getValue()) 
			$bean->renderer = $instance->getProperty('renderer')->getValue()->getName();
		$formatterDesc = $instance->getProperty('formatter')->getValue();
		$bean->formatter = $formatterDesc ? $formatterDesc->getName() : null;
		$bean->fieldName = $instance->getProperty('fieldName')->getValue();
		$bean->label = $instance->getProperty('label')->getValue();
		
		$validatorsDesc = $instance->getProperty("validators");
		$bean->validators = array();
		if ($validatorsDesc){
			$validatorsDesc = $validatorsDesc->getValue();
			if ($validatorsDesc){
				foreach ($validatorsDesc as $validator) {
					$bean->validators[] = $validator->getName();
				}
			}
		}
	}

	/**
	 * Load the properties' values of a ForeignKeyFieldDescriptor into a ForeignKeyFieldDescriptorBean 
	 * @param ForeignKeyFieldDescriptorBean $fkDescBean
	 * @param ForeignKeyFieldDescriptor $instance : the fieldDescriptor that will provide the data
	 */
	private function loadFKDescriptorValues(&$fkDescBean, $instance){
		/* @var $fkDescBean ForeignKeyFieldDescriptorBean */
		$daoDesc = $instance->getProperty('dao')->getValue();
		if ($daoDesc){
			$fkDescBean->daoName = $daoDesc->getName();
			$fkDescBean->daoData = $this->getDaoData($daoDesc->getClassName());
		
			$fkDescBean->dataMethod = $instance->getProperty('dataMethod')->getValue();
			$fkDescBean->linkedIdGetter = $instance->getProperty('linkedIdGetter')->getValue();
			$fkDescBean->linkedLabelGetter = $instance->getProperty('linkedLabelGetter')->getValue();
		}
	}
	
	/**
	 * Load the properties' values of a Many2ManyFieldDescriptor into a Many2ManyFieldDescriptorBean
	 * @param Many2ManyFieldDescriptorBean $fkDescBean
	 * @param Many2ManyFieldDescriptor $instance : the fieldDescriptor that will provide the data
	 */
	private function loadM2MDescriptorValues(&$m2mDescBean, $instance){
		/* @var $m2mDescBean Many2ManyFieldDescriptorBean */
		$mappingDaoDesc = $instance->getProperty("mappingDao")->getValue();
		if ($mappingDaoDesc){
			$m2mDescBean->mappingDaoName = $mappingDaoDesc->getName();
			$m2mDescBean->mappingDaoData = $this->getDaoData($mappingDaoDesc->getClassName());
			
			$m2mDescBean->mappingIdGetter = $instance->getProperty('mappingIdGetter')->getValue();
			$m2mDescBean->mappingLeftKeySetter = $instance->getProperty('mappingLeftKeySetter')->getValue();
			$m2mDescBean->mappingRightKeyGetter = $instance->getProperty('mappingRightKeyGetter')->getValue();
			$m2mDescBean->mappingRightKeySetter = $instance->getProperty('mappingRightKeySetter')->getValue();
			$m2mDescBean->beanValuesMethod = $instance->getProperty('beanValuesMethod')->getValue();
		}
		
		$linkedDaoDesc = $instance->getProperty("linkedDao")->getValue();
		if ($linkedDaoDesc){
			$m2mDescBean->linkedDaoName = $linkedDaoDesc->getName();
			$m2mDescBean->linkedDaoData = $this->getDaoData($linkedDaoDesc->getClassName());
			
			$m2mDescBean->linkedIdGetter = $instance->getProperty('linkedIdGetter')->getValue();
			$m2mDescBean->linkedLabelGetter = $instance->getProperty('linkedLabelGetter')->getValue();
			$m2mDescBean->dataMethod = $instance->getProperty('dataMethod')->getValue();
		}
	}
}
