<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

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
    public function renderEdit($descriptor) {
        /* @var $descriptor ForeignKeyFieldDescriptor */
        $fieldName = $descriptor->getFieldName();
        $data = $descriptor->getData();
        $value = $descriptor->getFieldValue();
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
    public function getJSEdit($descriptor) {
        return array();
    }

}