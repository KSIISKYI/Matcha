let max_distance_block = document.querySelector('#max_distance_block');
let age_range_block = document.querySelector('#age_range');
let fame_rating_range_block = document.querySelector('#fame_rating_range');
let data = new FormData;

let position;
let max_distance = document.querySelector('#max_distance');
let age_min = document.querySelector('#age_min');
let age_max = document.querySelector('#age_max');
let fame_rating_min = document.querySelector('#fame_rating_min');
let fame_rating_max = document.querySelector('#fame_rating_max');
let position_x;
let position_y;


noUiSlider.create(max_distance_block, {
    start: max_distance.innerHTML,
    step: 1,
    connect: [true, false],
    range: {
        'min': 1,
        'max': 100
    }
});

noUiSlider.create(age_range_block, {
    start: [age_min.innerHTML, age_max.innerHTML],
    step: 1,
    connect: true,
    range: {
        'min': 18,
        'max': 100
    }
});

noUiSlider.create(fame_rating_range_block, {
    start: [fame_rating_min.innerHTML, fame_rating_max.innerHTML],
    step: 1,
    connect: true,
    range: {
        'min': 0,
        'max': 100
    }
});


max_distance_block.noUiSlider.on('update', function(values, handle) {
	let p = max_distance_block.nextSibling.nextSibling.querySelector('p');
	p.innerHTML = parseInt(values[0]) + ' km';

    data.set('max_distance', values[0]);

    if (map) set_diapason(values[0] * 1000);
})

age_range_block.noUiSlider.on('update', function(values, handle) {
	let p = age_range_block.nextSibling.nextSibling.querySelectorAll('p');
	p[0].innerHTML = parseInt(values[0]);
	p[1].innerHTML = parseInt(values[1]);

    data.set('age_min', values[0]);
    data.set('age_max', values[1]);
})

fame_rating_range_block.noUiSlider.on('update', function(values, handle) {
	let p = fame_rating_range_block.nextSibling.nextSibling.querySelectorAll('p');
	p[0].innerHTML = parseInt(values[0]);
	p[1].innerHTML = parseInt(values[1]);

    data.set('fame_rating_min', values[0]);
    data.set('fame_rating_max', values[1]);
})

document.querySelector('.my_location').addEventListener('click', function(e) {
    e.preventDefault();

    set_marker([my_position.latitude, my_position.longitude]);
})

document.querySelector('#save').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (position_x && position_y) {
        data.append('position_x', position_x);
        data.append('position_y', position_y);
    }

    let form_data = new FormData(e.target);

    for(let elem of data.entries()) {
        form_data.set(elem[0], elem[1]);
    }

    fetch(e.target.action, {
        method: e.target.method,
        body: form_data
    }).then((response) => {location.replace(location.href)});
})