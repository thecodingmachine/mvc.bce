<?php
namespace Mouf\MVC\BCE\Classes\Descriptors;

use Mouf\Database\DAOInterface;

use Mouf\Html\Widgets\FileUploaderWidget\SimpleFileUploaderWidget;

use Mouf\MVC\BCE\BCEForm;

/**
 * This class is the simpliest FieldDescriptor:
 * it handles a field that has no "connections" to other objects (
 * as user name or login for example)
 * @Component
 */
class FileMultiUploaderFieldDescriptor extends FieldDescriptor {

	/**
	 * @Property
	 * @var DAOInterface
	 */
	public $fileDao;

	/**
	 * Name of the method that returns the beans of the mapping table that are already linked to the main bean
	 * @Property
	 * @var string
	 */
	public $filePathMethod;

	public $filePathGetter;
	public $filePathSetter;

	public $fkSetter;
	
	/**
	 * The value of the field once the FiedDescriptor has been loaded
	 * @var mixed
	 */
	public $values;

	/**
	 * Folder where the file will be saved. "resources" by default
	 * @var string
	 */
	public $folder = 'resources';

	/**
	 * 
	 * 
	 * @Property
	 * @var SimpleFileUploaderWidget
	 */
	public $fileUploaderWidget;
	

	/**
	 * Load main bean's values and avalable ones
	 * @param mixed $mainBeanId the id of the main bean
	 *
	 * @see BCEFieldDescriptorInterface::load()
	 */
	public function load($bean, $mainBeanId = null, &$form = null){
		$this->loadValues($mainBeanId);
	
		$descriptorInstance = new FieldDescriptorInstance($this, $form, $mainBeanId);
		$values = array();
		if($this->values) {
			foreach ($this->values as $bean){
				$values[] = call_user_func(array($bean, $this->filePathGetter));
			}
		}
		
		$descriptorInstance->value = $values;
		return $descriptorInstance;
	}
	
	/**
	 * Loads the values of the bean
	 * These values are stored in an associative array, the key being the linked bean's Id
	 * @param mixed $mainBeanId the id of the main bean
	 */
	public function loadValues($mainBeanId){
		$tmpArray = call_user_func(array($this->fileDao, $this->filePathMethod), $mainBeanId);
		foreach ($tmpArray as $bean){
			$this->values[] = $bean;
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
	public function preSave($post, BCEForm &$form, $bean) {
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see BCEFieldDescriptorInterface::postSave()
	 */
	public function postSave($bean, $beanId, $postValues) {
		$this->loadValues($beanId);
		// Retrieve post to check if the user delete a file
		if($postValues != null) {
			$removes = isset($postValues['remove-file-upload-'.$this->getFieldName()]) ? $postValues[$this->getFieldName()] : null;
		} else {
			$removes = get('remove-file-upload-'.$this->getFieldName());
		}
		if($removes) {
			foreach ($this->values as $bean) {
				$fileSave = call_user_func(array($bean, $this->filePathGetter));
				foreach ($removes as $file) {
					if($file == $fileSave) {
						unlink(ROOT_PATH.$file);
						$this->fileDao->delete($bean);
					}
				}
			}
		}
		if($this->fileUploaderWidget->hasFileToMove($this->getFieldName())) {
			$folder = $this->folder.DIRECTORY_SEPARATOR.$beanId;
			
			$fileList = $this->fileUploaderWidget->moveFile($this->getFieldName(), ROOT_PATH.$folder);

			foreach ($fileList as $file) {
				$bean = $this->fileDao->create();
				call_user_func(array($bean, $this->filePathSetter),  $folder.'\\'.$file);
				call_user_func(array($bean, $this->fkSetter), $beanId);
				$this->fileDao->save($bean);
				
			}
		}
		return;
	} 
	
	/**
	 * Simply calls the setter of the descriptor's related field into the bean
	 * @param mixed $mainBean
	 * @param mixed $value
	 */
	public function setValue($mainBean, $value) {
		call_user_func(array($mainBean, $this->setter), $value);
	}
	
	public function getValue($mainBean){
		if ($mainBean == null){
			$fieldValue = null;
		}else{
			$fieldValue = call_user_func(array($mainBean, $this->filePathGetter));
		}
		return $fieldValue;
	}
	


	/**
	 * Returns the label of the field
	 */
	public function getFieldLabel() {
		return $this->label;
	}

	/**
	 * Returns the instance of FileUploaderWidget
	 */
	public function getFileUploaderWidget() {
		return $this->fileUploaderWidget;
	}

}