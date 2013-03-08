<?php
namespace Mouf\MVC\BCE\FormRenderers\Bootstrap\Wrappers;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptor;

use Mouf\MVC\BCE\FormRenderers\FieldWrapperRendererInterface;

/**
 * Base class for wrapping simple fields
 * 
 * @ApplyTo { "php" :[ "string", "int", "number"] }
 */

class DefaultFieldWrapperRenderer implements FieldWrapperRendererInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\FormRenderers\FieldWrapperRendererInterface::render()
	 */
	public function render(FieldDescriptor $descriptor, $fieldHtml, $formMode) {
		?>
		<div class="control-group">
			<label for="<?php echo $descriptor->getFieldName() ?>" class="control-label">
				<?php 
				echo $descriptor->getFieldLabel();
				if($descriptor instanceof FieldDescriptor && $descriptor->getValidators()) {
					foreach ($descriptor->getValidators() as $value) {
						if(get_class($value) == 'RequiredValidator') {
							echo '<span class="required-field">*</span>';
							break;
						}
					}
				}
				?>
			</label>
			<div class="controls">
				<?php echo $descriptor->getRenderer()->render($descriptor, $formMode); ?>
			</div>
			<?php if ($formMode == 'edit' && $descriptor->getDescription()){ ?>
			<div class="description">
				<?php echo $descriptor->getDescription(); ?>
			</div>
			<?php }?>
		</div>
		<?php
	}
	
}