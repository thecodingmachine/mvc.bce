/* 
 * --------------------------------------------------------

 * ----------------------- HEAD UP!! ----------------------
 * --------------------------------------------------------
 * 
 * This is an important part of the bce admin interface. It works in addition to bce_utils.php in order to load form data
 * BUT also will suggest the user which fields could be added, or even will select the best fit for DAO or bean methods
 */

//Simple variable to define the options in the "action" attribute of the form
var formMethods = [ "GET" , "POST" ];

//Used to suggest bean or DAO methods for the FKDescriptor attributes
var refreshFKDaoSettings = {
		'id_getter':{
			field : "linkedIdGetter",
			defaultSelect : "getId|get_id",
			methodType : "getter"
		},
		'label_getter':{
			field : "linkedLabelGetter",
			defaultSelect : "getLabel|getValue",
			methodType : "getter"
		},
		'data_method':{
			field : "dataMethod",
			defaultSelect : "getList|getValues|getAll",
			methodType : "any"
		}
};

var refreshSubFormSettings = {
	'fk_getter':{
		field : "fk_getter",
	 	defaultSelect : null,
	    methodType : "getter"
	},
	'fk_setter':{
		field : "fk_setter",
		defaultSelect : null,
		methodType : "setter"
	},
	'beans_getter':{
		field : "beans_getter",
		defaultSelect : null,
		methodType : "any"
	},
	method: "formMainDaoData" 
};

jQuery(document).ready(function(){
	jQuery('#right-modal .search-query').keyup(function(){
		var search = jQuery(this).val();
		
		var words = search.split(' ');
		var matches = [];
		for (var i = 0; i < words.length; i++){
			if (words[i] != "") matches.push(new RegExp(words[i], 'gi'));
		}
		
		for (var i = 0; i < conditions.length; i++){
			var condition = conditions[i];
			var conditionElem = jQuery('#right-modal span[data-id='+conditions[i]+']');
			
			var matchCount = 0;
			for (var j = 0 ; j < matches.length ; j++){
				if (matches[j].exec(condition)){
					matchCount ++;
				}
			}
			if (matchCount == matches.length){
				conditionElem.show();
			}else{
				conditionElem.hide();
			}
		}
	});
	
	jQuery('#right-modal .set-right').click(function(){
		var condition = jQuery(this).attr('data-id');
		_chooseRight(condition);
	});
	
	jQuery('#right-modal .no-right').click(function(){
		_chooseRight("");
	});
});

	jQuery(document).on('click', '.field-values .remove i', function() {
		var spanElem = jQuery(this).parents('span');
		var container = jQuery(this).parents('.field-value-bloc');
		var val = spanElem.text();
		container.find('.list input[value='+val+']').attr('checked', false);
		container.find('select.available option[value='+ val +']').show();
		spanElem.remove();
	});
	
	jQuery(document).on('click', '.field-value-bloc .right', function(event) {
		_showRightModal(event);
	});
	
	jQuery(document).on('click', '.add-m2m', function(){
		addM2MBlock();
	});
	
	jQuery(document).on('click', '.pop.help', function(event){
		_showM2MHelpModal(event);
	});
	
function _chooseRight(condition){
	var target = jQuery('#right-modal .rightTarget').val();
	jQuery('#right-modal').modal('hide');
	jQuery('input[name='+escapeStr(target)+']').val(condition);
	var iconElem = jQuery('<i/>').addClass('icon-white icon-edit').attr('data-id', target);
	jQuery('i[data-id='+escapeStr(target)+']').parents('span').text(condition + "  ").append(iconElem);
}
	

//simple counter of new added fields 
var newId = 0;

//Used to suggest bean or DAO methods for the M2MDescriptor mapping attributes
var refreshM2MmappingDaoSettings = [
	{
		field : "mappingIdGetter",
     	defaultSelect : "getId|get_id",
        methodType : "getter"
	},
	{
		field : "mappingLeftKeySetter",
		defaultSelect : "left",
		methodType : "setter"
	},
	{
		field : "mappingRightKeyGetter",
		defaultSelect : "right",
		methodType : "getter"
	},
	{
		field : "mappingRightKeySetter",
		defaultSelect : "right",
		methodType : "setter"
	},
    {
    	field : "beanValuesMethod",
		defaultSelect : null,
    	methodType : "any"
    }
];

//Used to suggest bean or DAO methods for the M2MDescriptor "linked" attributes
var refreshM2MlinkedDaoSettings = [
	{
		field : "linkedIdGetter",
     	defaultSelect : "getId|get_id",
        methodType : "getter"
	},
	{
		field : "linkedLabelGetter",
		defaultSelect : "getLabel|getValue",
		methodType : "getter"
	},
    {
    	field : "dataMethod",
		defaultSelect : "getList|getValues|getAll",
    	methodType : "any"
    }
];

var existingFields = []; 
var fieldElements = [];
var newFieldElements = [];
var fkRefreshCalls = [];
var idDesc = null;

//default values for a new M2MDescritor
var emptyM2MField = {
		mappingDaoName : "",
		mappingDaoData : {
			beanClassFields : [],
			daoMethods : []
		},
		beanValuesMethod : "",
		mappingIdGetter : "",
		mappingLeftKeySetter : "",
		mappingRightKeyGetter : "",
		mappingRightKeySetter : "",
		linkedDaoName : "",
		linkedDaoData : {
			beanClassFields : [],
			daoMethods : []
		},
		linkedIdGetter : "",
		linkedLabelGetter : "",
		dataMethod : "",
		type : "m2m",
		name : "",
		renderer : null,
		formatter : null,
		fieldName : "",
		label : "",
		validators : [],
		isPK : false
}; 

//Will simply pre-check new suggested fields if set to true (ie if the form is being created)
var isNewform = false;

/* 
 * Called when the mainDao property of the form has been selected : does a simple ajax call to set
 * the DAO into the form instance, then call initInstance to load default suggested fields
 */
function refershValues(element, instanceName){
	if (!instanceName) return;
	isNewform = true;
	
	jQuery.ajax({
		url: bceSettings.rootUrl + "bceadmin/setDao",
		data: "dao=" + jQuery(element).val() + "&instance=" + instanceName,
		success: function(data){
			if (data == "1"){
				initInstance(instanceName);
			}else{
				alert('error!');
			}
		},
		error: function(error){
			alert('error!');
		}
	});
}

/**
 * Gets the instance's data from bce_utils (will return a JSON encoded BCEFormInstanceBean)
 * These data define existing field descriptors, the suggested fields that seem not to be imlemented,
 * and alll the other information of the form (attributes, renderer, jsValidationHandler, etc..)
 * @param string instanceName the name of the edited instance 
 */
