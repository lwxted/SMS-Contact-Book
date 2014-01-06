/**
 * Form functions
 */

/**
 * Update the front end when the form is submitting
 * @param  {string} form         The element ID used to locate the form item
 * @param  {string} submitButton The element ID used to locate the submit button within a desired form
 * @param  {string} loadingIcon  The element ID used to locate the loading button within a desired form
 * @param  {string} formError    The element ID used to locate the form error paragraph within a desired form
 */
function formSubmitting (form, submitButton, loadingIcon, formError) {
	$(form + " " + loadingIcon).show();
	$(form + " " + formError).html('');
	$(form + " " + formError).hide();
	$(form + " " + submitButton).attr('disabled', 'disabled');
}

/**
 * Capture and process the incoming data passed by the PHP.
 * @param  {string} form         The element ID used to locate the form item
 * @param  {string} submitButton The element ID used to locate the submit button within a desired form
 * @param  {string} loadingIcon  The element ID used to locate the loading button within a desired form
 * @param  {string} formError    The element ID used to locate the form error paragraph within a desired form
 * @param  {string} data         The incoming JSON data
 * @param  {function} succeeded  The function to be executed when the incoming JSON data indicated that 
 *                               the preivous action has successfully performed
 * @param  {function} failed     The function to be executed when the incoming JSON data indicated that 
 *                               the preivous action has failed to perform
 */
function formReturned (form, redirecting, submitButton, loadingIcon, formError, data, succeeded, failed, retrieved) {
	succeeded = typeof succeeded !== 'undefined' ? succeeded : function () {};
	failed = typeof failed !== 'undefined' ? failed : function () {};
	retrieved = typeof retrieved !== 'undefined' ? retrieved : function () {};
	var status = data.idt;
	if (status === 'idt_error') {
		$(form + " " + formError).text(data.payload);
		$(form + " " + formError).show();
		$(form + " " + loadingIcon).hide();
		$(form + " " + submitButton).removeAttr('disabled');
		failed(data.error_code);
	} else if (status === 'idt_success') {
		$(form + " " + formError).html('');
		$(form + " " + formError).hide();
		succeeded();
		if (!redirecting) {
			$(form + " " + loadingIcon).hide();
			$(form + " " + submitButton).removeAttr('disabled');
		}
	} else if (status === 'idt_data') {
		$(form + " " + formError).html('');
		$(form + " " + formError).hide();
		if (!redirecting) {
			$(form + " " + loadingIcon).hide();
			$(form + " " + submitButton).removeAttr('disabled');
		}
		retrieved(data.data);
	}
}

/**
 * Fired when there is no response from server for a long time. Assume that the network is down.
 * @param  {string} form         The element ID used to locate the form item
 * @param  {string} submitButton The element ID used to locate the submit button within a desired form
 * @param  {string} loadingIcon  The element ID used to locate the loading button within a desired form
 * @param  {string} formError    The element ID used to locate the form error paragraph within a desired form
 */
function formRequestTimedOut (form, submitButton, loadingIcon, formError) {
		$(form + " " + formError).show();
		$(form + " " + formError).text('There seemed to be a problem with your network. Please try again.');
		$(form + " " + loadingIcon).hide();
		$(form + " " + submitButton).removeAttr('disabled');
}

/**
 * Process a standard form.
 * @param  {string} form         The element ID used to locate the form item
 * @param  {string} baseURL      The base URL to request the JSON file through AJAX.
 * @param  {array}  inputs       Either an array of inputs whose value should be passed through the query, or a query directly. This can specified using:
 *                               Array ('ELEMENTS', ...)
 *                               Array ('QUERY', '<query>')
 *                               in order to retrieve the target JSON.
 * @param  {function} succeeded  The function to be executed when the incoming JSON data indicated that 
 *                               the preivous action has successfully performed
 * @param  {function} failed     The function to be executed when the incoming JSON data indicated that 
 *                               the preivous action has failed to perform
 * @param  {string} submitButton The element ID used to locate the submit button within a desired form
 * @param  {string} loadingIcon  The element ID used to locate the loading button within a desired form
 * @param  {string} formError    The element ID used to locate the form error paragraph within a desired form
 */
function formProcess (form, redirecting, baseURL, inputs, succeeded, failed, retrieved, submitButton, loadingIcon, formError) {
	succeeded = typeof succeeded !== 'undefined' ? succeeded : function () {};
	failed = typeof failed !== 'undefined' ? failed : function () {};
	retrieved = typeof retrieved !== 'undefined' ? retrieved : function () {};
	submitButton = typeof submitButton !== 'undefined' ? submitButton : '.submit';
	loadingIcon = typeof loadingIcon !== 'undefined' ? loadingIcon : '.loading';
	formError = typeof formError !== 'undefined' ? formError : '.form-error';

	formSubmitting(form, submitButton, loadingIcon, formError);
	var length = inputs.length,
		element = null;
	var dataObject = {};
	if (inputs[0] === 'ELEMENTS') {
		for (var i = 1; i < length; i++) {
			element = inputs[i];
			elementID = form + " [name=\"" + inputs[i] + "\"]";
			if ($(elementID).attr('type') !== 'checkbox') {
				dataObject[element] = $(elementID).val();
			// 	if (i !== 1) {
			// 		dataString = dataString + "&";
			// 	}
			// 	dataString = dataString + element + "=" + $(elementID).val();
			} else {
				if ($(elementID).is(':checked')) {
			// 		if (i !== 1) {
			// 			dataString = dataString + "&";
			// 		}
					dataObject[element] = $(elementID).val();
			// 		dataString = dataString + element + "=" + $(elementID).val();
				}
			}
			
		}
	} else if (inputs[0] === 'QUERY') {
		// dataString = dataString + inputs[1];
		dataObject = inputs[1];
	}
	$.ajax({
		url: baseURL,
		data: dataObject,
		type: 'POST',
		dataType: 'json',
		timeout: 25000,
		success: function(data, textStatus, xhr) {
			formReturned(form, redirecting, submitButton, loadingIcon, formError, data, succeeded, failed, retrieved);
		},
		error: function(xhr, textStatus, errorThrown) {
			formRequestTimedOut(form, submitButton, loadingIcon, formError);
		}
	});
}

function clearInputs(form, elements) {
	for (var i = elements.length - 1; i >= 0; i--) {
		element = elements[i];
		elementID = form + " [name=\"" + elements[i] + "\"]";
		$(elementID).val('');
	}
}

function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}