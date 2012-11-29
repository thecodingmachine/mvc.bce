<?php
namespace Mouf\MVC\BCE\classes;

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
	 * @var string
	 */
	public $label;

	/**
	 * The renderer that will be responsible for delivering the HTML for that field
	 * @Property
	 * @var FieldRendererInterface
	 */
	public $renderer;

	/**
	 * The validator of the field. Returns true/false
	 * @Property
	 * @var array<ValidatorInterface>
	 */
	public $validators;
	
	/**
	 * The javascript functions and calls of the form
	 * @var array<string, string>
	 */
	public $script = array();
	
	public function toHtml(){
		echo $this->getRenderer()->render($this);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see BCEFieldDescriptorInterface::getJS()
	 */
	public function getJS(){
		foreach ($this->renderer->getJS($this) as $scope => $script){
			$this->script[$scope][] = $script;
		}
		return $this->script;
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
	public function preSave($post = null, BCEForm &$form){
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
				if (!$validator->validate($value)){//TODO if return array (false, error), the tests passes ???
					$form->addError($this->fieldName, $validator->getErrorMessage());
				}
			}
		}
		
		//Set value context before saving
		$this->setValue($form->baseBean, $value);
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
	 * Get's the field's name (unique Id of the field inside a form (or name attribute)
	 */
	public function getFieldName(){
		return $this->fieldName;
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
	 * Returns the Renderer for that bean
	 */
	public function getRenderer(){
		return $this->renderer;
	}

	/**
	 * Returns the list of Validators of this field
	 */
	public function getValidators(){
		return $this->validators;
	}

	/**
	 * Returns the label of the field
	 */
	public function getFieldLabel(){
		return $this->label;
	}

	/**
	 * Returns the Formatter of the field
	 */
	function getFormatter(){
		return $this->formatter;
	}
}