function initInstance(instanceName){
	jQuery('#ajaxload').show();
	
	jQuery.ajax({
	  url: bceSettings.rootUrl + "bceadmin/getInstanceData",
	  data: "instance="+instanceName,
	  success: completeInstanceData,
	  error: function(error){
		  jQuery("#data").html();
		  addMessage('An error occurred in BCE: '+error.responseText, "alert alert-error");
	  },
	  dataType: 'json'
	});
}

/**
 * 
 * @param object data the data loaded by "initInstance"
 */
function completeInstanceData(data){
	jQuery('#ajaxload').hide();
	
	//Retrieve getters and setters of the main DAO
	var filterdMethods = _getFilteredMethods(data.daoData);
	getters = filterdMethods['getters'];
	setters = filterdMethods['setters'];
	
	/* load the table of the mail DAO, will be usefull to suggest the right methods later
	 * see _fillSelectOptions and _selectFromSettings functions
	 */
	bceSettings.mainBeanTableName = data.mainBeanTableName;

	
	/*
	 * Load the list of existing field descriptors, and fill an array that will tell 
	 * that those descriptors are loaded already, and should not be suggested.
	 */
	var fields = data.descriptors;
	for (var i=0; i<fields.length; i++){
		var field = fields[i];
		if (field.type != "m2m" && field.type != "custom"){
			//array tells this getter is already handled by a field descriptor
			existingFields.push(field.getter);
		}
		fieldElements[field.name] = field;
	}
	
	/* Loop on the new fields, ie the fields that seem not to be handled by existing descriptors */
	for (var newFieldName in data.daoData.beanClassFields){
		var newField = data.daoData.beanClassFields[newFieldName];
		var newGetter = newField.getter;
		/* 
		 * As above, the key for existing fields is the getter, 
		 * If the getter is used, then do not suggest the new field's descriptor
		 */
		if (existingFields.indexOf(newGetter.name) != -1 || (data.idFieldDescriptor && newGetter.name == data.idFieldDescriptor.getter)){
			continue;
		}
		
		/* check if the current fieldDescriptor is linked to a PK field */
		var notIdDesc = !newField.isPk; 
		
		/* auto activate the new fields if the form is being created */
		newField.asDescriptor.active = isNewform && newField.asDescriptor.active;
		
		/* 
		 * if the field should be added to the form, add it to the newFieldElements array,
		 * if the field is a FK descriptor, then remember to call "refresh" on the fk attributes (linked Id and Getter, and dataMathod function names) 
		 */
		if (notIdDesc){
			var tmpField = newField.asDescriptor;
			if (tmpField.type == 'fk'){
				fkRefreshCalls.push(tmpField.name);
			}
			newFieldElements[newFieldName] = newField.asDescriptor;
		}
		
		/* If the form has no idDescriptor set, then suggest the new field descriptor that has isPk = true*/ 
		else if(!data.idFieldDescriptor){
			idDesc = newField.asDescriptor;
			idDesc.name = _instanceName + idDesc.name;
		}
	}
	
	/* 
	 * Get the HTML code for the idFieldDescriptor into configuration tab 
	 */ 
	var idHtml = null;
	if (data.idFieldDescriptor){
		idHtml = _fieldHtml(data.idFieldDescriptor, "idField", 1);
		idDesc = data.idFieldDescriptor;
	}else if (idDesc){
		idHtml = _fieldHtml(idDesc, "idField", 1);
	}
	
	if (idHtml){
		jQuery('#id_desc').html(idHtml);
	}
	
	/*
	 * Load the existing fieldDescriptors' HTML
	 */
	for (var fieldName in fieldElements){
		var field = fieldElements[fieldName];
		if (field.type != null ){
			_addFieldDescriptor(field);
		}
	}
	
	/*
	 * Load the new fieldDescriptors' HTML
	 */
	for (var fieldName in newFieldElements){
		var field = newFieldElements[fieldName];
		field.name = _instanceName + field.name;
		if (field.type != null ){
			_addFieldDescriptor(field, 'new');
		}
	}

	jQuery('#descriptors-tab .desc-titles li:first-child').addClass('active');
	jQuery('#descriptors-tab .desc-content .tab-pane:first-child').addClass('active');
	
	jQuery('#descriptors-tab .desc-titles').append(jQuery('<li/>').append(jQuery('<a/>').addClass('add-m2m').text('add m2m')));
	jQuery('#descriptors-tab .desc-titles').append(jQuery('<li/>').append(jQuery('<a/>').addClass('add-subform').text('add SubForm')));
	
	
	/* initialize form configuration tab*/
	var formActionField = _getSimpleValueWrapper("Form Action URL", "action", "attr", data.action, 2);
	jQuery("#data_add").append(formActionField);
	var formMethodField = _getListValueWrapper("Method", "method", "attr", data.method, formMethods, null, 2);
	jQuery("#data_add").append(formMethodField);
	var formSaveLabelField = _getSimpleValueWrapper("Save label", "saveLabel", "attr", data.saveLabel, 2);
	jQuery("#data_add").append(formSaveLabelField);
	var formCancelLabelField = _getSimpleValueWrapper("Cancel label", "cancelLabel", "attr", data.cancelLabel, 2);
	jQuery("#data_add").append(formCancelLabelField);
	
	/* load form tag's attributes' values */
	for ( var attributeName in data.attributes) {
		var attributeValue = data.attributes[attributeName];
		jQuery("#data_add").append(_getSimpleValueWrapper(attributeName, attributeName, "attr", attributeValue, 2));
	}
	
	

	/*
	 * if no validationHandler has been set, just take the first one, and append it to the "configuration
	 */
	jQuery("#data_add").append(jQuery('<br/>').css('clear', 'both'));
	if (!data.validationHandler){
		data.validationHandler = validationHandlers[0];
	}
	var validatorField = _getListValueWrapper("Validate Handler", "validate", "attr", data.validationHandler, validationHandlers, null, 2);
	jQuery("#data_add").append(validatorField);
	/*
	 * same thing for the renderer
	 */
	if (!data.renderer){
		data.renderer = formRenderers[0];
	}
	var rendererField = _getListValueWrapper("Form Renderer", "renderer", "attr", data.renderer, formRenderers, null, 2);
	jQuery("#data_add").append(rendererField);
	
//	/* By default, all descriptor data container are collapsed */
//	jQuery(".field-data").each(function(){
//		jQuery(this).slideUp();
//	});

	
	
	/* Remember, when suggesting new fk fields, that linked Id/Label Getters and dataMethod names should be suggested */
	callFkDaoSelectRefresh();
	
	/* Endup ui initialisation (multiselect, tab system, etc...) */
	jQuery('#descriptors-tab .desc-titles').sortable();
	initUI();
}

