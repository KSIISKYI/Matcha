let socket;
let data;

let message = document.querySelector('#message');

let chat_header = document.querySelector('.chat-header');
let chat_body = document.querySelector('.chat-body');
let message_button = document.querySelector('#send_message');
let last_activity = document.querySelector('#last_activity');
let next_page = location.href + '/messages';

// options of selected message
let selected_message = document.querySelector('.selected_message');
let selected_message_button = selected_message.querySelector('.selected_message-cancel');

message.addEventListener('input', checkLengthMessage);

selected_message_button.onclick = function(e) {
    e.stopPropagation();
    e.preventDefault();

    selected_message.style.height = 0;
    message.setAttribute('mode', 'create');
    message.removeAttribute('id');
    message.value = '';
    checkLengthMessage();
}

message_button.onclick = function(e) {
    let mode = message.getAttribute('mode');
    let id = message.getAttribute('message_id');
    sendMessage({message: message.value, id: id}, mode);
    selected_message.style.height = 0;
    message.setAttribute('mode', 'create');
    message.removeAttribute('id');
}

async function getMessages()
{
    if (next_page) {
        let response = await fetch(next_page);
        let messages = await response.json();
        next_page = messages.next_page_url ? messages.next_page_url : null;

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

    setTimeZones(chat_body, 'chat-message', '.chat-message-time');
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

    var message_element = htmlToElement(`
        <div class="chat-message ${class_message}" id="msg${message_data.id}">
            <div class="profile-logo-demo" style="width: 50px; height:50px;">
                <img src="/img/${profile_photo}">
            </div>
            <div class="chat-message-context">
                <div class="chat-message-context-inner">
                    ${message_data.message}
                </div>
                <div class='message-options' style="right: ${class_message ? 0 : 'auto'};">
                    <i id="edit_message" 
                        class="fa-solid fa-pencil"
                        message_id="${message_data.id}"></i>
                    <i id="remove_message" 
                        class="fa-solid fa-trash"
                        message_id="${message_data.id}"
                        style="color: #ff0000d9;"></i>
                </div>
            </div>

            <div class="chat-message-time">${time}</div>
        </div>
    `)

    if (class_message) {
        initMessageOprtions(message_element);
    }

    return message_element;
}

function initMessageOprtions(message_element)
{
    let message_context = message_element.querySelector('.chat-message-context-inner');
    let edit_message = message_element.querySelector('#edit_message');
    let remove_message = message_element.querySelector('#remove_message');

    message_context.onclick = function(event) {
        event.stopPropagation();
        closeAllOptions();

        let options = this.parentElement.querySelector('.message-options');
        options.style.height = '70%';
    }

    edit_message.onclick = function(e) {
        message.value = message_context.innerHTML.trim();
        message.setAttribute('mode', 'update');
        message.setAttribute('message_id', this.getAttribute('message_id'));
        
        let selected_message_inner = selected_message.querySelector('.selected_message-inner');
        selected_message.style.height = 'auto';
        selected_message_inner.innerHTML = message_context.innerHTML.trim();

        checkLengthMessage();
    }

    remove_message.onclick = function(event) {
        sendMessage(this.getAttribute('message_id'), 'delete');
        setTimeZones(chat_body, 'chat-message', '.chat-message-time');
    }
}

function closeAllOptions()
{
    let options = chat_body.querySelectorAll('.message-options');

    options.forEach(option => {
        option.style.height = '0%';
    })
}


function createSocketConnection()
{
    document.cookie = 'from=' + data.my_participant.id + '; path=/';
    document.cookie = 'type=chat; path=/';
    socket = new WebSocket('ws://localhost:8090');
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

function sendMessage(mess_data, mode)
{
    var context = {
        type: 'chat',
        from: data.my_participant.id,
        to: data.other_participant.id,
        chat: data.chat.id,
        mode: mode,
        message: mess_data,   
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
    var status = '';

    data = await response.json();
    var other_profile_photo = data.other_participant.profile.profile_photos.length > 0 ? 
        data.other_participant.profile.profile_photos[0].path :
        '/def_avatar.jpeg';    

    if (moment.parseZone(data.other_participant.profile.last_activity).add(1, 'hour').unix() > moment().unix()) {
        status = `<div style="background-color: white; position: absolute; padding: 4px; border-radius: 50%; top: 76%; left: 61%;">
            <div class="notification-message-status"></div>
        </div>`;
    }

    chat_header.appendChild(htmlToElement(`
        <a href="/profiles/${data.other_participant.profile.id}" style="position: relative;">
            <div class="profile-logo-demo">
                <img src="/img/${other_profile_photo}">
            </div>
            ${status}
        </a>
    `));

    chat_header.appendChild(htmlToElement(`
        <div style="display: flex; flex-direction: column; width: 85%;">
            <a href="/profiles/${data.other_participant.profile.id}">
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

        switch (message_data.mode) {
            case 'create':
                appendMessage(message_data.message);
                crollBottom();
                break;
            case 'update':
                let m = chat_body.querySelector(`#msg${message_data.message.id}`);
                let m_i = m.querySelector('.chat-message-context-inner')
                m_i.innerHTML = message_data.message.message;
                break;
            case 'delete':
                chat_body.querySelector(`#msg${message_data.message}`).remove();
        }

        
    };

    chat_body.onscroll = async function(e) {
        if (!chat_body.scrollTop) {
            let w = chat_body.scrollHeight;
            let messages_data = await getMessages();
            appendMessages(messages_data);
            chat_body.scrollTop = chat_body.scrollHeight - w;
        }
        
    }

    document.addEventListener('click', function(e) {
        closeAllOptions();
    })
    
}

run()
