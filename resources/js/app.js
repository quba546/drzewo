require('./bootstrap');

// close alert message after specific time
let closeAlertMessage = function () {
    setTimeout(function () {
        if (document.getElementById('alert')) {
            document.getElementById('alert').style.display = 'none';
        }
    }, 3000);
}

closeAlertMessage();

document.getElementById('category-to-move').addEventListener('change', function () {
    let value = this.value;
    document.getElementById('moveCategory2').style.display = 'block';
    let options = document.getElementById('parent-to-moved').getElementsByTagName('option');
    for (let i = 0; i < options.length; i++) {
        if (options[i].value === value) {
            options[i].remove();
        }
    }
});
