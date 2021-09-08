function mvrchangefocus()
{
	document.getElementById( 'mvr-shopping-basket' ).click();
}

	/* Disabling Execution outside of MVR */
	var mvrIsactive = document.getElementsByClassName( 'mvr-nav-shortcode-outer-wrap' );

if ( mvrIsactive.length > 0) {
	mvrchangefocus();
}