function _addFieldDescriptor(field, type){
	var title = 'content-'+field.fieldName;
	var liElem = jQuery('<li/>');
	liElem = jQuery('<li/>').addClass(type);
	
	jQuery('#descriptors-tab .desc-titles').append(liElem.append(jQuery('<a/>').attr('data-toggle', 'tab').attr('href', "#" + title).text(field.fieldName)));
	jQuery('#descriptors-tab .desc-content').append(jQuery('<div/>').addClass('tab-pane').attr('id', title).html(_fieldHtml(field, type)));
	
}

/* 
 * Does UI refresh (will be called after adding new elements for example 
 * (adding a M2M descriptor or setting a field as idDescriptor) 
 */
function initUI(){
	jQuery( ".sortable" ).sortable("refresh");
	
	jQuery('#descriptors-tab .desc-titles').sortable('refresh');
	
	jQuery('select.available').change(function(){
		var container = jQuery(this).parents('.field-value-bloc');
		var val = jQuery(this).val();
		if (!val){
			return;
		}
		container.find('.list input[value='+val+']').attr('checked', true);
		container.find('.field-values').append(jQuery('<span/>').attr('data-target', val).addClass('label label-info remove').text(val + "  ").append(jQuery('<i/>').addClass('icon-white icon-remove')));
		jQuery(this).find('option[value='+ val +']').hide();
		jQuery(this).val('');
	});
}

/**
 * Return the HTML code of a field descriptor element
 * @param field 	the FieldDescriptor Bean object
 * @param addClass 	the class to be added on the container
 * @param fieldType the type of the field (null means descriptor, 1 for idDescriptor, 2 for configuration)
 * @param editName 	flag to add an exeption for added M2M descriptors which's name should be set by the user. 
 * 					If set to true, the container will allow the user to define a name for the field
 * @param isIdDesc	
 * @returns {String}
 */
function _fieldHtml(field, addClass, fieldType, editName){
	var isIdDesc = fieldType == 1;
	if (field.type == "custom"){
		return '<div class="field-bloc custom" id="wrapper-'+ field.name +'"><div class="field-title"><div class="name-val" style="float: left"><span class="element-type">Custom Field : </span>'+ field.name +'</div><div class="active-elem"><input type="checkbox" name="fields['+ field.name +'][active]" checked="checked"></div><div style="clear:both"></div></div><div class="field-data"><br style="clear: both;"><input type="hidden" name="fields['+ field.name +'][type]" class="field_type" value="custom"><input type="hidden" name="fields['+ field.name +'][new]" value="false"><input type="hidden" name="fields['+ field.name +'][instanceName]" value="'+ field.name +'"></div><br style="clear:both"></div>';
	}
	
	var strAddClass = addClass ? " "+addClass : "";
	
	/* 
	 * Base field attributes (becuase they hav to be linked to the main table's column) may be set as FK.
	 * Possible if the field isn't a PK yet
	 */
	var setFK = field.type == "base" && isIdDesc != true ? "<button class='naked set-fk' onclick='setFK(\""+ field.name +"\");return false;' title='Set FK'>&nbsp;</button>" : "";
	/* same thing for PK switch (one more condition) : 
	 *  -- > no existing idDescriptor yet */
	var setPK = field.type == "base" && isIdDesc != true && idDesc == null ? "<button class='naked set-pk' onclick='setPK(\""+ field.name +"\");return false;' title='Set PK'>&nbsp;</button>" : "";
	
	/* if the editName param is true, then add an editable "Name" field for setting the instance Name.
	 * Note : the edit name param will be set to "true" only if this is a new field added on the fly,
	 * therefore, no need to get field's name attribute from the "_getFieldNames" function*/
	var name = !editName ? field.name : "<input type='text' name='" + 'fields['+field.name+'][instanceNameInput]' + "' value=''/>";
	
	/* the "active" checkbox is defines if a given field should be kept / added to the form */
	var nameAttr = _getFieldNames(fieldType, "active", field.name);
	var checkActive = "<input type='checkbox' name='"+ nameAttr +"' "+ (field.active ? "checked='checked'" : "") +"/>";
	
	/* now  build the html ... */
	switch (field.type) {
		case "cust":
			elementType = "Custom";
			break;
		case "subform":
			elementType = "Sub Form";
			break;
		case "base":
			elementType = "Bean field (Base)";
			break;
		case "fk":
			elementType = "Bean field (FK)";
			break;
		case "m2m":
			elementType = "Simple M2M relation";
			break;
	}
	
	var fieldHTML;
	if (field.type != 'subform'){
		fieldHTML = _html(_getFieldElements(field, fieldType));
		/* depending on the field type, specific elements should be added, 
		 * like the 'linkedIdGetter' select box if it is a FKFieldDescriptor.
		 * 
		 * This script reflexes the type hierarchy, therefore, a FK Descriptor is built with Base AND FK descriptors' HTML */
		switch (field.type){
		case "base":
			fieldHTML += _html(_getBaseElements(field, fieldType));
			break;
		case "fk":
			fieldHTML += _html(_getBaseElements(field, fieldType));
			fieldHTML += "<br style='clear: both'/>"+_html(_getFKElements(field, fieldType));
			break;
		case "m2m":
			fieldHTML += "<br style='clear: both'/>"+_html(_getM2mElements(field, fieldType));
			break;
		}
	} else {
		fieldHTML = _getSubFormHtml(field, fieldType);
	}
	
	var html = "<div class='field-title'><div class='name-val' style='float: left'><span class='element-type'>" + elementType + " : </span>" + name + "</div><div class='active-elem'>" + checkActive + "</div>" + "<div style='float: right'>"+setFK+setPK+"</div><div style='clear:both'></div></div>" +
		"<div class='field-data'>"+	fieldHTML + "</div>";
	
	return "<div class='field-bloc "+field.type+strAddClass+"' id='wrapper-"+field.name+"'>"+
		html +
	"<br style='clear:both'/></div>";
}

