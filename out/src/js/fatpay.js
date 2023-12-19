let fatpay_form = document.getElementById('orderConfirmAgbBottom');
let fatpay_errorDiv = document.getElementById('fatpay_error_div');

fatpay_form.addEventListener('submit', fatpay_verify);

function fatpay_verify(e) {
    e.preventDefault();
    fatpay_errorDiv.className = 'd-none';
    let data = {
        shopsystem: 'oxid',
        shopversion: fcShopVersion,
        moduleversion: fcFatPayVersion,
        language: fcActiveLang,
        billing_firstname: fcBillingAddress.firstname,
        billing_lastname: fcBillingAddress.lastname,
        billing_street: fcBillingAddress.street,
        billing_zip: fcBillingAddress.zip,
        billing_city: fcBillingAddress.city,
        billing_country: fcBillingAddress.country,
        shipping_firstname: fcShippingAddress.firstname,
        shipping_lastname: fcShippingAddress.lastname,
        shipping_street: fcShippingAddress.street,
        shipping_zip: fcShippingAddress.zip,
        shipping_city: fcShippingAddress.city,
        shipping_country: fcShippingAddress.country,
        email: fcEmail,
        customer_nr: fcCustNr,
        order_sum: fcOrderSum,
        currency: fcCurrency,
        payment_type: fcPaymentMethod
    };

    fetch(fcUrl, {
        method: 'POST',
        mode: "no-cors",
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'data=' + JSON.stringify(data)
    })
        .then(response => response.json())
        .then(response => {
            if (response.status === 'APPROVED') {
                fatpay_form.submit();
            } else if (response.status === 'REDIRECT') {
                document.getElementById('fatredirect_url').value = response.url;
                fatpay_form.submit();
            } else if (response.status === 'ERROR') {
                document.getElementById('fatpay_error_message').innerHTML = response.errormessage;
                fatpay_errorDiv.className = '';
                fatpay_errorDiv.scrollIntoView({behavior: 'smooth'});
            } else {
                console.log(response);
            }
        })
        .catch(error => {
            console.error(error);
        });
}