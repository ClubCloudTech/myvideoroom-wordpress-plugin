/**
 * Tab Changer JavaScript file Visualiser
 *
 * @package MyVideoRoomPlugin
 */

function activateTab(pageId) {
	var tabCtrl = document.getElementById( 'tabCtrl' );
	var pageToActivate = document.getElementById(pageId);
for (var i = 0; i < tabCtrl.childNodes.length; i++) {
		var node = tabCtrl.childNodes[i];
		if (node.nodeType == 1) { /* Element */
			node.style.display = (node == pageToActivate) ? 'block' : 'none';
		}
	}
}

function activateTab2(pageId) {
	var tabCtrl2 = document.getElementById('tabCtrl2');
	var pageToActivate2 = document.getElementById(pageId);
	for (var i = 0; i < tabCtrl2.childNodes.length; i++) {
		var node3 = tabCtrl2.childNodes[i];
		if (node3.nodeType == 1) {
			/* Element */
			node3.style.display = (node3 == pageToActivate2) ? 'block' : 'none';
		}
	}
}