function _getSubFormHtml(field, fieldType) {
	var name = _getSimpleValueWrapper("Field Name", "fieldname", field.name, field.fieldName, fieldType); 
	var label =_getSimpleValueWrapper("Label", "label", field.name, field.label, fieldType);
	var wrapperRenderer =_getListValueWrapper("Wrapper Renderer", "wrapper_renderer", field.name, field.wrapperRenderer, subformWrapperRenderers, null, fieldType);
	
	var br = jQuery("<div/>").append(jQuery("<br/>").css('clear', 'both'));
	var br2 = br.clone();
	
	var description = _getSimpleValueWrapper("Description", "description", field.name, field.description, fieldType, true);
	
	var typeElemWrap = _getHiddenElemWrapper(fieldType, "type", field.name, field.type, 'field_type');
	var isNewElemWrap = _getHiddenElemWrapper(fieldType, "new", field.name, field.is_new);
	var instanceNameWrap = _getHiddenElemWrapper(fieldType, "instanceName", field.name, field.name);

	var itemWrapper = _getListValueWrapper("Item Wrapper", "item_wrapper", field.name, field.itemWrapperRenderer, itemWrapperRenderers, null, fieldType);

	var formWrapper = _getListValueWrapper("Sub Form", "sub_form", field.name, field.form, forms, "refreshSubFormSettings", fieldType);

	var daoMethods = _getDaoMethods(field.daoData);
	var beansGetter = _getListValueWrapper("Sub Form", "beans_getter", field.name, field.beansGetter, daoMethods, null, fieldType);
	
	var filterdMethods = _getFilteredMethods(field.daoData);
	fieldGetters = filterdMethods['getters'];
	fieldSetters = filterdMethods['setters'];
	var fkGetter = _getListValueWrapper("Parent id FK Getter", "fk_getter", field.name, field.fkGetter, fieldGetters, fieldType);
	var fkSetter = _getListValueWrapper("Parent id FK Setter", "fk_setter", field.name, field.fkSetter, fieldSetters, fieldType);

	return _html([name, label, wrapperRenderer, br, description, formWrapper, br2, beansGetter, fkGetter, fkSetter, typeElemWrap, isNewElemWrap, instanceNameWrap, itemWrapper]);
}

/* 
 * Simple helper that creates HTML from a jQuery Object 
 */
function _html(fields){
	var html = '';
	for ( var i = 0;i < fields.length; i++) {
		field = fields[i];
		html += field.html();
	}
	return html;
}

/**
 * Generate a jQuery object from a field object.
 * This function created the field that are common to all fields (except the custom ones):
 * name, label, renderer, ...
 * @param field 	the FieldDescriptor Bean object
 * @param fieldType	tells if the field's POST name sould be fields[..., config[... or idDescr[
 * @returns {Array}	a set of jQuery objects
 */
function _getFieldElements(field, fieldType){
	/*
	 * Build each descriptor's field in jQuery ... add breacks for convienence, 
	 * and that's it, return the orderer array of objects 
	 */
	var name = _getSimpleValueWrapper("Field Name", "fieldname", field.name, field.fieldName, fieldType); 
	var label =_getSimpleValueWrapper("Label", "label", field.name, field.label, fieldType);
	var renderer =_getListValueWrapper("Renderer", "renderer", field.name, field.renderer, field.type == 'm2m' ? multiRenderers : singleRenderers, null, fieldType);
	var wrapperRenderer =_getListValueWrapper("Wrapper Renderer", "wrapper_renderer", field.name, field.wrapperRenderer, wrapperRenderers, null, fieldType);
	var formatter =_getListValueWrapper("Formatter", "formatter", field.name, field.formatter, formatters, null, fieldType);
	var validatorsElem =_getMultiListValueWrapper("Validators", "validators", field.name, field.validators, validators, fieldType);
	
	var br = jQuery("<div/>").append(jQuery("<br/>").css('clear', 'both'));
	var br2 = br.clone();
	
	var description = _getSimpleValueWrapper("Description", "description", field.name, field.description, fieldType, true);
	
	var typeElemWrap = _getHiddenElemWrapper(fieldType, "type", field.name, field.type, 'field_type');
	var isNewElemWrap = _getHiddenElemWrapper(fieldType, "new", field.name, field.is_new);
	var instanceNameWrap = _getHiddenElemWrapper(fieldType, "instanceName", field.name, field.name); 
	
	var editRightWrap = _getRightElement("Edit Right", "edit_condition", field.name, field.editCondition, conditions, null, fieldType);
	var viewRightWrap = _getRightElement("View Right", "view_condition", field.name, field.viewCondition, conditions, null, fieldType);
	
	return [name, label, renderer, wrapperRenderer, formatter, br, validatorsElem, description, editRightWrap, viewRightWrap, br2, typeElemWrap, isNewElemWrap, instanceNameWrap];
}

/**
 * Simple helper that generates a jQuery hidden element
 * @param fieldType	tells if the field's POST name sould be fields[..., config[... or idDescr[
 * @param attr the name of the attribute 
 * @param name the name of the fiedDescriptor instance
 * @param value the value of the attribute.
 * @returns jQuery Object
 */
function _getHiddenElemWrapper(fieldType, attr, name, value, fieldClass){
	var nameAttr = _getFieldNames(fieldType, attr, name);
	
	//console.log(nameAttr);
	//console.log(value);
	if (value === null) {
		console.warn("WARNING! Hidden field "+name+" has empty value");
	}
	
	var typeElem = jQuery('<input/>').attr('type', "hidden").attr('name', nameAttr);
	if (fieldClass){
		typeElem.addClass(fieldClass);
	}
	typeElem.val(value);
	var typeElemWrap = jQuery("<div/>").append(typeElem);
	return typeElemWrap;
}

/**
 * Generates and returns the getter & setter attributes fields that are 
 * specific to instances of the BaseFieldDescriptor class (and extending classes) 
 * @param field 	the BaseFieldDescriptor Bean object
 * @param fieldType	tells if the field's POST name sould be fields[..., config[... or idDescr[
 * @returns jQuery Object Array
 */
function _getBaseElements(field, fieldType){
	var getter =  _getListValueWrapper("Getter", "getter", field.name, field.getter, getters, null, fieldType);
	var setter = _getListValueWrapper("Setter", "setter", field.name, field.setter, setters, null, fieldType);
	return [getter, setter];
}

/**
 * Same thing for FKFieldDescriptor
 * @param field 	the FKFieldDescriptor Bean object
 * @param fieldType	tells if the field's POST name sould be fields[..., config[... or idDescr[
 * @returns jQuery Object Array
 */
