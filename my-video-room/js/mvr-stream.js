window.addEventListener("load", function(){
 

/* Initialise Camera, and Listen to Buttons */
	document.getElementById("vid-picture").onclick = function(){
		document.getElementById("myvideoroom-picturewrap").classList.remove('mvr-hide');
		document.getElementById("vid-picture").classList.add('mvr-hide');
		document.getElementById("vid-up").classList.add('mvr-hide');
		document.getElementById("myvideoroom-picturedescription").classList.remove('mvr-hide');
		startcamera();
	}
	document.getElementById("vid-skip").onclick = function(){
	skipwindow();
	}
	document.getElementById("mvr-button-login").onclick = function(e){
		e.preventDefault();
		document.getElementById("mvr-picture").classList.add('mvr-hide');
		document.getElementById("mvr-login-form").classList.remove('mvr-hide');
		document.getElementById("myvideoroom-checksound").classList.add('mvr-hide');
		document.getElementById("myvideoroom-meeting-name").classList.add('mvr-hide');
	}
	document.getElementById("mvr-photo-image").onclick = function(e){
		e.preventDefault();
		document.getElementById("mvr-picture").classList.remove('mvr-hide');
		document.getElementById("mvr-login-form").classList.add('mvr-hide');
		document.getElementById("myvideoroom-checksound").classList.add('mvr-hide');
		document.getElementById("myvideoroom-meeting-name").classList.add('mvr-hide');
	}
	document.getElementById("mvr-name-user").onclick = function(e){
		e.preventDefault();
		skipwindow();
	}
	document.getElementById("mvr-check-sound").onclick = function(e){
		e.preventDefault();
		stopcamera();
		document.getElementById("mvr-picture").classList.add('mvr-hide');
		document.getElementById("mvr-login-form").classList.add('mvr-hide');
		document.getElementById("myvideoroom-meeting-name").classList.add('mvr-hide');
		document.getElementById("myvideoroom-checksound").classList.remove('mvr-hide');
	}
	document.getElementById("chk-sound").onclick = function(e){
		e.preventDefault();
		document.getElementById("myvideoroom-checksound").classList.remove('myvideoroom-center');
		checksound();
	}
	

function startcamera(){

	document.getElementById("vid-take").classList.remove('mvr-hide');
	  navigator.mediaDevices.getUserMedia({
		// (A1) THE EASY WAY
		// video: true
	
		// (A2) TO SPECIFY PREFERRED RESOLUTION
		video: {
		  width: { min: 213, ideal: 256, max: 1920 },
		  height: { min: 120, ideal: 144, max: 1080 }
		}
	  })
	
	  
	  .then(function(stream) {
	  
		var video = document.getElementById("vid-live");
		video.srcObject = stream;
		video.play();
	
		document.getElementById("vid-take").onclick = vidtake;
		document.getElementById("vid-up").onclick = vidup;
		document.getElementById("vid-retake").onclick = retakevideo;

	  })
	
	  // (C) FAILURE - NO WEBCAM ATTACHED AND/OR NO PERMISSION
	  .catch(function(err) {
		alert( err + " Please enable access and attach a webcam");
	  });
	  setTimeout(function(){
        cameratimeout();
   },30000);
}

function skipwindow() {
	document.getElementById("mvr-picture").classList.add('mvr-hide');

	document.getElementById("myvideoroom-meeting-name").classList.remove('mvr-hide');
	document.getElementById("myvideoroom-meeting-name").classList.add('myvideoroom-center');

}

  function vidtake() {
	/* Create Canvas */
	var video = document.getElementById("vid-live"),
		canvas = document.createElement("canvas"),
		context2D = canvas.getContext("2d");
	canvas.width = video.videoWidth;
	canvas.height = video.videoHeight;
	context2D.drawImage(video, 0, 0, video.videoWidth, video.videoHeight);
	var wrap = document.getElementById("vid-result");
	wrap.innerHTML = "";
	wrap.appendChild(canvas);

	/* Arrange Buttons for Retake, or Accept Image */
	document.getElementById("vid-result").classList.remove('mvr-hide');
	document.getElementById("vid-retake").classList.remove('mvr-hide');
	document.getElementById("vid-up").classList.remove('mvr-hide');
	document.getElementById("vid-live").classList.add('mvr-hide');
	document.getElementById("vid-take").classList.add('mvr-hide');
	document.getElementById("vid-result").classList.add('myvideoroom-image-result');

	
	
	
	stopcamera();
  }

function stopcamera(){
	navigator.mediaDevices.getUserMedia({
		// Resolution
		video: {
		  width: { min: 213, ideal: 256, max: 1920 },
		  height: { min: 120, ideal: 144, max: 1080 }
		}
	  })
	  .then(function(stream) {
  
		var video = document.getElementById("vid-live");
		video.srcObject = stream;
		
		stream.getTracks().forEach(track => track.stop())
	  })

}

function cameratimeout(){
	navigator.mediaDevices.getUserMedia({
		// Resolution
		video: {
		  width: { min: 213, ideal: 256, max: 1920 },
		  height: { min: 120, ideal: 144, max: 1080 }
		}
	  })
	  .then(function(stream) {
  
		var video = document.getElementById("vid-live");
		video.srcObject = stream;
		
		stream.getTracks().forEach(track => track.stop())
	  })

	  console.log('Stopcamera Command Sent');
	  document.getElementById("vid-retake").classList.remove('mvr-hide');
	  document.getElementById("vid-take").classList.add('mvr-hide');
	  document.getElementById("vid-live").classList.add('mvr-hide');
}

  function retakevideo() {
	document.getElementById("myvideoroom-picturedescription").classList.remove('mvr-hide');
	stopcamera();
	/* Reset Buttons for Retake */
	document.getElementById("vid-result").innerHTML = "";
	document.getElementById("vid-live").classList.remove('mvr-hide');
	document.getElementById("vid-result").classList.add('mvr-hide');
	document.getElementById("vid-up").classList.add('mvr-hide');
	document.getElementById("vid-take").classList.remove('mvr-hide');
	document.getElementById("vid-retake").classList.add('mvr-hide');
	document.getElementById("vid-up").value="Use This";
	
	startcamera();

	}


	function vidup () {
		
			canvas = document.querySelector('canvas');
			context2D = canvas.getContext("2d");
			canvas.toBlob(function(blob){
	
				// Prepare Form.
				var form_data = new FormData();
				form_data.append('upimage', blob);
				form_data.append('action','myvideoroom_file_upload');
						
				jQuery(function($) {
					var room_name     = $( '#roomid' ).data( 'roomName' );
					form_data.append('room_name', room_name );
					form_data.append('action_taken', 'update_picture' );
					form_data.append('security', myvideoroom_file_upload.security );
					$.ajax(
						{
							type: 'post',
							dataType: 'html',
							url: myvideoroom_file_upload.ajax_url,
							contentType: false,
							processData: false,
							data: form_data,
							success: function (response) {
								var state_response = JSON.parse( response );
								console.log( state_response.message );
								$( '#vid-up' ).prop('value', 'Saved !');
							},
							error: function ( response ){
								console.log('Error Uploading');
							}
						}
					);
				});
				
			});
		skipwindow();
	  }

	  function checksound() {
		console.log('Check sound starting');
		document.getElementById("stop-chk-sound").classList.remove('mvr-hide');
		// Prepare Form.
		var form_data = new FormData();
		form_data.append('action','myvideoroom_file_upload');
				
		jQuery(function($) {
			container      = $( '.myvideoroom-app' );
			notification   = $( '#mvr-above-article-notification' );
			form_data.append('security', myvideoroom_file_upload.security );
			form_data.append('action_taken', 'check_sound' );
			$.ajax(
				{
					type: 'post',
					dataType: 'html',
					url: myvideoroom_file_upload.ajax_url,
					contentType: false,
					processData: false,
					data: form_data,
					success: function (response) {
						// Hard Delete of Existing Container to Avoid Duplication.
						container_parent = container.parent().attr('id');
						container.empty();
												
						var state_response = JSON.parse( response );

						// Redraw Container.
						container.html( state_response.mainvideo );
						notification.html ( state_response.message);
							
						if (window.myvideoroom_tabbed_init) {
							window.myvideoroom_tabbed_init( container );
						}

						if (window.myvideoroom_app_init) {
							window.myvideoroom_app_init( container[0] );
						}

						if (window.myvideoroom_app_load) {
							window.myvideoroom_app_load();
						}

						if (window.myvideoroom_shoppingbasket_init) {
							window.myvideoroom_shoppingbasket_init();
						}
						
						$( '#vid-up' ).prop('value', 'Saved !');
					},
					error: function ( response ){
						console.log('Error Uploading');
					}
				}
			);
		});  
				// Change Focus to Video Tab.
				document.getElementById( 'mvr-video' ).click();
}


	  function startmeeting() {
				// Prepare Form.
				var form_data = new FormData();
				form_data.append('action','myvideoroom_file_upload');
						
				jQuery(function($) {
					var room_name  = $( '#roomid' ).data( 'roomName' ),
					display_name   = $( '#vid-name' ).val(),
					original_room  = $( '.myvideoroom-app' ).data( 'roomName' ),
					container      = $( '.myvideoroom-app' );
					
					form_data.append('room_name', room_name );
					form_data.append('security', myvideoroom_file_upload.security );
					form_data.append('display_name', display_name );
					form_data.append('action_taken', 'start_meeting' );
					form_data.append('original_room', original_room );
					$.ajax(
						{
							type: 'post',
							dataType: 'html',
							url: myvideoroom_file_upload.ajax_url,
							contentType: false,
							processData: false,
							data: form_data,
							success: function (response) {
								// Hard Delete of Existing Container to Avoid Duplication.
								container_parent = container.parent().attr('id');
								container.empty();
								container.parent().empty();
								$( '#'+container_parent ).prepend('<div class="myvideoroom-app"></div>');
								container      = $( '.myvideoroom-app' );
								var state_response = JSON.parse( response );
								// Redraw Container.
								container.html( state_response.mainvideo );
									
								if (window.myvideoroom_tabbed_init) {
									window.myvideoroom_tabbed_init( container );
								}

								if (window.myvideoroom_app_init) {
									window.myvideoroom_app_init( container[0] );
								}

								if (window.myvideoroom_app_load) {
									window.myvideoroom_app_load();
								}

								if (window.myvideoroom_shoppingbasket_init) {
									window.myvideoroom_shoppingbasket_init();
								}
								
								$( '#vid-up' ).prop('value', 'Saved !');
							},
							error: function ( response ){
								console.log('Error Uploading');
							}
						}
					);
				});  
				// Change Focus to Video Tab.
				document.getElementById( 'mvr-video' ).click();
	  }

	  document.getElementById("vid-name").onkeyup = function() {
		document.getElementById("vid-name").innerHTML='';
		document.getElementById("vid-down").disabled = false;

	  document.getElementById("vid-down").onclick = startmeeting;	
	
	};




});
