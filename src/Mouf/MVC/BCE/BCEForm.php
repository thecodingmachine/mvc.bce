<?php
namespace Mouf\MVC\BCE;
use Mouf\Html\HtmlElement\HtmlElementInterface;

use Mouf\MVC\BCE\classes\FieldDescriptor;
use Mouf\Database\DAOInterface;
use Mouf;
use Mouf\MVC\BCE\classes\BCEFieldDescriptorInterface;

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
class BCEForm implements HtmlElementInterface {
	
	/**
	 * The main bean of the form, i.e. the object that define the edited data in the form
	 *
	 */
	public $baseBean;
	
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
	public $scripts = array();
	
	
	
	/**
	 * Load the main bean of the Form, and then the linked descriptors to display bean values
	 * @param mixed $id: The id of the bean (may be null for new objects)
	 */
	public function load($id = null){
		//Intantiate form's main bean (like JAVA Spring's formBindingObject), if ot is an existing one
		$this->baseBean = $id ? $this->mainDAO->getById($id) :  null;
		//Load bean values into related field Descriptors
		$this->idFieldDescriptor->load($this->baseBean, $id, $this);
		foreach ($this->fieldDescriptors as $descriptor) {
			/* @var $descriptor FieldDescriptor */
			$descriptor->load($this->baseBean, $id, $this);
			if ($descriptor instanceof FieldDescriptor) {
				$this->validationHandler->buildValidationScript($descriptor, $this->attributes['id']);
			}
			$this->loadScripts($descriptor);
			
			// Create an array of field descriptor by name
			$this->fieldDescriptorsByName[$descriptor->getFieldName()] = $descriptor;
		}
		
		//Instantiate new bean (after because of TDBM's constraint to trigger complete save when getting other objects, like FKDaos List methods)
		if ($id == null){
			$this->baseBean = $this->mainDAO->create();
		}
		
		//Load required libraries
		Mouf::getDefaultWebLibraryManager()->addLibrary($this->renderer->getSkin());
		Mouf::getDefaultWebLibraryManager()->addLibrary($this->validationHandler->getJsLibrary());
	}
	
	/**
	 * Returns the JS validation strings of the form in HTML
	 * @return string
	 */
	public function getValidationJS(){
		$js = $this->validationHandler->getValidationJs($this->attributes['id']);
		$js .= $this->renderScripts();
		
		return $js;
	}
	
	public function renderScripts(){
		$jsPrefix = $jsSuffix = $js = "";
		
		foreach ($this->scripts as $scope => $values){
			switch ($scope) {
				case "ready":
					$jsPrefix = "
						$(document).ready(function(){";
					$jsSuffix = "
						});";
				break;
				case "load":
					$jsPrefix = "
						$(window).ready(function(){";
					$jsSuffix = "
						});";
				break;
				case "unload":
					$jsPrefix = "
						$(window).ready(function(){";
					$jsSuffix = "
						});";
				break;
			}
			foreach ($values as $value){
				$js .= "
					$value
				";
			}
		}
		
		return $jsPrefix . $js . $jsSuffix;
	}
	
	/**
	 * Outputs the form's HTML
	 */
	public function toHTML(){
		//Render the form
		$this->renderer->render($this);
	}
	
	public function loadScripts($descriptor){
		foreach ($descriptor->getJs($descriptor) as $scope => $scripts){
			foreach ($scripts as $script){
				$this->scripts[$scope][] = $script;
			}
		}
	}
	
	/**
	 * Make the save form action.
	 * 
	 * @param array $postValues
	 */
	public function save($postValues = null){
		if($postValues != null) {
			$id = $postValues[$this->idFieldDescriptor->getFieldName()];
		} else {
			$id = get($this->idFieldDescriptor->getFieldName());
		}
		$this->baseBean = empty($id) ? $this->mainDAO->create() : $this->mainDAO->getById($id);
		
		foreach ($this->fieldDescriptors as $descriptor) {
			$descriptor->preSave($postValues, $this);
		}
		if (!count($this->errorMessages)){
			//save the main bean
			$this->mainDAO->save($this->baseBean);
			
			$id = $this->getMainBeanId();//Get bean Id after save if it's an add
			//Now call the postSave scripts (important for M2M descs for example)
			foreach ($this->fieldDescriptors as $descriptor){ 
				$descriptor->postSave($this->baseBean, $id);
			}
			
			return $id;
		}else{
			return false;
		}
	}
	
	/**
	 * Gets the id of the bean
	 * @param mixed $id the old id of the bean
	 */
	private function getMainBeanId(){
		$this->idFieldDescriptor->load($this->baseBean);
		return $this->idFieldDescriptor->getFieldValue();
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
}