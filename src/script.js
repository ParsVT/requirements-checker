document.addEventListener('DOMContentLoaded', function(event) {

	var nVer = navigator.appVersion;
	var nAgt = navigator.userAgent;
	var browserName = navigator.appName;
	var fullVersion = '' + parseFloat(navigator.appVersion);
	var majorVersion = parseInt(navigator.appVersion, 10);
	var nameOffset, verOffset, ix;

	//In Opera, the true version is after "OPR" or after "Version"
	if ((verOffset = nAgt.indexOf('OPR')) != -1) {
		browserName = 'Opera';
		fullVersion = nAgt.substring(verOffset + 4);
		if ((verOffset = nAgt.indexOf('Version')) != -1)
			fullVersion = nAgt.substring(verOffset + 8);
	}
	//In MS Edge, the true version is after "Edg" in userAgent
	else if ((verOffset = nAgt.indexOf('Edg')) != -1) {
		browserName = 'Microsoft Edge';
		fullVersion = nAgt.substring(verOffset + 4);
	}
	//In MSIE, the true version is after "MSIE" in userAgent
	else if ((verOffset = nAgt.indexOf('MSIE')) != -1) {
		browserName = 'Microsoft Internet Explorer';
		fullVersion = nAgt.substring(verOffset + 5);
	}
	//In Chrome, the true version is after "Chrome"
	else if ((verOffset = nAgt.indexOf('Chrome')) != -1) {
		browserName = 'Chrome';
		fullVersion = nAgt.substring(verOffset + 7);
	}
	//In Safari, the true version is after "Safari" or after "Version"
	else if ((verOffset = nAgt.indexOf('Safari')) != -1) {
		browserName = 'Safari';
		fullVersion = nAgt.substring(verOffset + 7);
		if ((verOffset = nAgt.indexOf('Version')) != -1)
			fullVersion = nAgt.substring(verOffset + 8);
	}
	//In Firefox, the true version is after "Firefox"
	else if ((verOffset = nAgt.indexOf('Firefox')) != -1) {
		browserName = 'Firefox';
		fullVersion = nAgt.substring(verOffset + 8);
	}
	//In most other browsers, "name/version" is at the end of userAgent
	else if ((nameOffset = nAgt.lastIndexOf(' ') + 1) <
		(verOffset = nAgt.lastIndexOf('/'))) {
		browserName = nAgt.substring(nameOffset, verOffset);
		fullVersion = nAgt.substring(verOffset + 1);
		if (browserName.toLowerCase() == browserName.toUpperCase()) {
			browserName = navigator.appName;
		}
	}
	//Trim the fullVersion string at semicolon/space if present
	if ((ix = fullVersion.indexOf(';')) != -1)
		fullVersion = fullVersion.substring(0, ix);
	if ((ix = fullVersion.indexOf(' ')) != -1)
		fullVersion = fullVersion.substring(0, ix);

	majorVersion = parseInt('' + fullVersion, 10);
	if (isNaN(majorVersion)) {
		fullVersion = '' + parseFloat(navigator.appVersion);
		majorVersion = parseInt(navigator.appVersion, 10);
	}

	var test_canvas = document.createElement('canvas') //Try and create sample canvas element
	var HMTL5 = (test_canvas.getContext) ? true : false

	var OSName = 'Unknown';
	if (window.navigator.userAgent.indexOf('Windows NT 10.0') != -1) OSName = 'Windows 10';
	if (window.navigator.userAgent.indexOf('Windows NT 6.3') != -1) OSName = 'Windows 8.1';
	if (window.navigator.userAgent.indexOf('Windows NT 6.2') != -1) OSName = 'Windows 8';
	if (window.navigator.userAgent.indexOf('Windows NT 6.1') != -1) OSName = 'Windows 7';
	if (window.navigator.userAgent.indexOf('Windows NT 6.0') != -1) OSName = 'Windows Vista';
	if (window.navigator.userAgent.indexOf('Windows NT 5.1') != -1) OSName = 'Windows XP';
	if (window.navigator.userAgent.indexOf('Windows NT 5.0') != -1) OSName = 'Windows 2000';
	if (window.navigator.userAgent.indexOf('Mac') != -1) OSName = 'Mac/iOS';
	if (window.navigator.userAgent.indexOf('X11') != -1) OSName = 'UNIX';
	if (window.navigator.userAgent.indexOf('Linux') != -1) OSName = 'Linux';
	var yes = '<?php echo ParsVT_Check_Requirements::$yes; ?>';
	var no = '<?php echo ParsVT_Check_Requirements::$no; ?>';

	document.getElementById('screenheight1').innerHTML = screen.height;
	document.getElementById('screenheight2').innerHTML = (screen.height < 768 ? no : yes);

	document.getElementById('screenwidth1').innerHTML = screen.width;
	document.getElementById('screenwidth2').innerHTML = (screen.width < 1025 ? no : yes);

	document.getElementById('userAgent1').innerHTML = navigator.userAgent;
	document.getElementById('userAgent2').innerHTML = yes;

	document.getElementById('cookieEnabled1').innerHTML = navigator.cookieEnabled;
	document.getElementById('cookieEnabled2').innerHTML = (navigator.cookieEnabled ? yes : no);

	document.getElementById('browserName1').innerHTML = browserName + ' v' + fullVersion;
	document.getElementById('browserName2').innerHTML = yes;

	document.getElementById('HTML1').innerHTML = HMTL5;
	document.getElementById('HTML2').innerHTML = (HMTL5 ? yes : no);

	document.getElementById('OS1').innerHTML = OSName;
	document.getElementById('OS2').innerHTML = yes;
});