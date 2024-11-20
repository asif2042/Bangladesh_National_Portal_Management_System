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



//side bar 

		const menu_toggle = document.querySelector('.menu-toggle');
		const sidebar = document.querySelector('.sidebar');

		menu_toggle.addEventListener('click', () => {
			menu_toggle.classList.toggle('is-active');
			sidebar.classList.toggle('is-active');
		});






  // application form 
  


  //for searching 
  document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-input');
    const searchResults = document.getElementById('search-results');

    searchInput.addEventListener('input', async (e) => {
        const query = e.target.value.trim();

        if (query === "") {
            searchResults.classList.remove('active');
            searchResults.innerHTML = '<p class="empty-message">Empty</p>';
            return;
        }

        try {
            const response = await fetch(`search_service.php?query=${query}`);
            const services = await response.json();

            if (services.length > 0) {
                const listItems = services.map(service => `<li>${service}</li>`).join('');
                searchResults.innerHTML = `<ul>${listItems}</ul>`;
            } else {
                searchResults.innerHTML = '<p class="empty-message">No matching services</p>';
            }

            searchResults.classList.add('active');
        } catch (error) {
            console.error('Error fetching search results:', error);
        }
    });

    // Hide results when clicking outside
    document.addEventListener('click', (event) => {
        if (!searchResults.contains(event.target) && event.target !== searchInput) {
            searchResults.classList.remove('active');
        }
    });
});
