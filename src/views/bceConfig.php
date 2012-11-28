<?php 
/* @var $this BceConfigController */
?>


<style>
	.ui-tabs-vertical { width: 950px; }
	.ui-tabs-vertical .ui-tabs-nav { float: left; width: 100px; padding: 0; border: 1px solid gray;}
	.ui-tabs-vertical .ui-tabs-nav li { clear: left; width: 100%; }
	.ui-tabs-vertical .ui-tabs-nav li a { display:block; }
	.ui-tabs-vertical .ui-tabs-nav li.ui-tabs-selected {  }
	.ui-tabs-vertical .ui-tabs-panel { float: left; width: 813px; background-color: white;}
</style>

<script type="text/javascript">
<!--
var daos = ['<?php echo implode("', '", $this->daoInstances); ?>']

var singleRenderers = ['<?php echo implode("', '", $this->singleRenderers); ?>'];
var multiRenderers = ['<?php echo implode("', '", $this->multiRenderers); ?>'];
var formatters = ['<?php echo implode("', '", $this->formatters); ?>'];
var validators = ['<?php echo implode("', '", $this->validators); ?>'];

var formRenderers = ['<?php echo implode("', '", $this->formRenderers); ?>'];
var validationHandlers = ['<?php echo implode("', '", $this->validationHandlers); ?>'];

var bceSettings = {
	rootUrl : "<?php echo ROOT_URL?>"
}
jQuery(document).ready(function() {
	
	<?php 
	if ($this->mainDAOName){
	?>
	initInstance('<?php echo $this->instanceName; ?>');
	<?php
	}
	?>
});
//-->
</script>
<?php if ($this->success == 1){
?>
<div class="success">Form '<?php echo $this->instanceName ?>' has been saved</div>
<?php
}?>
<h1>Configuration of <i>'<?php echo $this->instanceName ?>'</i> instance</h1>
	<label class="label">Main DAO :</label>
	<span>
	<?php
	$value= "";
	if ($this->mainDAOName){
		$value = "<span>$this->mainDAOName</span>";
	}else{
		foreach ($this->daoInstances as $daoInstance) {
			$value .= "<option value='$daoInstance' id='$daoInstance'>$daoInstance</option>";
		}
		$value = "<select onchange='refershValues(this, \"".$this->instanceName."\")'>$value</select>";
	}
	echo $value;
	?>
	</span>
	<form action="save" method="post">
	<input type="hidden" name="formInstanceName" value="<?php echo $this->instanceName; ?>" />
	<div id="tabs">
		<ul>
			<li><a href="#descriptors-tab">Descriptors</a></li>
			<li><a href="#config-tab">Configuration</a></li>
			<li>&nbsp;</li>
			<li><div onclick="addM2MBlock(); return false;" class="naked addm2m">m2m</div></li>
		</ul>
		<div id="descriptors-tab">
			<div id="data" class="sortable" style="width: 750px; float: left;">
			</div>
			<div style="clear: both"></div>
		</div>
		<div id="config-tab">
			<div id="data_add" style="width: 850px; float: left">
				<div>
					<label>Id descriptor</label>
					<div id="id_desc"></div>
				</div>
			</div>
		</div>		
	</div>
	<button type="submit" onclick="getNewM2MInstancesNames()">Save</button>
	</form>
