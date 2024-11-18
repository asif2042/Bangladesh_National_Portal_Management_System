// Function to update date and time
function updateDateTime() {
    const now = new Date();
    
    // Format: Day-Month-Year Hour:Minute:Second (e.g., 01-04-2024 14:30:45)
    const formattedDateTime = now.toLocaleString('en-GB', {
        weekday: 'long', // Full weekday name (e.g., Monday)
        year: 'numeric',
        month: '2-digit',
      
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false // Use 24-hour time
    });

    // Update the text of the element with id 'datetime'
    document.getElementById('datetime').innerText = formattedDateTime;
}

// Call the function once to display initial date and time
updateDateTime();

// Update the date and time every second (1000 milliseconds)
setInterval(updateDateTime, 1000);



//action for panel-logo

let panelLogo = document.querySelector(".panel-logo-link");
panelLogo.addEventListener('click', ()=>{
    console.log("hey! I am from panel_logo and you done it.")
    window.open('https://a2i.gov.bd/');
});


//for sign in and sign up 
// JavaScript code to handle navigation click
document.addEventListener('DOMContentLoaded', () => {
    const navSignin = document.querySelector('.nav-signin');
    if (navSignin) {
        navSignin.addEventListener('click', () => {

            window.location.href = 'login.php'; // Redirects to signin.php
        });
    }
});

