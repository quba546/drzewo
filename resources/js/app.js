require('./bootstrap');

// close alert message after specific time
let closeAlertMessage = function () {
    setTimeout(function () {
        if (document.getElementById('alert')) {
            document.getElementById('alert').style.display = 'none';
        }
    }, 5000);
}

closeAlertMessage();
