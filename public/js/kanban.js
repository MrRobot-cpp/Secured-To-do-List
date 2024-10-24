document.addEventListener('DOMContentLoaded', function() {
    const searchBar = document.getElementById('search-bar');
    const priorityFilter = document.getElementById('priority-filter');
    const newTaskButtons = document.querySelectorAll('.new-task');

    searchBar.addEventListener('input', filterTasks);
    priorityFilter.addEventListener('change', filterTasks);

    newTaskButtons.forEach(button => {
        button.addEventListener('click', function() {
            showTaskForm(button.parentElement);
        });
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

        form.querySelector('form').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            formData.append('user_id', userId);  // Use the userId variable defined in HTML
            formData.append('status', columnElement.getAttribute('data-priority'));

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
            
        //     fetch('Controller/TaskController.php', {
        //         method: 'POST',
        //         body: formData
        //     })
        //     .then(response => response.json())
        //     .then(data => {
        //         if (data.success) {
        //             location.reload();
        //         } else {
        //             alert('Failed to add task: ' + data.message);
        //         }
        //     })
        //     .catch(error => console.error('Error:', error));
        // });

        form.querySelector('.cancel-btn').addEventListener('click', function() {
            form.remove();
        });
    }
});
