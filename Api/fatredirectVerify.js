let form = document.getElementById('form');
form.addEventListener('submit', function(e){
    e.preventDefault();
    verify_age();
});

function verify_age()
{
    let bday = new Date(document.getElementById('birthday').value).getTime();
    let today = new Date().getTime();

    if (((today - bday) / (1000*60*60*24*365.25)) < 18) {
        document.getElementById('error').className = '';
    } else {
        updateTranactionStatus();
        form.action = decodeURIComponent(document.getElementById('redirectUrl').value);
        form.submit();
    }
}

function updateTranactionStatus()
{
    let url = window.location.protocol + '//' + window.location.host + window.location.pathname;
    url = url.replace('fatredirect.php','FatpayAPI.php');

    let xhr = new XMLHttpRequest();
    xhr.open('PUT', url);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send(document.getElementById('transaction').value);
}