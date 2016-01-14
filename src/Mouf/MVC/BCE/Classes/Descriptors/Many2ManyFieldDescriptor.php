<?php
namespace Mouf\MVC\BCE\Classes\Descriptors;

use Mouf\Database\DAOInterface;

/**
 * This field descriptor can be used in order to handle many to may urelations of the Form's main bean.
 * For example, a user has a set of hobbies, that are stored in a hobby table, and linked to the user by a user_hobby table
 * 
 * Therefore, this class references
 * 		- a mapping DAO that handles the relation beans ('userhobby' bean)
 * 		- a linked DAO that handles the linked beans ('hobby' bean)
 * 
 * <pre>
 *    Main DAO	                Mapping DAO                          Linked DAO
 *    	                       (bean values)                      (available data)
 *   |--------|	      |-------------------------------|
 *   |  user  |       |       user hobby              |	       |--------------------|
 *   |--------|	      |-------------------------------|	       |        hobby       |
 *   |   id   | <---- |       id (mappingId)          |	       |--------------------|
 *   |________|	      |  user_id (mappingLeftKey)     | -----> |    id (linkedId)   |
 *                    | hobby_id (mappingRightKey)    |        | label (linkedLabel)|
 *                    |_______________________________|        |____________________|
 * </pre>
 */
class Many2ManyFieldDescriptor extends FieldDescriptor {
	
	/**
	 * @Property
	 * @var DAOInterface
	 */
	public $mappingDao;
	
	/**
	 * @Property
	 * @var DAOInterface
	 */
	public $linkedDao;	
	
	/**
	 * Name of the method that get's the id of the linked Bean
	 * @Property
	 * @var string
	 */
	public $linkedIdGetter;
	
	/**
	 * Name of the method that get's the label of the linked Bean
	 * @Property
	 * @var string
	 */
	public $linkedLabelGetter;
	
	/**
	 * Name of the method that returns the list of beans to be linked
	 * @Property
	 * @var string
	 */
	public $dataMethod;
	
	/**
	 * Name of the method that returns the beans of the mapping table that are already linked to the main bean
	 * @Property
	 * @var string
	 */
	public $beanValuesMethod;
	
	/**
	 * Name of the method that gets the Id of the mapping table
	 * @Property
	 * @var string
	 */
	public $mappingIdGetter;
	
	/**
	 * Name of the method that sets main bean's main bean's Id in mapping table (left column)
	 * @Property
	 * @var string
	 */
	public $mappingLeftKeySetter;
	
	/**
	 * Name of the method that gets linked bean's foreing key from mapping table (right column)
	 * @Property
	 * @var string
	 */
	public $mappingRightKeyGetter;
	
	/**
	 * Name of the method that sets linked bean's foreing key into mapping table (right column)
	 * @Property
	 * @var string
	 */
	public $mappingRightKeySetter;
	
	/**
	 * List of all beans available to be linked 
	 * @var array<mixed>
	 */
	private $data;
	
	/**
	 * List of all beans from the mapping table that are linked to the main bean
	 * @var array<mixed>
	 */
	private $beanValues = array();
	
	/**
	 * array of ids to be saved
	 * @var array<mixed>
	 */
	private $saveValues = array();
	
	/**
	 * Load main bean's values and avalable ones
	 * @param mixed $mainBeanId the id of the main bean
	 * 
	 * @see BCEFieldDescriptorInterface::load()
	 */
	public function load($bean, $mainBeanId = null, &$form = null){
		$this->loadValues($mainBeanId);
		$this->loadData();
		
		$descriptorInstance = new FieldDescriptorInstance($this, $form, $mainBeanId);
		$descriptorInstance->value = $this->beanValues;
		return $descriptorInstance;
	}
	
