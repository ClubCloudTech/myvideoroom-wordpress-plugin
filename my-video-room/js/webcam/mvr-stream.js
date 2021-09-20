window.addEventListener("load", function(){
  // (A) ASK FOR USER PERMISSION TO ACCESS CAMERA
  navigator.mediaDevices.getUserMedia({
    // (A1) THE EASY WAY
    // video: true

    // (A2) TO SPECIFY PREFERRED RESOLUTION
    video: {
      width: { min: 852, ideal: 1280, max: 1920 },
      height: { min: 480, ideal: 720, max: 1080 }
    }
  })

  
  .then(function(stream) {
  
    var video = document.getElementById("vid-live");
    video.srcObject = stream;
    video.play();

    // (B2) ENABLE BUTTONS
    document.getElementById("vid-take").onclick = vidtake;
    document.getElementById("vid-down").onclick = viddown;
    document.getElementById("vid-up").onclick = vidup;
	document.getElementById("vid-retake").onclick = retakevideo;
  })

  // (C) FAILURE - NO WEBCAM ATTACHED AND/OR NO PERMISSION
  .catch(function(err) {
    alert( err + " Please enable access and attach a webcam");
  });

  function vidtake() {
	// (A) SNAPSHOT VIDEO TO HTML CANVAS
	var video = document.getElementById("vid-live"),
		canvas = document.createElement("canvas"),
		context2D = canvas.getContext("2d");
	canvas.width = video.videoWidth;
	canvas.height = video.videoHeight;
	context2D.drawImage(video, 0, 0, video.videoWidth, video.videoHeight);
	
	document.getElementById("vid-take").classList.add('mvr-hide');
	document.getElementById("vid-retake").classList.remove('mvr-hide');
	document.getElementById("vid-retake").onclick = retakevideo;

  
	// (B) PUT SNAPSHOT INTO WRAPPER
	video.pause();
  }

  function retakevideo() {
	console.log('retake');
	document.getElementById("vid-take").classList.remove('mvr-hide');
	document.getElementById("vid-retake").classList.add('mvr-hide');
	
	navigator.mediaDevices.getUserMedia({
		// (A1) THE EASY WAY
		// video: true
	
		// (A2) TO SPECIFY PREFERRED RESOLUTION
		video: {
		  width: { min: 852, ideal: 1280, max: 1920 },
		  height: { min: 480, ideal: 720, max: 1080 }
		}
	  })
	
	  
	  .then(function(stream) {
	  
		var video = document.getElementById("vid-live");
		video.srcObject = stream;
		video.play();
	
		// (B2) ENABLE BUTTONS
		document.getElementById("vid-take").onclick = vidtake;
		document.getElementById("vid-down").onclick = viddown;
		document.getElementById("vid-up").onclick = vidup;
		document.getElementById("vid-retake").onclick = retakevideo;
	  })
	}

	function viddown () {
		// (A) CREATE SNAPSHOT FROM VIDEO
		var video = document.getElementById("vid-live"),
			canvas = document.createElement("canvas"),
			context2D = canvas.getContext("2d");
		canvas.width = video.videoWidth;
		canvas.height = video.videoHeight;
		context2D.drawImage(video, 0, 0, video.videoWidth, video.videoHeight);
	  
		// (B) CREATE DOWNLOAD LINK
		var wrap = document.getElementById("vid-result"),
			anchor = document.createElement("a");
		anchor.href = canvas.toDataURL("image/png");
		anchor.download = "webcam.png";
		anchor.innerHTML = "Click to Download";
		wrap.innerHTML = "";
		wrap.appendChild(anchor);
	  
		// (C) AUTOMATIC DOWNLOAD - MAY NOT WORK ON SOME BROWSERS
		// anchor.click();
	  }
	  function vidup () {
		// (A) CREATE SNAPSHOT FROM VIDEO
		var video = document.getElementById("vid-live"),
			canvas = document.createElement("canvas"),
			context2D = canvas.getContext("2d");
		canvas.width = video.videoWidth;
		canvas.height = video.videoHeight;
		context2D.drawImage(video, 0, 0, video.videoWidth, video.videoHeight);
		
		// (B) CONVERT TO BLOB + UPLOAD
		canvas.toBlob(function(blob){
		  // (B1) FORM DATA
		  var data = new FormData();
		  data.append('upimage', blob);
		  
		  // (B2) AJAX UPLOAD
		  var xhr = new XMLHttpRequest();
		  xhr.open('POST', "mvr-upload.php");
		  xhr.onload = function(){ alert(this.response); };
		  xhr.send(data);
		});
	  }
});
