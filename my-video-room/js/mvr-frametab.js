/**
 * Frame Handling JavaScript for the Plugin
 *
 * @file mvr-mvr-frametab.js
 * @package MyVideoRoomPlugin
 *  */

function activateTab(pageId) {
	var tabCtrl        = document.getElementById( 'tabCtrl' );
	var pageToActivate = document.getElementById( pageId );
	var tabLength      = tabCtrl.childNodes.length;
	for (var i = 0; i < tabLength; i++) {
		var node = tabCtrl.childNodes[i];
		if (node.nodeType == 1) {
			node.style.display = (node == pageToActivate) ? 'block' : 'none';
		}
	}
}

function activateTab2(pageId) {
	var tabCtrl2        = document.getElementById( 'tabCtrl2' );
	var pageToActivate2 = document.getElementById( pageId );
	var tabLength       = tabCtrl2.childNodes.length;
	for (var i = 0; i < tabLength; i++) {
		var node3 = tabCtrl2.childNodes[i];
		if (node3.nodeType == 1) {
			node3.style.display = (node3 == pageToActivate2) ? 'block' : 'none';
		}
	}
}
