<?php
namespace Mouf\MVC\BCE\FormRenderers\Bootstrap\Wrappers;
use Mouf\MVC\BCE\Classes\ScriptManagers\ScriptManager;

use Mouf\MVC\BCE\BCEFormInstance;
use Mouf\MVC\BCE\Classes\Descriptors\SubFormFieldDescriptor;
use Mouf\MVC\BCE\FormRenderers\SubFormItemWrapperInterface;

class BootstrapSubFormItemWrapper implements SubFormItemWrapperInterface {
	
	/**
	 * @param SubFormFieldDescriptor $desc
	 * @param BCEFormInstance $formInstance
	 */
	public function toHtml(SubFormFieldDescriptor $desc, BCEFormInstance $formInstance, $index = "") {
		?>
		<style>
		<!--
		.controls .subform-item {
			overflow: hidden;
			padding-top: 10px;
			margin-bottom: 5px;
		}
		.controls .subform-item.odd{
			background: #EAEAEA;
		}
		-->
		</style>
		<div class="subform-item <?php echo $index ?>">
			<div class='form-horizontal col-sm-11'>
			<?php 
				$formInstance->toHtml();
			?>
			</div>
			<div class='remove-item col-sm-1'>
				<button type="button" class="btn btn-danger do-remove delete-persist" data-id='<?php echo $formInstance->beanId ?>' title="Remove">
				  <span class="glyphicon glyphicon-remove"></span>
				</button>
				<button style="display: none" type="button" class="btn btn-success undo-remove delete-persist" data-id='<?php echo $formInstance->beanId ?>' title="Cancel removal">
				  <span class="glyphicon glyphicon-remove-sign"></span>
				</button>
				<input type='hidden' value='0' class="delete-persist" name='<?php echo $desc->getFieldName() ?>[<?php echo $formInstance->beanId ?>][__bce__delete]'/>
			</div>
		</div>
		<?php
	}
	
	public function getRemoveItemJS(){
		//moved to subformfield descriptor
	}
	
}