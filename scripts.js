// JavaScript for form validation and error handling
function validateForm() {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const errorDiv = document.getElementById('error');

    // Clear any previous error messages
    errorDiv.innerHTML = "";

    // Example static validation (Replace with server-side validation)
    if (username === "admin" && password === "admin123") {
        alert("Login successful!");
        return true;  // Allow form submission
    } else {
        errorDiv.innerHTML = "Invalid username or password. Please try again.";
        errorDiv.style.color = "red";
        return false;  // Prevent form submission
    }
}
function saveFormData() {
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const college_name = document.getElementById('college_name').value;
    const degree = document.getElementById('degree').value;
    const department = document.getElementById('department').value;

    // Save data to cookies
    document.cookie = "user_name=" + name + "; path=/; max-age=" + (30 * 24 * 60 * 60);
    document.cookie = "user_email=" + email + "; path=/; max-age=" + (30 * 24 * 60 * 60);
    document.cookie = "user_college_name=" + college_name + "; path=/; max-age=" + (30 * 24 * 60 * 60);
    document.cookie = "user_degree=" + degree + "; path=/; max-age=" + (30 * 24 * 60 * 60);
    document.cookie = "user_department=" + department + "; path=/; max-age=" + (30 * 24 * 60 * 60);
}
