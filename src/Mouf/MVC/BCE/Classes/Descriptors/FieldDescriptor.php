<?php 
namespace Mouf\MVC\BCE\Classes\Descriptors;

use Mouf\Utils\Value\ValueUtils;

use Mouf\Utils\Value\ValueInterface;

use Mouf\MoufManager;

use Mouf\MVC\BCE\Classes\ValidationHandlers\JSValidationData;

use Mouf\MVC\BCE\Classes\ValidationHandlers\JsValidationHandlerInterface;

use Mouf\Utils\Common\Validators\ValidatorInterface;

use Mouf\Utils\Common\Validators\JsValidatorInterface;

use Mouf\MVC\BCE\Classes\Renderers\ViewFieldRendererInterface;

use Mouf\Utils\Common\Formatters\BijectiveFormatterInterface;

use Mouf\Utils\Common\ConditionInterface\ConditionInterface;
use Mouf\MVC\BCE\Classes\Renderers\FieldRendererInterface;
use Mouf\MVC\BCE\FormRenderers\FieldWrapperRendererInterface;
use Mouf\MVC\BCE\BCEForm;
use Mouf\Utils\Common\Formatters\FormatterInterface;
/**
 * This class is the simpliest FieldDescriptor:
 * it handles a field that has no "connections" to other objects (
 * as user name or login for example)
 * @Component
 */
abstract class FieldDescriptor implements BCEFieldDescriptorInterface {

	/**
	 * Name of the field. This value must remain unique inside a form,
	 * it will be used for name and id attributes.
	 * @Property
	 * @var string
	 */
	public $fieldName;

	/**
	 * Optional formatter that will display a formatted value (example 2012-01-30 --> 01/30/2012).
	 * The formatter is also responsible for the reverse operation (01/30/2012 --> 2012-01-30).
	 * @Property
	 * @var FormatterInterface
	 */
	public $formatter;

	/**
	 * The label of the field as displayed in the form
	 * @Property
	 * @var string|ValueInterface
	 */
	public $label;
	
	/**
	 * The description of the field as displayed in the form
	 * @Property
	 * @var string
	 */
	public $description;

	/**
	 * The renderer that will be responsible for delivering the HTML for that field
	 * @Property
	 * @var FieldRendererInterface
	 */
	public $renderer;
	
	/**
	 * The renderer that will display the whole DOM associated to the field (label included)
	 * @Property
	 * @var FieldWrapperRendererInterface
	 */
	public $fieldWrapperRenderer;

	/**
	 * The validator of the field. Returns true/false
	 * @Property
	 * @var array<ValidatorInterface>
	 */
	public $validators = array();
	
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
	 * @var int
	 */
	public $beanId;
	
	/**
	 * 
	 * @var string|ValueInterface
	 */
	private $defaultValue;
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Descriptors\BCEFieldDescriptorInterface::addJS()
	 */
	public function addJS(BCEForm & $form, $bean, $id){
		foreach ($this->renderer->getJS($this, $form->mode, $bean, $id) as $scope => $script){
			$form->scriptManager->addScript($scope, $script);
		}
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
	public function preSave($post = null, BCEForm &$form, $bean){
		if (!$this->canEdit()){
			$value = $this->getDefaultValue();
		}else{
			if($post != null) {
				$value = isset($post[$this->getFieldName()]) ? $post[$this->getFieldName()] : null;
			} else {
				$value = get($this->getFieldName());
			}
			
			//unformat values
			$formatter = $this->getFormatter();
			if ($formatter && $formatter instanceof BijectiveFormatterInterface) {
				$value = $formatter->unformat($value);
			}
			
			//validate fields
			$validators = $this->getValidators();
			if (count($validators)){
				foreach ($validators as $validator) {
					/* @var $validator ValidatorInterface */
					if ($validator->validate($value) !== true){
						$form->addError($this->fieldName, $validator->getErrorMessage());
					}
				}
			}
		}
		
		//Set value context before saving
		
		$this->setValue($bean, $value);
	}
	
	/**
	 * Abstract method : sets the value(s) context before saving data.
	 *   - Base and FK Descriptors just set the value into the bean,
	 *   - M2M Decsriptors set the array of linked beans ID's 
	 * @param mixed $baseBean
	 * @param mixed $value
	 */
	public abstract function setValue($baseBean, $value);

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
		return $this->fieldName;
	}

	/**
	 * Returns the label of the field
	 */
	public function getFieldLabel(){
		return ValueUtils::val($this->label);
	}
	
	/**
	 * Set the label of the descriptor.
	 * 
	 * @param string $label
	 */
	public function setLabel($label){
		$this->label = $label;
	}

	/**
	 * Returns the description of the field
	 */
	public function getDescription(){
		return $this->description;
	}
	
	/**
	 * Sets the description of the field
	 */
	public function setDescription($description){
		$this->description = $description;
	}
	
	/**
	 * Returns the Renderer for that bean
	 * return FieldRendererInterface
	 */
	public function toHTML($descriptorInstance, $formMode){
		return $this->renderer->render($descriptorInstance, $formMode);
	}
	
	/**
	 * Returns the WrapperRenderer for that bean
	 * @return FieldWrapperRendererInterface
	 */
	public function getFieldWrapperRenderer(){
		return $this->fieldWrapperRenderer;
	}

	/**
	 * Returns the list of Validators of this field
	 */
	public function getValidators(){
		return $this->validators;
	}

	/**
	 * Set validators
	 * @param ValidatorInterface[] $validators
	 */
	public function setValidators($validators){
		$this->validators = $validators;
	}

	/**
	 * Add validators
	 * @param ValidatorInterface
	 */
	public function addValidator($validator){
		$this->validators[] = $validator;
	}

	/**
	 * Returns the Formatter of the field
	 */
	public function getFormatter(){
		return $this->formatter;
	}
	
	/**
	 * returns the default value casted as a string if needed
	 * @return string
	 */
	public function getDefaultValue(){
		return ValueUtils::val($this->defaultValue);
	}
	
	/**
	 * Sets the default value
	 * @param string|ValueInterface $value
	 */
	public function setDefaultValue($value){
		$this->defaultValue  = $value;
	}
}