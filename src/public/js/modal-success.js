let modal = document.querySelector('.modal');
let modal_message = modal.querySelector('.modal_message');
let success_message;
let report_button = document.querySelector('#report');
let block_button = document.querySelector('#block');


report_button.onclick = (e) => {
    modal.style.display = 'flex';
    setTimeout(() => { modal_message.classList.remove('close') }, 500);

    fetch(e.target.getAttribute('href')).then(response => {
        if (response.status === 200) {
            e.target.style.backgroundColor = '#ccc';
            e.target.style.pointerEvents = 'none';
            e.target.disabled = true;
            e.target.innerHTML = 'Reported';
        }
    });
};

block_button.onclick = (e) => {
    modal.style.display = 'flex';
    setTimeout(() => { modal_message.classList.remove('close') }, 500);

    if (e.target.getAttribute('blocked')) {
        fetch(e.target.getAttribute('unblock-action')).then(response => {
            if (response.status === 200) {
                e.target.style.backgroundColor = '#ff0000a3';
                e.target.innerHTML = 'Block';
                e.target.setAttribute('blocked', '');
            }
        });
    } else {
        fetch(e.target.getAttribute('block-action')).then(response => {
            if (response.status === 200) {
                e.target.style.backgroundColor = 'rgba(1, 172, 35, 0.74)';
                e.target.innerHTML = 'Unblock';
                e.target.setAttribute('blocked', 1)
            }
        });
    }
};

modal_message.onclick = function(e) {
	e.stopPropagation();
}

modal.onclick = function() {
	modal.style.display = 'none';
	modal.querySelector('.modal_message').classList.add('close');
}
