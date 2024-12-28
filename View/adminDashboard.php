<?php
// Start the session and include necessary files.
session_start();
require_once '../Model/Database.php';
require_once '../Controller/TaskController.php';
require_once '../Controller/UserController.php';
require_once '../Controller/ProjectController.php';


// Ensure the user is logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Create a new database connection and controllers.
$db = Database::getInstance()->getConnection();
$userController = new UserController($db);
$taskController = new TaskController($db);
$projectController = new ProjectController($db);



// Retrieve user data, including the usertypes_id.
$user = $userController->getUserById($_SESSION['user_id']);
$name = $user['name'] ?? 'User';


// Retrieve all tasks for the user.
$tasks = $taskController->getAllTasksByUser($_SESSION['user_id']);
$projects = $projectController->countProjectsByUserId($_SESSION['user_id']);

//    STARTT    //
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_unset();  // Unset all session variables
    session_destroy();  // Destroy the session
    header("Location: login.php");  // Redirect to the login page
    exit();
}
$tasks = $taskController->getAllTasks();
$totalTasks = count($tasks);
$totalUsers = count($userController->getAllUsers());

// Fetch task status counts.
$toDoCount = $taskController->getTaskCountByStatus('todo');
$inProgressCount = $taskController->getTaskCountByStatus('inprogress');
$doneCount = $taskController->getTaskCountByStatus('finished');

$complete=$taskController->completionPercentage($totalTasks, $doneCount);
$inProgress=$taskController->completionPercentage($totalTasks, $inProgressCount);
$toDo=$taskController->completionPercentage($totalTasks, $toDoCount);

$topFiveUsers = $taskController->getTopFiveUsers();
$userNames = [];
$taskCounts = [];

foreach ($topFiveUsers as $user) {
    $userNames[] = $user['name'];
    $taskCounts[] = $user['task_count'];
}
$users = $userController->getAllUsers();




$userNamesJson = json_encode($userNames);
$taskCountsJson = json_encode($taskCounts);
// Create an array to hold users with task counts
$userData = [];

foreach ($users as $userRow) {
    // Get task counts for each user
    $taskCounts = $taskController->getTaskCountsByUser($userRow['id']);
    
    $userData[] = [
      'name' => $userRow['name'],
      'id' => $userRow['id'],
      'done' => $taskCounts['done'],
      'to_do' => $taskCounts['to_do'],
      'in_progress' => $taskCounts['in_progress']
  ];
  
}
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
            
                <li class="sidebar-item">
                    <a href="#manage" class="sidebar-link">

                        <i class="material-icons-outlined">manage_accounts</i> 
                        <span>Manage Users</span>
                    </a>
                </li>
         
              
            </ul>
            <div class="sidebar-footer">
                <a href="?action=logout" class="sidebar-link">
                    <i class="lni lni-exit"></i>
                    <span>Logout</span>
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
            <div class="row"id="Statistics">
                <div class="container-fluid">
                    <div class="mb-3">
                        <h3 class="fw-bold fs-4 mb-3">Admin Dashboard</h3>
                        <div class="main-cards" >

                            <div class="card">
                            <div class="card-inner">
                                  <h3><?php echo $inProgressCount?></h3>
                                  <span class="material-icons-outlined">assignment_turned_in</span>

                              </div>
                              <h1>IN PROGRESS TASKS</h1>
                              <div class="progress-container">
                                  <span class="progress" data-value="<?php echo $inProgress?>%"></span>
                                  <span class="label"><?php echo $inProgress?>%</span>
                              </div>
                          </div>
                  
                          <div class="card">
                              <div class="card-inner">
                                  <h3><?php echo $doneCount?></h3>
                                  <span class="material-icons-outlined">assignment_turned_in</span>

                              </div>
                              <h1>COMPLETED TASKS</h1>
                              <div class="progress-container">
                                  <span class="progress" data-value="<?php echo $complete?>%"></span>
                                  <span class="label"><?php echo $complete?>%</span>
                              </div>
                          </div>
                  
                          <div class="card">
                          <div class="card-inner">
                                  <h3><?php echo $toDoCount?></h3>
                                  <span class="material-icons-outlined">assignment_turned_in</span>

                              </div>
                              <h1>TO DO TASKS</h1>
                              <div class="progress-container">
                                  <span class="progress" data-value="<?php echo $toDo?>%"></span>
                                  <span class="label"><?php echo $toDo?>%</span>
                              </div>
                          </div>
                      </div></div>
                     <!-----TASKS ANALYSIS CHARTS----->
                      <h3 class="fw-bold fs-4 my-3"id="task"> Tasks Analysis
                    </h3>
                    <div class="row" >
                
                        
                            <div class="charts">
                              <div class="charts-card-tasks" id="task">
                                  <h2 class="chart-title">Task Status</h2>
                                  <div id="pie-chart"></div>
                                  </div>
                                  <div class="charts-card" id="top">
                            <h2 class="chart-title">Top 5 Users</h2>
                            <div id="bar-chart-top-users"></div>
                        </div>
                </div>
                    </div>

                 <!--------MANAGE USERS TABLE------->
                 <h3 class="fw-bold fs-4 my-3" id="manage">Manage Users</h3>
        <div class="row mb-4">
            <div class="col-md-6">
                <input type="text" id="user-search" class="form-control" placeholder="Search user by name">
            </div>
        </div>

        <!-- User Cards -->
        <div class="row" id="user-cards-container">
    <?php foreach ($userData as $user): ?>
        <div class="col-md-4 mb-3">
            <div class="card user-card">
                <div class="card-inner">
                    <h5 class="card-title text-center fw-bold">
                        <?php echo htmlspecialchars($user['name']); ?>
                    </h5>
                    <div class="text-center">
                        <p><strong>Projects:</strong> 
                           <?php echo htmlspecialchars($projectController->countProjectsByUserId($user['id'])); ?>
                        </p>
                        <a href="user_projects.php?user_id=<?php echo $user['id']; ?>" class="btn btn-primary mt-2">
                            View More
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
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
//theme toggle
// Theme toggle
const themeToggleButton = document.getElementById('theme-toggle-button');
const themeDropdownContainer = document.getElementById('theme-dropdown-container');
const themeOptions = document.querySelectorAll('.theme-option');

