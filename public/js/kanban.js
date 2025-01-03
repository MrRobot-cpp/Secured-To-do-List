document.addEventListener('DOMContentLoaded', function() {
    const searchBar = document.getElementById('search-bar');
    const priorityFilter = document.getElementById('priority-filter');
    const newTaskButtons = document.querySelectorAll('.new-task');
    const checkboxes = document.querySelectorAll(".mark-as-finished");

//cheching the checkbox of finished tasks
checkboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", function () {
        if (this.checked) {
            const taskId = this.getAttribute("data-task-id");

            // Move the task to the "Finished" column
            const taskElement = this.closest(".task");
            const finishedColumn = document.querySelector(".kanban-column[data-status='finished']");
            finishedColumn.appendChild(taskElement);

            // Remove the checkbox after moving
            this.remove();

            // Update the status in the database
            updateTaskStatus(taskId, "finished");
        }
    });
});
function updateTaskStatus(taskId, newStatus) {
    fetch("../Controller/TaskController.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            action: "update_task_status",
            task_id: taskId,
            status: newStatus,
        }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                console.log("Task status updated successfully.");
            } else {
                console.error("Failed to update task status:", data.message);
            }
        })
        .catch((error) => {
            console.error("Error updating task status:", error);
        });
    }

    searchBar.addEventListener('input', filterTasks);
    priorityFilter.addEventListener('change', filterTasks);

    newTaskButtons.forEach(button => {
        button.addEventListener('click', function() {
            showTaskForm(button.parentElement);
        });
    });

    const updateButtons = document.querySelectorAll('.update-task');
    const deleteButtons = document.querySelectorAll('.delete-task');
    const taskForm = document.getElementById('task-form');
    const updateForm = document.getElementById('update-form');
    const deleteForm = document.getElementById('delete-form');

    // Update Task
    updateButtons.forEach(button => {
        button.addEventListener('click', function() {
            const taskId = this.getAttribute('data-task-id');
            const taskTitle = this.parentElement.querySelector('h3').innerText;
            const taskDescription = this.parentElement.querySelector('p').innerText;
            const taskPriority = this.parentElement.getAttribute('data-priority');
            const taskDeadline = this.parentElement.getAttribute('data-deadline');

            updateForm.querySelector('input[name="task_id"]').value = taskId;
            updateForm.querySelector('input[name="title"]').value = taskTitle;
            updateForm.querySelector('textarea[name="description"]').value = taskDescription;
            updateForm.querySelector('select[name="priority"]').value = taskPriority;
            updateForm.querySelector('input[name="deadline"]').value = taskDeadline;

            updateForm.style.display = 'block';
        });
    });

    // Delete Task
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const taskId = this.getAttribute('data-task-id');
            if (confirm("Are you sure you want to delete this task?")) {
                fetch('../Controller/TaskController.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'delete_task',
                        task_id: taskId,
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the task element from the DOM
                        const taskElement = this.closest('.task');
                        taskElement.remove();
                    } else {
                        alert('Failed to delete the task: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error deleting the task:', error);
                });
            }
        });
    });
    

    // Hide the Update Task Form
    document.getElementById('update-form-cancel').addEventListener('click', function() {
        updateForm.style.display = 'none';
    });

    function filterTasks() {
        const searchTerm = searchBar.value.toLowerCase();
        const selectedPriority = priorityFilter.value;
        const tasks = document.querySelectorAll('.task');

        tasks.forEach(task => {
            const title = task.getAttribute('data-title').toLowerCase();
            const priority = task.getAttribute('data-priority');
            const matchesSearch = title.includes(searchTerm);
            const matchesPriority = selectedPriority === 'all' || priority === selectedPriority;

            task.style.display = matchesSearch && matchesPriority ? 'block' : 'none';
        });
    }

    function showTaskForm(columnElement) {
        const existingForm = document.getElementById('task-form');
        if (existingForm) {
            existingForm.remove();
        }

        const form = document.createElement('div');
        form.id = 'task-form';
        form.innerHTML = `
            <form>
                <input type="text" name="title" placeholder="Task Title" required>
                <textarea name="description" placeholder="Task Description"></textarea>
                <input type="date" name="deadline" required>
                <select name="priority">
                    <option value="Urgent">Urgent</option>
                    <option value="High">High</option>
                    <option value="Normal">Normal</option>
                </select>
                <button type="submit">Add Task</button>
                <button type="button" class="cancel-btn">Cancel</button>
            </form>
        `;
        
        columnElement.appendChild(form);

        // Get project_id from the URL
        const urlParams = new URLSearchParams(window.location.search);
        const projectId = urlParams.get('project_id'); // This should be the correct query parameter

        if (!projectId) {
            alert('Project ID is missing.');
            return;
        }

        form.querySelector('form').addEventListener('submit', function(event) {
            event.preventDefault();
            const projectId = new URLSearchParams(window.location.search).get('project_id');
            const formData = new FormData(event.target);
            formData.append('project_id', projectId);
            formData.append('status', columnElement.getAttribute('data-status'));

            fetch('../Controller/TaskController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())  // Get the response as plain text first
            .then(text => {
                console.log('Raw response:', text);  // Log the raw response
            
                // Now attempt to parse it as JSON if it's valid JSON
                try {
                    const data = JSON.parse(text);  // Parse the text as JSON
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Failed to add task: ' + data.message);
                    }
                } catch (error) {
                    console.error('Error parsing JSON:', error);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
            });
        });

        form.querySelector('.cancel-btn').addEventListener('click', function() {
            form.remove();
        });
    }

    // Theme toggle 
    const themeToggleButton = document.getElementById('theme-toggle-button');
    const themeDropdownContainer = document.getElementById('theme-dropdown-container');
    const themeOptions = document.querySelectorAll('.theme-option');

    if (themeToggleButton && themeDropdownContainer) {
        // Apply the saved theme from localStorage if it exists
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            document.body.className = savedTheme; // Apply saved theme on page load
        }

        themeToggleButton.addEventListener('click', function (event) {
            event.stopPropagation();
            themeDropdownContainer.classList.toggle('visible');
            themeDropdownContainer.classList.toggle('hidden');
        });

        themeOptions.forEach(option => {
            option.addEventListener('click', function () {
                const theme = option.getAttribute('data-theme');
                document.body.className = theme;  // Apply the theme to body

                // Save the selected theme to localStorage
                localStorage.setItem('theme', theme);

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
});
document.addEventListener("DOMContentLoaded", () => {
    let draggedTask = null;

    // Allow the drop operation
    function allowDrop(event) {
        event.preventDefault();
    }

    // Capture the dragged task
    function handleDragStart(event) {
        draggedTask = event.target;
        event.dataTransfer.setData("text/plain", event.target.dataset.taskId);
    }

    async function handleDrop(event) {
        event.preventDefault();
    
        // Ensure the drop is on a column
        const column = event.target.closest(".kanban-column");
        if (!column) return;
    
        const newStatus = column.dataset.status;
        const taskId = draggedTask.dataset.taskId;
    
        if (draggedTask && newStatus) {
            // Move the task visually to the top of the column
            const firstChild = column.querySelector(".task, .new-task");
            if (firstChild) {
                column.insertBefore(draggedTask, firstChild);
            } else {
                column.appendChild(draggedTask);
            }
    
            // Update the task status in the server
            try {
                const response = await fetch("../Controller/TaskController.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                        action: "update_task_status",
                        task_id: taskId,
                        status: newStatus,
                    }),
                });
    
                const result = await response.json();
                if (!result.success) {
                    alert("Failed to update task status");
                }
            } catch (error) {
                console.error("Error updating task status:", error);
            }
        }
    }
    
    // Expose the functions globally
    window.allowDrop = allowDrop;
    window.handleDragStart = handleDragStart;
    window.handleDrop = handleDrop;
});

