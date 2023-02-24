'use strict';

let tinder_container = document.querySelector('.tinder');
let all_cards;
var nope = document.getElementById('nope');
var love = document.getElementById('love');
let cards_container = document.querySelector('.tinder--cards');
let index = 0;
let current_page = 1;

var nope = document.getElementById('nope');
var love = document.getElementById('love');
let open_profile = document.getElementById('open_profile');
let current_profile_id;

// function get profiles by step "current_page"
async function getProfiles() {
    let response = await fetch(`/profiles?page=${current_page}`);
    let profiles = await response.json();

    return profiles
}

async function sendAnswer(profile_id, is_like) {
    let response = await fetch('/find_match/' + profile_id, {
        method: 'post',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({is_like: is_like})
    })

    if (is_like) {
        sendNotification(my_profile.id, profile_id, 1);
    }

    let data = await response.json();

    if (Object.keys(data).length > 0) {
        sendNotification(my_profile.id, profile_id, 2);
        sendNotification(profile_id, my_profile.id, 2);
        modal_init(my_profile, data);
    } 
}

async function createCards(profiles) {
    for (let profile of profiles) {
        let postion = index === 0 ? "position:relative;" : "";
        let photo = profile.profile_photos[0] ? profile.profile_photos[0].path : 'def_avatar.jpeg';
        let new_card = htmlToElement(
            `<div class="tinder--card" style="z-index:0;${postion}" profile_id=${profile.id}>
                <img src="/img/${photo}">
                <div id="shadow" style="pointer-events: none;"></div> 
                <div style="position: absolute; bottom: 0; color: #dddddd; padding: 20px; pointer-events: none;">
                    <h1>${profile.user.username}, ${profile.age}</h1>
                    <br>
                    <p><i class="fa-solid fa-desktop"></i> ${getDiffTime(profile.last_activity)}</p>
                    <br>
                    <p><i class="fa-solid fa-location-dot"></i> ${profile.distance} kilometer(s) from you</p>  
                </div>
            </div>`
        );

        index++;
        cards_container.appendChild(new_card);
    }
}

function initCards() {
    all_cards = document.querySelectorAll('.tinder--card');
    let new_cards = document.querySelectorAll('.tinder--card:not(.removed)');

    new_cards.forEach((card, index) => {
        if (!index) {
            current_profile_id = card.getAttribute('profile_id');
        }
        card.style.zIndex = all_cards.length - index;
    })

    tinder_container.classList.add('loaded');
}

async function initHummer() {
    for (let el of all_cards) {
        let hammertime = new Hammer(el);

        hammertime.on('pan', function (event) {
            el.classList.add('moving');
        });

        hammertime.on('pan', function (event) {
            if (event.deltaX === 0) return;
            if (event.center.x === 0 && event.center.y === 0) return;
        
            tinder_container.classList.toggle('tinder_love', event.deltaX > 0);
            tinder_container.classList.toggle('tinder_nope', event.deltaX < 0);
        
            let xMulti = event.deltaX * 0.03;
            let yMulti = event.deltaY / 80;
            let rotate = xMulti * yMulti;
        
            event.target.style.transform = 'translate(' + event.deltaX + 'px, ' + event.deltaY + 'px) rotate(' + rotate + 'deg)';
        });

        hammertime.on('panend', async function (event) {
            el.classList.remove('moving');
            tinder_container.classList.remove('tinder_love');
            tinder_container.classList.remove('tinder_nope');
        
            let moveOutWidth = document.body.clientWidth;
            let keep = Math.abs(event.deltaX) < 80 || Math.abs(event.velocityX) < 0.5;
        
            event.target.classList.toggle('removed', !keep);
        
            if (keep) {
                event.target.style.transform = '';
            } else {
                let endX = Math.max(Math.abs(event.velocityX) * moveOutWidth, moveOutWidth);
                let toX = event.deltaX > 0 ? endX : -endX;
                let endY = Math.abs(event.velocityY) * moveOutWidth;
                let toY = event.deltaY > 0 ? endY : -endY;
                let xMulti = event.deltaX * 0.03;
                let yMulti = event.deltaY / 80;
                let rotate = xMulti * yMulti;
            
                event.target.style.transform = 'translate(' + toX + 'px, ' + (toY + event.deltaY) + 'px) rotate(' + rotate + 'deg)';

                await sendAnswer(current_profile_id, toX > 0);

                // is checked, if there are 0 cards left, we create new cards
                if(document.querySelectorAll('.tinder--card:not(.removed)').length < 1) {
                    let profiles = await getProfiles();

                    if (profiles.length < 1) {
                        closeSwipeMenu();
                    } else {
                        createCards(profiles);
                        initCards();
                    }

                    await initHummer();
                } else {
                    initCards();
                }
            }
        });
    }
}

async function createButtonListener(love) {
    return async function (event) {
        var cards = document.querySelectorAll('.tinder--card:not(.removed)');
        var moveOutWidth = document.body.clientWidth * 1.5;
  
        if (!cards.length) return false;
  
        var card = cards[0];
        card.classList.add('removed');
  
        if (love) {
            card.style.transform = 'translate(' + moveOutWidth + 'px, -100px) rotate(-30deg)';
        } else {
            card.style.transform = 'translate(-' + moveOutWidth + 'px, -100px) rotate(30deg)';
        }

        //send answer
        await sendAnswer(current_profile_id, love);
  
        // is checked, if there are 0 cards left, we create new cards
        if(document.querySelectorAll('.tinder--card:not(.removed)').length < 1) {
            let profiles = await getProfiles();

            if (profiles.length < 1) {
                closeSwipeMenu();
            } else {
                createCards(profiles);
                initCards();
            }

            await initHummer();
        } else {
            initCards();
        }
    
        event.preventDefault();
    };
}

function closeSwipeMenu()
{
    tinder_container.remove();
    let content = document.querySelector('.content');
    let response_element = htmlToElement(`
        <div class="container">
            <h1 style="text-align: center; color: grey; margin-top: 30%;">
                At the moment the suitable profiles are not found, try changing your 
                <a href="/discovery_settings" style="color: #636363; text-decoration: underline;">search settings</a>
            </h1>
        </div>
    `)
    content.appendChild(response_element);
}

async function run() {
    let profiles = await getProfiles();

    if (profiles.length > 0) {
        let nopeListener = await createButtonListener(false);
        let loveListener = await createButtonListener(true);
    
        nope.addEventListener('click', nopeListener);
        love.addEventListener('click', loveListener);
    
        open_profile.addEventListener('click', (e) => {
            window.location.replace('/profiles/' + current_profile_id)
        })
    
        createCards(profiles);
        initCards();
    
        await initHummer()
    } else {
        closeSwipeMenu();
    }
}

run()
