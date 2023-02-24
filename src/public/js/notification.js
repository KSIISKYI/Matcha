let socket;
let my_profile;
let notification_demo = document.querySelector('.notification-demo');
let notification_count = document.querySelector('#notification_count');
let notifications_block = document.querySelector('#notifications');

async function getMyProfile()
{
    let response = await fetch('/profiles/my', {
        headers: {
            'Content-Type': 'application/json'
        }
    });
    my_profile = await response.json();
}


function createSocketConnection()
{
    document.cookie = 'from=' + my_profile.id + '; path=/';
    document.cookie = 'type=notification; path=/';
    socket = new WebSocket('ws://localhost:8090');
}

function markReviewed()
{
    let notifications = document.querySelectorAll('.notification-message');

    for (let notif_mess of notifications) {
        let link = notif_mess.getAttribute('href');
        fetch(link);
    }
}

function sendNotification(from, to, event_id)
{
    console.log(from, to, event_id);
    var context = {
        type: 'notification',
        from: from,
        to: to,
        mode: 'create',
        event_id: event_id,   
    };
    
    socket.send(JSON.stringify(context));
}

async function run()
{
    if (notifications_block) {
        removeTimeZones(notifications_block);
        setTimeZones(notifications_block, 'notification-message', '.notification-time');
    }

    markReviewed();
    await getMyProfile();
    createSocketConnection();
    
    socket.onopen = function() {
        console.log('Connect');
    };
    
    socket.onerror = function(error) {
        console.log(error.message);
    }
    
    socket.onclose = function() {
        console.log('Connection close');
    };

    socket.onmessage = function(event) {
        let response = JSON.parse(event.data);
        let notification_data = response.message;
        let context = notification_data.event_id == 1 ? 'You have new like' : 'You have new match';
        let notif_class = notification_data.event_id == 1 ? 'fa-solid fa-heart' : 'fa-solid fa-fire';
        let notification = htmlToElement(`
                <a href="#">
                    <div class="notification-demo-message">
                        <div class="notification-demo-message-event">
                            <i class="${notif_class}"></i>
                        </div>
                        <p>${context}</p>
                    </div>
                </a>
            `)
        
        notification_count.style.display = 'block';
        notification_count.innerHTML = parseInt(notification_count.innerHTML) + 1;
        notification_demo.querySelector('.notification-demo-inner').appendChild(notification);
        notification_demo.classList.remove('close');
        
        setTimeout(function() {
            notification_demo.classList.add('close');
            setTimeout(function() {
                notification.remove();
            }, 500)
        }, 5000)
    }
}

run();