function _getFKElements(field, fieldType){
	/*
	 * get the methods from the linked DAO (getters only)
	 */
	var filterdMethods = _getFilteredMethods(field.daoData);
	fieldGetters = filterdMethods['getters'];
	
	/*
	 * Select box for choosing / changing the linked DAO
	 */
	var linkedDao = _getListValueWrapper("Linked Dao", "linkedDao", field.name, field.daoName, daos, "refreshFKDaoSettings", fieldType);
	
	/*
	 * Select box of linkedDao's methods
	 */
	var daoMethods = _getDaoMethods(field.daoData);
	var dataMethod = _getListValueWrapper("dataMethod", "dataMethod", field.name, field.dataMethod, daoMethods, null, fieldType);
	
	/*
	 * Select box of linkedDao's getters
	 */
	var linkedIdGetter = _getListValueWrapper("Linked id Getter", "linkedIdGetter", field.name, field.linkedIdGetter, fieldGetters, null, fieldType);
	
	/*
	 * Select box of linkedDao's getters
	 */
	var linkedLabelGetter = _getListValueWrapper("Linked label Getter", "linkedLabelGetter", field.name, field.linkedLabelGetter, fieldGetters, null, fieldType);
	
	return [linkedDao, dataMethod, linkedIdGetter, linkedLabelGetter];
}


/**
 * Same thing for M2MFieldDescriptor
 * @param field 	the FKFieldDescriptor Bean object
 * @param fieldType	tells if the field's POST name sould be fields[..., config[... or idDescr[
 * @returns jQuery Object Array
 */
function _getM2mElements(field, fieldType){
	/*
	 * Get getters, setters, and other methods of the mapping DAO.
	 */
	var filterdMethods = _getFilteredMethods(field.mappingDaoData);
	var fieldGetters = filterdMethods['getters'];
	var fieldSetters = filterdMethods['setters'];
	var mappingDaoMethods = _getDaoMethods(field.mappingDaoData);

	var helpLink = $('<a/>').attr('href', "#").text("Click here to understand how to configure a M2M descriptor").addClass("help pop");
	helpLink = $('<div/>').append(helpLink);
	
	/*
	 * Build mapping DAO select box
	 */
	var mappingDao = _getListValueWrapper("Mappging Dao", "mappingDao", field.name, field.mappingDaoName, daos, "refreshM2MmappingDaoSettings", fieldType);
	
	/*
	 * Build all methods select boxes
	 */
	var mappingIdGetter = _getListValueWrapper("Mapping Id Getter", "mappingIdGetter", field.name, field.mappingIdGetter, fieldGetters, null,fieldType);
	var mappingLeftKeySetter = _getListValueWrapper("Mapping Left Key Setter", "mappingLeftKeySetter", field.name, field.mappingLeftKeySetter, fieldSetters, null, fieldType);
	var mappingRightKeyGetter = _getListValueWrapper("Mapping Right Key Getter", "mappingRightKeyGetter", field.name, field.mappingRightKeyGetter, fieldGetters, null, fieldType);
	var mappingRightKeySetter = _getListValueWrapper("Mapping Right Key Setter", "mappingRightKeySetter", field.name, field.mappingRightKeySetter, fieldSetters, null, fieldType);
	var beanValuesMethod = _getListValueWrapper("Beans values method", "beanValuesMethod", field.name, field.beanValuesMethod, mappingDaoMethods, null, fieldType);
	
	var br = jQuery("<div/>").append(jQuery("<br/>").css('clear', 'both'));
	
	/*
	 * ... And do the same on the linked Dao side ... 
	 */
	var filterdMethods = _getFilteredMethods(field.linkedDaoData);
	var fieldGetters = filterdMethods['getters'];
	var fieldSetters = filterdMethods['setters'];
	var daoMethods = _getDaoMethods(field.linkedDaoData);
	
	var linkedDao = _getListValueWrapper("Linked Dao", "linkedDao", field.name, field.linkedDaoName, daos, "refreshM2MlinkedDaoSettings", fieldType);
	var linkedIdGetter = _getListValueWrapper("linkedIdGetter", "linkedIdGetter", field.name, field.linkedIdGetter, fieldGetters, null, fieldType);
	var linkedLabelGetter = _getListValueWrapper("linkedLabelGetter", "linkedLabelGetter", field.name, field.linkedLabelGetter, fieldGetters, null, fieldType);
	var dataMethod = _getListValueWrapper("dataMethod", "dataMethod", field.name, field.dataMethod, daoMethods, null, fieldType);
	
	return [helpLink, br, mappingDao, mappingIdGetter, mappingLeftKeySetter, mappingRightKeyGetter, mappingRightKeySetter,beanValuesMethod, br, linkedDao, linkedIdGetter, linkedLabelGetter, dataMethod];
}

/**
 * Helper to get the name of a field in the configuration form.
 * @param fieldType : the type of the field. Can have 3 values : 
 *   - 1 for idDescriptor
 *   - 2 for configuration attributes like form renderer, validation handler, tag attributes, etc...
 *   - 0 or not set : by default the field will be treated as a fieldDescriptor
 * @param prop : the name of the property that is handling the field
 * @param name : the name of the fieldDescriptor (will be usefull only if fieldType isn't set or equals 0
 * @returns {String} the name of the field
 * 
 * Example : 
 *   * _getFieldNames(1, 'getter', 'anything') returns idField[getter]
 *   * _getFieldNames(null, 'getter', 'emailDescriptor') returns fields[emailDescriptor][getter]
 */
function _getFieldNames(fieldType, prop, name){
	if (!fieldType || fieldType == 0) return 'fields['+name+']['+prop+']';
	else if (fieldType == 1){
		return 'idField['+prop+']';
	}else if (fieldType == 2){
		return  'config['+prop+']';
	}
}

/**
 * Builds a simple text input using jQuery and return the object 
 * @param label:		the label to display in front of the field
 * @param prop:			the property handled by the field
 * @param name:			the name of the fieldDescriptor (if it is a fieldDescriptor)
 * @param value:		the value (if any) to be set in the text input
 * @param fieldType:	the type of the field (1: idDesc, 2: config, 0 or null: fieldDescriptor
 * @returns jQuery Object
 */
function _getSimpleValueWrapper(label, prop, name, value, fieldType, isTextArea){
	var fieldNameAttr = _getFieldNames(fieldType, prop, name);
	var divElem = jQuery("<div/>").addClass('field-value-bloc prop-'+prop);
	divElem.append(jQuery('<label/>').text(label));
	var input = !isTextArea ? 
		jQuery("<input/>").attr('type', 'text').attr('id', name+"_"+prop).attr('name', fieldNameAttr).attr('value',value)
		: jQuery('<textarea/>').attr('id', name+"_"+prop).attr('name', fieldNameAttr).text(value ? value : "");
	input.attr('placeholder', label);
	divElem.append(input);
	return jQuery("<div/>").append(divElem);
}

