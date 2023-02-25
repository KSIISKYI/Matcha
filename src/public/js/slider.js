let img_offset = 0;
let pointer_offset = 0;

const slider_line = document.querySelector('.slider-line');
let slider_pointer = document.querySelector('.slider-pointer');

let images = slider_line.querySelectorAll('img');
let img_count = images.length;
let slider_gap = 600 / img_count;
let last_activity = document.querySelector('#last_activity');

initActivity(last_activity);

slider_pointer.style.width = 600 / img_count + 'px';

document.querySelector('#slider-next').addEventListener('click', function() {
	img_offset += 600;
	pointer_offset += slider_gap;

	if (img_offset > (img_count - 1) * 600) {
		pointer_offset = 0;
		img_offset = 0;
	}
	slider_pointer.style.left = pointer_offset + 'px';
	slider_line.style.left = -img_offset + 'px';
})

document.querySelector('#slider-prev').addEventListener('click', function() {
	img_offset -= 600;
	pointer_offset -= slider_gap;

	if (img_offset < 0) {
		img_offset = (img_count- 1) * 600;
		pointer_offset = (img_count- 1) * slider_gap;
	}
	slider_pointer.style.left = pointer_offset + 'px';
	slider_line.style.left = -img_offset + 'px';
})

function initActivity(last_activity) {
	if (last_activity) {
		let activity = getDiffTime(last_activity.getAttribute('value'));
		last_activity.appendChild(htmlToElement(`<div><i class="fa-solid fa-desktop"></i> ${activity}</div>`));
	} 
}
