<?php
namespace Mouf\MVC\BCE\admin;
/**
 * These classes are simple stringifyed representations of the BCE elements.
 * 
 * It has no other use than providing autocompletion, in building the objects 
 * that will be used by the administration interface of BCE.
 * 
 * @author Kevin
 *
 */
class FieldDescriptorBean {
	
	/**
	 * The type of the Descriptor. It can be on of "base", "fk", "m2m" or "custom"
	 * @var string
	 */
	public $type;
	
	/**
	 * The name of the descriptor instance
	 * @var string
	 */
	public $name;
	
	/**
	 * The name of the renderer instance of the descriptor
	 * @var string
	 */
	public $renderer;
	
	/**
	 * The name of the formatter instance of the descriptor
	 * @var string
	 */
	public $formatter;
	
	/**
	 * The name of the field, will be used as a unique key in the 
	 * Form since it will also be the "name" attribute of the generated fields.
	 * @var string
	 */
	public $fieldName;
	
	/**
	 * The Label of the field
	 * @var unknown_type
	 */
	public $label;
	
	/**
	 * The instance names of the validators of the descriptor
	 * @var array<string>
	 */
	public $validators;
	
	/**
	 * If the descriptor is pointing to a primary key field or not
	 * @var boolean
	 */
	public $isPk = false;
	
	/**
	 * If the descriptor should be added to the form or not
	 * @var boolean
	 */
	public $active = true;
	
	/**
	 * If the descriptor is a new one or not (means that it has been detected from an unimplemented getter
	 * @var boolean
	 */
	public $is_new = false;
	
	/**
	 * The name of the column handled by the decsriptor  
	 * @var string
	 */
	public $db_column;
}