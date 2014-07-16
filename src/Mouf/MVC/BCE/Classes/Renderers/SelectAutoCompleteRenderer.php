<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

use Mouf\Html\Widgets\Form\Styles\LayoutStyle;
use Mouf\MVC\BCE\Classes\Descriptors\ForeignKeyFieldDescriptor;
use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;
use Mouf\Html\Widgets\Form\SelectField;
use Mouf\Html\Tags\Option;
use Mouf\MVC\BCE\Classes\ValidationHandlers\BCEValidationUtils;
use Mouf\Html\Widgets\Form\RadiosField;
use Mouf\Html\Widgets\Form\RadioField;
use Mouf\Utils\Value\ValueUtils;
use Mouf\MoufManager;
use Mouf\MVC\BCE\Classes\ScriptManagers\ScriptManager;
use Mouf\Html\Widgets\Form\TextField;
use Mouf\Html\Widgets\Form\InputField;
use Mouf\Html\Tags\Input;


/**
 * A renderer class that ouputs a simple select box: it doesn't handle multiple selection
 *
 * @Component
 * @ApplyTo {"type": ["fk"]}
 */
class SelectAutoCompleteRenderer extends DefaultViewFieldRenderer implements SingleFieldRendererInterface {

	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::render()
	 */
	public function renderEdit($descriptorInstance){
		$textField = new TextField($descriptorInstance->fieldDescriptor->getFieldLabel(), $descriptorInstance->getFieldName().'_display', $descriptorInstance->getFieldValue());
		if($descriptorInstance->getValidator()) {
			$textField->setInputClasses($descriptorInstance->getValidator());
		}
		
		$textField->getInput()->setId($descriptorInstance->getFieldName().'_display');
		$textField->getInput()->setReadonly((!$descriptorInstance->fieldDescriptor->canEdit()) ? "readonly" : null);
		if(isset($descriptorInstance->attributes['styles'])) {
			$textField->getInput()->setStyles($descriptorInstance->attributes['styles']);
		}

		$textField->setHelpText($descriptorInstance->fieldDescriptor->getDescription());
		
		$textField->setRequired(BCEValidationUtils::hasRequiredValidator($descriptorInstance->fieldDescriptor->getValidators()));
		
		if ($this->getLayout() == null){
			$textField->setLayout($descriptorInstance->form->getDefaultLayout());
		}
		
		$hiddenField = new Input();
		$hiddenField->setType("hidden");
		$hiddenField->setName($descriptorInstance->getFieldName());
		$hiddenField->setId($descriptorInstance->getFieldName());
		$hiddenField->setValue($descriptorInstance->getFieldValue());
		
		ob_start();
		$textField->toHtml();
		$hiddenField->toHtml();
		return ob_get_clean();
	}

	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::getJS()
	 */
	public function getJSEdit($descriptor, $bean, $id){
		/* @var $descriptor ForeignKeyFieldDescriptor */
		$values = $descriptor->getData();
		$items = [];
        $displayVal = null;
        foreach ($values as $value) {
            $beanId = $descriptor->getRelatedBeanId($value);
            $beanLabel = $descriptor->getRelatedBeanLabel($value);
			if ($descriptor->getValue($bean) == $beanId){
				$displayVal = $beanLabel;
			}
			$items[] = ['value' => $beanLabel, "id" => $beanId];
		}
		
		$fieldName = $descriptor->getFieldName();
		
		$js = "
			var ".$fieldName."_data = ".json_encode($items).";
			$('#".$fieldName."_display').val('$displayVal');
			
			$('#".$fieldName."_display').autocomplete({
				source: ".$fieldName."_data,
				select: function( event, ui ) {
					$('#$fieldName').val(ui.item.id);
				}
		    })
		";
		return array(
			ScriptManager::SCOPE_READY => $js
		);
	}

}