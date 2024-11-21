<?php
include 'config.php';
session_start();

// Initialize variables to avoid undefined errors
$logged_in_user = null;
$logged_in_admin = null;

// Check session for logged-in user or admin
if (isset($_SESSION['user_email'])) {
    $email = $_SESSION['user_email'];
    $sql = "SELECT * FROM user WHERE mail = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $logged_in_user = $result->fetch_assoc();
    }
} elseif (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];
    $sql = "SELECT * FROM admin WHERE adminId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $logged_in_admin = $result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bangladesh National Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Styling for the search container */
        .search-container {
            position: relative;
            width: 100%;
            max-width: 500px;
            margin: auto;
        }

        .search-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-height: 300px;
            overflow-y: auto;
            z-index: 10;
            display: none;
            color: black
        }

        .search-results.show {
            display: block;
        }

        .search-results p {
            padding: 10px;
            margin: 0;
            cursor: pointer;
            font-size: 14px;
        }

        .search-results p:hover {
            background-color: #f0f0f0;
        }

        @media screen and (max-width: 768px) {
            .search-container {
                width: 90%;
            }
        }
    </style>
</head>
<body>
<header>
    <div class="navbar">
       
        <div class="nav-search">
            <div class="search-container">
                <input type="text" id="search-bar" class="search-input" placeholder="Search services">
                <div id="search-results" class="search-results"></div>
            </div>
        </div>
    </div>
</header>



<script>
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
</script>
</body>
</html>
