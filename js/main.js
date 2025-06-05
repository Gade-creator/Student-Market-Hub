function validateRegistrationForm(formId) {
    const form = document.getElementById(formId);
    const username = form.querySelector("[name='username']").value.trim();
    const password = form.querySelector("[name='password']").value;
    const confirmPassword = form.querySelector("[name='confirm_password']").value;

    if (!username || !password || !confirmPassword) {
        alert("All fields are required.");
        return false;
    }

    if (password.length < 6) {
        alert("Password must be at least 6 characters long.");
        return false;
    }

    if (password !== confirmPassword) {
        alert("Passwords do not match.");
        return false;
    }

    return true;
}


// Get the 'See More' button and hidden product cards
const seeMoreBtn = document.getElementById('see-more-btn');
const hiddenProducts = document.querySelectorAll('.hidden');

// Function to toggle visibility of hidden products
function toggleProducts() {
    hiddenProducts.forEach(product => {
        product.classList.toggle('hidden'); // Show or hide product
    });

    // Change button text after click
    if (seeMoreBtn.innerText === 'See More') {
        seeMoreBtn.innerText = 'See Less'; // Update to "See Less"
    } else {
        seeMoreBtn.innerText = 'See More'; // Update back to "See More"
    }
}

// Add event listener to button
seeMoreBtn.addEventListener('click', toggleProducts);
