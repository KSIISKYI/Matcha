show_pass_btns = document.querySelectorAll('.show_pass');

function show_password(button, input) {
	function inner() {
		if (input.type === 'text') {
			input.setAttribute('type', 'password')
			button.classList = 'fa-regular fa-eye';
		} else {
			input.setAttribute('type', 'text')
			button.classList = 'fa-regular fa-eye-slash'
		}
	}
	return inner
}

show_pass_btns.forEach(btn => {
	btn.addEventListener('click', show_password(btn, btn.nextSibling.nextSibling.nextSibling.nextSibling));
})

document.querySelector('#google_auth').addEventListener('click', function(e) {
	e.preventDefault();
	let link = e.target.getAttribute('value');

	location.replace(link);
})
