// Client-side JavaScript (if needed)
// For now, your design is mostly static/CSS-driven.
// We can add interactivity later, like for the newsletter form.

document.addEventListener('DOMContentLoaded', function() {
    // Example: Handle newsletter subscription via AJAX
    const subscribeForm = document.querySelector('.content-section .flex'); // Target the form container
    if (subscribeForm) {
        const emailInput = subscribeForm.querySelector('input[type="email"]');
        const subscribeButton = subscribeForm.querySelector('button');
        const originalButtonText = subscribeButton.textContent;

        if (emailInput && subscribeButton) {
            subscribeButton.addEventListener('click', function(e) {
                e.preventDefault();
                const email = emailInput.value.trim();

                if (!email) {
                    alert('Please enter your email address.');
                    return;
                }

                // Disable button and show loading state
                subscribeButton.disabled = true;
                subscribeButton.textContent = '> SUBSCRIBING...';

                // Send request to server (assuming an endpoint exists)
                fetch('subscribe.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'email=' + encodeURIComponent(email)
                })
                .then(response => response.text())
                .then(data => {
                    if (data === 'success') {
                        alert('Thank you for subscribing!');
                        emailInput.value = ''; // Clear the input
                    } else {
                        alert('Subscription failed: ' + data);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                })
                .finally(() => {
                    // Re-enable button
                    subscribeButton.disabled = false;
                    subscribeButton.textContent = originalButtonText;
                });
            });
        }
    }
});
