<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

use Mouf\Html\Widgets\Form\Styles\LayoutStyle;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;
use Mouf\Html\Widgets\Form\SelectField;
use Mouf\Html\Tags\Option;
use Mouf\MVC\BCE\Classes\ValidationHandlers\BCEValidationUtils;
use Mouf\Html\Widgets\Form\RadiosField;
use Mouf\Html\Widgets\Form\RadioField;
use Mouf\Utils\Value\ValueUtils;
use Mouf\Html\Widgets\Form\TextField;
use Mouf\MVC\BCE\Classes\Descriptors\ForeignKeyAutocompleteFieldDescriptor;
use Mouf\MVC\BCE\Classes\ScriptManagers\ScriptManager;
use Mouf\Html\Tags\Input;


/**
 * A renderer class that outputs an ajax autocomplete field
 * 
 * @Component
 * @ApplyTo {"type": ["fk"]}
 */
class AutocompleteFieldRenderer extends DefaultViewFieldRenderer implements SingleFieldRendererInterface {
	
	
	/**
	 * The URL where to get the results from, relative to the ROOT_URL.
	 * 
	 * @var string
	 */
	public $ajaxQueryUrl;
	
	/**
	 * Hidden field name
	 * @var unknown
	 */
	private $hiddenFieldName;
	
	/**
	 * The name of the key containing the ID of the returned item
	 * 
	 * @var string
	 */
	public $ajaxItemIdField = "id";
	
	/**
	 * The name of the key containing the label of the returned item
	 *
	 * @var string
	 */
	public $ajaxItemLabelField = "label";
	
	/**
	 * The GET parameter that will contain the query.
	 * 
	 * @var string
	 */
	public $queryField = "q";
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::render()
	 */
	public function renderEdit($descriptorInstance){
		/* @var $descriptorInstance FieldDescriptorInstance */
		$descriptor = $descriptorInstance->fieldDescriptor;
		/* @var $descriptor ForeignKeyAutocompleteFieldDescriptor */
		$value = $descriptorInstance->getFieldValue();
		
		$textField = new TextField($descriptor->getFieldLabel(), $descriptorInstance->getFieldName()."_container", $descriptor->linkedBeanLabel);
		$textField->getInput()->setAutocomplete("off");
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
		$fieldName = $descriptor->getFieldName();
		return array(
				ScriptManager::SCOPE_READY => "
				$('[name=".$fieldName."_container]').typeahead({
					source: function (query, process) {
				        return $.getJSON(
			        		rootUrl+'".$this->ajaxQueryUrl."',
				            { ".$this->queryField.": query },
				            function (data) {
				                return process(data);
				            });
				    },
				    updater: function (item) {
				    	$('#$fieldName').val(item.".$this->ajaxItemIdField.");
						return item.".$this->ajaxItemLabelField.";
				    },
				    matcher: function (item) {
				        return true;
				    },
				    sorter: function (items) {
				        return items;
				    },
				    highlighter: function (item) {
				       return item.".$this->ajaxItemLabelField.";
				    }
				});"
		);
	}
	
}