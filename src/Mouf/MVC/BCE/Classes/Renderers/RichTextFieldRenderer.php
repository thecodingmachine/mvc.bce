<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

use Mouf\MVC\BCE\Classes\ScriptManagers\ScriptManager;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;
use Mouf\MVC\BCE\Classes\ValidationHandlers\BCEValidationUtils;
use Mouf\Html\Widgets\Form\TextAreaField;

/**
 * Base class for rendering rich text fields using CKEditor
 */
class RichTextFieldRenderer extends TextAreaFieldRenderer implements SingleFieldRendererInterface {
	
	/**
	 * Custom configuration file, relative to the ROOT_URL
	 * @var string
	 */
	protected $customConfigFile;
	
	protected $allowedContent;
	
	/**
	 * Custom configuration file, relative to the ROOT_URL
	 * @param string $customConfigFile
	 */
	public function setCustomConfigfile($customConfigFile) {
		$this->customConfigFile = $customConfigFile;
		return $this;
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::getJS()
	 */
	public function getJSEdit($descriptor, $bean, $id){
		/* @var $descriptor BCEFieldDescriptorInterface */
		$fieldName = $descriptor->getFieldName();
		
		$config = [
			"allowedContent" => true,
			"language" => "en"
		];
		if ($this->customConfigFile) {
			$config['customConfig'] = ROOT_URL.$this->customConfigFile;
		}
		
		return array(ScriptManager::SCOPE_READY => "CKEDITOR.replace( '$fieldName', ".json_encode($config)." );");
	}
	
	
	
}