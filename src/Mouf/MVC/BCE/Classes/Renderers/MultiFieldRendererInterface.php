<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

interface MultiFieldRendererInterface extends EditFieldRendererInterface {
	
	/**
	 * 
	 * @param bool $tradMode
	 */
	public function seti18nUtilisation($tradMode);
}