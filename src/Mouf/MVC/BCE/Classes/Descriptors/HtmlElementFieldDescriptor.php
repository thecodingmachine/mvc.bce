<?php
namespace Mouf\MVC\BCE\Classes\Descriptors;

use Mouf\Html\HtmlElement\HtmlElementInterface;
use Mouf\MVC\BCE\BCEForm;

/**
 * This descriptor render a html element or a html string in the BCE form
 */
class HtmlElementFieldDescriptor implements BCEFieldDescriptorInterface {
	
	/**
	 * 
	 * @var HtmlElementInterface|string
	 */
	private $html;

	/**
	 * The Condition to respect in order to be allowed to edit the field
	 * @Property
	 * @var ConditionInterface
	 */
	public $editCondition;

	/**
	 * The Condition to respect in order to be allowed to view the field
	 * @Property
	 * @var ConditionInterface
	 */
	public $viewCondition;
	
	/**
	 * 
	 * @param HtmlElementInterface|string $html
	 */
	public function __construct($html) {
		$this->html = $html;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Descriptors\BCEFieldDescriptorInterface::addJS()
	 */
	public function addJS(BCEForm & $form, $bean, $id){
	}
	
	/**
	 * (non-PHPdoc)
	 * For a FieldDecsriptor instance, the preSave function id responsible for :
	 *  - unformatting the posted value
	 *  - valideting the value
	 *  - setting the value into the bean (case of BaseFieldDescriptors)
	 *  - settings the linked ids to associate in mapping table (Many2ManyFieldDEscriptors)
	 * @see BCEFieldDescriptorInterface::preSave()
	 */
	public function preSave($post = null, BCEForm &$form, $bean, $isNew){
	}

	/**
	 * (non-PHPdoc)
	 * @see BCEFieldDescriptorInterface::postSave()
	 */
	public function postSave($bean, $beanId, $postValues = null){
		return;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Descriptors\BCEFieldDescriptorInterface::load()
	 */
	public function load($bean, $id = null, &$form = null) {
		$descriptorInstance = new FieldDescriptorInstance($this, $form, $id);
		return $descriptorInstance;
	}
	
	/**
	 * Tells if the field is editable
	 * @return boolean
	*/
	public function canEdit(){
		return $this->editCondition === null || $this->editCondition->isOk();
	}
	
	/**
	 * Tells if the field's value can be viewed
	 * @return boolean
	 */
	public function canView(){
		return $this->viewCondition === null || $this->viewCondition->isOk();
	}
	
	/**
	 * Get's the field's name (unique Id of the field inside a form (or name attribute)
	 */
	public function getFieldName(){
		return '';
	}
	
	/**
	 * Returns the label of the field
	 */
	public function getFieldLabel(){
		return '';
	}
	
	/**
	 * returns the default value casted as a string if needed
	 * @return string
	 */
	public function getDefaultValue(){
		return '';
	}
	
	/**
	 * Display the html link add to the class
	 */
	public function toHTML($descriptorInstance, $formMode){
		if(is_object($this->html)) {
			echo $this->html->toHtml();
		}
		else {
			echo $this->html;
		}
	}
}