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
$usertypes_id = $user['usertypes_id'] ?? null;  // Fetch usertypes_id

// Check if usertypes_id is 1 (admin), otherwise redirect to kanban.php.
if ($usertypes_id != 1) {
    header("Location: kanban.php");
    exit();
}

// Retrieve all tasks for the user.
$tasks = $taskController->getAllTasksByUser($_SESSION['user_id']);
//    STARTT    //
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_unset();  // Unset all session variables
    session_destroy();  // Destroy the session
    header("Location: login.php");  // Redirect to the login page
    exit();
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
            <!--ADMIN PICTURE-->
            <nav class="navbar navbar-expand px-4 py-3">
                <form action="#" class="d-none d-sm-inline-block">

                </form>
                <div class="navbar-collapse collapse">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a href="#" data-bs-toggle="dropdown" class="nav-icon pe-md-0">
                                <img src="new/account.png" class="avatar img-fluid" alt="">
                            </a>
                            <div class="dropdown-menu dropdown-menu-end rounded">

                            </div>
                        </li>
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
                                  <h3>204</h3>
                                  <span class="material-icons-outlined">assignment_turned_in</span>

                              </div>
                              <h1>COMPLETED TASKS</h1>
                              <div class="progress-container">
                                  <span class="progress" data-value="60%"></span>
                                  <span class="label">60%</span>
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
                    <h3 class="fw-bold fs-4 my-3"id="manage">Manage Users
                    </h3>
                    <div class="row" >
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
                                        <th scope="col">First</th>
                                        <th scope="col">Last</th>
                                        <th scope="col" colspan="3" class="text-center">Tasks</th>
                                    </tr>
                                    <tr class="highlight">
                                        <th></th>
                                        <th></th>
                                        <th>Complete</th>
                                        <th>Overdue</th>
                                        <th>In Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr data-status="inactive">
                                        <th scope="row">Mark</th>
                                        <td>Otto</td>
                                        <td>5</td> 
                                        <td>2</td> 
                                        <td>1</td> 
                                    </tr>
                                    <tr data-status="inactive">
                                        <th scope="row">Jacob</th>
                                        <td>Thornton</td>
                                        <td>8</td>
                                        <td>1</td>
                                        <td>0</td>
                                    </tr>
                                    <tr data-status="inactive">
                                        <th scope="row">Larry</th>
                                        <td>Thornton</td>
                                        <td>3</td>
                                        <td>0</td>
                                        <td>4</td>
                                    </tr>
                                </tbody>
                            </table>
                            
                        </div>
                    </div>
                     <!-----USER ACTIVITY CHARTS----->
                <h3 class="fw-bold fs-4 my-3"id="active">User Activity
                </h3>
                <div class="row" >
                    <div class="charts">
                        <div class="charts-card" id="activity">
                            <h2 class="chart-title">Active Users</h2>
                            <div id="line-chart"></div>
                        </div>
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

    <script src="../public/js/admin.js"></script>
</body>

</html>