/**
 * Builds a strict autocomplete textbox 
 * @param label:		the label to display in front of the field
 * @param prop:			the property handled by the field
 * @param name:			the name of the fieldDescriptor (if it is a fieldDescriptor)
 * @param selectValue	the current value to be selected
 * @param fieldType		the type of the field (1: idDesc, 2: config, 0 or null: fieldDescriptor
 * @returns
 */
function _getRightElement(label, prop, name, selectValue, list, fieldType){
	/* get the name of the field */
	var fieldNameAttr = _getFieldNames(fieldType, prop, name);
	
	var divElem = jQuery("<div/>").addClass('field-value-bloc right');
	divElem.append(jQuery('<label/>').text(label));
	var iconElem = jQuery('<i/>').addClass('icon-white icon-edit').attr('data-id', fieldNameAttr);
	
	divElem.append(jQuery('<span/>').addClass('label label-info right').text(selectValue ? selectValue + "  " : "").append(iconElem));
	divElem.append(jQuery('<input/>').attr('type', "hidden").attr('name', fieldNameAttr).attr('value', selectValue));
	
	return jQuery("<div/>").append(divElem);
}

function _showRightModal(event){
	var target = jQuery(event.target);
	if (target.is('span')){
		iElem = target.find('i'); 
	}else{
		iElem = target;
	}
	var fieldTarget = iElem.attr('data-id');
	jQuery('#right-modal .rightTarget').attr('value', fieldTarget);
	jQuery('#right-modal .search-query').attr('value', "");
	jQuery('#right-modal .set-right').show();
	
	jQuery('#right-modal').modal();
}

function _showM2MHelpModal(event){
	jQuery('#m2m-help-modal').modal();
}
/**
 * Builds a select box field for handling form or fieldDescriptor properties.
 * Sometimes, this select box may trigger the refresh of other select boxes 
 * (when the DAO changes, the related methods' lists should be updated and suggested)
 * @param label:		the label to display in front of the field
 * 
 * @param prop:			the property handled by the field
 * 
 * @param name:			the name of the fieldDescriptor (if it is a fieldDescriptor)
 * 
 * @param selectValue	the current value to be selected
 * 
 * @param list			the values of the list
 * 
 * @param settings		if set these settings object will be used in order to refresh the child list values.
 * 						These settings will be set only for DAO select boxes:
							- fkDesc.linkedDao,
							- m2mDesc.mappingDao,
							- m2mDesc.linkedDao.
 *  					Changing the value of one of these select boxes will push new values into the linkedIdGetter, beanValuesMethods, etc... lists and suggest to select the best matches.
 *  
 * @param fieldType		the type of the field (1: idDesc, 2: config, 0 or null: fieldDescriptor
 * @returns
 */
function _getListValueWrapper(label, prop, name, selectValue, list, settings, fieldType){
	/*
	 * As explained above, if the settings parameter is set, trigger refresh behavior if the value is changed 
	 */
	var onchangehtml = settings ? "refreshBeanMethods("+settings+", \""+name+"\", this.value)" : "";
	
	/*
	 * get the name of the field 
	 */
	var fieldNameAttr = _getFieldNames(fieldType, prop, name);
	
	var divElem = jQuery("<div/>").addClass('field-value-bloc');
	divElem.append(jQuery('<label/>').text(label));
	/*
	 * Build the select box
	 */
	var selectElem = jQuery("<select/>").attr('id', name+"_"+prop).attr("name", fieldNameAttr).attr("onchange", onchangehtml);
	
	selectElem.append(jQuery("<option/>").attr('value', '').html('- ' + capitalize(label) + " -"));
	for (var i = 0; i < list.length ; i++){
		var opt = list[i];
		selectElem.append(jQuery("<option/>").attr('value', opt).html(opt).attr('selected', selectValue == opt));
	}
	divElem.append(selectElem);
	

	return jQuery("<div/>").append(divElem);
}

/**
 * Buid a multiselect box widget (using the jquery multiselect plugging) that allows ordering.
 * This function is used only for validators list so far.
 * 
 * @see the previous function for parameters explanations
 * 
 * 
 * @returns
 */
function _getMultiListValueWrapper(label, prop, name, selectValues, list, fieldType){
	var fieldNameAttr = _getFieldNames(fieldType, prop, name);
	
	var availables = jQuery('<select/>').addClass('available');
	var divElem = jQuery("<div/>").addClass('field-value-bloc multi').append(jQuery("<label/>").text(label)).append(availables);
	var selected = jQuery('<div>').addClass('field-values');
	var values = jQuery('<div>').addClass('list');
	availables.append(jQuery('<option/>').val("").text("- Add a validator -"));
	for (var i = 0; i < list.length; i++){
		var opt = list[i];
		var chbx = jQuery('<input/>').attr('type', 'checkbox').attr('name', fieldNameAttr+"[]").val(opt);
		var optElem = jQuery('<option/>').val(opt).text(opt);
		if (selectValues && selectValues.indexOf(opt) != -1) {
			chbx.attr("checked", true);
			var spanElem = jQuery('<span/>').addClass('label label-info remove').text(opt+"  ").append(jQuery('<i/>').addClass('icon-white icon-remove'));
			spanElem.attr(opt);
			selected.append(spanElem);
			optElem.hide();
		}
		availables.append(optElem);
		values.append(chbx);
	}
	
	divElem.append(selected).append(values);
	jQuery(".field-values").sortable({
		update: function( event, ui ) {
			var validator = jQuery(event.toElement).attr('data-target');
			var list = jQuery(this).parent().find('.list');
			var chbx = list.find('input[value='+validator+']');
			var clone = chbx.clone();
			chbx.remove();
			var index = jQuery(event.toElement).index();
			
			if (index === 0) {
				list.prepend(clone);
	        } else {
	        	list.children().eq(index - 1).after(clone);
	        }
		}
	});
	jQuery(".field-values").disableSelection();
	return jQuery("<div/>").append(divElem);

//	var fieldNameAttr = _getFieldNames(fieldType, prop, name);
//	var finalList = list;
//	if (selectValues){
//		finalList = selectValues.slice(0);
//		for (var i = 0; i < list.length ; i++){
//			var opt = list[i];
//			if (selectValues.indexOf(opt) == -1) finalList.push(opt);
//		}
//	}
//	
//	var selectElem = jQuery("<select/>")
//		.attr("name", fieldNameAttr+"[]").addClass("multiselect")
//		.attr("id", name+"_"+prop)
//		.attr("multiple", true);
//	for (var i = 0; i < finalList.length ; i++){
//		var opt = finalList[i];
//		var optionElem = jQuery("<option/>").val(opt).text(opt);
//		if (selectValues && selectValues.indexOf(opt) != -1) {
//			optionElem.attr("selected", true);
//		}
//		optionElem.appendTo(selectElem);
//	}
//	
//	var divElem = jQuery("<div/>").addClass('field-value-bloc');
//	jQuery("<label/>").text(label).appendTo(divElem);
//	selectElem.appendTo(divElem);
//
//	return jQuery("<div/>").append(divElem);
}

