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

  const debounce = (func, delay) => {
    let timeout;
    return (...args) => {
      clearTimeout(timeout);
      timeout = setTimeout(() => func.apply(this, args), delay);
    };
  };

  const checkDuplicateOnServer = async (input, type) => {
    const val = input.value.trim();
    if (val === '') return;

    try {
      const res = await fetch(`/uoc-sports/public/api/user/check-duplicate?type=${type}&value=${encodeURIComponent(val)}`);
      const data = await res.json();

      if (data.status === 'success' && data.exists) {
        showError(input, data.message);
        input.setAttribute('data-duplicate', 'true');
      } else {
        input.removeAttribute('data-duplicate');
        // Re-validate field to clear duplicate error if a basic error exists
        // but if it was just the duplicate error, hide it
        const errorDiv = input.parentElement.querySelector('.error');
        if (errorDiv && errorDiv.innerText.includes('already registered')) {
            hideError(input);
        }
      }
    } catch (err) {
      console.error('Error checking duplicate:', err);
    }
  };

  const debouncedCheck = debounce(checkDuplicateOnServer, 500);

  const validateField = (input) => {
    if (!input) return; // Skip if field doesn't exist
    const id = input.id;
    const val = input.value.trim();

    // Clear duplicate flag when user modifies input
    if (id === 'email-inp' || id === 'student-id-inp') {
        input.removeAttribute('data-duplicate');
    }

    switch (id) {
      case 'fname-inp':
      case 'lname-inp':
        if (val === '') showError(input, 'This field cannot be empty!');
        else hideError(input);
        break;

      case 'email-inp':
        if (val === '') {
            showError(input, 'Email cannot be empty!');
        } else if (!validateEmail(val)) {
            showError(input, 'Please enter a valid email!');
        } else {
            hideError(input);
            // Don't check for duplicates if it's a student sign up (email is verified/readonly)
            if (!input.readOnly) {
                debouncedCheck(input, 'email');
            }
        }
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
        const selectedFacultyId = faculty ? faculty.value : 'none';
        const patterns = {
          '1': /^\d{4}\s*\/\s*(CS|IS)\s*\/\s*\d{3,4}$/i, // UCSC
          '2': /^\d{4}\s*\/\s*S\s*\/\s*\d{4}$/i, // Science
          '3': /^\d{4}\s*\/\s*A\s*\/\s*\d{4}$/i, // Arts
          '4': /^\d{4}\s*\/\s*E\s*\/\s*\d{4}$/i, // Education
          '5': /^\d{4}\s*\/\s*IM\s*\/\s*\d{4}$/i, // Indigenous Medicine
          '6': /^\d{4}\s*\/\s*L\s*\/\s*\d{4}$/i, // Law
          '7': /^\d{4}\s*\/\s*BA\s*\/\s*\d{4}$/i, // Management & Finance
          '8': /^\d{4}\s*\/\s*M\s*\/\s*\d{4}$/i, // Medicine
          '9': /^\d{4}\s*\/\s*N\s*\/\s*\d{4}$/i, // Nursing
          '10': /^\d{4}\s*\/\s*T\s*\/\s*\d{4}$/i, // Technology
        };

        if (val === '') {
          showError(input, 'Student ID cannot be empty!');
        } else if (selectedFacultyId === 'none') {
          showError(input, 'Please select a faculty first!');
        } else if (
          patterns[selectedFacultyId] &&
          !patterns[selectedFacultyId].test(val)
        ) {
          let formatHint = 'Invalid format for selected faculty!';
          if (selectedFacultyId === '1')
            formatHint = 'Format: 202X / CS / XXX or 202X / IS / XXX';
          else if (selectedFacultyId === '2')
            formatHint = 'Format: 202X / S / XXXX';
          else if (selectedFacultyId === '3')
            formatHint = 'Format: 202X / A / XXXX';
          else if (selectedFacultyId === '7')
            formatHint = 'Format: 202X / BA / XXXX';
          else
             formatHint = 'Invalid format! (e.g., 2023 / [F] / 1234)';

          showError(input, formatHint);
        } else {
          hideError(input);
          debouncedCheck(input, 'student_id');
        }
        break;

      case 'faculty-inp':
        if (val === '' || val === 'none') {
          showError(input, 'Please select your Faculty!');
          if (studentId) {
            studentId.disabled = true;
            studentId.value = '';
            studentId.placeholder = 'Select Faculty first';
            hideError(studentId);
          }
        } else {
          hideError(input);
          if (studentId) {
            studentId.disabled = false;
            // Set placeholder hint based on faculty
            const hints = {
              '1': 'e.g., 2023 / CS / 123',
              '2': 'e.g., 2023 / S / 1234',
              '3': 'e.g., 2023 / A / 1234',
              '7': 'e.g., 2023 / BA / 1234',
              '6': 'e.g., 2023 / L / 1234',
              '8': 'e.g., 2023 / M / 1234',
              '9': 'e.g., 2023 / N / 1234',
            };
            studentId.placeholder =
              hints[val] || 'e.g., 2023 / [F] / 1234';

            // When faculty changes, re-validate student ID if it has value
            if (studentId.value.trim() !== '') {
              validateField(studentId);
            }
          }
        }
        break;
    }
  };

  // Initialize Student ID state
  if (studentId) {
    if (!faculty || faculty.value === 'none') {
      studentId.disabled = true;
      studentId.placeholder = 'Select Faculty first';
    }

    // Show error if user tries to click Student ID before selecting faculty
    studentId.parentElement.addEventListener('click', function () {
      if (!faculty || faculty.value === 'none') {
        showError(faculty, 'Please select your faculty first!');
        faculty.focus();
      }
    });
  }

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

  const handleSubmit = function (e) {
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
      
      // Also check the specific duplicate attribute
      if (input.getAttribute('data-duplicate') === 'true') valid = false;
    });

    if (valid) {
      document.querySelector('form').submit();
    } else {
        // If there's a duplicate error that hasn't appeared yet (e.g. latency), 
        // we might submit incorrectly. But typically the attribute check catches it.
        console.warn('Form validation failed.');
    }
  };

  submitBtn.addEventListener('click', handleSubmit);

  // Allow Enter key to submit the form
  document.querySelector('form').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
      handleSubmit(e);
    }
  });
});
