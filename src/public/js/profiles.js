let profiles = document.querySelectorAll('.tinder--card');
let profiles_grid = document.querySelector('.profiles_grid');

if (profiles.length < 2) {
    profiles_grid.style.gridTemplateColumns = '1fr';
    profiles_grid.style.width = 'auto';
}

for(let profile of profiles) {
    let activity = profile.querySelector('span')
    activity.innerHTML = getDiffTime(activity.innerHTML);
}
