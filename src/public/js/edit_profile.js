let new_images = [];
let rm_images = [];
let about_me = document.querySelector('#about-me');

about_me.addEventListener('input', function(e) {
	e.target.nextSibling.nextSibling.innerHTML = e.target.value.length + '/250'
})

function countChars(about_me) {
	about_me.nextSibling.nextSibling.innerHTML = about_me.value.length + '/250'
}

countChars(about_me);

let forms = document.querySelectorAll('.input_photo');
let update_form = new FormData();

forms.forEach(form => {
    form.addEventListener('input', function(e) {
    	e.preventDefault();

    	let cropped_img = change_and_add_img(e.target, this);
    })
})

function change_and_add_img(input, form) {
	let raw_img = new Image();
	let cropped_img = document.createElement('img');
	let reader = new FileReader();

	reader.onload = function() {
		raw_img.src = reader.result;
	}

	reader.readAsDataURL(input.files[0]);

	raw_img.onload = function() {
		cropp_img(raw_img, cropped_img);
	}

	cropped_img.onload = function() {
		form.parentElement.parentElement.appendChild(cropped_img);

		let remove_button = htmlToElement('<span class="rm_img">&times;</span>'); //create remove button
		remove_button.onclick = remove_img;

		form.parentElement.parentElement.appendChild(remove_button); // add remove button
		form.parentElement.parentElement.classList = '';

        form.parentElement.remove();
	}

	return cropped_img;
}

function cropp_img(raw_img, cropped_img) {
	let canvas = document.createElement('canvas');

	let origin_w = raw_img.width;
	let origin_h = raw_img.height;

	let cropped_w;
	let cropped_h;
	let cropped_x;
	let cropped_y;

	if (origin_h > origin_w) {
		if (origin_h > origin_w * 1.2) {
			cropped_w = origin_w;
			cropped_h = cropped_w * 1.2;

			cropped_x = 0;
			cropped_y = (origin_h - cropped_h) / 2;
		} else {
			cropped_h = origin_h;
			cropped_w = cropped_h / 1.2;

			cropped_x = (origin_w - cropped_w) / 2;
			cropped_y = (origin_h - cropped_h) / 2;
		}
	} else {
		cropped_h = origin_h;
		cropped_w = cropped_h / 1.2;

		cropped_x = (origin_w - cropped_w) / 2;
		cropped_y = 0;
	}

	if (cropped_w < 600) {
		canvas.width = 600;
		canvas.height = 720;

		let context = canvas.getContext('2d');
		context.drawImage(raw_img, cropped_x, cropped_y, cropped_w, cropped_h, 0, 0,  canvas.width, canvas.height);

	} else {
		canvas.width = cropped_w;
		canvas.height = cropped_h;

		let context = canvas.getContext('2d');
		context.drawImage(raw_img, -cropped_x, -cropped_y, origin_w, origin_h);
	}

	new_images.push(canvas.toDataURL('image/jpeg', 1.0));


	cropped_img.src = canvas.toDataURL('image/jpeg', 1.0);
}


function htmlToElement(html) {
    var template = document.createElement('template');
    html = html.trim(); // Never return a text node of whitespace as the result
    template.innerHTML = html;
    return template.content.firstChild;
}

function remove_img() {
	let new_el_id = Math.random();
	let new_el = htmlToElement(`<div class="add_img">\
                                    Add photo\
                                    <form style="margin: 0;">\
                                        <label for="${new_el_id}"><i class="fa-solid fa-file-circle-plus"></i></label>\
                                        <input id="${new_el_id}" type="file" name="img" accept="image/*" required style="display: none;">\
                                    </form>\
                                </div>`
                                )
	new_el.querySelector('form').addEventListener('input', function(e) {
		e.preventDefault();

	  	let cropped_img = change_and_add_img(e.target, this);
	})

	this.parentElement.appendChild(new_el)
	if (this.parentElement.id === 'main_img') this.parentElement.classList = 'add';

	check_is_saved_img(this.parentElement.querySelector('img'));

	this.previousSibling.remove();
	this.previousSibling.remove();
	this.remove();
}

function check_is_saved_img(img) {
	let i = new_images.indexOf(img.src);

	if (i !== -1) {
		new_images.splice(i, 1);
	} else {
		rm_images.push(img.getAttribute('value'));
	}
}

document.querySelectorAll('.rm_img').forEach(button => {
	button.onclick = remove_img;
})

document.querySelector('#update_profile').addEventListener('submit', function(e) {
	e.preventDefault();

	let form_data = new FormData(e.target);

	send_edit_profile_form(form_data).then(console.log('finosh'));
});

async function send_edit_profile_form(form_data) {

	for(img_id of rm_images) {
		await fetch(`http://localhost:8000/profile/profile_images/${img_id}`, {
			method: 'DELETE'
		})
	}

	for (new_img of new_images) {
		let img_new_form = new FormData;
		img_new_form.append('img_base64', new_img.replace(/^data:image\/jpeg;base64,/, ''));

		 await fetch('http://localhost:8000/profile/profile_images', {
			method: 'POST',
			body: img_new_form
		})
	}

	await fetch(`http://localhost:8000/profile/settings`, {
		method: 'POST',
		body: form_data
	})

	location.replace(location.href)
}

