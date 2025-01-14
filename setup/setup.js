/*	Setup
	================================================
	================================================ */

	'use strict';
	window.onerror = function(message, url, line) {
		alert(`Error: ${message}\n${url}: ${line}`);
	};

	function copy(element) {
		element.select();
		return document.execCommand('copy');
	}

	init();

	function init() {
		let form;

		function fetchPassword(event) {
			event.preventDefault();
			var password = form.elements['password-text'].value;
			fetch(`/setup/ajax.php?password=${password}`)
			.then(response => response.text())
			.then((body) => {
				form.elements['password-hash'].value = body;
				//	console.log(body);
//				copy(form.elements['password-hash']);
				form.elements['password-hash'].select();
				if(navigator.clipboard) {
					navigator.clipboard.writeText(form.elements['password-hash'])
					.then(() => {
						console.log('ok');
					})
					.catch(	() => {
						console.log(oops);
					});
				}
				else document.execCommand('copy');
			});
		}


		form = document.querySelector('form#setup');
		form.elements['do-password-hash'].onclick
			= form.elements['password-hash'].onclick
			= fetchPassword;
	}
