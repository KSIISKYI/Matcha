let socket;
let data;

let message = document.querySelector('#message');

let chat_header = document.querySelector('.chat-header');
let chat_body = document.querySelector('.chat-body');
let message_button = document.querySelector('#send_message');
let last_activity = document.querySelector('#last_activity');
let next_page = location.href + '/messages';

message.addEventListener('input', checkLengthMessage);
message_button.onclick = sendMessage;

async function getMessages()
{
    if (next_page) {
        let response = await fetch(next_page);
        let messages = await response.json();

        next_page = messages.next_page_url ? 'http://localhost:8000' + messages.next_page_url : null;

        return messages.data;
    }

    return [];
}

async function appendMessages(messages_data)
{
    for(let message_data of messages_data) {
        let messageHTML = createHTMLMessageElement(message_data);

        chat_body.insertBefore(messageHTML, chat_body.firstChild);
    }
    
    setTimeZones(chat_body, 'chat-message', '.chat-message-time');
}

function appendMessage(message_data, end=true)
{
    let messageHTML = createHTMLMessageElement(message_data);

    if (end) {        
        chat_body.appendChild(messageHTML);

    } else {
        chat_body.insertBefore(messageHTML, chat_body.firstChild);
    }

    setTimeZones();
}

function createHTMLMessageElement(message_data)
{
    let time = moment((new Date(message_data.created_at))).format('L LT');
        
    let class_message = message_data.participant_id === data.my_participant.id ? 'own' : '';
    let profile_photo;
    
    if (class_message == 'own') {
        profile_photo = data.my_participant.profile.profile_photos.length > 0 ? 
            data.my_participant.profile.profile_photos[0].path :
            '/def_avatar.jpeg';
    } else {
        profile_photo = data.other_participant.profile.profile_photos.length > 0 ? 
            data.other_participant.profile.profile_photos[0].path :
            '/def_avatar.jpeg';
    }

    var message = htmlToElement(`
        <div class="chat-message ${class_message}">
            <div class="profile-logo-demo" style="width: 50px; height:50px;">
                <img src="http://localhost:8000/img/${profile_photo}">
            </div>
            <div class="chat-message-context">
                <div class="chat-message-context-inner">
                    ${message_data.message}
                </div>
        </div>
        <div class="chat-message-time">${time}</div>
        </div>
    `)

    return message;
}


function createSocketConnection()
{
    document.cookie = 'from=' + data.my_participant.id + '; path=/';
    document.cookie = 'type=chat; path=/';
    socket = new WebSocket('ws://Oleksiii:sdfdfdfsdf21edsfwrfsdvewrok3iwryiso@localhost:8090');
}

function checkLengthMessage()
{
    if (message.value.length < 1) {
        message_button.disabled = true;
        message_button.style.backgroundColor = '#ccc';
    } else {
        message_button.disabled = false;
        message_button.style.backgroundColor = '#95b9a3';
    }
}

function sendMessage()
{
    var context = {
        type: 'chat',
        from: data.my_participant.id,
        to: data.other_participant.id,
        chat: data.chat.id,
        message: message.value,   
    };

    socket.send(JSON.stringify(context));
    message.value = '';
    checkLengthMessage();
}

function crollBottom()
{
    chat_body.scrollTo(0, chat_body.scrollHeight);
}

async function initChat()
{
    var response = await fetch(location.href, {
        headers : {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    });

    data = await response.json();
    var other_profile_photo = data.other_participant.profile.profile_photos.length > 0 ? 
        data.other_participant.profile.profile_photos[0].path :
        '/def_avatar.jpeg';

    chat_header.appendChild(htmlToElement(`
        <a href="http://localhost:8000/profiles/${data.other_participant.profile.id}">
            <div class="profile-logo-demo">
                <img src="http://localhost:8000/img/${other_profile_photo}">
            </div>
        </a>
    `));

    chat_header.appendChild(htmlToElement(`
        <div style="display: flex; flex-direction: column; width: 85%;">
            <a href="http://localhost:8000/profiles/${data.other_participant.profile.id}">
                <h2 style="color: grey; margin-bottom: 10px;">${data.other_participant.profile.user.username}</h2>
            </a>
            <h3 style="color:#ccc" id="last_activity">${getDiffTime(data.other_participant.profile.last_activity)}</h3>
        </div>
    `));

}

async function run()
{
    await initChat();
    let messages_data = await getMessages();
    appendMessages(messages_data);
    crollBottom();
    checkLengthMessage();
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
        let message_data = JSON.parse(event.data);

        appendMessage(message_data);

        crollBottom();
    };

    chat_body.onscroll = async function(e) {
        if (!chat_body.scrollTop) {
            let w = chat_body.scrollHeight;
            let messages_data = await getMessages();
            appendMessages(messages_data);
            chat_body.scrollTop = chat_body.scrollHeight - w;
        }
        
    }
}

run()
