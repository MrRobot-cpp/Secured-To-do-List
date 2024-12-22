<?php
// Start the session and include necessary files.
session_start();
require_once '../Model/Database.php';
require_once '../Controller/ProjectController.php';
require_once '../Controller/UserController.php';
require_once '../Controller/TaskController.php';

// Ensure the user is logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Create a new database connection and controllers.
$db = (new Database())->getConnection();
$userController = new UserController($db);
$projectController = new ProjectController($db);
$taskController = new TaskController($db);
$usertypes_id = $user['usertypes_id'] ?? null;


// Retrieve user data.
$user = $userController->getUserById($_SESSION['user_id']);
$name = $user['name'] ?? 'User';

// Fetch all projects for the logged-in user.
$projects = $projectController->getProjectsByUserId($_SESSION['user_id']);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Dashboard</title>
    <link rel="stylesheet" href="../public/assets/css/kanban.css">
</head>
<body>
    <!-- Main Content -->
    <div class="container">
        <aside class="navbar">
            <h2>Welcome, <?php echo htmlspecialchars($name); ?></h2>
            <ul>
                <li><a href="kanban.php" class="active">Home</a></li>
            </ul>
        </aside>

        <main class="project-board">
            <div class="search-filter">
            <form id="searchForm" method="POST">
    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
    <input type="text" id="search-bar" name="search_term" placeholder="Search projects..." required>
    <button class="search-btn"type="submit" name="search_project">Search</button>
    <button class="cancel-search-btn" type="button" id="cancel-search-btn">Cancel</button>
