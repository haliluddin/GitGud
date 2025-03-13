window.onload = function() {
    const email = localStorage.getItem('email');
    const password = localStorage.getItem('password');

    if (email) {
        document.querySelector('input[name="email"]').value = email;
    }
    if (password) {
        document.querySelector('input[name="password"]').value = password;
    }
};