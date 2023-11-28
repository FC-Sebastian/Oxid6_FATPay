let fatpay_form = document.getElementById('orderConfirmAgbBottom');

fatpay_form.addEventListener('submit', fatpay_verify);

async function fatpay_verify(e) {
    e.preventDefault();
    let url = 'http://localhost:81';
    let data = {shopsystem: 'oxid'};
    const respone = await fetch(url);
    const status = await respone.json();

    if (status.status === 'APPROVED') {
        fatpay_form.submit();
    } else if (status.status === 'ERROR') {
        document.getElementById('fatpay_error_div').className = '';
        document.getElementById('fatpay_error_message').innerHTML = status.errormessage;
    } else {
        console.log(status);
    }
}