let multiselect_block_values = [];
let new_images = [];
let rm_images = [];

let multiselect_block = document.querySelectorAll(".multiselect_block");
    multiselect_block.forEach(parent => {
        let label = parent.querySelector(".field_multiselect");
        let select = parent.querySelector(".field_select");
        let text = label.innerHTML;
        select.addEventListener("change", function(element) {
        	multiselect_block_values = [];

            let selectedOptions = this.selectedOptions;
            label.innerHTML = "";
            for (let option of selectedOptions) {
                let button = document.createElement("button");
                button.type = "button";
                button.className = "btn_multiselect";
                button.textContent = option.innerHTML;
                multiselect_block_values.push(option.value) // append selected value of multiselect_block_values array
                option.style.display = 'none';
                button.onclick = _ => {
                    option.style.display = 'block';
                    option.selected = false;
                    button.remove();

                    let i = multiselect_block_values.indexOf(option.value);
                    
                    if (i !== -1) multiselect_block_values.splice(i, 1); // delete selected value of multiselect_block_values array

                    if (!select.selectedOptions.length) label.innerHTML = text
                };
                label.append(button);
            }
        })
    })


let forms = document.querySelectorAll('form');
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

document.querySelector('#update').onclick = function() {
	// console.log(new_images);
	// console.log(rm_images);

	rm_images.forEach(img_id => {
		fetch(`http://localhost:8000/profile/profile_images/${img_id}`, {
			method: 'DELETE'
		})
	});

	new_images.forEach(new_img => {
		let img_new_form = new FormData;
		img_new_form.append('img_base64', new_img.replace(/^data:image\/jpeg;base64,/, ''));

		fetch('http://localhost:8000/profile/profile_images', {
			method: 'POST',
			body: img_new_form
		})
	});

	location.replace(location.href);
	// fetch('save.php', {
 //                method: 'POST',
 //                body: update_form
 //            })
 //            .then((response) => {
 //                    console.log('ok');
 //            })
}











