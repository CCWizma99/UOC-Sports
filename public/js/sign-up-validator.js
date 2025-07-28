document.addEventListener('DOMContentLoaded', function () {
    const fname = document.getElementById('fname-inp');
    const lname = document.getElementById('lname-inp');
    const email = document.getElementById('email-inp');
    const password = document.getElementById('password-inp');
    const confirmPassword = document.getElementById('confirm-password-inp');
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
  
    const validatePassword = (pass) => {
      return /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&]).{8,}$/.test(pass);
    };
  
    const validateField = (input) => {
      const id = input.id;
      const val = input.value.trim();
  
      switch (id) {
        case 'fname-inp':
        case 'lname-inp':
          if (val === '') {
            showError(input, 'This field cannot be empty!');
          } else {
            hideError(input);
          }
          break;
  
        case 'email-inp':
          if (!validateEmail(val)) {
            showError(input, 'Please enter a valid email!');
          } else {
            hideError(input);
          }
          break;
  
        case 'password-inp':
          if (!validatePassword(val)) {
            showError(input, 'Password must be at least 8 characters and include <br/> a number and a special character!');
          } else {
            hideError(input);
          }
          break;
  
        case 'confirm-password-inp':
          if (val !== password.value.trim()) {
            showError(input, "Passwords don't match!");
          } else {
            hideError(input);
          }
          break;
      }
    };
  
    [fname, lname, email, password, confirmPassword].forEach((input) => {
      input.addEventListener('input', () => validateField(input));
    });
  
    submitBtn.addEventListener('click', function (e) {
      e.preventDefault();
      let valid = true;
  
      [fname, lname, email, password, confirmPassword].forEach((input) => {
        validateField(input);
        if (input.nextElementSibling.style.display === 'block') valid = false;
      });
  
      if (valid) {
        alert('Form submitted successfully!');
        // document.querySelector('form').submit();
      }
    });
  });
  