	/**
	 * Loads the values of the bean
	 * These values are stored in an associative array, the key being the linked bean's Id
	 * @param mixed $mainBeanId the id of the main bean 
	 */
	public function loadValues($mainBeanId){
		if ($mainBeanId == null){
			$this->beanValues = $this->getDefaultValue();
		}else{
                        $this->beanValues = array();
			$tmpArray = call_user_func(array($this->mappingDao, $this->beanValuesMethod), $mainBeanId);
			foreach ($tmpArray as $bean){
				$this->beanValues[$this->getMappingRightKey($bean)] = $bean;
			}
		}
	}
	
	/**
	 * Loads all available data
	 */
	public function loadData(){
		$this->data = call_user_func(array($this->linkedDao, $this->dataMethod));
	}
	
	/**
	 * Main bean Id setter (sets the foreign key value for main bean)
	 * @param mixed $id
	 */
	public function setMappingLeftKey($id, $bean){
		call_user_func(array($bean, $this->mappingLeftKeySetter), $id);
	}
	
	/**
	 * This Descriptor will do all the persistance job in this postSave method, beacause is has to wait the main bean has been persisted 
	 * @see BCEFieldDescriptorInterface::postSave()
	 */
	public function postSave($bean, $beanId, $postValues = null){
		//First remember which "secondary beans" the main bean was linked to
		$this->loadValues($beanId);

		
		//Retrieve data of linked table to delete only those element
		$this->loadData();
		$dataLinked = array();
		foreach ($this->data as $value) {
			$dataLinked[$this->getRelatedBeanId($value)] = true;
		}
		
		$beforVals = $this->getBeanValues();
		//Persist them into a keyset of "secondary bean IDs" 
		//E.G the ids of all hobbies that the usr has 
		//Delete only the linked element
		$beforeValues = array();
        if(!empty($beforVals)){
            foreach ($beforVals as $bean) {
                $value = $this->getMappingRightKey($bean);
                if(isset($dataLinked[$value])) {
                    $beforeValues[] = $value;
                }
            }
        }

		//Save values have been set by the preSave handler 
		//(defined in the FieldDescriptor class), that calls in fact the own "setValue" method
		$finalValues = $this->getSaveValues();
		if(!is_array($finalValues)) {
			$finalValues = array($finalValues);
		}

		//Make 2 inverse diffs to idenItify which mappings have changed (added and removed) 
		$toDelete = array_diff($beforeValues, $finalValues);
		$toSave = array_diff($finalValues, $beforeValues);

		foreach ($toDelete as $linkedBeanId) {
			$this->mappingDao->delete($beforVals[$linkedBeanId]);
		}
		foreach ($toSave as $linkedBeanId) {
			$bean = $this->mappingDao->create();
			$this->setMappingLeftKey($beanId, $bean);
			$this->setMappingRightKey($linkedBeanId, $bean);
			$this->mappingDao->save($bean);
		}
	}
	
	/**
	 * Linked bean Id setter (sets the foreign key value for main bean)
	 * @param mixed $id
	 */
	public function setMappingRightKey($id, $bean){
		call_user_func(array($bean, $this->mappingRightKeySetter), $id);
	}
	
	public function getMappingRightKey($bean){
		return call_user_func(array($bean, $this->mappingRightKeyGetter));
	}
	
	public function getMappingId($bean){
		return call_user_func(array($bean, $this->mappingIdGetter));
	}
	
	public function getRelatedBeanId($bean){
		return call_user_func(array($bean, $this->linkedIdGetter));
	}
	
	public function getRelatedBeanLabel($bean){
		return call_user_func(array($bean, $this->linkedLabelGetter));
	}
	
	/**
	 * Sets the values to be saved during validation process.
	 * If everything went well, thses values will be used to perform saves and deletes
	 * @param mixed $values: the values to be set
	 */
	public function setValue($baseBean, $values){
		$this->saveValues = $values;
	}
	
	/**
	 * Returns the values avalable for the field
	 */
	public function getData(){
		return $this->data;
	}
	
	/**
	 * Returns the values already set for the field
	 */
	public function getBeanValues(){
		return $this->beanValues;
	}
	
	/**
	 * Returns the values to be set for the field
	 */
	public function getSaveValues(){
		return $this->saveValues== null ? array() : $this->saveValues;
	}
	
}