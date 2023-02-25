let multiselect_block = document.querySelector(".multiselect_block");
let label = multiselect_block.querySelector(".field_multiselect");
let select = multiselect_block.querySelector(".field_select");
let text = label.innerHTML;


function multielect(option) {
	if (option.selected) {
		let button = document.createElement("button");
		button.type = "button";
		button.className = "btn_multiselect";
		button.textContent = option.innerHTML;
		option.style.display = 'none';
		button.onclick = _ => {
			option.style.display = 'block';
			option.selected = false;
			button.remove();
			if (!select.selectedOptions.length) label.innerHTML = text
		};
		label.append(button);
	} else {
		option.style.display = 'block';
	}
}

select.addEventListener("change", function(element) {
	let options = this.options;
	label.innerHTML = "";
	
	for (let option of options) {
		multielect(option);
	}
})

let options = select.options;
if (select.selectedOptions.length > 0) label.innerHTML = '';

for (let option of options) {
	multielect(option);
}
