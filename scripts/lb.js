/*
	Lightbox Clone
	Mark Simon, Comparity Training
	http://resources.comparity.net
	Feel Free to use

	This script is a stripped-down light box clone.
	To use:

	1.	Copy the lb.js and the optional lb.css files somewhere

	2.	Head Section:

		<link rel="stylesheet" type="text/css" href="includes/lb.css"/>
		<script type="text/javascript" src="includes/lb.js"></script>

		Of course, you need to adjust the src and href values
		to match the location of your lb.js and lb.css files.

	3. 	Around your thumbnail images:

		<a href="..." class="light"><img ... /></a>

		Your href should be the location of the enlarged image.
		Your img src is probably your thumbnail.

		Your thumbnail images are expected to have an alt attribute.
		They will also need a title attribute, since this is what
		will appear as the tile of the image.

	The class name of your anchors above defaults to "light".
	However,

	(a)	You can change it as long as it matches the
		var className below.

	(b)	You can combine it with other classes you want
		to use on your anchors by separating them
		with spaces:

		<a class="somethingelse light" ...>

	Disclaimer:

	This script probably works and it does what it does.
	What it doesn’t do it doesn’t do. E & OE

	To do:

	1.	Incorporate slide show.
	2.	Incorporate div for additional descriptive text:
		<a href="..." class="light"><img ... /><div>Description</div></a>
	3.	Modify to allow unlinked thumbnails (Where to reference image?)
	4.	Modify to not use thumbnails
		<ul|ol><li ...>
		</ul|ol>
*/

/*
	window.onload=function() {
	new lb('light','light','fade');
}
*/
function lb(className,imageDiv,backgroundDiv,next,show) {
	//	Config
	className=className?className:'light';
	imageDiv=imageDiv?imageDiv:'light';
	backgroundDiv=backgroundDiv?backgroundDiv:'fade';
	var imageImg=className+'Img';
	var padding=8;
	var thing=this;
	var ws=getWindowSize();
	this.width=ws.width;
	this.height=ws.height;
	this.slide=0;
	this.timeout=2000;

	//this.show=true;
	this.show=show?true:false;

	var imageRefs=document.getElementsByTagName('a');
	className=new RegExp("\\b"+className+"\\b");
	this.images=new Array();
	var image=0;
	if(imageRefs.length) for(var i=0;i<imageRefs.length;i++)
		if(imageRefs[i].className && imageRefs[i].className.match(className)) {
			this.images.push(imageRefs[i]);
		}

	this.next=next?true:false;
	this.next=this.images.length>1?next:false;
//	this.next=true;

	this.background=document.createElement('div');
	this.background.setAttribute('id',backgroundDiv);
	document.body.insertBefore(this.background,document.body.childNodes[0]);
	this.background.onclick=hideImage;

	this.light=document.createElement('div');
	this.light.setAttribute('id',imageDiv);
	document.body.insertBefore(this.light,document.body.childNodes[0]);
	if(!this.next) this.light.onclick=hideImage;

	this.img=document.createElement('img');
	this.img.setAttribute('id',imageImg);
	this.light.appendChild(this.img);

	this.label=document.createElement('p');
	this.light.appendChild(this.label);
	this.labelText=document.createTextNode('This Space for Rent');
	this.label.appendChild(this.labelText);
	if(this.next) {
		this.labelPrevious=createAnchor('#','« ','Previous Image');
		this.labelNext=createAnchor('#',' »','Next Image');
		this.label.insertBefore(this.labelPrevious,this.label.childNodes[0]);
		this.label.appendChild(this.labelNext);
	}

	if(this.images.length) for(i=0;i<this.images.length;i++) {
		this.images[i].index=i;
		this.images[i].image=new Image();
		this.images[i].image.src=this.images[i];
		this.images[i].image.alt=this.images[i].getElementsByTagName('img')[0].alt;
		this.images[i].image.title=this.images[i].getElementsByTagName('img')[0].title;
		this.images[i].onclick=this.show?startShow:showImage;
		this.images[i].previous=i>0?this.images[i-1].href:'';
		this.images[i].next=i<this.images.length-1?this.images[i+1]:'';
	}

	var style='#'+imageDiv+' {\n\
	position: absolute;\n\
	margin-left: auto;\n\
	margin-bottom: auto;\n\
	display: none;\n\
	background-color: white;\n\
	border: 2px solid black;\n\
	z-index: 1002;\n\
}\n';
	style+='#'+imageDiv+' p {\n\
	text-align: center;\n\
	font-weight: bold;\n\
	font-family: sans-serif;\n\
	font-size: 120%;\n\
	color: black;\n\
}\n';
	style+='#'+imageDiv+' a {\n\
	text-align: center;\n\
	font-weight: bold;\n\
	font-family: sans-serif;\n\
	font-size: 120%;\n\
	color: black;\n\
}\n';
	style+='#'+backgroundDiv+' {\n\
	display: none;\n\
	position: absolute;\n\
	top: 0px;\n\
	left: 0px;\n\
	width: 100%;\n\
	height: 100%;\n\
	background-color: black;\n\
	z-index:1001;\n\
	opacity:.60;\n\
	filter: alpha(opacity=60);\n\
}\n';

	addCSS(style);

	function getTarget(e) {
		var target;
		if (e.target)  return e.target;
		else if (e.srcElement) return e.srcElement;
		return target;
	}


	function setImage(index) {
		// var image=thing.images[index];
		var image=new Image();
		image.src=thing.images[index].href;

		thing.img.setAttribute('src',thing.images[index].href);
		thing.img.setAttribute('alt',thing.images[index].title);
		thing.img.setAttribute('title',thing.images[index].title);
		thing.img.setAttribute('width',image.width);
		thing.img.setAttribute('height',image.height);

		thing.light.style.padding=padding+"px";
		thing.light.style.left=(thing.width-image.width)/2-padding+'px';

		//	Adjust Div Width:
		thing.light.style.width=image.width+'px';

		thing.labelText.nodeValue=thing.images[index].image.title;
		if(thing.next) {
			thing.labelPrevious.index=index;
			thing.labelNext.index=index;
		}
	}

	function showImage(e) {
		e=e||window.event;
		var target=e.target||e.srcElement||null;

		//var image=new Image();
		setImage(target.parentNode.index);
		if(thing.next) {
			thing.labelPrevious.onclick=previousImage;
			thing.labelNext.onclick=nextImage;
		}

		thing.light.style.display=thing.background.style.display='block';

		if(e.stopPropogation) e.stopPropogation();
		else e.cancelBubble=true;
		return false;
	}

	function startShow(e) {
		e=e||window.event;
		var target=e.target||e.srcElement||null;

		if(e.stopPropogation) e.stopPropogation();
		else e.cancelBubble=true;
	}
	function stopShow() {

	}

	function hideImage() {
		thing.light.style.display=thing.background.style.display='none';
		if(thing.show) stopShow();
	}

	function previousImage(e) {
		e=e||window.event;
		var target=e.target||e.srcElement||null;
		if(target.index>0) setImage(target.index-1);
		if(e.stopPropogation) e.stopPropogation();
		else e.cancelBubble=true;
	}

	function nextImage(e) {
		e=e||window.event;
		var target=e.target||e.srcElement||null;
		if(target.index<thing.images.length-1) setImage(target.index+1);
		if(e.stopPropogation) e.stopPropogation();
		else e.cancelBubble=true;
	}

}

