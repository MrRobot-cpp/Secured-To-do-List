document.addEventListener('DOMContentLoaded', function() {
    const searchBar = document.getElementById('search-bar');
    const priorityFilter = document.getElementById('priority-filter');
    const newTaskButtons = document.querySelectorAll('.new-task');
const draggables = document.querySelectorAll('.task');
const droppables= document.querySelectorAll('.kanban-column');

draggables.forEach((task) => {

    task.addEventListener('dragstart', () => {
        task.classList.add('is-dragging');
    });

    task.addEventListener('dragend', () => {
        task.classList.remove('is-dragging');
    });

});
droppables.forEach((zone) => {
    zone.addEventListener('dragover', (e) => {
        e.preventDefault();
        const buttomTask= insertAboveTask(zone, e.clientY);
        const curTask = document.querySelector('.is-dragging');
         if (!buttomTask) {
            zone.appendChild(curTask);
            const newStatus = zone.getAttribute('data-status');
            statusUpdateForm.querySelector('input[name="task_id"]').value = taskId;
            statusUpdateForm.querySelector('input[name="status"]').value = newStatus;
            statusUpdateForm.submit();

        }
        else {
            zone.insertBefore(curTask, buttomTask);
        }
        
    });


});
const insertAboveTask = (zone, mousey) => {
    const task = zone.querySelectorAll(".task:not(.is-dragging)");
    let closestTask = null;
    let closestoffset = Number.NEGATIVE_INFINITY;
    task.forEach((task) => {
        const {top} = task.getBoundingClientRect();
        const offset = mousey - top;
        if (offset < 0 && offset > closestoffset) {
            closestoffset = offset;
            closestTask = task;
        }
    });
    return closestTask;
};

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
                deleteForm.querySelector('input[name="task_id"]').value = taskId;
                deleteForm.submit();
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
                    <option value="urgent">Urgent</option>
                    <option value="high">High</option>
                    <option value="normal">Normal</option>
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
