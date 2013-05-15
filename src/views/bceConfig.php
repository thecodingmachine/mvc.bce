<?php 
/* @var $this BceConfigController */
?>


<script type="text/javascript">
<!--
var daos = ['<?php echo implode("', '", $this->daoInstances); ?>']
var _instanceName = '<?php echo $this->instanceName; ?>';
var singleRenderers = ['<?php echo implode("', '", $this->singleRenderers); ?>'];
var multiRenderers = ['<?php echo implode("', '", $this->multiRenderers); ?>'];
var formatters = ['<?php echo implode("', '", $this->formatters); ?>'];
var validators = ['<?php echo implode("', '", $this->validators); ?>'];
var conditions = ['<?php echo implode("', '", $this->conditions); ?>'];
<?php 
if (count($this->wrapperRenderers)){
?>
var wrapperRenderers = ['<?php echo implode("', '", $this->wrapperRenderers); ?>'];
<?php
}else echo "wrapperRenderers = []";
?>

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
	<label class="label">Main DAO :
	<span>
	<?php
	$value= "";
	if ($this->mainDAOName){
		$value = "<span>$this->mainDAOName</span>";
	}else{
		$value = "<option value='' id=''>Choose a DAO</option>";
		foreach ($this->daoInstances as $daoInstance) {
			$value .= "<option value='$daoInstance' id='$daoInstance'>$daoInstance</option>";
		}
		$value = "<select onchange='refershValues(this, \"".$this->instanceName."\")'>$value</select>";
	}
	echo $value;
	?>
	</span>
	</label>
	<form action="save" method="post">
	<input type="hidden" name="formInstanceName" value="<?php echo $this->instanceName; ?>" />
	<div id="content">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#descriptors-tab" data-toggle="tab">Descriptors</a></li>
			<li><a href="#config-tab" data-toggle="tab">Configuration</a></li>
			<li><a href="#build-tab" data-toggle="tab">Builder</a></li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="descriptors-tab">
				<div class="tabbable tabs-left">
					<ul class="nav nav-tabs desc-titles">
					</ul>
					<div class="tab-content desc-content">
					</div>
				</div>
			</div>
			<div class="tab-pane" id="config-tab">
				<div class="tabbable tabs-left">
					<ul class="nav nav-tabs config-titles">
						<li class='active'><a href='#id_desc' data-toggle="tab">Id descriptor</a></li>
						<li><a href='#data_add' data-toggle="tab">Form Attributes</a></li>
					</ul>
					<div class="tab-content config-content">
						<div class="tab-pane active" id="id_desc"></div>
						<div class="tab-pane" id="data_add"></div>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="build-tab">
				Build!
			</div>
			<div id="ajaxload"></div>
		</div>		
	</div>
	<button type="submit" class="btn btn-primary">Save</button>
	</form>
	<div id="right-modal" class="modal hide fade sign-in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<input type="text" name="query" value="" class="search-query" placeholder="Search for a condition" autocomplete="off">
		</div>
		<div class="modal-body">
			<span class='label label-important no-right'>No Condition&nbsp;<i class="icon-white icon-remove"></i></span>
		<?php 
		foreach ($this->conditions as $condition){
		?>
			<span class='label label-info set-right' data-id='<?php echo $condition; ?>'><?php echo $condition; ?></span>
		<?php
		}
		?>
			<input type="hidden" class='rightTarget' value=""/>
		</div>
	</div>