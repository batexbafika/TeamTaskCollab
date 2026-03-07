document.addEventListener('DOMContentLoaded', function () {
    var loginForm = document.getElementById('loginForm');
    var registerForm = document.getElementById('registerForm');

    if (loginForm) {
        loginForm.onsubmit = async function (e) {
            e.preventDefault();
            var formData = new FormData(loginForm);
            var body = Object.fromEntries(formData.entries());

            try {
                var response = await api.post('/login', body);
                localStorage.setItem('token', response.token);
                localStorage.setItem('user', JSON.stringify(response.user));
                window.location.href = 'dashboard.html';
            } catch (err) {
                var errBox = document.getElementById('authError');
                if (errBox) {
                    errBox.textContent = err.message || 'Login failed';
                    errBox.style.display = 'block';
                }
            }
        };
    }

    if (registerForm) {
        registerForm.onsubmit = async function (e) {
            e.preventDefault();
            var formData = new FormData(registerForm);
            var body = Object.fromEntries(formData.entries());

            try {
                var response = await api.post('/register', body);
                localStorage.setItem('token', response.token);
                localStorage.setItem('user', JSON.stringify(response.user));
                window.location.href = 'dashboard.html';
            } catch (err) {
                var errBox = document.getElementById('authError');
                if (errBox) {
                    errBox.textContent = err.message || 'Registration failed';
                    errBox.style.display = 'block';
                }
            }
        };
    }
});
