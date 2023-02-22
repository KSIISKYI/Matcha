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

function removeTimeZones(body)
{
    let time_zones = [...body.children].filter(elem => [...elem.classList].includes('separator_line'))
    
    for (let time_zone of time_zones) {
        time_zone.remove()
    }
}

function insertTimeZone(message, body, time_zone)
{
    let diff_time_elem = htmlToElement(`
        <div class="separator_line" style="font-size:20px; margin:30px 0 20px;">
            <div class="line"></div>
            <div class="separator_text" style="top: -10px;">${time_zone}</div>
        </div>
    `);
    body.insertBefore(diff_time_elem, message);
}

function setTimeZones(body, needed_class, time_class)
{
    removeTimeZones(body);

    let messages = ([...body.children].reverse()).filter(elem => [...elem.classList].includes(needed_class));

    for(let i = 0; i < messages.length; i++) {
        if (i + 1 === messages.length) {
            if (messages[i].previousElementSibling) {
                break;
                
            } else {
                let res = null;

                switch (getDiffTime2(
                        moment(),
                        moment(messages[i].querySelector(time_class).innerHTML),
                )) {
                    case 0:
                        res = "Today";
                        break; 
                    case 1:
                        res = "Yesterday";
                        break;
                    default :
                        res = moment(messages[i].querySelector(time_class).innerHTML).format('L');
                }
                
                insertTimeZone(messages[i], body, res);
                break;
            }
        }

        let res = null;

        switch (getDiffTimeZone(
                moment(messages[i].querySelector(time_class).innerHTML),
                moment(messages[i+1].querySelector(time_class).innerHTML),
            )) {
            case 0:
                break;
            case 1:
                res = "Today";
                break;
            case 2: 
                res = "Yesterday";
                break;
            case 3:
                res = moment(messages[i].querySelector(time_class).innerHTML).format('L')
        }

        if (res && [...messages[i].previousElementSibling.classList].includes(needed_class)) {
            insertTimeZone(messages[i], body, res);
        }
    }
}

function getDiffTime2(date1, date2)
{
    let d_diff = date1.format('D') - date2.format('D');
    let m_diff = date1.format('M') - date2.format('M');
    let y_diff = date1.format('YYYY') - date2.format('YYYY');
    let diff = y_diff * 365 + m_diff * 30 + d_diff;

    return diff;
}

function getDiffTimeZone(date1, date2)
{
    let diff = getDiffTime2(date1, date2);
    let diff_now = getDiffTime2(moment(), date1);

    if (diff === 0) {
        return 0;
    } else if (diff > 0 && diff_now === 0) {
        return 1;
    } else if (diff > 0 && diff_now === 1) {
        return 2;
    } else {
        return 3;
    }
}

for (link of burger_menu_links) {
	if (location.protocol + '//' + location.host + location.pathname === link.href) {
		link.querySelector('.menu-link').classList.add('selected');
		break;
	}
}

burger_btn.addEventListener('click', function() {
	document.querySelector('.header').classList.toggle("open");
})
