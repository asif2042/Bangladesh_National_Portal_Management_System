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
  const searchBar = document.getElementById('search-bar');
    const searchResults = document.getElementById('search-results');

    searchBar.addEventListener('input', async (e) => {
        const query = e.target.value.trim();

        if (query.length > 0) {
            try {
                const response = await fetch(`search.php?query=${encodeURIComponent(query)}`);
                const results = await response.json();

                searchResults.innerHTML = ''; // Clear previous results

                if (results.length > 0) {
                    results.forEach(item => {
                        const resultItem = document.createElement('p');
                        resultItem.textContent = item;
                        resultItem.addEventListener('click', () => {
                            searchBar.value = item;
                            searchResults.innerHTML = '';
                            searchResults.classList.remove('show');
                        });
                        searchResults.appendChild(resultItem);
                    });
                } else {
                    searchResults.innerHTML = '<p>No results found</p>';
                }

                searchResults.classList.add('show');
            } catch (error) {
                console.error('Error fetching search results:', error);
            }
        } else {
            searchResults.innerHTML = '';
            searchResults.classList.remove('show');
        }
    });

    document.addEventListener('click', (e) => {
        if (!searchBar.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.innerHTML = '';
            searchResults.classList.remove('show');
        }
    });





    // admin - user management panel



    $(document).ready(function () {
        $('#userTable').DataTable();
    });
    
    function openModal(mode) {
        document.getElementById('userModal').style.display = 'block';
        if (mode === 'add') {
            document.getElementById('modal-title').innerText = 'Add New User';
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
        }
    }
    
    function closeModal() {
        document.getElementById('userModal').style.display = 'none';
    }
    
    function editRecord(record) {
    // Ensure 'record' contains the necessary data
    console.log(record); // Debug to check if 'review' is being passed correctly
    
    // Set values in the modal form
    document.getElementById('userId').value = record.applicant_id || '';
    document.getElementById('name').value = record.name || '';
    document.getElementById('mail').value = record.mail || '';
    document.getElementById('phone').value = record.phone || '';
    document.getElementById('feedback_comments').value = record.review || ''; // Populate the feedback comments
    document.getElementById('application_status').value = record.application_status || 'Pending';

    // Open the modal
    openModal('edit');
}


   


    function handleResponse(response) {
        if (response.success) {
            alert(response.message);
            location.reload(); // Reload the page to reflect changes
        } else {
            alert(response.message);
        }
    }
    
    function sendRequest(url, formData) {
        fetch(url, {
            method: 'POST',
            body: formData,
        })
            .then(response => response.json())
            .then(data => handleResponse(data))
            .catch(error => console.error('Error:', error));
    }


    document.getElementById("userForm").addEventListener("submit", function (e) {
    e.preventDefault(); // Prevent default form submission

    // Get form data
    const applicantId = document.getElementById("userId").value;
    const applicationStatus = document.getElementById("application_status").value;

    // Validate data
    if (!applicantId || !applicationStatus) {
        alert("Applicant ID and Application Status are required.");
        return;
    }

    // Send data to the server
    const formData = new URLSearchParams();
    formData.append("applicant_id", applicantId);
    formData.append("application_status", applicationStatus);

    fetch("update_status.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
        },
        body: formData.toString(),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                alert(data.success); // Success message
                location.reload();   // Reload the page to reflect changes
            } else {
                alert(data.error);   // Error message
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            alert("An unexpected error occurred.");
        });
});



