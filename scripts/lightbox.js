/*  function doLightbox(container)
	================================================
	Function to implement lightbox functionality for
	a gallery of images.

	Use the following Structure:

	<div id="…">
		<a href="…"><img src="…" title="…"></a>
		<a href="…"><img src="…" title="…"></a>
		<a href="…"><img src="…" title="…"></a>
	</div>

	The anchors include a reference to the larger image.
	The images include a thumbnail of the image.
	================================================ */

	function doLightbox(container, useCapture=false) {
		var anchors = document.querySelectorAll(`${container} a`);

		anchors.forEach(a => {
			//	a.onclick = show;
			a.addEventListener('click', show, useCapture);
			//	a.addEventListener('click',doit2,false);

		});
		function doit(event) {
			console.log(event.target);
			event.preventDefault;
		}
		function doit2(event) {
			console.log(event.target);
//			event.preventDefault();
				currentAnchor = event.currentTarget;
				loadImage();

//				document.addEventListener('keydown',doKeys);
		}

		let currentAnchor;	//	current anchor

		//	Create Elements
			var background = document.createElement('div');

			var figure = document.createElement('figure');
			var img = document.createElement('img');
			var figcaption = document.createElement('figcaption');

			background.id = 'lightbox-background';
			figure.id = 'lightbox';

			figure.appendChild(img);
			figure.appendChild(figcaption);

			document.body.appendChild(background);
			document.body.appendChild(figure);

		//	Style Sheet
			let style = document.createElement('style');
			document.head.insertAdjacentElement('afterbegin',style);

		//	Lightbox Element Styles
			style.sheet.insertRule(`
				div#lightbox-background {
						position: fixed;
						top: 0; left: 0; width:100%; height: 100%;
						z-index: 1;
						background-color: rgb(0,0,0,0.5);
					}
			`);
			style.sheet.insertRule(`
				figure#lightbox {
						position: fixed;
						top: 50%; left: 50%; margin-right: -50%;
						transform: translate(-50%, -50%);
						z-index: 2;
					}
			`);

		//	Showing & Hiding
			background.onclick = hide;
			hide();	//	hide now

			function loadImage() {
				let currentImage = currentAnchor.querySelector('img');
				//	populate image element
					img.src = currentAnchor.href;
				//	caption text
					figcaption.textContent = img.alt = img.title = currentImage.title;

				img.onload = event => {
					background.style.display = 'block';
					figure.classList.add('open');
					figure.classList.remove('closed');
				};
			}

			function show(event) {
				event.preventDefault();
				currentAnchor = event.currentTarget;
				loadImage();

				document.addEventListener('keydown',doKeys);
			}
			function hide(event) {
				background.style.display = 'none';
				figure.classList.remove('open');
				figure.classList.add('closed');

				document.removeEventListener('keydown',doKeys);
			}

			function doKeys(event) {
				event.preventDefault();

				switch(event.key) {
					case 'Esc':	//	Old Version
					case 'Escape':
						hide();
						break;
					case 'Left':	//	Old Version
					case 'ArrowLeft':
						currentAnchor = currentAnchor.previousElementSibling ||
							currentAnchor.parentNode.lastElementChild;
						loadImage();
						break;
					case 'Right':	//	Old Version
					case 'ArrowRight':
						currentAnchor = currentAnchor.nextElementSibling ||
							currentAnchor.parentNode.firstElementChild;
						loadImage();
						break;
					case 'Home':
					case 'Up':		//	Old Version
					case 'ArrowUp':
						currentAnchor = currentAnchor.parentNode.firstElementChild;
						loadImage();
						break;
					case 'End':
					case 'Down':	//	Old Version
					case 'ArrowDown':
						currentAnchor = currentAnchor.parentNode.lastElementChild;
						loadImage();
						break;
				}
			}
	}
