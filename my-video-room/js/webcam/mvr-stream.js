window.addEventListener("load", function(){
 

/* Initialise Camera, and Listen to Buttons */
	document.getElementById("vid-picture").onclick = function(){
		document.getElementById("myvideoroom-picturewrap").classList.remove('mvr-hide');
		document.getElementById("vid-picture").classList.add('mvr-hide');
		document.getElementById("myvideoroom-picturedescription").classList.remove('mvr-hide');
		startcamera();
	}
	document.getElementById("vid-skip").onclick = function(){
	skipwindow();
	}

function startcamera(){
	  // (A) ASK FOR USER PERMISSION TO ACCESS CAMERA
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
	document.getElementById("myvideoroom-picturewrap").classList.add('mvr-hide');
	document.getElementById("myvideoroom-picturedescription").classList.add('mvr-hide');
	document.getElementById("vid-picture").classList.remove('mvr-hide');
	document.getElementById("myvideoroom-meeting-name").classList.remove('mvr-hide');
	
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
		
		document.getElementById("myvideoroom-meeting-name").classList.remove('mvr-hide');
		
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
								alert(response);
								$( '#vid-up' ).prop('value', 'Saved !');
							},
							error: function ( response ){
								console.log('Error Uploading');
							}
						}
					);
				});
				
			});
		
	  }

	  function startmeeting() {
				// Prepare Form.
				var form_data = new FormData();
				form_data.append('action','myvideoroom_file_upload');
						
				jQuery(function($) {
					var room_name     = $( '#roomid' ).data( 'roomName' ),
					display_name      = $( '#vid-name' ).val();
					
					form_data.append('room_name', room_name );
					form_data.append('security', myvideoroom_file_upload.security );
					form_data.append('display_name', display_name );
					form_data.append('action_taken', 'start_meeting' );
					$.ajax(
						{
							type: 'post',
							dataType: 'html',
							url: myvideoroom_file_upload.ajax_url,
							contentType: false,
							processData: false,
							data: form_data,
							success: function (response) {
								alert(response);
								$( '#vid-up' ).prop('value', 'Saved !');
							},
							error: function ( response ){
								console.log('Error Uploading');
							}
						}
					);
				});  

	  }

	  document.getElementById("vid-name").onkeyup = function() {
		document.getElementById("vid-name").innerHTML='';
		document.getElementById("vid-down").disabled = false;

	  document.getElementById("vid-down").onclick = startmeeting;	
	
	};




});
