// admin registration
function checkAdmin() {
    var username = document.getElementById("username").value;
    var password = document.getElementById("password").value;

    // Validate username (letters only, no numbers)
    var letters = /^[a-zA-Z]+$/;
    if (!letters.test(username)) {
        alert("Username must contain letters only, no numbers allowed.");
        return false; 
    }

    // Validate password (at least 4 characters)
    if (password.length < 4) {
        alert("Password must be at least 4 characters long.");
        return false; 
    }

    return true;
}
// user payment
    function validateCreditCard() {
        var creditCardNumber = document.getElementById("creditCardNumber").value;
        if (creditCardNumber.length !== 16 || isNaN(creditCardNumber)) {
            alert("Invalid credit card number. Please enter a valid 16-digit numeric number.");
            return false; 
        }
        return true; 
    }


