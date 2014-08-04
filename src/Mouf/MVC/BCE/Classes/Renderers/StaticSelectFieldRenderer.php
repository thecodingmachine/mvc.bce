<?php

namespace Mouf\MVC\BCE\Classes\Renderers;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;
use Mouf\Html\Widgets\Form;
use Mouf\Html\Widgets\Form\TextField;
use Mouf\MVC\BCE\Classes\ValidationHandlers\BCEValidationUtils;
use Mouf\Html\Tags\Span;
use Mouf\MVC\BCE\Classes\Renderers\DefaultViewFieldRenderer;
use Mouf\MVC\BCE\Classes\Renderers\SingleFieldRendererInterface;
use Mouf\MVC\BCE\Classes\BCEException;

/**
 * This renderer handles select field with static options and values
 *
 * Class StaticSelectFieldRenderer
 * @package Mouf\MVC\BCE
 */
class StaticSelectFieldRenderer  extends DefaultViewFieldRenderer implements SingleFieldRendererInterface {

    protected $options;

    /**
     * @param array<string, string> $options
     */
    public function __construct($options) {
        $this->options = $options;
    }

    /**
     * (non-PHPdoc)
     * @see FieldRendererInterface::render()
     */
    public function renderEdit($descriptorInstance){
        /* @var $descriptorInstance FieldDescriptorInstance */
        $descriptor = $descriptorInstance->fieldDescriptor;

        $selectField = new Form\SelectField($descriptorInstance->fieldDescriptor->getFieldLabel(), $descriptorInstance->getFieldName(), $descriptorInstance->getFieldValue(), $this->options);
        $selectField->setRequired(BCEValidationUtils::hasRequiredValidator($descriptorInstance->fieldDescriptor->getValidators()));
        $selectField->getSelect()->setDisabled((!$descriptor->canEdit()) ? "disabled" : null);
        ob_start();
        $selectField->toHtml();
        return ob_get_clean();
    }

    /**
     * (non-PHPdoc)
     * @see FieldRendererInterface::getJS()
     */
    public function getJSEdit($descriptor, $bean, $id){
        /* @var $descriptorInstance FieldDescriptorInstance */
        return array();
    }
}