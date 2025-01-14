/*	JavaScript
	================================================ */

	'use strict';

	window.onerror=function(message,url,line) {
		alert(`Error: ${message}\n${url}: ${line}`);
	};

	//	Initialise
		init();
		function init() {
			//	Background Image
				if(localStorage.getItem('debug')) document.body.classList.add('debug');

				document.querySelector('header').onclick = event => {
					if(event.shiftKey) document.body.classList.toggle('debug');
					if(document.body.classList.contains('debug')) localStorage.setItem('debug',true);
					else localStorage.removeItem('debug');
				};

			//	Show Password
				let form;
				if(form = document.querySelector('form#login')) {
					form.elements['show-password'].onclick = doShowPassword.bind(null,form);
				}

			//	File Inputs
				let elements;
				if(elements = document.querySelectorAll('input[type="file"][data-preview]')) {
					elements.forEach(element => {
						doFilePreview(element, document.querySelector(`img#${element.dataset.preview}`), {dw: 240, dh: 180});
					});
				}

			//	Preview Image
				let useImage;
				if(useImage = document.querySelector('div#use-image')) {
					let previewImage = document.querySelector(`img#preview-new-image`);
					useImage.onclick = event => {
						showImage(previewImage, event.target.dataset.preview, {dw: 240, dh: 180});
					};
				}

			//	Lightbox
				let container;
				if(document.querySelector(container = 'ul.lb')) doLightbox(container);
				if(document.querySelector(container = 'table.manage')) doLightbox(container);
				if(document.querySelector(container = 'article#blogarticle>div#article>figure')) doLightbox(container);
			//	if(document.querySelector(container = 'article#editblog p#use-image')) doLightbox(container, true);

			//	Site Map
				let sitemap;
				if(sitemap=document.querySelector('article#site-map')) doSiteMap();

			//	Collabsiple Items
				if(elements=document.querySelectorAll('.collapsible')) elements.forEach(function(element) { doCollapsible(element); });

			//	Textarea Tabs
				var textareas = document.querySelectorAll('textarea');
				textareas.forEach(textarea => {
					textarea.addEventListener('keydown',handleTab);
				});
		}

	//	Show Password
		function doShowPassword(form, event) {
			form.elements['password'].type = 'text';
			form.elements['show-password'].setAttribute('on', true);
			window.setTimeout(() => {
				form.elements['password'].type = 'password';
				form.elements['show-password'].removeAttribute('on');
			}, 10000);
			event.preventDefault();
		}

	//	Collapsible Sections
		function doCollapsible(element) {
			var h2=element.querySelectorAll('h2');
			h2.forEach(h=>h.onclick=toggle);
			function toggle(event) {
				if(this.hasAttribute('open')) this.removeAttribute('open');
				else this.setAttribute('open',true);
			}
		}

		function showImage(preview, src, limit) {
			preview.src = '';
			[preview.width, preview.height] = [limit.dw, limit.dh];
			let cache = new Image();
			cache.src = src;
			cache.onload = e => {
				let img = e.target;
				if(limit) {
					if(img.width/img.height < limit.dw/limit.dh)  preview.width = limit.dh * img.width / img.height;
					else preview.height = limit.dw * img.height / img.width;
				}
				preview.src = img.src;
			};
		}


	//	Preview Uploaded Image
		function doFilePreview(fileInput, preview, limit) {
			if(!preview) return;
			//	Remember Original
				preview.original = {
					src: preview.src,
					width: preview.width,
					height: preview.height
				};
			//	Shift-Click -> Reset to Original
				fileInput.onclick = event => {
					if(event.shiftKey) {
						fileInput.value = null;
						event.preventDefault();
						preview.src = preview.original.src;
						preview.width = preview.original.width;
						preview.height = preview.original.height;
					}
				};
			//	Preview Attached File
				fileInput.onchange = () => {
					try {
						let reader = new FileReader();
						reader.readAsDataURL(fileInput.files[0]);
						if(limit) [preview.width, preview.height] = [limit.dw, limit.dh];
						reader.onload = event => {
							showImage(preview, reader.result, limit);
						};
					}
					catch (error) {
						preview.src='';
					}
				};
		}

	//	Tab Handler: use element.onkeydown
		function handleTab(event) {
			if(event.keyCode != 9) return;
			var element = event.target;
			var start = element.selectionStart;
			element.value = `${element.value.slice(0,start)}\t${element.value.slice(start)}`;
			element.setSelectionRange(start+1,start+1);
			event.preventDefault();
		}

	//	Site Map
		function doSiteMap() {
			var map=document.querySelector('ul#map');
			var li=map.querySelectorAll('li');
			li.forEach(value=>{
				if(value.querySelector('ul')) {
					var span;
					if(span=value.querySelector('span:first-child')) span.remove();
					span=document.createElement('span');
					span.textContent='❯';
					value.insertAdjacentElement('afterbegin',span);
					value.classList.add('closed');
					span.onclick=function(event) {value.classList.toggle('closed')};
				}
			});
		}
