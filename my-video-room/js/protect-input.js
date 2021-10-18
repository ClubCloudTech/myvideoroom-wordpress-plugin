/**
 * Restricts an input to only alphanumeric characters
 *
 * @package MyVideoRoomPlugin
 */

(function($) {
    var $inputs = $('input.myvideoroom-input-restrict-alphanumeric');
    $inputs.on(
        'keyup keydown',
        function(e) {
            return !!(/[a-z0-9]$/i.test(e.key));
        }
    );
    var $inputspace = $('input.myvideoroom-input-restrict-alphanumeric-space');
    $inputspace.on(
        'keyup keydown',
        function(e) {
            return !!(/[a-z 0-9]$/i.test(e.key));
        }
    );
})(jQuery);