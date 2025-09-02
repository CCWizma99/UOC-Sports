document.addEventListener('DOMContentLoaded', function () {
    const email = document.getElementById('email-inp');
    const password = document.getElementById('password-inp');
    const submitBtn = document.getElementById('submit-btn');

    const showError = (input, message) => {
        const errorDiv = input.nextElementSibling;
        errorDiv.innerHTML = message;
        errorDiv.style.display = 'block';
    };

    const hideError = (input) => {
        const errorDiv = input.nextElementSibling;
        errorDiv.style.display = 'none';
    };

    const validateEmail = (emailStr) => {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailStr);
    };

    const validateField = (input) => {
        const id = input.id;
        const val = input.value.trim();

        switch (id) {
            case 'email-inp':
                if (!validateEmail(val)) {
                    showError(input, 'Please enter a valid email!');
                } else {
                    hideError(input);
                }
                break;

            case 'password-inp':
                if (val === '') {
                    showError(input, 'Password cannot be empty!');
                } else {
                    hideError(input);
                }
                break;
        }
    };

    [email, password].forEach((input) => {
        input.addEventListener('input', () => validateField(input));
    });

    submitBtn.addEventListener('click', function (e) {
        e.preventDefault();
        let valid = true;

        [email, password].forEach((input) => {
            validateField(input);
            if (input.nextElementSibling.style.display === 'block') valid = false;
        });

        if (valid) {
            document.querySelector('form').submit();
        }
    });
});
