<?php
namespace Mouf\MVC\BCE\FormRenderers\Bootstrap\Wrappers;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;

use Mouf\MVC\BCE\FormRenderers\DescriptionRendererInterface;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptor;

use Mouf\MVC\BCE\FormRenderers\FieldWrapperRendererInterface;

/**
 * Base class for wrapping simple fields
 * 
 * @ApplyTo { "php" :[ "string", "int", "number"] }
 */

class DefaultFieldWrapperRenderer implements FieldWrapperRendererInterface {
	
	/**
	 * @var DescriptionRendererInterface
	 */
	public $descriptionRenderer;
	
	/**
	 * @var bool
	 */
	public $clearControlWrapper = false;
	
	/**
	 * (non-PHPdoc)
	 * @param FieldDescriptorInstance $descriptorInstance
	 * @see \Mouf\MVC\BCE\FormRenderers\FieldWrapperRendererInterface::render()
	 */
	public function render($descriptorInstance, $fieldHtml, $formMode) {
		?>
		<div class="control-group default-wrapper-renderer<?php if ($this->clearControlWrapper) echo " clear-wrap" ?>">
			<label for="<?php echo $descriptorInstance->getFieldName() ?>" class="control-label">
				<?php 
				echo $descriptorInstance->fieldDescriptor->getFieldLabel();
				if($descriptorInstance->fieldDescriptor instanceof FieldDescriptor && $descriptorInstance->fieldDescriptor->getValidators()) {
					foreach ($descriptorInstance->fieldDescriptor->getValidators() as $value) {
						if(get_class($value) == 'RequiredValidator') {
							echo '<span class="required-field">*</span>';
							break;
						}
					}
				}
				?>
			</label>
			<div class="controls">
				<?php 
				echo $descriptorInstance->fieldDescriptor->toHTML($descriptorInstance, $formMode);
				if ($formMode == 'edit' && $descriptorInstance->fieldDescriptor->getDescription() && $this->descriptionRenderer){
					$this->descriptionRenderer->render($descriptorInstance->fieldDescriptor->getDescription());
				}
				?>
			</div>
		</div>
		<?php
	}
	
	public function setDescriptionRenderer(DescriptionRendererInterface $renderer){
		$this->descriptionRenderer = $renderer;
	}	
}