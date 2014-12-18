<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

use Mouf\MoufManager;
use Mouf\Html\Widgets\Form\FileUploaderField;
use Mouf\MVC\BCE\Classes\ValidationHandlers\BCEValidationUtils;
use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;

/**
 * This renderer handles date / timestamp input fields with the jQuery DatePicker
 * @ApplyTo { "php" :[ "timestamp", "datetime", "date" ] }
 * @Component
 */
class FileUploaderRenderer extends DefaultViewFieldRenderer implements SingleFieldRendererInterface {
	
	/**
	 * 
	 * @var bool
	 */
	public $onlyOneFile;

	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::render()
	 */
	public function renderEdit($descriptorInstance){
		$descriptor = $descriptorInstance->fieldDescriptor;
		/* @var $descriptor FileUploaderFieldDescriptor */
		$fieldName = $descriptor->getFieldName();
		$values = $descriptorInstance->getFieldValue();
		if(!is_array($values)) {
			$values = array($values);
		}

		if($values) {
			// set has value or not...
		}
		
		$fileUploader = $descriptor->getFileUploaderWidget();
		$fileUploader->directory = $descriptor->folder;
		if($this->onlyOneFile) {
			$fileUploader->multiple = false;
			$fileUploader->onlyOneFile = true;
		}
		else {
			$fileUploader->multiple = true;
			$fileUploader->onlyOneFile = false;
		}
		
		$fileUploader->inputName = $fieldName;
		if($descriptorInstance->getValidator()) {
			$fileUploader->setInputClasses($descriptorInstance->getValidator());
		}

		$fileUploaderField = new FileUploaderField($descriptor->getFieldLabel(), $fileUploader, $values);
		$fileUploaderField->setRequired(BCEValidationUtils::hasRequiredValidator($descriptorInstance->fieldDescriptor->getValidators()));
		
		ob_start();
		$fileUploaderField->toHtml();
		return ob_get_clean();
		
		/*
		$html = '';
                $scriptVals = array();
		if($values) {
			$html = '<table>';
			$i = 0;
			foreach ($values as $value) {
				if($value) {
					$html .= '<tr class="file-upload-'.$fieldName.'-'.$i.'">';
						$html .= '<td>';
							$ext = substr($value, strrpos($value, '.') + 1);
							$fileUrl = ROOT_URL.str_replace('\\', '/', $value);
							if($ext == 'jpeg' || $ext == 'jpg' || $ext == 'gif' || $ext == 'png'){
								$html .= '<img src="'.$fileUrl.'" style="width: 50px; height: 50px" /> (<a href="'.$fileUrl.'">link here</a>)';
							}else
								$html .= $value . "(<a href='$fileUrl'>link here</a>)";
						$html .= '</td>';
						$html .= '<td>';
							$html .= '<a href="#" onclick="return removeFileUpload_'.$fieldName.'('.$i.', \''.str_replace('\\', '\\\\', $value).'\')">remove</a>';
						$html .= '</td>';
					$html .= '</tr>';
					
					$paths = explode(DIRECTORY_SEPARATOR, $value);
					$scriptVals[ROOT_URL.str_replace(DIRECTORY_SEPARATOR, "/", $value)] = $paths[count($paths) - 1];
					
					$i ++;
				}
			}
			
			$html .= '</table>';
			$html .= '<div id="remove-file-upload-'.$fileUploader->inputName.'"></div>';
			$html .= '
				<script>
					if (typeof bce_files === "undefined"){
						bce_files = {};
					}
					bce_files = $.extend(bce_files, '.json_encode($scriptVals).');
				</script>';
		}
		*/
		return $html.$fileUploader->returnHtmlString();
		
	}
	
	/**
	 * (non-PHPdoc)
	 * The datepicker depends on jQueryUI's datepicker widget, therefore load the library into the WebLibrary manager, and call the datepicker initialization on dom ready
	 * @see FieldRendererInterface::getJS()
	 */
	public function getJSEdit($descriptor, $bean, $id){
		/* @var $descriptor FieldDescriptorInstance */
		$fieldName = $descriptor->getFieldName();
		$fileUploader = new \stdClass();
		$fileUploader->inputName = $fieldName;
		/* @var $libManager WebLibraryManager */
		$moufManager = MoufManager::getMoufManager();
		$fileUploaderLibrary = $moufManager->getInstance('fileUploaderLibrary');
		$descriptor->form->getWeblibraryManager()->addLibrary($fileUploaderLibrary);
		
		$fieldName = $descriptor->getFieldName();

		$script = 'function removeFileUpload_'.$fieldName.'(el, file) {
						var input = $("<input />")
										.attr("type", "hidden")
										.attr("name", "remove-file-upload-'.$fieldName.'[]")
										.attr("value", file);
						$("#remove-file-upload-'.$fieldName.'").append(input);
						$(".file-upload-'.$fieldName.'-"+el).remove();
						return false;
					}';
		/*
		$script .= '$(document).ready(function(){
				$.validator.addMethod(
				"fileuploader_require",
				function(value, element) {
				
					var functionCall =
					function (value, element){
						var hasElement = _getLength(value, element) > 0;
						if(hasElement) {
							return true;
						}
						else {
							_getLength(value, element);
						}
					}
				
				})
			//$("#document").rules("add", {"fileuploader_require": 1});
			})
				';
		*/
		return array($script);
	}
	
	
}