/**
 * Function called when a DAO select boxes' value changes.
 * It will refresh the bean and DAO methods select boxes matching the new selected DAO (completeDaoFields function)
 * 
 * E.G.:	if a fk "linkedDao" select box changes, then the related linkedIdGetter,
 * 			linkedLabelGetter and dataMethod lists will be updated
 * 
 * Then, it will select best fitting values in those select lists (_fillSelectOptions function)
 * 
 * @param settings:		the settings that will be used to suggest the best fitting values inside the updated lists
 * @param fieldName:	the name of the field
 * @param newDaoName:	the new value of the DAO select box
 */
function refreshBeanMethods(settings, fieldName, newDaoName){
	//var method = settings.method ? settings.method : "daoData";
	var method;
	if (settings.method && settings.method == 'formMainDaoData') {
		method = 'getDaoDataFromForm';
	} else {
		method = 'getDaoDataFromInstance';
	}
	
	/*
	 * Get the DAO data for the new DAO (ie DAO methods and related bean's getters and setters) 
	 */
	jQuery.ajax({
	  url: bceSettings.rootUrl + "bceadmin/" + method,
	  data: "instance="+newDaoName,
	  success: function (data){
		  /*
		   * Update the related lists with the DAO data
		   */
		  completeDaoFields(data, settings, fieldName);
	  },
	  error: function(error){jQuery("#error").html(error);},
	  dataType: 'json'
	});
}

/**
 * Define the new options that should fill the new DAO's related select boxes,
 * and pass the values and suggestion settings to the _fillSelectOptions function
 * 
 * For FKDescriptors, the related select boxes are related to the linkedDao: 
 * 		- linkedIdGetter
 * 		- linkedLabelGetter
 * 		- dataMethod
 * 
 * For M2MDescriptors, the related select boxes are related to the mappingDAO:
 * 		- mappingIdGetter,
 *		- mappingLeftKeySetter,
 *		- mappingRightKeyGetter,
 *		- mappingRightKeySetter,
 *   	- beanValuesMethod
 * ... and to the linkedDAO : 
 * 		- linkedIdGetter
 * 		- linkedLabelGetter
 * 		- dataMethod
 * 
 * @param data:		 	the new dao's data (that contains both dao's methods and bean's getters and setters
 * @param settings:		the settings that will tell which fields should be updated, and which values (patterns) should be suggested
 * @param fieldName:	the name of the field
 */
function completeDaoFields(data, settings, fieldName){
	/*
	 * retrive getters and setters 
	 */
	var filterdMethods = _getFilteredMethods(data);
	var getters = filterdMethods['getters'];
	var setters = filterdMethods['setters'];
	
	/*
	 * ... and dao's methods 
	 */
	var all = _getDaoMethods(data);

	/*
	 * Depending on the fields described in settings, fill the lists with
	 *  - bean getters (e.g. linkedIdGetter),
	 *	- setters (mappingLeftKeySetter)
	 *	- DAO methods (dataMethod)
	 */
	for(var key in settings){
		if (typeof(key) == 'undefined') continue;
		var setting = settings[key];
		var selectElem = jQuery("#"+fieldName+"_"+setting.field);
		var methods = null;
		if (setting.methodType == "getter"){
			methods = getters;
		}else if (setting.methodType == "setter"){
			methods = setters;
		}else{
			methods = all;
		}
		_fillSelectOptions(methods, selectElem, setting.defaultSelect);
	}
}

/**
 * Fills a given select box with provided values, and selects the suggested value
 * 
 * @param values			: The values for the new options
 * @param selectElem		: The select box object which will be filled
 * @param defaultValueType	: The pattern that will allow to suggest a value (there are two special value):
 * 								- left : match the main table name (eg if main dao is userDao, then the suggested value must contain "user"
 * 								- right : match different than the main table's name, or equals getId or setId
 * 								- any other value is taken directly as the pattern
 */
function _fillSelectOptions(values, selectElem, defaultValueType){
	/*
	 * define the pattern to be matched
	 */
	if (defaultValueType !== null){
		var pattern = defaultValueType;
		if (pattern == "left"){
			pattern = bceSettings.mainBeanTableName;
		}else if (pattern == "right"){
			pattern = "^((?!"+bceSettings.mainBeanTableName+"|[gs]etId).)*$";
		}
		var match = new RegExp(pattern, 'gi');
	}
	
	/*
	 * Empty the select box
	 */
	selectElem.children().remove();
	var selectVal = "";
	/*
	 * Loop on new values, check for matching result, and fill the select box
	 */
	jQuery.each(values, function(i, value) {
		var optElem = jQuery("<option/>").val(value).text(value);
		if (defaultValueType && match.exec(value) && selectVal == ""){
			selectVal = value;
			optElem.attr('seleted', true);
		}
		optElem.appendTo(selectElem);
	});
	selectElem.val(selectVal);
}

/**
 * Parses the list of bean's functions and separate them between getters and setters
 * 
 * @param daoData : the data of the bean's related DAO.
 * @returns {{Array}} the array of methods with two keys at first level : 'getters' and 'setters'
 */
function _getFilteredMethods(daoData){
	var methods = [];
	methods['getters'] = [];
	methods['setters'] = [];
	if (daoData){
		jQuery.each(daoData.beanClassFields, function(i, field) {
			methods['getters'].push(field.getter.name);
			methods['setters'].push(field.setter.name);
		});
	}
	return methods;
}

/**
 * Returns methods of a given DAO
 * @param daoData
 * @returns {Array} the names of the methods
 */
function _getDaoMethods(daoData){
	var methods = [];
	if (daoData) {
		jQuery.each(daoData.daoMethods, function(i, method) {
			methods.push(method);
		});
	}
	return methods;
}

