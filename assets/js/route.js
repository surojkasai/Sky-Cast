// Track login state (initially false)
let isLoggedIn = false;

// Add event listener for login and signup button clicks
document.addEventListener('click', function(event) {
    // If not logged in, show a popup when clicking on anywhere except login or signup buttons
    if (!isLoggedIn) {
        if (event.target.id !== 'loginBtn' && event.target.id !== 'signupBtn') {
            showPopup("You need to be logged in to view this content.");
        } else if (event.target.id === 'loginBtn') {
            window.location.href = 'assets/uservalidation/login.php'; // Redirect to login page
        } else if (event.target.id === 'signupBtn') {
            window.location.href = 'assets/uservalidation/signup.php'; // Redirect to signup page
        }
    } else {
        // If the user is logged in and clicks on blurred area, you can handle it here (optional)
        if (event.target.matches('.blurred-area')) {
            showPopup("You are already logged in. Enjoy!");
        }
    }
});

// Function to check if the user is logged in
function checkSession() {
    fetch('assets/uservalidation/check-session.php') // Path to check session file
        .then(response => response.json())
        .then(data => {
            if (data.loggedIn) {
                // User is logged in, set isLoggedIn flag to true
                isLoggedIn = true;
                removeBlur(); // Remove blur effects from specific elements
            } else {
                // User is not logged in, apply the blur
                applyBlur();
                isLoggedIn = false;
            }
        })
        .catch(error => console.error('Error checking session:', error));
}

// Show popup alert
// function showPopup(message) {
//     const popup = document.createElement('div');
//     popup.className = 'popup'; // Add a class for styling
//     popup.innerText = message;

//     // Append the popup to the body
//     document.body.appendChild(popup);

//     // Remove the popup after a few seconds
//     setTimeout(() => {
//         popup.remove();
//     }, 5000); // Popup will disappear after 3 seconds
// }

// Show popup alert
function showPopup(message) {
    // Create the overlay
    const overlay = document.createElement('div');
    overlay.className = 'popup-overlay'; // Add a class for styling the overlay

    // Create the popup container
    const popup = document.createElement('div');
    popup.className = 'popup-container'; // Add a class for styling the popup

    // Create the close button
    const closeBtn = document.createElement('span');
    closeBtn.className = 'popup-close';
    closeBtn.innerHTML = '&times;'; // Close icon (×)

    // Close the popup when the close button or overlay is clicked
    closeBtn.onclick = function() {
        document.body.removeChild(overlay);
    };
    overlay.onclick = function(event) {
        if (event.target === overlay) {
            document.body.removeChild(overlay);
        }
    };

    // Create the popup message content
    const content = document.createElement('div');
    content.className = 'popup-content';
    content.innerHTML = `
        <p>To view full details, please log in or sign up</p>
        <div>
            <span>Already have an account? <a href="assets/uservalidation/login.php">Login</a></span><br>
            <span>Create a new account? <a href="assets/uservalidation/signup.php">Signup</a></span>
        </div>
    `;

    // Append the close button and content to the popup container
    popup.appendChild(closeBtn);
    popup.appendChild(content);

    // Append the popup to the overlay
    overlay.appendChild(popup);

    // Append the overlay to the body
    document.body.appendChild(overlay);
}


// Apply blur effect to necessary elements except for specific headers
function applyBlur() {
    const dayForecastElement = document.querySelector('.day-forecast'); // Select 5-day forecast
    const hourlyForecast = document.querySelector('.hourly-forecast'); // Select hourly forecast
    const weatherRightContent = document.querySelectorAll('.weather-right > *:not(h2)'); // Select all elements in weather-right except <h2>

    // Apply blur to day-forecast section
    if (dayForecastElement) {
        dayForecastElement.style.filter = 'blur(8px)';
        dayForecastElement.style.pointerEvents = 'none'; // Disable interaction
        dayForecastElement.classList.add('blurred-area'); // Add a class for event listener
    }

    // Apply blur to hourly forecast
    if (hourlyForecast) {
        hourlyForecast.style.filter = 'blur(8px)';
        hourlyForecast.style.pointerEvents = 'none'; // Disable interaction
        hourlyForecast.classList.add('blurred-area'); // Add a class for event listener
    }

    // Apply blur to all children of .weather-right except h2
    if (weatherRightContent) {
        weatherRightContent.forEach(content => {
            content.style.filter = 'blur(8px)';
            content.style.pointerEvents = 'none'; // Disable interaction
            content.classList.add('blurred-area'); // Add a class for event listener
        });
    }
}

// Remove blur from all elements
function removeBlur() {
    const blurredElements = document.querySelectorAll('.weather-left, .weather-right > *, .day-forecast, .hourly-forecast');

    blurredElements.forEach(element => {
        element.style.filter = 'none'; // Remove blur
        element.style.pointerEvents = 'auto'; // Re-enable interaction
    });
}

// Apply the blur only if session is invalid
checkSession();

// Check session on page load
window.onload = function () {
    checkSession(); // Verify login state on page load
};
