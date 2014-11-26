<?php
namespace Mouf\MVC\BCE;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;

use Mouf\Html\HtmlElement\HtmlElementInterface;
use Mouf\MVC\BCE\Classes\BCEException;

class BCEFormInstance implements HtmlElementInterface{
		
	/**
	 * @var BCEForm
	 */
	public $form;
	
	/**
	 * 
	 * @var FieldDescriptorInstance[]
	 */
	public $descriptorInstances;
	
	/**
	 * 
	 * @var FieldDescriptorInstance
	 */
	public $idDescriptorInstance;
	
	
	/**
	 * The main bean of the form, i.e. the object that define the edited data in the form
	 */
	public $baseBean;
	
	/**
	 * The id of the main bean of the form
	 */
	public $beanId;
	
	
	public function load($id = null){
		$this->baseBean = $id ? $this->form->mainDAO->getById($id) :  null;
		$this->beanId = $id;
		$this->loadBean();
	}
	
	public function loadBean(){
		list( $this->idDescriptorInstance, $this->descriptorInstances ) = $this->form->load($this->baseBean, $this->beanId);
	}
	
	public function toHtml(){
		if($this->idDescriptorInstance == null){
			throw new BCEException("BCEFormInstance::load() method must be called before calling BCEFormInstance::toHtml()");
		}else{
			$this->form->renderer->render($this->form, $this->descriptorInstances, $this->idDescriptorInstance);
		}
	}
	
	/**
	 * Make the save form action.
	 *
	 * @param array $postValues
	 */
	public function save($postValues = null){
		return $this->form->save($postValues);
	}
	
	/**
	 * @param string $fieldName
	 * @return FieldDescriptorInstance
	 */
	public function getDescriptorInstance($fieldName){
		return $this->descriptorInstances[$fieldName];
	}
	
	
}