</form>

                <div class="project-header">
                    <button id="new-project-btn">+ New Project</button>
                </div>
            </div>
            <div id="searchResults"></div> 
            <div class="project-columns">
                <?php 
                foreach ($projects as $project) {
                    echo "<div class='project-column' data-id='" . $project['id'] . "'>";
                    echo "<h3>" . htmlspecialchars($project['name']) . "</h3>";
                    echo "<button class='add-task-btn' data-project-id='" . $project['id'] . "'>Add Task</button>";
                    echo "<button class='update-btn' data-project-id='" . $project['id'] . "'>Update</button>";
                    echo "<button class='delete-btn' data-project-id='" . $project['id'] . "'>Delete</button>";
                    
                    echo "</div>";
                }
                ?>
            </div>
        </main>
    </div>

    <!-- New Project Form -->
    <div id="project-form" style="display: none;">
        <form action="../Controller/ProjectController.php" method="POST">
            <input type="text" name="name" placeholder="Project Name" required>
            <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
            <button type="submit" name="add_project">Add Project</button>
            <button type="button" id="project-form-cancel">Cancel</button>
        </form>
    </div>

    <!-- Update Project Form -->
    <div id="update-form" style="display: none;">
        <form action="../Controller/ProjectController.php" method="POST">
            <input type="hidden" name="project_id" id="update_project_id" value="">
            <input type="text" name="name" id="update_project_name" placeholder="Project Name" required>
            <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
            <button type="submit" name="update_project">Update Project</button>
            <button type="button" id="update-form-cancel">Cancel</button>
        </form>
    </div>

    <!-- Theme Toggle -->
    <div id="theme-toggle-container">
        <img id="theme-toggle-button" src="../public/assets/img/themeLogo.png" alt="Theme Toggle">
        <div class="theme-selector hidden" id="theme-dropdown-container">
            <ul>
                <li class="theme-option" data-theme="">Default</li>
                <li class="theme-option" data-theme="monochrome-theme">Blue</li>
                <li class="theme-option" data-theme="forest-theme">Green</li>
            </ul>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const themeToggleButton = document.getElementById('theme-toggle-button');
        const themeDropdownContainer = document.getElementById('theme-dropdown-container');
        const themeOptions = document.querySelectorAll('.theme-option');
        
        // Apply the saved theme from localStorage if it exists
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            document.body.className = savedTheme; // Apply saved theme on page load
        }

        themeToggleButton.addEventListener('click', function(event) {
            event.stopPropagation();
            themeDropdownContainer.classList.toggle('visible');
            themeDropdownContainer.classList.toggle('hidden');
        });

        themeOptions.forEach(option => {
            option.addEventListener('click', function() {
                const theme = option.getAttribute('data-theme');
                document.body.className = theme;  // Apply the theme to body

                // Save the selected theme to localStorage
                localStorage.setItem('theme', theme);

                themeDropdownContainer.classList.add('hidden');
                themeDropdownContainer.classList.remove('visible');
            });
        });

        // Close theme dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!themeToggleButton.contains(event.target) && !themeDropdownContainer.contains(event.target)) {
                themeDropdownContainer.classList.add('hidden');
                themeDropdownContainer.classList.remove('visible');
            }
        });

        const newProjectButton = document.getElementById('new-project-btn');
        const projectFormContainer = document.getElementById('project-form');
        const projectCancelButton = document.getElementById('project-form-cancel');
        const updateFormContainer = document.getElementById('update-form');
        const updateCancelButton = document.getElementById('update-form-cancel');

        // Show the New Project Form
        newProjectButton.addEventListener('click', function() {
            projectFormContainer.style.display = 'block';
        });

        // Hide the New Project Form
        projectCancelButton.addEventListener('click', function() {
            projectFormContainer.style.display = 'none';
        });

        // Handle Search Form Submission
        document.getElementById('searchForm').addEventListener('submit', function(event) {
            event.preventDefault();
            
            const searchTerm = document.getElementById('search-bar').value;
            const userId = document.querySelector('[name="user_id"]').value;
            
            const formData = new FormData();
            formData.append('user_id', userId);
            formData.append('search_term', searchTerm);
            formData.append('search_project', true);
            
            fetch('../Controller/ProjectController.php', {  
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const searchResultsContainer = document.getElementById('searchResults');
                searchResultsContainer.style.display = 'block';
                if (data.error) {
                    searchResultsContainer.innerHTML = `<p>${data.error}</p>`;
                } else {
                    let resultsHtml = '';
                    data.forEach(project => {
                        resultsHtml += `
                            <div class="project-column" data-id="${project.id}">
                                <h3>${project.name}</h3>
                                <button class="add-task-btn" data-project-id="${project.id}">Add Task</button>
                                <button class="update-btn" data-project-id="${project.id}">Update</button>
                                <button class="delete-btn" data-project-id="${project.id}">Delete</button>
                            </div>
                        `;
                    });
                    searchResultsContainer.innerHTML = resultsHtml;
                    bindButtons();
                }
            })
            .catch(error => {
                document.getElementById('searchResults').innerHTML = `<p>Error occurred while searching. Please try again.</p>`;
                console.error(error);
            });
        });

        document.getElementById('cancel-search-btn').addEventListener('click', function() {
            document.getElementById('search-bar').value = '';
            document.getElementById('searchResults').style.display = 'none';
        });

        function bindButtons() {
            const addTaskButtons = document.querySelectorAll('.add-task-btn');
            const updateButtons = document.querySelectorAll('.update-btn');
            const deleteButtons = document.querySelectorAll('.delete-btn');

            addTaskButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const projectId = button.getAttribute('data-project-id');
                    window.location.href = `../view/kanban.php?project_id=${projectId}`;
                });
            });

            // Show the Update Project Form
            updateButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const projectId = button.getAttribute('data-project-id');
                    const projectName = button.closest('.project-column').querySelector('h3').innerText;
                    document.getElementById('update_project_id').value = projectId;
                    document.getElementById('update_project_name').value = projectName;
                    updateFormContainer.style.display = 'block';
                });
            });

            // Delete Project
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const projectId = button.getAttribute('data-project-id');
                    const confirmation = confirm("Are you sure you want to delete this project?");
                    
                    if (confirmation) {
                        fetch('../Controller/ProjectController.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `delete_project=true&project_id=${projectId}`
                        })
                        .then(response => response.text())
                        .then(data => {
                            if (data === 'success') {
                                alert("Project deleted successfully!");
                                const projectColumn = button.closest('.project-column');
                                if (projectColumn) {
                                    projectColumn.remove();
                                }
                            } else {
                                alert("Failed to delete project.");
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert("An error occurred. Please try again.");
                        });
                    }
                });
            });
        }
        bindButtons();
    });
    </script>
</body>
</html>
