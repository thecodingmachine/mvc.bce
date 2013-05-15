<?php
namespace Mouf\MVC\BCE\Admin;

use Mouf\MVC\BCE\Classes\ValidationHandlers\JsValidationHandlerInterface;
use Mouf\MVC\BCE\FormRenderers\BCERendererInterface;
/**
 * These classes are simple stringifyed representations of the BCE elements.
 * 
 * It has no other use than providing autocompletion, in building the objects 
 * that will be used by the administration interface of BCE.
 * 
 * @author Kevin
 *
 */
class BCEFormInstanceBean {
	
	/**
	 * @var DaoDescriptorBean
	 */
	public $daoData;
	
	/**
	 * @var BaseFieldDescriptorBean
	 */
	public $idFieldDescriptor;

	/**
	 * The name of the DB table related to the main bean
	 * @var string
	 */
	public $mainBeanTableName;
	
	/**
	 * The field descriptors of the Form
	 * @var array<BaseFieldDescriptorBean>
	 */
	public $descriptors;

	/**
	 * The Renderer for the Form
	 * @var BCERendererInterface
	 */
	public $renderer;
	
	/**
	 * The validation handler 
	 * @var JsValidationHandlerInterface
	 */
	public $validationHandler;
	
	/**
	 * The action attribute of the form
	 * @var string
	 */
	public $action;
	
	/**
	 * The method attribute of the form
	 * @var string
	 */
	public $method;

	/**
	 * All the others attributes of the form
	 * @var array<string, string>
	 */
	public $attributes;
}