if (themeToggleButton && themeDropdownContainer) {
    // Check if a theme is saved in localStorage
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        document.body.className = savedTheme; // Apply the saved theme
    }

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


// ---------- CHARTS ----------//


//PIE CHART
 
const pieChartOptions = {
  series: [<?php echo $doneCount?>, <?php echo $inProgressCount?>, <?php echo $toDoCount?>],
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






//BAR CHART(top users)
const barChartTopUsersOptions = {
  series: [{
      name: 'Tasks Created',
      data: <?php echo $taskCountsJson; ?>
  }],
  chart: {
      type: 'bar',
      height: 350,
      background: 'transparent'
  },
  colors: ['var(--primary-text)', 'var(--accent)'],
  plotOptions: {
      bar: {
          distributed: true,
          borderRadius: 4
      }
  },
  xaxis: {
      categories: <?php echo $userNamesJson; ?>,
      labels: {
          style: {
              colors: 'var(--primary-text)'
          }
      },
      axisBorder: { color: 'var(--primary-text)' }
  },
  yaxis: {
      title: {
          text: 'Tasks',
          style: { color: 'var(--primary-text)' }
      },
      labels: {
          style: { colors: 'var(--primary-text)' }
      }
  },
  grid: { borderColor: 'var(--primary-text)' }
};

const barChartTopUsers = new ApexCharts(document.querySelector('#bar-chart-top-users'), barChartTopUsersOptions);
barChartTopUsers.render();

//USER STATUS

document.addEventListener("DOMContentLoaded", function() {
    // Fetch users on page load
    fetchUsers();

    // Add event listener for the filter
    document.getElementById("user-status-filter").addEventListener("change", function() {
        fetchUsers(this.value);
    });

    }
  );
  //search users 
const userSearchInput = document.getElementById('user-search');
const userCardsContainer = document.getElementById('user-cards-container');
const userCards = Array.from(userCardsContainer.getElementsByClassName('user-card'));

userSearchInput.addEventListener('input', function () {
    const searchTerm = this.value.toLowerCase();

    userCards.forEach(card => {
        const userName = card.querySelector('.card-title').textContent.toLowerCase();
        if (userName.includes(searchTerm)) {
            card.parentElement.style.display = 'block';
        } else {
            card.parentElement.style.display = 'none';
        }
    });
});

</script>

</body>

</html>