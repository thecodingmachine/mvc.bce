<?php
namespace Mouf\MVC\BCE\Classes\Renderers;


use Mouf\MoufManager;

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
		
		$fileUploader = $descriptor->getFileUploaderWidget();
		if($this->onlyOneFile) {
			$fileUploader->multiple = false;
			$fileUploader->onlyOneFile = true;
		}
		else {
			$fileUploader->multiple = true;
			$fileUploader->onlyOneFile = false;
		}
		
		$fileUploader->inputName = $fieldName;
		$fileUploader->directory = null;
		
		$html = '';
		if($values) {
			$html = '<table>';
			$i = 0;
			foreach ($values as $value) {
				if($value) {
					$html .= '<tr class="file-upload-'.$fieldName.'-'.$i.'">';
						$html .= '<td>';
							$ext = substr($value, strrpos($value, '.') + 1);
							if($ext == 'jpeg' || $ext == 'jpg' || $ext == 'gif' || $ext == 'png'){
								$fileUrl = ROOT_URL.str_replace('\\', '/', $value);
								$html .= '<img src="'.$fileUrl.'" style="width: 50px; height: 50px" /> (<a href="'.$fileUrl.'">link here</a>)';
							}else
								$html .= $value;
						$html .= '</td>';
						$html .= '<td>';
							$html .= '<a href="#" onclick="return removeFileUpload_'.$fieldName.'('.$i.', \''.str_replace('\\', '\\\\', $value).'\')">remove</a>';
						$html .= '</td>';
					$html .= '</tr>';
					$i ++;
				}
			}
			
			$html .= '</table>';
			$html .= '<div id="remove-file-upload-'.$fileUploader->inputName.'"></div>';
		}
		return $html.$fileUploader->returnHtmlString();
		
	}
	
	/**
	 * (non-PHPdoc)
	 * The datepicker depends on jQueryUI's datepicker widget, therefore load the library into the WebLibrary manager, and call the datepicker initialization on dom ready
	 * @see FieldRendererInterface::getJS()
	 */
	public function getJSEdit($descriptor, $bean, $id){
		$fieldName = $descriptor->getFieldName();
		$fileUploader->inputName = $fieldName;
		/* @var $libManager WebLibraryManager */
		$moufManager = MoufManager::getMoufManager();
		$fileUploaderLibrary = $moufManager->getInstance('fileUploaderLibrary');
		\Mouf::getDefaultWebLibraryManager()->addLibrary($fileUploaderLibrary);
		
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
		
		return array($script);
	}
	
	
}