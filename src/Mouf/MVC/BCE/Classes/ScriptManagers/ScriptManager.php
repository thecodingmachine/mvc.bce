<?php
namespace Mouf\MVC\BCE\Classes\ScriptManagers;


class ScriptManager {
	
	private $scripts = array();
	
	const SCOPE_READY = "ready";
	const SCOPE_LOAD = "load";
	const SCOPE_UNLOAD = "unload";
	const SCOPE_WINDOW = "unload";

	/*
	 * $jsValidate = $this->validationHandler->getValidationJs($this->attributes['id']);
		$keys = array_keys($jsValidate);
		$scope = $keys[0];
		$this->addScript($scope, $jsValidate[$scope]);
	 * 
	 * */
	
	public function addScript($scope, $script){
		$this->scripts[$scope][] = $script;
	}
	
	public function renderScripts(){
		$rendererScripts = array();
		
		foreach ($this->scripts as $scope => $values){
			if ($scope != self::SCOPE_WINDOW){
				$rendererScripts[] = "
				(function($) {
					$(document).$scope(function(){
						" . implode("\n", $values) . "
					});
				})(jQuery);";
			}else{
				$rendererScripts[] = "
					" . implode("\n", $values);
			}
		}
		
		return implode("\n", $rendererScripts);
	}
	
}