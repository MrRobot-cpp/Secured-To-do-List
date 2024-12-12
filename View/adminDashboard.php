<?php
// Start the session and include necessary files.
session_start();
require_once '../Model/Database.php';
require_once '../Controller/TaskController.php';
require_once '../Controller/UserController.php';

// Ensure the user is logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Create a new database connection and controllers.
$db = (new Database())->getConnection();
$userController = new UserController($db);
$taskController = new TaskController($db);


// Retrieve user data, including the usertypes_id.
$user = $userController->getUserById($_SESSION['user_id']);
$name = $user['name'] ?? 'User';


// Retrieve all tasks for the user.
$tasks = $taskController->getAllTasksByUser($_SESSION['user_id']);
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
$toDoCount = $taskController->getTaskCountByStatus('to_do');
$inProgressCount = $taskController->getTaskCountByStatus('in_progress');
$doneCount = $taskController->getTaskCountByStatus('done');

$complete=$taskController->completionPercentage($totalTasks, $doneCount);
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
    
    // Add user and task counts to the array
    $userData[] = [
        'name' => $userRow['name'],
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
                <li class="sidebar-item">
                    <a href="#active" class="sidebar-link">
                    <i class="material-icons-outlined">bar_chart</i>
                    <span>User Activity</span>
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
            <li class="theme-option" data-theme="monochrome-theme">Monochrome</li>
            <li class="theme-option" data-theme="forest-theme">Forest</li>
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
                                  <h3>458</h3>
                                  <span class="material-icons-outlined">check_circle</span>
                                  </div>
                              <h1>ACTIVE USERS</h1>
                              <div class="progress-container">
                                  <span class="progress" data-value="80%"></span>
                                  <span class="label">80%</span>
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
                                  <h3>35</h3>
                                  <span class="material-icons-outlined">highlight_off</span>
                                  </div>
                              <h1>INACTIVE USERS</h1>
                              <div class="progress-container">
                                  <span class="progress" data-value="90%"></span>
                                  <span class="label">90%</span>
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
                              <div class="charts-card-tasks" id="completion">
                                  <h2 class="chart-title">Task Completion Over Weeks</h2>
                                  <div id="line-chart-tasks"></div>
                              </div>
                </div>
                    </div>

                 <!--------MANAGE USERS TABLE------->
                 <h3 class="fw-bold fs-4 my-3" id="manage">Manage Users</h3>
<div class="row">
    <div class="filter">
        <label for="user-status-filter">Filter by status:</label>
        <select id="user-status-filter">
            <option value="all">All Users</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>

    <div class="col-12">
    <table class="table table-striped">
    <thead>
                                    <tr class="highlight">
                                        <th scope="col">Name</th>
                                        
                                        <th scope="col" colspan="3" class="text-center">Tasks</th>
                                    </tr>
                                    <tr class="highlight">
                                        <th></th>

                                        <th>Complete</th>
                                        <th>Overdue</th>
                                        <th>In Progress</th>

                                    </tr>
                                </thead>
        <tbody>
            
            <?php foreach ($userData as $user): ?>
                <tr>
                    <th scope="row"><?php echo ($user['name']); ?></th>

                    <td class="text-center"><?php echo ($user['done']); ?></td>
                    <td class="text-center"><?php echo ($user['to_do']); ?></td>
                    <td class="text-center"><?php echo ($user['in_progress']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

                     <!-----USER ACTIVITY CHARTS----->
                <h3 class="fw-bold fs-4 my-3"id="active">User Activity
                </h3>
                <div class="row" >
                    <div class="charts">
                    
                        <div class="charts-card" id="growth">
                            <h2 class="chart-title">User Growth</h2>
                            <div id="bar-chart-growth"></div>
                        </div>
                        <div class="charts-card" id="top">
                            <h2 class="chart-title">Top 5 Users</h2>
                            <div id="bar-chart-top-users"></div>
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
//theme toggle
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
            document.body.className = theme;
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
}//end theme toggle



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
  series: [<?php echo $doneCount?>, <?php echo $inProgressCount?>, <?php echo $toDoCount ?>],
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


//BAR CHART(growth)
const barChartGrowthOptions = {
  series: [{
    name: 'New Users',
    data: [<?php echo $totalUsers?>, 30, 25, 40, 35, 45, 50]
  }],
  chart: {
    type: 'bar',
    height: 350,
    background: 'transparent'
  },
  colors: ['var(--accent)'],
  plotOptions: {
    bar: {
      horizontal: false,
      columnWidth: '50%',
      borderRadius: 4
    }
  },
  dataLabels: {
    enabled: false
  },
  xaxis: {
    categories: ['Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr'],
    labels: {
      style: {
        colors: 'var(--primary-text)'
      }
    },
    axisBorder: { color: 'var(--primary-text)' }
  },
  yaxis: {
    title: {
      text: 'New Users',
      style: { color: 'var(--primary-text)' }
    },
    labels: {
      style: { colors: 'var(--primary-text)' }
    }
  },
  grid: { borderColor: 'var(--primary-text)' },
  legend: {
    labels: {
      colors: 'var(--primary-text)'
    },
    show: true,
    position: 'top'
  },
  tooltip: {
    shared: true,
    intersect: false,
    theme: 'dark'
  }
};

const barChartGrowth = new ApexCharts(document.querySelector('#bar-chart-growth'), barChartGrowthOptions);
barChartGrowth.render();



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
//LINE CHART
   // Update Line Chart
   const lineChartTasks = {
    series: [
      {
        name: 'Tasks Completed',
        data: [<?php echo $doneCount?>, 20, 15, 30, 25, 40, 35], // Use real data for other weeks if available
      },
    ],
    chart: {
      type: 'line',
      background: 'transparent',
      height: 350,
      toolbar: {
        show: false,
      },
    },
    colors: ['var(--accent)'],
    dataLabels: {
      enabled: false,
    },
    stroke: {
      curve: 'smooth',
      width: 4,
    },
    xaxis: {
      categories: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6', 'Week 7'],
      labels: {
        style: {
          colors: 'var(--primary-text)',
        },
      },
    },
    yaxis: {
      title: {
        text: 'Tasks Completed',
        style: {
          color: 'var(--primary-text)',
        },
      },
      labels: {
        style: {
          colors: 'var(--primary-text)',
        },
      },
    },
    tooltip: {
      shared: true,
      intersect: false,
      theme: 'dark',
    },
  };

  const lineTasks = new ApexCharts(document.querySelector('#line-chart-tasks'), lineChartTasks);
  lineTasks.render();
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

</script>

</body>

</html>