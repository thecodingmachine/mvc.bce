<?php
namespace Mouf\MVC\BCE\FormRenderers\Bootstrap;

use Mouf\MVC\BCE\Classes\Descriptors\BCEFieldDescriptorInterface;
use Mouf\MVC\BCE\FormRenderers\BCERendererInterface;
use Mouf\Html\Utils\WebLibraryManager\WebLibrary;
use Mouf\MVC\BCE\BCEForm;
/**
 * This is a simple form rendering class, using a simple field layout :
 *
 * @Component
 *
 */
class BootstrapFormRenderer implements BCERendererInterface {
	
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
			$descriptor->toHtml($form->getMode());
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