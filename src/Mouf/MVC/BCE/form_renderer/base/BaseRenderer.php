<?php
namespace Mouf\MVC\BCE\form_renderer\base;

use Mouf\MVC\BCE\BCEForm;
use Mouf\MVC\BCE\form_renderer\BCERendererInterface;

/**
 * This is a simple form rendering class, using a simple field layout :
 *
 * @Component
 *
 */
class BaseRenderer implements BCERendererInterface{
	
	/**
	 * @Property
	 * @var WebLibrary
	 */
	public $skin;
	
	public function render(BCEForm $form){
?>
	<form class="form-horizontal" action="<?php echo ROOT_URL.$form->action; ?>" method="<?php echo $form->method?>" <?php foreach ($form->attributes as $attrName => $value){ echo "$attrName='$value' "; }?>>
	<fieldset>
		<?php
		$idDescriptor = $form->idFieldDescriptor;
		$idRenderer = $idDescriptor->getRenderer();
		echo $idRenderer->render($idDescriptor);
		foreach ($form->fieldDescriptors as $descriptor) {
			/* @var $descriptor BCEFieldDescriptorInterface */
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
					<?php echo $descriptor->toHtml(); ?>
				</div>
			</div>
			<?php
		}
		?>
		<div class="form-actions">
			<button class="btn btn-primary" type="submit"><?php echo $form->saveLabel; ?></button>
			<button class="btn" type="reset"><?php echo $form->cancelLabel; ?></button>
		</div>
	</fieldset>
	</form>
<?php
	}
	
	public function getSkin(){
		return $this->skin;
	}
}