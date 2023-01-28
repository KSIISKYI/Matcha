let map_block = document.querySelector('#map')
let map;
let my_position;
let marker;
let diapason;

map_block.addEventListener('click', function(e) {
    this.innerHTML = '';

    get_my_location().then(function() {
        open_map();

        map.on('click', function (e) {

            set_marker(e.latlng);
        });
    })
}, { once: true })

function get_my_location() {
    return new Promise(function(resolve, reject) {
        navigator.geolocation.getCurrentPosition(function(position) {
            my_position = position.coords;

            resolve();
        }, function(error) {
            my_position = {latitude: 0, longitude: 0};

            resolve();
        })
    })
}

function open_map() {
    map = L.map('map').setView([my_position.latitude, my_position.longitude], 5); 

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    map_block.querySelector('.leaflet-control-attribution').remove();
}

function set_diapason(radius) {
    if (diapason) {
        map.removeLayer(diapason);
    }

    if (marker) {
        diapason = L.circle(marker.getLatLng(), {
            color: 'green',
            fillColor: 'green',
            fillOpacity: 0.2,
            radius: radius
        }).addTo(map);

        zoom_map(marker.getLatLng());
    }
}

function set_marker(position) {
    if (marker) {
        map.removeLayer(marker);
    }
    
    position_x = position.lat ? position.lat : position[0];
    position_y = position.lng ? position.lng : position[1];

    marker = new L.Marker(position).addTo(map);
    set_diapason(max_distance.innerHTML.split(' ')[0] * 1000);
}

function zoom_map(position) {
    let scale;

    let max_dist = max_distance.innerHTML.split(' ')[0];

    if (max_dist <= 5) {
        scale = 11;
    }

    if (5 < max_dist && max_dist <= 10) {
        scale = 10;
    } 

    if (10 < max_dist && max_dist <= 20) {
        scale = 9;
    } 

    if (20 < max_dist && max_dist <= 50) {
        scale = 8;
    } 
    
    if (50 < max_dist) {
        scale = 7;
    }

    map.setView(position, scale);
}
