/**
 * Loads a JavaScript file dinamically
 * @param {String} url 
 * @returns {Promise}
 */
function loadScript(url) {
	return new Promise((resolve, reject) => {
		const script = document.createElement('script');
		script.async = true;
		script.defer = true;
		script.src = url;
		script.onload = () => {
			resolve();
		};

		document.querySelector('body').appendChild(script);
	});
}

/**
 * Set a mask
 * @param {String} selector 
 * @param {String} pattern
 */
function setMask(selector, pattern) {

	// Check if the lib was included already
	if (typeof VMasker == 'undefined') {

		// Includes the lib and calls the function again
		const vanillaMaskFile = `${vendorPath}vanilla-masker/vanilla-masker.min.js`;
		loadScript(vanillaMaskFile)
			.then(() => {
				setMask(selector, pattern);
			});

	} else {
		if (pattern === 'money') {
			VMasker(document.querySelector(selector)).maskMoney({
				precision: 2,
				separator: ',',
				delimiter: '.'
			});
		} else {
			VMasker(document.querySelector(selector)).maskPattern(pattern);
		}
	}
}

/**
 * Parse a number as float
 * The built-in parseFloat function doesn't work with dot thousands separator
 * @param {String} number 
 * @return {Float}
 */
function getAsFloat(number) {
	return parseFloat(
		(number.replace('.', ''))
			.replace(',', '.')
	);
}

/**
 * Converts a dd/mm/YYYY date format to JavaScript Date (mm/dd/YYYY)
 * @param {String} date Date in dd/mm/YYYY format
 * @return {Date}
 */
function getAsJsDate(date) {
	return new Date(
		`${date.substr(3, 2)}/${date.substr(0, 2)}/${date.substr(6, 4)}`
	);
}

/**
 * Apply mask to all the fields
 */
document.querySelectorAll('*[data-mask]').forEach((item) => {
	const selector = `#${item.getAttribute('id')}`;
	const pattern = item.getAttribute('data-mask');

	setMask(selector, pattern);
});

/**
 * Pops a message in a SnackBar
 * @param {String} msg Message to be displayed
 */
function popMessage(msg) {
	const notification = document.querySelector('.mdl-js-snackbar');
	notification.MaterialSnackbar.showSnackbar({
		message: msg
	});
}

/**
 * Ask for confirmation before delete
 */
document.querySelectorAll('*[ask-for-confirmation]').forEach((item) => {
	item.addEventListener('click', function (event) {
		msg = this.getAttribute('ask-for-confirmation')

		if (!confirm(msg)) {
			event.preventDefault();
			return false;
		}
	});
});

/**
 * Remove all non-numeric characters
 * @param {String} string 
 */
function extractNumbers(string) {
	return string.replace(/[^0-9]/g, '');
}

/**
 * Validate CPF
 * @param {String} cpf 
 * @return {Bool}
 * @author DevMedia <https://www.devmedia.com.br/validar-cpf-com-javascript/23916>
 */
function validateCpf(cpf) {
	cpf = extractNumbers(cpf);
	if (cpf == '00000000000' ||
		cpf == '11111111111' ||
		cpf == '22222222222' ||
		cpf == '33333333333' ||
		cpf == '44444444444' ||
		cpf == '55555555555' ||
		cpf == '66666666666' ||
		cpf == '77777777777' ||
		cpf == '88888888888' ||
		cpf == '99999999999') return false;

	let sum = 0;
	let rest;

	for (i = 1; i <= 9; i++) sum = sum + parseInt(cpf.substr(i - 1, 1)) * (11 - i);
	rest = (sum * 10) % 11;
	if ((rest == 10) || (rest == 11)) rest = 0;
	if (rest != parseInt(cpf.substr(9, 1))) return false;

	sum = 0;
	for (i = 1; i <= 10; i++) sum = sum + parseInt(cpf.substr(i - 1, 1)) * (12 - i);
	rest = (sum * 10) % 11;

	if ((rest == 10) || (rest == 11)) rest = 0;
	if (rest != parseInt(cpf.substr(10, 1))) return false;
	return true;
}

/**
 * Validate e-mail
 * @param {String} email 
 * @author C.Lee <https://stackoverflow.com/questions/46155/how-to-validate-an-email-address-in-javascript/9204568#9204568>
 */
function validateEmail(email) {
	const regex = /\S+@\S+\.\S+/;
	return regex.test(email);
}