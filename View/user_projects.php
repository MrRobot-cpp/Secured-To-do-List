<?php
session_start();
require_once '../Model/Database.php';
require_once '../Controller/TaskController.php';
require_once '../Controller/UserController.php';
require_once '../Controller/ProjectController.php';

$db = Database::getInstance()->getConnection();
$projectController = new ProjectController($db);
$taskController = new TaskController($db);
$userController = new UserController($db);


if (isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];
    $user=$userController->getUserById($userId);
    $userProjects = $projectController->getProjectsByUserId($userId);
} else {
    echo "User ID not provided!";
    exit;
}
if (isset($_GET['action']) && $_GET['action'] == 'back') {
    session_unset();  // Unset all session variables
    session_destroy();  // Destroy the session
    header("Location: adminDashboard.php");  // Redirect to the login page
    exit();
}
$toDoCount = $taskController->getTaskCountByStatusPerUser('todo',$userId);
$inProgressCount = $taskController->getTaskCountByStatusPerUser('inprogress',$userId);
$doneCount = $taskController->getTaskCountByStatusPerUser('finished',$userId);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <title>Admin Dashboard</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="../public/assets/css/admin.css">
</head>

<body>
<div class="wrapper">
<!------SIDEBAR------->
        <aside id="sidebar">
            <div class="d-flex">
                <button class="toggle-btn" type="button">
<i class="material-icons-outlined">dashboard</i>                </button>
                <div class="sidebar-logo">
                    <a href="#"></a>
                </div>
            </div>
            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="#Statistics" class="sidebar-link">
                    <i class="material-icons-outlined">visibility</i>
                        <span>Overview</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#task" class="sidebar-link">
                    <i class="material-icons-outlined">list_alt</i>
                    <span>Tasks Analysis</span>
                    </a>
                </li>
            
              
            </ul>
            <div class="sidebar-footer">
                <a href="?action=back" class="sidebar-link">
                    <i class="lni lni-exit"></i>
                    <span>back</span>
                </a>
            </div>
        </aside>
        <div class="main">
            <!--THEME TOGGLE-->
            <nav class="navbar navbar-expand px-4 py-3">
                <div id="theme-toggle-container">
    <img id="theme-toggle-button" src="../public/assets/img/themeLogo.png" alt="Theme Toggle">
    <div class="theme-selector hidden" id="theme-dropdown-container">
        <ul>
            <li class="theme-option" data-theme="">Default</li>
            <li class="theme-option" data-theme="monochrome-theme">Blue</li>
            <li class="theme-option" data-theme="forest-theme">Green</li>
        </ul>
    </div>
            </nav>
              <!-----CARDS------>
              <div class="row" id="Statistics">
    <div class="container-fluid">
        <div class="mb-3">
            <h3 class="fw-bold fs-4 mb-3"><?php echo $user['name']?> Dashboard</h3>
            <div class="user-cards-container">
            <?php foreach ($userProjects as $project): ?>
    <?php 
        // Use the controller to get task counts
        $totalTasks = $taskController->getTaskCountByProjectId($project['id']);
        $completedTasks = $taskController->getTaskCountByStatusPerProject('finished', $project['id']);
        $progressPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

    ?>
    <div class="card new-card">
        <div class="card-inner">
            <h3><?php echo htmlspecialchars($project['name']); ?></h3>
            <span class="material-icons-outlined">folder</span>
        </div>
        <h1 >Completed tasks</h1>
        <div class="progress-container">
                                  <span class="progress" data-value="<?php echo $progressPercentage?>%"></span>
                                  <span class="label"><?php echo $progressPercentage?>%</span>
                              </div>
    </div>
<?php endforeach; ?>

    </div>
</div>
   

                     <!-----TASKS ANALYSIS CHARTS----->
                      <h3 class="fw-bold fs-4 my-3"id="task"> Tasks Analysis
                    </h3>
                    <div class="row" >
                
                        
                            <div class="charts"id="task-chart">
                              <div class="chart charts-card-tasks" id="task">
                                  <h2 class="chart-title">Task Status</h2>
                                  <div id="pie-chart"></div>
                                  </div>
                             
                </div>
                    </div>


         
            </main>
            <footer class="footer">

            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>


<script>
    //PROGRESS BAR
const bar = document.querySelector(".toggle-btn");

bar.addEventListener("click", function () {
  document.querySelector("#sidebar").classList.toggle("expand");
});
document.querySelectorAll('.progress').forEach(progress => {
  const value = progress.getAttribute('data-value');
  const progressBar = document.createElement('div');
  progressBar.classList.add('progress-bar');
  progressBar.style.width = value; 
  progress.appendChild(progressBar);
});

//theme toggle
// Apply saved theme from localStorage when the page loads
document.addEventListener('DOMContentLoaded', function () {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        document.body.className = savedTheme; // Apply the saved theme
    }
});

// Theme toggle logic 
const themeToggleButton = document.getElementById('theme-toggle-button');
const themeDropdownContainer = document.getElementById('theme-dropdown-container');
const themeOptions = document.querySelectorAll('.theme-option');

if (themeToggleButton && themeDropdownContainer) {
    themeToggleButton.addEventListener('click', function (event) {
        event.stopPropagation();
        themeDropdownContainer.classList.toggle('visible');
        themeDropdownContainer.classList.toggle('hidden');
    });

    themeOptions.forEach(option => {
        option.addEventListener('click', function () {
            const theme = option.getAttribute('data-theme');
            document.body.className = theme; // Change the theme
            localStorage.setItem('theme', theme); // Save the theme to localStorage
            themeDropdownContainer.classList.add('hidden');
            themeDropdownContainer.classList.remove('visible');
        });
    });

    document.addEventListener('click', function (event) {
        if (!themeToggleButton.contains(event.target) && !themeDropdownContainer.contains(event.target)) {
            themeDropdownContainer.classList.add('hidden');
            themeDropdownContainer.classList.remove('visible');
        }
    });
}
//end theme toggle





// ---------- CHARTS ----------//


//PIE CHART
 
const pieChartOptions = {
  series: [<?php echo $doneCount?>,<?php echo $inProgressCount?> , <?php echo $toDoCount?>],
  chart: {
    type: 'pie',
    background: 'transparent',
    height: 350,
    toolbar: {
      show: false,
    },
  },
  colors: ['var(--primary-text)', 'var(--accent)', 'var(--black)'],
  labels: ['Completed', 'In Progrss', 'To Do'],
  legend: {
    position: 'top',
    labels: {
      colors: 'var(--accent-dark)',
    },
  },
  tooltip: {
    theme: 'dark',
  },
};

const pieChart = new ApexCharts(document.querySelector('#pie-chart'), pieChartOptions);
pieChart.render();


</script>

</body>

</html>