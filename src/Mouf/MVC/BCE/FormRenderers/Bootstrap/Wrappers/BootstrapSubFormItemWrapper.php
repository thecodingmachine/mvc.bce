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
		<div class='subform-item <?php echo $index ?>'>
		<?php 
			$formInstance->toHtml();
		?>
			<div class='remove-item'>
				<i class='do-remove icon icon-remove' data-id='<?php echo $formInstance->beanId ?>'></i>
				<i class='undo-remove icon icon-white icon-remove-sign' data-id='<?php echo $formInstance->beanId ?>'></i>
				<input type='hidden' value='0' class="delete-persist" name='<?php echo $desc->getFieldName() ?>[<?php echo $formInstance->beanId ?>][__bce__delete]'/>
			</div>
			<div class="bce-fade"></div>
		</div>
		<?php
	}
	
	public function getRemoveItemJS(){
		$script = "
			$(document).on('click', '.subform-item .do-remove', function(){
				$(this).parents('.remove-item').find('input').val(1);
				$(this).parents('.subform-item').find('.undo-remove').css('display', 'block');
				$(this).parents('.subform-item').find('.bce-fade').fadeIn();
				$(this).hide();
		
				$(this).parents('.subform-item').find(':not(.delete-persist)').attr('disabled', true);
			});
		
			$(document).on('click', '.subform-item .undo-remove', function(){
				$(this).parents('.remove-item').find('input').val(0);
				$(this).parents('.subform-item').find('.do-remove').css('display', 'block');
				$(this).parents('.subform-item').find('.bce-fade').fadeOut();
				$(this).hide();
		
				$(this).parents('.subform-item').find('input, select').attr('disabled', false);
			});
		";
		
		return array(ScriptManager::SCOPE_WINDOW, $script);
	}
	
}