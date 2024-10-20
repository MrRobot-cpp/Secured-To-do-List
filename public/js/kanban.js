document.addEventListener('DOMContentLoaded', function() {
    const searchBar = document.getElementById('search-bar');
    const priorityFilter = document.getElementById('priority-filter');
    const tasks = document.querySelectorAll('.task');

    searchBar.addEventListener('input', function() {
        filterTasks();
    });

    priorityFilter.addEventListener('change', function() {
        filterTasks();
    });

    function filterTasks() {
        const searchTerm = searchBar.value.toLowerCase();
        const selectedPriority = priorityFilter.value;

        tasks.forEach(task => {
            const title = task.getAttribute('data-title').toLowerCase();
            const priority = task.getAttribute('data-priority');

            const matchesSearch = title.includes(searchTerm);
            const matchesPriority = selectedPriority === 'all' || priority === selectedPriority;

            if (matchesSearch && matchesPriority) {
                task.style.display = 'block';
            } else {
                task.style.display = 'none';
            }
        });
    }
});
