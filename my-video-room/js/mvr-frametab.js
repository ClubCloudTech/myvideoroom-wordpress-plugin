/**
 * Frame Handling JavaScript for the Plugin
 *
 * @package MyVideoRoomPlugin
 * */

/**
 * Click Based Tab Block:Hide Script - Variation to Handle two scripts on same page
 *
 * @package MyVideoRoomPlugin
 * */
function activateTab2(pageId) {
	var tabCtrl2        = document.getElementById( 'tabCtrl2' );
	var pageToActivate2 = document.getElementById( pageId );
	var tabLength       = tabCtrl2.childNodes.length;
	for (var i = 0; i < tabLength; i++) {
		var node3 = tabCtrl2.childNodes[i];
		if (node3.nodeType === 1) {
			node3.style.display = (node3 === pageToActivate2) ? 'block' : 'none';
		}
	}
}
