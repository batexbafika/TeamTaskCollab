document.addEventListener('DOMContentLoaded', async function () {
    var user = JSON.parse(localStorage.getItem('user'));
    var urlParams = new URLSearchParams(window.location.search);
    var projectId = urlParams.get('projectId');

    try {
        var endpoint = projectId ? '/projects/' + projectId + '/tasks' : '/tasks';
        var tasks = await api.get(endpoint);
        renderTasks(tasks, user);

        if (projectId) {
            var titleEl = document.getElementById('projectTitle');
            if (titleEl) titleEl.textContent = 'Project Tasks #' + projectId;
        }

        var actionsEl = document.getElementById('pmTaskActions');
        if (actionsEl) actionsEl.style.display = 'block';
        loadUsersForAssignment();
    } catch (err) {
        console.error('Failed to fetch tasks:', err);
    }

    setupTaskFormHandler(projectId);
});

function renderTasks(tasks, user) {
    var list = document.getElementById('taskList');
    if (!list) return;

    list.innerHTML = tasks.map(function (t) {
        return '<div class="card">' +
            '<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1rem;">' +
            '<p style="font-size:1rem;margin:0;flex:1;">' + t.description + '</p>' +
            '<span class="badge ' + mapStatusToBadge(t.status) + '">' + mapStatusToDisplay(t.status) + '</span>' +
            '</div>' +
            '<div class="mt-4"><small>Deadline: <strong>' + (t.deadline ? new Date(t.deadline).toLocaleDateString() : 'None') + '</strong></small></div>' +
            '<div class="flex-end mt-4">' +
            '<select onchange="updateTaskStatus(' + t.taskID + ', this.value)" style="padding:0.5rem;border:1px solid var(--border);border-radius:var(--radius);font-size:0.875rem;">' +
            '<option value="open"' + (t.status === 'open' ? ' selected' : '') + '>Open</option>' +
            '<option value="inProgress"' + (t.status === 'inProgress' ? ' selected' : '') + '>In Progress</option>' +
            '<option value="completed"' + (t.status === 'completed' ? ' selected' : '') + '>Completed</option>' +
            '</select>' +
            '</div>' +
            '</div>';
    }).join('') || '<p class="text-muted">No tasks found.</p>';
}

async function updateTaskStatus(taskID, newStatus) {
    try {
        await api.put('/tasks/' + taskID, { status: newStatus });
        window.location.reload();
    } catch (err) { console.error(err); }
}

async function loadUsersForAssignment() {
    try {
        var users = await api.get('/users');
        var select = document.querySelector('select[name="assigned_to"]');
        if (select) {
            select.innerHTML = users.map(function (u) { return '<option value="' + u.userID + '">' + u.name + '</option>'; }).join('');
        }
    } catch (err) { console.error(err); }
}

function setupTaskFormHandler(projectId) {
    var form = document.getElementById('taskForm');
    if (form) {
        form.onsubmit = async function (e) {
            e.preventDefault();
            var formData = new FormData(form);
            var body = Object.fromEntries(formData.entries());
            if (projectId) body.projectID = parseInt(projectId);
            try {
                await api.post('/tasks', body);
                window.location.reload();
            } catch (err) { console.error(err); }
        };
    }
}
