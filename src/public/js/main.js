let burger_btn = document.querySelector('#burger');
let burger_menu = document.querySelector('.burger-menu');
let burger_menu_links = burger_menu.querySelectorAll('a');

function htmlToElement(html) {
    var template = document.createElement('template');
    html = html.trim(); // Never return a text node of whitespace as the result
    template.innerHTML = html;
    return template.content.firstChild;
}
    
function getDiffTime(date) {
	let time = (new Date(date)).getTime() + 3600000 * 2;
	let now = new Date();
	let diff_time = new Date(now - time - 3600000 * 3);
  
  	if ((diff = diff_time.getFullYear()) > 1970) {
		return `active ${diff - 1970} year(s) ago`;
	}
  
  	if ((diff = diff_time.getMonth()) > 0) {
  		return `active ${diff} month(s) ago`;
	}
  
  	if ((diff = diff_time.getDate()) > 1) {
  		return `active ${diff} day(s) ago`;
	}
  
  	if ((diff = diff_time.getHours()) > 0) {
  		return `active ${diff} hour(s) ago`;
	} else {
		return 'online';
	}
}

for (link of burger_menu_links) {
	if (location.href === link.href) {
		link.querySelector('.menu-link').classList.add('selected');
		break;
	}
}

burger_btn.addEventListener('click', function() {
	document.querySelector('.header').classList.toggle("open");
})