/**
 * Used to add a new M2M block dynamically (Base and FK field descriptors are already detected, no need to add them)
 */
function addM2MBlock(){
	/*
	 * Customize the default data in order to have unique name
	 */
	emptyM2MField.fieldName = "newField_"+newId;
	emptyM2MField.name = "newField_"+newId;
	
	/*
	 * Build the HTML and append it 
	 */
	var html = _fieldHtml(emptyM2MField, "new", 0, true);
	
	var liElem = jQuery('<li/>');
	
	jQuery('#descriptors-tab .desc-titles li:last-child').before(liElem.append(jQuery('<a/>').attr('data-toggle', 'tab').attr('href', "#" + emptyM2MField.fieldName).text(emptyM2MField.fieldName)));
	jQuery('#descriptors-tab .desc-content').append(jQuery('<div/>').addClass('tab-pane').attr('id', emptyM2MField.fieldName).html(html));
	

	
	newId ++;
	
	/*
	 * Refresh javascript behavior such as slidup - down, and multiselect widjet
	 */
	initUI("wrapper-" + emptyM2MField.name);
}

/**
 * Switches a Base or FK field descriptor into the idFieldDescriptor (in configuration tab) 
 * @param fieldName the name of the field to be switched
 */
function setPK(fieldName){
	/*
	 * Get the field
	 */
	var field = fieldElements[fieldName];
	if (!field) field = newFieldElements[fieldName];
	
	/*
	 * Generate the html in 'id descriptor' mode
	 */
	var idHtml = _fieldHtml(field, "idField", 1, null, true);
	
	/*
	 * Select the config tab
	 */
	jQuery( "#tabs" ).tabs('select', 1);
	
	/*
	 * remove the fieldDescriptor
	 */
	
	jQuery("#wrapper-"+fieldName).remove();
	/*
	 * append the id descriptor
	 */
	jQuery('#id_desc').html(idHtml);
	
	/*
	 * One an idFieldDescriptor has been set, no other can be
	 */
	jQuery('.set-pk').each(function(){
		jQuery(this).remove();
	});
	
	/*
	 * refresh multiselect and accordion system
	 */
	initUI("wrapper-"+fieldName);
}

/*
 * Triggers the setFK function for the event target
 */
function setFK2(event){
	var fieldName = event.data.elem;
	setFK(fieldName);
}

/**
 * Will switch a base field descriptor to a FK field descriptor
 * 
 * Note : 
 * 		This is in case the foreign key has not been detected (MyIsam engine, or DB newbee :) for instance ).
 * 		Else, the FK has already been detected and suggested
 */
function setFK(fieldName){
	/*
	 * Get the field's data from the field data variable
	 */
	var field = fieldElements[fieldName];
	
	if (field.type != "base") return;
	
	/*
	 * Build FK HTML elements
	 */
	var elements = _getFKElements(field);
	
	/*
	 * Add a line breack to append FK specific elements (linked DAO, linkedIdGetter and linkedLabelGetter,
	 * The append the elements 
	 */
	jQuery("#wrapper-"+fieldName+" .field-data").append(jQuery("<br/>").css('clear', 'both'));
	jQuery.each(elements, function(i, element) {
		jQuery("#wrapper-"+fieldName+" .field-data").append(element);
	});
	
	/*
	 * Update field's type
	 */
	field.type = "fk";
	jQuery("#wrapper-"+fieldName+" .field-data input.field_type").attr('value','fk');
	jQuery("#wrapper-"+fieldName+" .field-data input.field_type").val('fk');
	
	/*
	 * unset "setFK" behavior and icon and replace it with the "unsetFK" one
	 */
	jQuery("#wrapper-"+fieldName+" .field-title button.set-fk").each(function(){
		jQuery(this).removeClass("set-fk").addClass('unset-fk').unbind('click').click({elem : fieldName}, unSetFK);
	});
}

/**
 * Will do the opposite than the previous function (setFK) : switches a FK descriptor to a Base one
 * @param event
 */
function unSetFK(event){
	var fieldName = event.data.elem;
	
	var field = fieldElements[fieldName];
	
	jQuery('#'+fieldName+'_linkedDao').parent().parent().remove();
	jQuery('#'+fieldName+'_dataMethod').parent().parent().remove();
	jQuery('#'+fieldName+'_linkedIdGetter').parent().parent().remove();
	jQuery('#'+fieldName+'_linkedLabelGetter').parent().parent().remove();
	
	field.type = "base";
	
	jQuery("#wrapper-"+fieldName+" .field-data br").last().remove();
	
	jQuery("#wrapper-"+fieldName+" .field-title button.unset-fk").each(function(){
		jQuery(this).removeClass("unset-fk").addClass('set-fk').unbind('click').click({elem : fieldName}, setFK2);
	});
}

/**
 * Will loop on every new detected FK fields, (see completeInstanceData function) 
 * and suggest the appropriate getters for both "linkedIdGetter and linkedLabelGetter" select list
 */
function callFkDaoSelectRefresh(){
	jQuery.each(fkRefreshCalls, function(i, fieldName) {
		var linkedIdGetterElem = jQuery("#" + _instanceName + fieldName + "_linkedIdGetter");
		var linkedLabelGetterElem = jQuery("#" + _instanceName + fieldName + "_linkedLabelGetter");
		
		_selectFromSettings(linkedIdGetterElem, refreshFKDaoSettings.id_getter);
		_selectFromSettings(linkedLabelGetterElem, refreshFKDaoSettings.label_getter);
	});
}

/**
 * Selects the best matching value in a given selectbox
 * 
 * @param elem: the select box element
 * @param setting : the setting variable (on of the elements of the globale refreshFKDaoSettings variable)
 */
function _selectFromSettings(elem, setting){
	/*
	 * Get the pattern to match
	 */
	var defaultValueType = setting.defaultSelect;
	if (defaultValueType !== null){
		var match = new RegExp(defaultValueType, 'gi');
	}
	var selectVal = "";
	
	/*
	 * loop on each option and select the first matching one
	 */
	elem.children().each(function (){
		var optElem = jQuery(this);
		var optValue = optElem.val();
		if (defaultValueType && match.exec(optValue) && selectVal == ""){
			selectVal = optValue;
			optElem.attr('seleted', true);
		}
	});
	
	elem.val(selectVal);
}

function capitalize (text) {
    return text.charAt(0).toUpperCase() + text.slice(1).toLowerCase();
}
function escapeStr( str) {
 if( str)
     return str.replace(/([ #;&,.+*~\':"!^$[\]()=>|\/@])/g,'\\$1');
 else
     return str;
}