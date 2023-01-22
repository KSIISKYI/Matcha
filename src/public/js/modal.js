// let modal = document.querySelector('.modal');
// let modal_content = modal.querySelector('.modal-content');
// let close_btn = modal.querySelector('.close');


// for(let i = 0; i < images.length; i++) {
// 	images[i].addEventListener('click', function(e) {
// 		modal_content.querySelector('img').src = e.target.src;
// 		modal.style.display = 'flex';
// 	})
// }

// modal.onclick = function() {
// 	modal.style.display = 'none';
// }

// close_btn.onclick = function() {
// 	modal.style.display = 'none';
// }


let forgot_pass_btn = document.querySelector('.forgot_pass');

let modal = document.querySelector('.modal');
let modal_message = modal.querySelector('.modal_message');

forgot_pass_btn.onclick = function(e) {
		modal.style.display = 'flex';
		
		setTimeout(() => { modal_message.classList.remove('close') }, 100);
	}

modal_message.onclick = function(e) {
	e.stopPropagation();
}

modal.onclick = function() {
	modal.style.display = 'none';
	modal.querySelector('.modal_message').classList.add('close');
}

document.querySelector('#reset_password').addEventListener('submit', function(e) {
	e.preventDefault();

	let data = new FormData(e.target);
	fetch('http://localhost:8000/account_settings/reset_password', {
		method: 'POST',
		body: data
	})
	modal.style.display = 'none';
	modal.querySelector('.modal_message').classList.add('close');
})
