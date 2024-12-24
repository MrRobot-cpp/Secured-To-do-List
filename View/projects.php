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
</form>

<div class="project-header">
                    <button id="new-project-btn">+ New Project</button>
                </div>
            </div>
            <div id="searchResults" style="display: none;"></div>
            <div class="project-columns" id="projectColumns">
                <?php 
                foreach ($projects as $project) {
                    echo "<div class='project-column' data-id='" . $project['id'] . "' data-name='" . htmlspecialchars($project['name']) . "'>";
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
    document.addEventListener('DOMContentLoaded', function () {
        const searchBar = document.getElementById('search-bar');
        const projectColumns = document.getElementById('projectColumns');
        const searchResults = document.getElementById('searchResults');

        searchBar.addEventListener('input', function () {
            const searchTerm = searchBar.value.toLowerCase();
            const projects = projectColumns.querySelectorAll('.project-column');

            let hasResults = false;

            projects.forEach(project => {
                const projectName = project.getAttribute('data-name').toLowerCase();

                if (projectName.includes(searchTerm)) {
                    project.style.display = 'block';
                    hasResults = true;
                } else {
                    project.style.display = 'none';
                }
            });

            searchResults.style.display = hasResults ? 'none' : 'block';
            searchResults.innerHTML = hasResults ? '' : '<p>No matching projects found.</p>';
        });
        
        //Add task button
        const addTaskButtons = document.querySelectorAll('.add-task-btn');
    addTaskButtons.forEach(button => {
        button.addEventListener('click', function() {
            const projectId = button.getAttribute('data-project-id');
            window.location.href = `../view/kanban.php?project_id=${projectId}`;
        });
    });

    // Handle Update Button click
    const updateButtons = document.querySelectorAll('.update-btn');
    updateButtons.forEach(button => {
        button.addEventListener('click', function() {
            const projectId = button.getAttribute('data-project-id');
            const projectName = button.closest('.project-column').querySelector('h3').textContent;
            document.getElementById('update_project_id').value = projectId;
            document.getElementById('update_project_name').value = projectName;
            updateFormContainer.style.display = 'block';
        });
    });
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

        const deleteButtons = document.querySelectorAll('.delete-btn');
        deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
         const projectId = button.getAttribute('data-project-id');
        
        // Send delete request to the server directly
        fetch('../Controller/ProjectController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `delete_project=true&project_id=${projectId}`
        })
        .then(response => response.text())
        .then(data => {
            // Check for success response
            if (data.trim() === 'success') {
                const projectColumn = button.closest('.project-column');
                if (projectColumn) {
                    projectColumn.remove(); // Remove the project from the UI
                }
            } else {
                console.error("Failed to delete project. Server response:", data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
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

        // hide the Update Project Form
        updateCancelButton.addEventListener('click', function() {
            updateFormContainer.style.display = 'none';
        });

    });
    </script>
</body>
</html>