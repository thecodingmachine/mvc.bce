<?php
namespace Mouf\MVC\BCE\FormRenderers;

interface DescriptionRendererInterface {
	
	/**
	 * renders the description of the field within a block
	 * @param string $description
	 */
	public function render($description);
	
}