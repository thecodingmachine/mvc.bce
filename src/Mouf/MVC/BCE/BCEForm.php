<?php
namespace Mouf\MVC\BCE;

use Mouf\MVC\BCE\Classes\ValidationHandlers\JsValidationHandlerInterface;
use Mouf\MVC\BCE\FormRenderers\BCERendererInterface;

use Mouf\MVC\BCE\Classes\Descriptors\BaseFieldDescriptor;

use Mouf\MVC\BCE\Classes\ScriptManagers\ScriptManager;
use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;
use Mouf\Html\Utils\WebLibraryManager\InlineWebLibrary;
use Mouf\MVC\BCE\Classes\Descriptors\BCEFieldDescriptorInterface;
use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptor;
use Mouf\Database\DAOInterface;
use Mouf;
/**
 * 
 * Root Object of the BCE package.<br/>
 * <br/>
 * This component composes of:<br/>
 * <ul>
 *   <li>a main DAO, that will perform data access and persistence</li>
 *   <li>a set of fied descriptors, that define the fields of the form</li> 
 *   <li>a renderer that will generate the form's HTML output (sort of a template)</li>
 *   <li>a javascript validation handler that will generate the client side validation script</li>
 * </ul>
 * @Component
 * @ExtendedAction {"name":"Configure Form", "url":"bceadmin/", "default":true}
 * @author Kevin
 *
 */
class BCEForm {
	
	/**
	 * Field Decriptors define which fields avaiable through the main DAO should be involved in the form.<br/>
	 * They define a lot of data<br/>
	 * 
	 * @Property 
	 * @var array<BCEFieldDescriptorInterface>
	 */
	public $fieldDescriptors = array();
	
	/**
	 * Field Decriptors by name define which fields avaiable through the main DAO should be involved in the form.<br/>
	 * They define a lot of data<br/>
	 * 
	 * @var array<BCEFieldDescriptorInterface>
	 */
	public $fieldDescriptorsByName = array();
	
	/**
	 * Field Decriptors of the bean's identifier.
	 * This is a special field because the id will help to retrive bean before saving it.
	 * 
	 * @Property 
	 * @var BaseFieldDescriptor
	 */
	public $idFieldDescriptor;
	
	/**
	 * The DAO reponsible of retrieving bean data and persist them
	 * 
	 * @Property
	 * @var DAOInterface
	 */
	public $mainDAO;
	
	/**
	 * The template used to render the form
	 * 
	 * @Property
	 * @var BCERendererInterface
	 */
	public $renderer;
	
	/**
	 * This object is responsible of generating the javascript validation code of the fields and the form 
	 * 
	 * @Property
	 * @var JsValidationHandlerInterface
	 */
	public $validationHandler;
	
	/**
	 * The action attribute of the form 
	 * @Property
	 * @var string
	 */
	public $action = "save";
	
	/**
	 * The submit method
	 * 
	 * @Property
	 * @var string
	 */
	public $method = "POST";
	
	/**
	 * The save button label 
	 * @Property
	 * @var string
	 */
	public $saveLabel = "Save changes";
	
	/**
	 * The cancel button label
	 * 
	 * @Property
	 * @var string
	 */
	public $cancelLabel = "Cancel";
	
	/**
	 * The attributes of the form
	 *
	 * @Property
	 * @var array<string, string>
	 */
	public $attributes = array(
		"id" => "default_id",
		"name" => "default_name",
		"accept-charset" => "UTF-8",
		"class" => "",
		"enctype" => "application/x-www-form-urlencoded"
	);
	
	/**
	 * The errors returned by the fields' validators
	 * @var array<string>
	 */
	public $errorMessages;
	
	/**
	 * The js scripts added by the renderers
	 * @var array<string>
	 */
	public $scripts = array();//TODO add a JSFormHandler for handling all scripts

	/**
	 * The instance that will handle scripts aggregation and rendering
	 * @var ScriptManager
	 */
	public $scriptManager;//TODO add a JSFormHandler for handling all scripts
	
