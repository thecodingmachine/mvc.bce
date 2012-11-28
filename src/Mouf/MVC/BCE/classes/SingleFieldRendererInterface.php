<?php

namespace Mouf\MVC\BCE;


interface SingleFieldRendererInterface extends FieldRendererInterface{

	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::render()
	 */
	public function render($descriptor);

	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::getJS()
	*/
	public function getJS($descriptor);
}