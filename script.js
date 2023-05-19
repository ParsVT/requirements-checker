const loginForm = document.getElementById('loginForm');
const loggedInContent = document.getElementById('loggedInContent');

loginForm.addEventListener('submit', function(event) {
	event.preventDefault();

	const username = document.getElementById('login-username').value;
	const password = document.getElementById('login-password').value;

	if (loginSuccessful) {
		loginForm.style.display = 'none';
		loggedInContent.style.display = 'block';
	}
});
