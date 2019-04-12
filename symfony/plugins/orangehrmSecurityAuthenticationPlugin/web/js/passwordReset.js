function goToLogin() {
    var url = location.href;
    var urlSegments = url.toString().split('index.php');
    location.href = urlSegments[0] + 'index.php/auth/login';
}

$(document).ready(function() {
    $('#btnCancel').click(goToLogin);
});