function createImg(src,alt,title,id) {
	var img=document.createElement('img');
	img.setAttribute('src',src);
	img.setAttribute('alt',alt);
	img.setAttribute('title',title||alt);
	if(id) img.setAttribute('id',id);
	return img;
}

function createAnchor(href,content,title) {
	var a=document.createElement('a');
	a.setAttribute('href',href);
	a.setAttribute('title',title||href);
	a.appendChild(document.createTextNode(content));
	return a;
}

function getWindowSize() {
	var width=0;
	var height=0;
	if(typeof(window.innerWidth)=='number') {
		width=window.innerWidth;
		height=window.innerHeight;
	}
	else if(document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
		width=document.documentElement.clientWidth;
		height=document.documentElement.clientHeight;
	}
	else if(document.body && (document.body.clientWidth || document.body.clientHeight)) {
		width=document.body.clientWidth;
		height=document.body.clientHeight;
	}
	return { width: width, height: height};
}

function addCSS(cssCode) {
	//	http://yuiblog.com/blog/2007/06/07/style/
	var styleElement=document.createElement('style');
	styleElement.type='text/css';
	if(styleElement.styleSheet) styleElement.styleSheet.cssText=cssCode;
	else styleElement.appendChild(document.createTextNode(cssCode));
	var head=document.getElementsByTagName('head')[0];
	head.insertBefore(styleElement,head.childNodes[0]);
}

/*
	if(window.addEventListener)
		window.addEventListener('load', function() {
			new lb('light','light','fade');
		}, false);
	else if(window.attachEvent) {
		window.attachEvent('onload', function() {
			new lb('light','light','fade');
		});
	}
*/
/*
	if(window.addEventListener)
		window.addEventListener('load', function() {
			new lb('light','light','fade');
		}, false);
	else if(window.attachEvent) {
		window.attachEvent('onload', function() {
			new lb('light','light','fade');
		});
	}
	function addEvent( obj, type, fn )
	{
		if (obj.addEventListener)
			obj.addEventListener( type, fn, false );
		else if (obj.attachEvent)
		{
			obj["e"+type+fn] = fn;
			obj[type+fn] = function() { obj["e"+type+fn]( window.event ); }
			obj.attachEvent( "on"+type, obj[type+fn] );
		}
	}
*/