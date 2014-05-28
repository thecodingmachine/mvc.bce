<?php
namespace Mouf\MVC\BCE\Classes\Descriptors;

use Mouf\Database\DAOInterface;

use Mouf\Html\Widgets\FileUploaderWidget\SimpleFileUploaderWidget;

use Mouf\MVC\BCE\BCEForm;

/**
 * This class is used to manage the upload of multiple files in a bean.
 */
class FileMultiUploaderFieldDescriptor extends FieldDescriptor {

	/**
	 * The DAO pointing to the table that contains the list of files associated with the main bean.
	 * 
	 * @var DAOInterface
	 */
	public $fileDao;

	/**
	 * Name of the method of fileDao that returns the beans of the mapping table that are already linked to the main bean.
	 * The method signature is:
	 * 	<pre>function filePathMethod($beanId)</pre>
	 * where $beanId is the id of the main bean.
	 * 
	 * @var string
	 */
	public $filePathMethod;

	/**
	 * Name of the getter of the file bean used to retrieve the file name.
	 * The path returned by this getter must be relative to ROOT_PATH.
	 * The path must not start with a /.
	 * 
	 * @var string
	 */
	public $filePathGetter;
	
	/**
	 * Name of the setter of the file bean used to set the file name.
	 * The parameter passed to this setter must be relative to ROOT_PATH.
	 * The path must not start with a /.
	 *
	 * @var string
	 */
	public $filePathSetter;

	/**
	 * The name of the setter of the file bean that will set the ID of the main bean.
	 * The parameter passed to this setter is the ID of the main bean.
	 * 
	 * @var string
	 */
	public $fkSetter;
	
	/**
	 * The value of the field once the FiedDescriptor has been loaded
	 * @var mixed
	 */
	public $values;

	/**
	 * Folder where the file will be saved. "resources" by default. Relative to ROOT_PATH.
	 * @var string
	 */
	public $folder = 'resources';

    /**
     * Whether we should set in the bean the relative path to $folder or the relative path to ROOT_PATH
     * @var bool
     */
    public $saveAbsolutePath = true;

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
	public function preSave($post, BCEForm &$form, $bean, $isNew) {
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
		
		$uniqueId = get('document');

		if($this->fileUploaderWidget->hasFileToMove($this->getFieldName()) && !$this->fileUploaderWidget->noTemporarySave) {
			$folder = $this->folder.DIRECTORY_SEPARATOR.$beanId;
			
			$fileList = $this->fileUploaderWidget->moveFile($this->getFieldName(), ROOT_PATH.$folder);

			foreach ($fileList as $file) {
				$bean = $this->fileDao->create();
                if($this->saveAbsolutePath){
                    call_user_func(array($bean, $this->filePathSetter), $folder.DIRECTORY_SEPARATOR.$file);
                }else{
                    call_user_func(array($bean, $this->filePathSetter), $file);
                }
				call_user_func(array($bean, $this->fkSetter), $beanId);
				$this->fileDao->save($bean);
				
			}
		}
		// If there isn't data in post value. Retrieve session to save the file list.
		else if($uniqueId && isset($_SESSION["mouf_fileupload_autorizeduploads"][$uniqueId]['files']) && $this->fileUploaderWidget->noTemporarySave) {
			foreach ($_SESSION["mouf_fileupload_autorizeduploads"][$uniqueId]['files'] as $file) {

				$bean = $this->fileDao->create();
				call_user_func(array($bean, $this->filePathSetter),  $file);
				call_user_func(array($bean, $this->fkSetter), $beanId);
				$this->fileDao->save($bean);
			}
		}
		unset($_SESSION["mouf_fileupload_autorizeduploads"][$uniqueId]);
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