document.addEventListener('DOMContentLoaded', function () {
  const fname = document.getElementById('fname-inp');
  const lname = document.getElementById('lname-inp');
  const email = document.getElementById('email-inp');
  const password = document.getElementById('password-inp');
  const confirmPassword = document.getElementById('confirm-password-inp');
  const submitBtn = document.getElementById('submit-btn');

  // Optional (for student sign up)
  const studentId = document.getElementById('student-id-inp');
  const faculty = document.getElementById('faculty-inp');

  const showError = (input, message) => {
    const errorDiv = input.parentElement.querySelector('.error');
    if (errorDiv) {
      errorDiv.innerHTML = message;
      errorDiv.style.display = 'block';
    }
  };

  const hideError = (input) => {
    const errorDiv = input.parentElement.querySelector('.error');
    if (errorDiv) {
      errorDiv.style.display = 'none';
    }
  };

  const validateEmail = (emailStr) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailStr);

  const validatePassword = (pass) =>
    /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&]).{8,}$/.test(pass);

  const validateField = (input) => {
    if (!input) return; // Skip if field doesn't exist
    const id = input.id;
    const val = input.value.trim();

    switch (id) {
      case 'fname-inp':
      case 'lname-inp':
        if (val === '') showError(input, 'This field cannot be empty!');
        else hideError(input);
        break;

      case 'email-inp':
        if (!validateEmail(val)) showError(input, 'Please enter a valid email!');
        else hideError(input);
        break;

      case 'password-inp':
        if (!validatePassword(val))
          showError(
            input,
            'Password must be at least 8 characters and include <br/> a number and a special character!'
          );
        else hideError(input);
        break;

      case 'confirm-password-inp':
        if (val !== password.value.trim())
          showError(input, "Passwords don't match!");
        else hideError(input);
        break;

      case 'student-id-inp':
        if (val === '' || !/^[A-Za-z0-9_-]+$/.test(val))
          showError(input, 'Invalid Student ID!');
        else hideError(input);
        break;

      case 'faculty-inp':
        if (val === '' || val === 'none')
          showError(input, 'Please select your Faculty!');
        else hideError(input);
        break;
    }
  };

  // Add input listeners to all available fields
  [fname, lname, email, password, confirmPassword, studentId, faculty].forEach(
    (input) => {
      if (input) {
        input.addEventListener('input', () => validateField(input));
        if (input.tagName === 'SELECT') {
          input.addEventListener('change', () => validateField(input));
        }
      }
    }
  );

  submitBtn.addEventListener('click', function (e) {
    e.preventDefault();
    let valid = true;

    const fieldsToCheck = [
      fname,
      lname,
      email,
      password,
      confirmPassword,
      studentId,
      faculty,
    ].filter(Boolean); // remove nulls for normal signup

    fieldsToCheck.forEach((input) => {
      validateField(input);
      const errorDiv = input.parentElement.querySelector('.error');
      if (errorDiv && errorDiv.style.display === 'block') valid = false;
    });

    if (valid) {
      document.querySelector('form').submit();
    }
  });
});