	/**
	 * The mode (edit or view) of the form.
	 * <ul>
	 * 	<li><b>Edit mode<b></li> will allow user to modify the bean handled by the Form (if fields' edit conditions are satisfied)
	 * 	<li><b>View mode<b></li> is simply a mode for reading form's bean fields.
	 * <ul> 
	 * 
	 * @OneOf "edit","view"
	 * @var string
	 */
	public $mode = "edit";
	
	/**
	 * @var boolean
	 */
	public $isMain = true;
	
	
	/**
	 * Load the main bean of the Form, and then the linked descriptors to display bean values
	 * @param mixed $id: The id of the bean (may be null for new objects)
	 */
	public function load($bean, $id = null){
		$descriptorInstances = array();

		//Load bean values into related field Descriptors
		$idDescriptorInstance = $this->idFieldDescriptor->load($bean, $id, $this, true);
		if (!$id){
			$id = $this->idFieldDescriptor->getValue($bean);
		}
		
		foreach ($this->fieldDescriptors as $descriptor) {
			/* @var $descriptor FieldDescriptor */
			if (!$descriptor->canEdit() && !$descriptor->canView()){
				continue;
			}
			
			$descriptorInstance = $descriptor->load($bean, $id, $this);
			$descriptorInstance->addValidationData($this->validationHandler);
			$descriptor->addJS($this, $bean, $id);
			
			// Create an array of field descriptor by name
			$this->fieldDescriptorsByName[$descriptor->getFieldName()] = $descriptor;
			
			$descriptorInstances[$descriptor->getFieldName()] = $descriptorInstance;
		}
		
		
		//Instantiate new bean (after because of TDBM's constraint to trigger complete save when getting other objects, like FKDaos List methods)
		if ($id == null){
			$bean = $this->mainDAO->create();
		}
		
		if ($this->isMain){
			$this->validationHandler->addJS($this);
			$this->loadScripts();
		}
		return array($idDescriptorInstance, $descriptorInstances);
	}
	
	public function loadScripts(){
		//Load required libraries
		$lib = new InlineWebLibrary();
		$lib->setJSFromText($this->scriptManager->renderScripts());
		Mouf::getDefaultWebLibraryManager()->addLibrary($lib);
		Mouf::getDefaultWebLibraryManager()->addLibrary($this->renderer->getSkin());
		Mouf::getDefaultWebLibraryManager()->addLibrary($this->validationHandler->getJsLibrary());
	}
	
	public function setAttribute($attributeName, $value){
		if (array_key_exists($attributeName, $this->attributes)){
			$this->attributes[$attributeName] = $value;
		}else{
			throw new \Exception("Attribute '$attributeName' of BCEForm can not be set...");
		}
	}
	
	
	public function addError($fieldName, $errorMessage){
		$this->errorMessages[$fieldName][] = $errorMessage;
	}
	
	/**
	 * Get a field descriptor with its name.
	 * 
	 * @param string $name
	 * @return BCEFieldDescriptorInterface or null
	 */
	public function getFieldDescriptorByName($name) {
		if(array_key_exists($name, $this->fieldDescriptorsByName))
			return $this->fieldDescriptorsByName[$name];
		else
			return null;
	}
	
	public function getMode(){
		return $this->mode; 
	}
	
	/**
	 * Make the save form action.
	 *
	 * @param array $postValues
	 */
	public function save($postValues = null, $bean = null){
		if($postValues != null) {
			$id = $postValues[$this->idFieldDescriptor->getFieldName()];
		} else {
			$id = get($this->idFieldDescriptor->getFieldName());
		}
		$bean = $bean ? $bean : (empty($id) ? $this->mainDAO->create() : $this->mainDAO->getById($id));
	
		foreach ($this->fieldDescriptors as $descriptor) {
			$descriptor->preSave($postValues, $this, $bean);
		}
		if (!count($this->errorMessages)){
			//save the main bean
			$this->mainDAO->save($bean);
				
			$id = $this->idFieldDescriptor->getValue($bean);//Get bean Id after save if it's an add
			//Now call the postSave scripts (important for M2M descs for example)
			foreach ($this->fieldDescriptors as $descriptor){
				if (!$descriptor->canEdit()){
					continue;
				}
				$descriptor->postSave($bean, $id, $postValues);
			}
				
			return $id;
		}else{
			return false;
		}
	}

}