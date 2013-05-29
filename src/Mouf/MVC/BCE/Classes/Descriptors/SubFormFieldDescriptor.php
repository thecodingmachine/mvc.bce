<?php
namespace Mouf\MVC\BCE\Classes\Descriptors;

use Mouf\MVC\BCE\FormRenderers\SubFormItemWrapperInterface;
use Mouf\MVC\BCE\Classes\ScriptManagers\ScriptManager;
use Mouf\MVC\BCE\FormRenderers\FieldWrapperRendererInterface;
use Mouf\MVC\BCE\BCEFormInstance;
use Mouf\MVC\BCE\BCEForm;

/**
 * This class is the simpliest FieldDescriptor:
 * it handles a field that has no "connections" to other objects (
 * as user name or login for example)
 * @Component
 */
class SubFormFieldDescriptor implements BCEFieldDescriptorInterface {
	
	/**
	 * @var string
	 */
	public $fieldName;
	
	/**
	 * @var string
	 */
	public $fieldLabel;
	
	/**
	 * The description of the field as displayed in the form
	 * @Property
	 * @var string
	 */
	public $description;
	
	/**
	 * @var BCEForm
	 */
	public $form;
	
	/**
	 * @var BCEFormInstance[]
	 */
	private $formInstances = array();
	
	/**
	 * @var BCEFormInstance
	 */
	private $emptyFormInstance = array();

	/**
	 * @var string
	 */
	public $beansGetter;
	
	/**
	 * @var string
	 */
	public $fkGetter;
	
	/**
	 * @var string
	 */
	public $fkSetter;
	
	/**
	 * @var mixed $bean
	 */
	private $beans = array();
	
	/**
	 * The renderer that will display the whole DOM associated to the field (label included)
	 * @Property
	 * @var SubFormFieldWrapperRendererInterface
	 */
	public $fieldWrapperRenderer;
	
	/**
	 * The renderer that will display each item (sub form) individually
	 * @Property
	 * @var SubFormItemWrapperInterface
	 */
	public $itemWrapperRenderer;
	
	/**
	 * The name of the subForm
	 * @see \Mouf\MVC\BCE\Classes\Descriptors\BCEFieldDescriptorInterface::getFieldName()
	 */
	public function getFieldName(){
		return $this->fieldName;
	}
	
	/**
	 * The label of the subform section in the mainform
	 * @see \Mouf\MVC\BCE\Classes\Descriptors\BCEFieldDescriptorInterface::getFieldLabel()
	 */
	public function getFieldLabel(){
		return $this->fieldLabel;
	}
	
	public function load($bean, $id = null, &$form = null){
		//$bean will contain the parent's id go get the child beans
		$this->getBeans($bean, $id, $form);
		$this->form->validationHandler = $form->validationHandler;
		$this->form->scriptManager = $form->scriptManager;
		$this->form->isMain = false;
		$this->form->mode = $form->mode;
		foreach ($this->beans as $bean){
			$formInstance = new BCEFormInstance();
			$formInstance->form = $this->form;
			$formInstance->baseBean = $bean;
			$formInstance->loadBean();
			$formInstance->beanId = $formInstance->idDescriptorInstance->getFieldValue();
			$formInstance->idDescriptorInstance->setContainerName($this->getFieldName());
			foreach ($formInstance->descriptorInstances as $descInstance){
				/* @var $descInstance FieldDescriptorInstance */
				$descInstance->setContainerName($this->getFieldName());
			}
			$this->formInstances[] = $formInstance;
		}
		
		$this->emptyFormInstance = new BCEFormInstance();
		$this->emptyFormInstance->form = $this->form;
		$this->emptyFormInstance->baseBean = $this->getEmptyBean();
		$this->emptyFormInstance->beanId = 'a';
		$this->emptyFormInstance->loadBean();
		$this->emptyFormInstance->idDescriptorInstance->setContainerName($this->getFieldName());
		foreach ($this->emptyFormInstance->descriptorInstances as $descInstance){
			/* @var $descInstance FieldDescriptorInstance */
			$descInstance->setContainerName($this->getFieldName());
		}
		
		$descriptorInstance = new FieldDescriptorInstance($this, $form, $id);
		$descriptorInstance->value = $this->beans;
		return $descriptorInstance;
	}
	
	private function getBeans($bean, $id = null, $form = null){
		$this->beans = call_user_func(array($this->form->mainDAO, $this->beansGetter), $bean);
	}
	
	private function getEmptyBean(){
		return call_user_func(array($this->form->mainDAO, 'create'));
	}
	
	
	public function addJS(BCEForm & $form){
		
		$script = "
			var new".$this->fieldName."_index = 1;
			
			function ".$this->getAddItemFonction()."{
				var  fieldId = '".$this->fieldName."';
				var template = $('#' + fieldId + '_template');
				var html = template.text(); 
				while(html.indexOf('\\[a\\]') != -1){
					html = html.replace('\\[a\\]','[__bce__add_' + new".$this->fieldName."_index + ']');
				}
				template.before( html );
				
				$('.subform-wrapper.' + fieldId + ' .subform-item').each(function(index){
					$(this).addClass(index % 2 == 0 ? 'odd' : 'even');
				});
				
				new".$this->fieldName."_index ++;
				
				return false;
			}
		";
		$form->scriptManager->addScript(ScriptManager::SCOPE_WINDOW, $script);
		
		list($scope, $script) = $this->itemWrapperRenderer->getRemoveItemJS();
		$form->scriptManager->addScript($scope, $script);
	}
	
	public function getAddItemFonction(){
		return "add_$this->fieldName"."_item()";
	}
	
	public function getRemoveItemFonction(){
		return "remove_$this->fieldName"."_item()";
	}
	
	/**
	 * Returns the Renderer for that bean
	 * return FieldRendererInterface
	 */
	public function toHTML($descriptorInstance, $formMode){
		ob_start();
		$index = "odd";
		foreach ($this->formInstances as $formInstance){
			$this->itemWrapperRenderer->toHtml($this, $formInstance, $index);
			$index = $index == "odd" ? "even" : "odd";
		}
		echo "<textarea style='display: none' id='" . $this->getFieldName() . "_template'>";
		$this->itemWrapperRenderer->toHtml($this, $this->emptyFormInstance);
		echo "</textarea>";
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
	
	public function getFieldWrapperRenderer(){
		return $this->fieldWrapperRenderer;
	}
	
	public function preSave($post, BCEForm &$form, $bean){
		//TODO if needed
	}
	
	public function postSave($parentBean, $parentBeanId, $postValues = null){
		$data = get($this->getFieldName());
		foreach ($data as $key => $values){
			$isAdd = substr($key, 0, 11) == "__bce__add_";
			if ($values['__bce__delete'] == 0){
				$bean = $isAdd ? $this->form->mainDAO->create() : $this->form->mainDAO->getById($key);
				$this->setParentFK($bean, $parentBeanId); 
				$this->form->save($values, $bean);
			}else if (!$isAdd){
				$this->form->mainDAO->delete($bean);
			}
		}
	}
	
	private function setParentFK(& $bean, $parentBeanId){
		call_user_func(array($bean, $this->fkSetter), $parentBeanId);
	}
	
	/**
	 * Tells if the field is editable
	 * @return boolean
	 */
	public function canEdit(){
		return true;
	}
	
	/**
	 * Tells if the field's value can be viewed
	 * @return boolean
	 */
	public function canView(){
		return true;
	}
	
}