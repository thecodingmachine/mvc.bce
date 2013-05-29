<?php
namespace Mouf\MVC\BCE\FormRenderers\Bootstrap\Wrappers;

use Mouf\MVC\BCE\FormRenderers\SubFormFieldWrapperRendererInterface;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;

use Mouf\MVC\BCE\FormRenderers\DescriptionRendererInterface;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptor;

use Mouf\MVC\BCE\FormRenderers\FieldWrapperRendererInterface;

/**
 * Base class for wrapping simple fields
 * 
 * @ApplyTo { "php" :[ "string", "int", "number"] }
 */

class SubFormFieldWrapperRenderer implements SubFormFieldWrapperRendererInterface {
	
	/**
	 * (non-PHPdoc)
	 * @param FieldDescriptorInstance $descriptorInstance
	 * @see \Mouf\MVC\BCE\FormRenderers\FieldWrapperRendererInterface::render()
	 */
	public function render($descriptorInstance, $fieldHtml, $formMode) {
		?>
		<div class="subform-wrapper form-inline <?php echo $descriptorInstance->getFieldName(); ?>">
			<label for="<?php echo $descriptorInstance->getFieldName() ?>" class="control-label">
				<?php 
				echo $descriptorInstance->fieldDescriptor->getFieldLabel();
				?>
			</label>
			<div class="controls">
				<?php 
					echo $descriptorInstance->fieldDescriptor->toHTML($descriptorInstance, $formMode);
				?>
			<div class="form-actions">
				<button class="btn" onclick="<?php echo $descriptorInstance->fieldDescriptor->getAddItemFonction(); ?>; return false;"><i class="icon icon-plus-sign"></i>&nbsp;Add an Item</button>
			</div>
			</div>
		</div>
		<?php
	}
	
	public function setDescriptionRenderer(DescriptionRendererInterface $renderer){
		return;
	}
}