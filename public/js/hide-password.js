document.querySelectorAll('.toggle-password').forEach((eyeIcon) => {
    eyeIcon.addEventListener('click', () => {
      const inputId = eyeIcon.getAttribute('data-target');
      const input = document.getElementById(inputId);
  
      if (input.type === 'password') {
        input.type = 'text';
        eyeIcon.innerHTML = '<i class="fa-solid fa-eye"></i>';
      } else {
        input.type = 'password';
        eyeIcon.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';
      }
    });
  });
  