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
$usertypes_id = $user['usertypes_id'] ?? null;  // Fetch usertypes_id


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
                <input type="text" id="search-bar" placeholder="Search projects...">
                <div class="project-header">
                    <button id="new-project-btn">+ New Project</button>
                </div>
            </div>

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
                <li class="theme-option" data-theme="monochrome-theme">Monochrome</li>
                <li class="theme-option" data-theme="forest-theme">Forest</li>
            </ul>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
document.addEventListener('DOMContentLoaded', function() {
    const newProjectButton = document.getElementById('new-project-btn');
    const projectFormContainer = document.getElementById('project-form');
    const projectCancelButton = document.getElementById('project-form-cancel');
    const addTaskButtons = document.querySelectorAll('.add-task-btn');
    const updateButtons = document.querySelectorAll('.update-btn');
    const updateFormContainer = document.getElementById('update-form');
    const updateCancelButton = document.getElementById('update-form-cancel');
    const deleteButtons = document.querySelectorAll('.delete-btn');

    // Show the New Project Form
    newProjectButton.addEventListener('click', function() {
        projectFormContainer.style.display = 'block';
    });

    // Hide the New Project Form
    projectCancelButton.addEventListener('click', function() {
        projectFormContainer.style.display = 'none';
    });

    // Add event listeners to all "Add Task" buttons
    addTaskButtons.forEach(button => {
        button.addEventListener('click', function() {
            const projectId = button.getAttribute('data-project-id');
            // Navigate to the Kanban page with the specific project ID
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

    // Cancel the Update Form
    updateCancelButton.addEventListener('click', function() {
        updateFormContainer.style.display = 'none';
    });

    // Theme Toggle functionality
    const themeToggleButton = document.getElementById('theme-toggle-button');
    const themeDropdownContainer = document.getElementById('theme-dropdown-container');
    const themeOptions = document.querySelectorAll('.theme-option');

    if (themeToggleButton && themeDropdownContainer) {
        themeToggleButton.addEventListener('click', function(event) {
            event.stopPropagation();
            themeDropdownContainer.classList.toggle('visible');
            themeDropdownContainer.classList.toggle('hidden');
        });

        themeOptions.forEach(option => {
            option.addEventListener('click', function() {
                const theme = option.getAttribute('data-theme');
                document.body.className = theme;
                themeDropdownContainer.classList.add('hidden');
                themeDropdownContainer.classList.remove('visible');
            });
        });

        document.addEventListener('click', function(event) {
            if (!themeToggleButton.contains(event.target) && !themeDropdownContainer.contains(event.target)) {
                themeDropdownContainer.classList.add('hidden');
                themeDropdownContainer.classList.remove('visible');
            }
        });
    }

    
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
});







    </script>
</body>
</html>
