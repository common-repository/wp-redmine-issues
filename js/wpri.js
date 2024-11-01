/*
 * WP-Redmine-Issues (WPRI) by arcanasoft
**/

function wpriSubmitForm(submitel) {
	var curform=jQuery(submitel).closest('form')
	var allgood = true;
	jQuery(curform).find(':input').removeClass('required-field-error');
	jQuery.each(jQuery(curform).find(':input'), function (inpkey, input) {
		if(isMandatoryInput(input)){
			if(jQuery(input).val() == '') {
				allgood = false;
				markEmptyInput(input);
				//return false;
			}
		}
	});
	if(allgood) {
		jQuery(curform).submit();
	} else {
		addErrorMessage(curform, 'Nicht alle Pflichtfelder ausgefüllt');
	}
}

function isMandatoryInput(inputel) {
	var mandattr = jQuery(inputel).attr('wprimandatoryinput');
	return (typeof mandattr !== typeof undefined && mandattr !== false);
}

function addErrorMessage(form, message) {
	jQuery('#wpricurerrormsg').remove();
	jQuery('<div id="wpricurerrormsg" class="error settings-error notice"><p><strong>'+message+'</strong></p></div>').insertBefore(form);
}

function markEmptyInput(input) {
	jQuery(input).addClass('required-field-error');
}

jQuery(document).ready(function() {
console.log(jQuery('.wpri-input-form').length);
if(jQuery('.wpri-input-form').length > 0) {
jQuery.each(jQuery('.wpri-input-form'), function (formkey,form) {
	if(jQuery(form).has(':submit')) {
		jQuery.each(jQuery(form).find(':submit'), function (inputkey, input) {
			jQuery(input).click(function(event) {
				event.preventDefault();
				wpriSubmitForm(event.target);
			});
		});
		console.log();
	}
});

}
});
