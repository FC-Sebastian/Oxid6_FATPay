let form = document.getElementById('form');
form.addEventListener('submit', function(e){
    e.preventDefault();
    verify_age();
});

async function verify_age()
{
    let bday = document.getElementById('birthday').value;
    let response = await validateTransction(bday);
    response = JSON.parse(response)

    if (response.status === 'SUCCESS') {
        form.action = decodeURIComponent(document.getElementById('redirectUrl').value);
        form.submit();
    } else if(response.status === 'ERROR') {
        let errorDiv = document.getElementById('error');
        errorDiv.firstElementChild.innerHTML = response.errormessage;
        errorDiv.className = '';
    }
}

function validateTransction(bday)
{
    return new Promise(function (resolve) {
        let url = window.location.protocol + '//' + window.location.host + window.location.pathname;
        url = url.replace('fatredirect.php','FatRedirectAjax.php');

        let xhr = new XMLHttpRequest();
        xhr.onload = () => {
            resolve(xhr.responseText);
        };

        xhr.open('POST', url);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send('transaction=' + document.getElementById('transaction').value + '&bday='+bday);
    })

}