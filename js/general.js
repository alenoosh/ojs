/**
 * general.js
 *
 * Copyright (c) 2003-2006 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Site-wide common JavaScript functions. 
 *
 * $Id$
 */

/**
 * Prompt user for confirmation prior to loading a URL.
 */
function confirmAction(url, msg) {
	if (confirm(msg)) {
		if (url) {
			document.location.href=url;
		}
		return true;
	}
	return false;
}

/**
 * Open window displaying help.
 */
function openHelp(url) {
	window.open(url, 'Help', 'width=700,height=600,screenX=100,screenY=100,toolbar=0,scrollbars=1');
}

/**
 * Open window displaying comments.
 */
function openComments(url) {
	window.open(url, 'Comments', 'width=700,height=600,screenX=100,screenY=100,toolbar=0,resizable=1,scrollbars=1');
}

/**
 * Open window for preview.
 */
function openWindow(url) {
	window.open(url, 'Window', 'width=600,height=550,screenX=100,screenY=100,toolbar=0,resizable=1,scrollbars=1');
}

/**
 * Open window for reading tools.
 */
function openRTWindow(url) {
	window.open(url, 'RT', 'width=700,height=500,screenX=100,screenY=100,toolbar=0,resizable=1,scrollbars=1');
}
function openRTWindowWithToolbar(url) {
	window.open(url, 'RT', 'width=700,height=500,screenX=100,screenY=100,toolbar=1,resizable=1,scrollbars=1');
}

/**
 * browser object availability detection
 * @param objectId string of object needed
 * @param style int (0 or 1) if style object is needed
 * @return javascript object specific to current browser
 */
function getBrowserObject(objectId, style) {
	var isNE4 = 0;
	var currObject;

	// browser object for ie5+ and ns6+
	if (document.getElementById) {
		currObject = document.getElementById(objectId);
	// browser object for ie4+
	} else if (document.all) {
		currObject = document.all[objectId];
	// browser object for ne4
	} else if (document.layers) {
		currObject = document.layers[objectId];
		isNE4 = 1;
	} else {
		// do nothing
	}
	
	// check if style is needed
	if (style && !isNE4) {
		currObject = currObject.style;
	}
	
	return currObject;
}

/**
 * Load a URL.
 */
function loadUrl(url) {
	document.location.href=url;	
}

function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

/**
 * Asynchronous request functions
 */
function makeAsyncRequest(){
	var req=(window.XMLHttpRequest)?new XMLHttpRequest():new ActiveXObject('Microsoft.XMLHTTP');
	return req;
}

function sendAsyncRequest(req, url, data, method) {
	var header = 'Content-Type:text/html; Charset=utf-8';
	req.open(method, url, true);
	req.setRequestHeader(header.split(':')[0],header.split(':')[1]);
	req.send(data);
}


/**
 * Change the form action
 * @param formName string
 * @param action string 
 */
function changeFormAction(formName, action) {
	document.forms[formName].action = action;
	document.forms[formName].submit();
}
// Opatan Inc. Task 6 :redirect link from article value
function getSelectedArticleId() {
	for (var i=0; i<=document.formName.radioButtonName.length; i++) {
		if (document.formName.radioButtonName[i].checked) {
			var c  = document.formName.radioButtonName[i].id;
			var d  = document.formName.radioButtonName[i].value;
			var a  = document.test.to.value;
			var b  = document.test.redirectUrl.value;
			var ar = [a,b,c,d];
			return ar; 
				
		}
	}
	
	
}
function callSelectedUrl(url,op,anchor) {
	article_id = getSelectedArticleId();
	if (article_id == -1) {
		alert("Please Select an Article");
		return;
	}
	url = url.replace("article_id", article_id);
	url = url.replace("function", op);
	url = url.replace("anchor",anchor);
	
	window.location.href = url;
}
function callUrl(url,page,op) {
		article_id = getSelectedArticleId();
		//document.write(article_id[0]);
	if (article_id == -1) {
		alert("Please Select an Article");
		return;
	}
	url = url.replace("hip",page);
	url = url.replace("function", op);
	url = url.replace("to_email",article_id[0]);
	url = url.replace("redirect_url",article_id[1]);
	url = url.replace("subjects",article_id[2]);
	url = url.replace("article_id", article_id[3]);
	

	
	
	window.location.href = url;

}

