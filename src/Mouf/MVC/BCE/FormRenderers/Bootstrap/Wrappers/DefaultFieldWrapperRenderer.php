<?php
use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptor;

use Mouf\MVC\BCE\FormRenderers\FieldWrapperRendererInterface;

class DefaultFieldWrapperRenderer implements FieldWrapperRendererInterface {
	
	public function render(FieldDescriptor $descriptor, $fromEditMode) {
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
				<?php echo $descriptor->getRenderer()->render($descriptor, $fromEditMode); ?>
			</div>
			<?php if ($fromEditMode == 'edit' && $descriptor->getDescription()){ ?>
			<div class="description">
				<?php echo $descriptor->getDescription(); ?>
			</div>
			<?php }?>
		</div>
		<?php
	}
	
}