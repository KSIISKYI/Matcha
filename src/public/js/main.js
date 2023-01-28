let burger_btn = document.querySelector('#burger');
let burger_menu = document.querySelector('.burger-menu');
let burger_menu_links = burger_menu.querySelectorAll('a');

for (link of burger_menu_links) {
	if (location.href === link.href) {
		link.querySelector('.menu-link').classList.add('selected');
		break;
	}
}

burger_btn.addEventListener('click', function() {
	document.querySelector('.header').classList.toggle("open");
})
