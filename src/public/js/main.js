let burger_btn = document.querySelector('#burger');
let burger_menu = document.querySelector('.burger-menu');
let burger_menu_links = burger_menu.querySelectorAll('a');

burger_menu_links.forEach(link => {
	if (location.href == link.href) {
		link.querySelector('.menu-link').classList.add('selected');
	}
});

burger_btn.addEventListener('click', function() {
	document.querySelector('.header').classList.toggle("open");
})

document.querySelector('#about-me').addEventListener('input', function(e) {
	e.target.nextSibling.nextSibling.innerHTML = e.target.value.length + '/250'
})
