document.getElementById('form').addEventListener('submit', function(e){
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
        window.location.replace(document.getElementById('refererUrl').value);
    }
}