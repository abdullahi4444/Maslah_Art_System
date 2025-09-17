// Password toggle functionality
const stdPasswordToggle = document.getElementById("std-password-toggle");
const stdPassword = document.getElementById("std-password");
const stdPasswordIcon = stdPasswordToggle.querySelector("i");

stdPasswordToggle.addEventListener("click", function () {
  if (stdPassword.type === "password") {
    stdPassword.type = "text";
    stdPasswordIcon.classList.remove("fa-eye-slash");
    stdPasswordIcon.classList.add("fa-eye");
  } else {
    stdPassword.type = "password";
    stdPasswordIcon.classList.remove("fa-eye");
    stdPasswordIcon.classList.add("fa-eye-slash");
  }
});

// Form submission
const stdSignupForm = document.getElementById("std-signup-form");
stdSignupForm.addEventListener("submit", function (e) {
  e.preventDefault();

  const stdEmail = stdSignupForm.querySelector('input[type="email"]').value;
  const stdUsername = stdSignupForm.querySelector('input[type="text"]').value;
  const stdPasswordValue = stdPassword.value;

  // Here you can add your form submission logic
  console.log("Sign up attempt:", {
    email: stdEmail,
    username: stdUsername,
    password: stdPasswordValue,
  });

  // For demo purposes, show an alert
  alert("Sign up form submitted! Check console for details.");
});
