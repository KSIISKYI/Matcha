let modal = document.querySelector('.modal');
let modal_message = modal.querySelector('.modal_message');
let match_message;

function modal_init(my_profile, another_profile) {
    let my_profile_photo = my_profile.profile_photos[0] ? my_profile.profile_photos[0].path : 'def_avatar.jpeg';
    let another_profile_photo = another_profile.profile_photos[0] ? another_profile.profile_photos[0].path : 'def_avatar.jpeg';
    match_message = htmlToElement(`
        <div class="match_message">
			<h1 style="font-size: 60px;">It's a Match</h1>
			<p style="margin: 20px 0 0;">You and ${another_profile.user.username} have liked each other.</p>
			<div style="display:flex; justify-content: space-between; margin: 30px 0; width: 100%; align-items: center;">
				<div class="profile-logo-demo" style="width: 200px; height: 200px; border: 3px solid white;">
					<img src="/img/${my_profile_photo}">
				</div>
				<div style="color: #FD6F70; font-size: 0px">
					<i class="fa-solid fa-heart"></i>
				</div>
				<div class="profile-logo-demo" style="width: 200px; height: 200px; border: 3px solid white;">
					<img src="/img/${another_profile_photo}"></div>
				</div>
			<a href="/chats/${another_profile.new_chat.id}" style="width: 50%;"><div class="match_button">go to chat</div></a>
		</div>`
    );

    modal_message.appendChild(match_message);
    let i = match_message.querySelector('i');

    setTimeout(() => { modal.style.display = 'flex' }, 300);
    setTimeout(() => { modal_message.classList.remove('close') }, 700);
    setTimeout(() => { i.style.fontSize = '55px' }, 1000);
}

modal_message.onclick = function(e) {
	e.stopPropagation();
}

modal.onclick = function() {
	modal.style.display = 'none';
	modal.querySelector('.modal_message').classList.add('close');
    match_message.remove();
}
