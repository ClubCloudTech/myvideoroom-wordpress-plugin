/**
 * Add dynamic tabs to MyVideoRoom Outer Navigation Templates
 *
 * @package MyVideoRoomPlugin
 */

function chText()
{
	var str   = document.getElementById( "myvideoroom_add_room_slug" );
	var regex = /[^A-Za-z0-9]/gi;
	str.value = str.value.replace( regex ,"" );
}
