<?php
namespace Mouf\MVC\BCE\FormRenderers\Bootstrap\Wrappers\DescriptionRenderer;

use Mouf\MVC\BCE\FormRenderers\DescriptionRendererInterface;

class  BlockDescriptionRenderer implements DescriptionRendererInterface{

	/**
	 * (non-PHPdoc)
	 * @see DescriptionRendererInterface::render()
	 */
	public function render($description){
	?>
		<span class="help-block description">
			<?php echo $description; ?>
		</span>
	<?php 
	}
	
}