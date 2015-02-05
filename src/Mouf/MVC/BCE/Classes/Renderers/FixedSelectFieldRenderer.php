<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

use Mouf\Html\Utils\WebLibraryManager\WebLibraryManager;
use Mouf\MVC\BCE\Classes\Descriptors\BCEFieldDescriptorInterface;
use Mouf\MVC\BCE\Classes\Descriptors\ForeignKeyFieldDescriptor;
use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptor;
use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;

/**
 * This renderer handles Read-Only fields
 * @ApplyTo { "pk" : [ "pk" ] }
 * @Component
 */
class FixedSelectFieldRenderer extends DefaultViewFieldRenderer implements SingleFieldRendererInterface {

    /**
     * Tells if the field should display a select box or a radio button group
     * @Property
     * @var bool
     */
    public $radioMode = false;

    /**
     * (non-PHPdoc)
     * @see FieldRendererInterface::render()
     */
    public function renderEdit($descriptorInstance) {
        /* @var $descriptorInstance FieldDescriptorInstance */
    	$descriptor = $descriptorInstance->fieldDescriptor;
        /* @var $descriptor ForeignKeyFieldDescriptor */
        $fieldName = $descriptorInstance->getFieldName();
        $data = $descriptor->getData();
        $value = $descriptorInstance->getFieldValue();
        $html = "";

        $selected = " - ";
        foreach ($data as $linkedBean) {
            $beanId = $descriptor->getRelatedBeanId($linkedBean);
            $beanLabel = $descriptor->getRelatedBeanLabel($linkedBean);
            if ($beanId == $value)
                $selected = $beanLabel;
        }

        $html .= "<span style='padding-top:5px;display:block;'>" . $selected . "</span><input type='hidden' name='" . $fieldName . "' value='" . $value . "' />";
        return $html;
    }

    /**
     * (non-PHPdoc)
     * @see FieldRendererInterface::getJS()
     */
    public function getJSEdit(BCEFieldDescriptorInterface $descriptor, $bean, $id, WebLibraryManager $webLibraryManager) {
        /* @var $descriptorInstance FieldDescriptorInstance */
        return array();
    }

}