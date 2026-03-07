document.addEventListener('DOMContentLoaded', async function () {
    var user = JSON.parse(localStorage.getItem('user'));
    if (!user) return;

    var welcomeMsg = document.getElementById('welcomeMessage');
    var roleBadge = document.getElementById('userRoleBadge');
    if (welcomeMsg) welcomeMsg.textContent = 'Welcome back, ' + user.name + '!';
    if (roleBadge) {
        roleBadge.textContent = 'MEMBER';
        roleBadge.className = 'chip role';
    }

    refreshDashboard(user);
    setupFormHandlers();
});

async function refreshDashboard(user) {
    try {
        var allTasks = await api.get('/tasks');
        var projects = await api.get('/projects');

        var statsContainer = document.getElementById('adminStats');
        if (statsContainer) {
            statsContainer.innerHTML =
                '<div class="stat-card"><h3>Projects</h3><p>' + projects.length + '</p></div>' +
                '<div class="stat-card"><h3>Tasks</h3><p>' + allTasks.length + '</p></div>' +
                '<div class="stat-card"><h3>Completed</h3><p>' + allTasks.filter(function (t) { return t.status === 'completed'; }).length + '</p></div>';
        }

        var pmList = document.getElementById('pmProjectList');
        if (pmList) {
            pmList.innerHTML = projects.map(function (p) {
                return '<div class="card project-card">' +
                    '<h3>' + p.name + '</h3>' +
                    '<p class="text-muted">' + (p.description || 'No description') + '</p>' +
                    '<div class="flex-end mt-4"><a href="projects.html?id=' + p.id + '" class="btn-text">Manage</a></div>' +
                    '</div>';
            }).join('') || '<p class="text-muted">No projects created yet.</p>';
        }

        var memberList = document.getElementById('memberTaskList');
        if (memberList) {
            var assignments = await api.get('/task-assignments');
            var myAssignments = assignments.filter(function (a) { return a.userID === user.id; });
            var myTaskIds = myAssignments.map(function (a) { return a.taskID; });
            var myTasks = allTasks.filter(function (t) { return myTaskIds.indexOf(t.taskID) !== -1; });
            if (myTasks.length === 0) myTasks = allTasks;
            memberList.innerHTML = myTasks.map(function (t) {
                return '<div class="card task-card">' +
                    '<p>' + t.description + '</p>' +
                    '<div class="flex-end mt-4" style="justify-content:space-between;align-items:center;">' +
                    '<span class="badge ' + mapStatusToBadge(t.status) + '">' + mapStatusToDisplay(t.status) + '</span>' +
                    '<select onchange="updateTaskStatus(' + t.taskID + ', this.value)" class="status-select">' +
                    '<option value="open"' + (t.status === 'open' ? ' selected' : '') + '>Open</option>' +
                    '<option value="inProgress"' + (t.status === 'inProgress' ? ' selected' : '') + '>In Progress</option>' +
                    '<option value="completed"' + (t.status === 'completed' ? ' selected' : '') + '>Completed</option>' +
                    '</select>' +
                    '</div>' +
                    '</div>';
            }).join('') || '<p class="text-muted">No tasks assigned to you.</p>';
        }

        renderOverdue(allTasks);
    } catch (err) {
        console.error('Dashboard refresh failed:', err);
    }
}

function renderOverdue(tasks) {
    var container = document.getElementById('overdueTasks');
    if (!container) return;
    var now = new Date();
    var overdue = tasks.filter(function (t) { return new Date(t.deadline) < now && t.status !== 'completed'; });
    container.innerHTML = overdue.map(function (t) {
        return '<div class="flex-end" style="justify-content:space-between;border-bottom:1px solid var(--border);padding:0.5rem 0;">' +
            '<span>' + t.description.substring(0, 40) + '</span>' +
            '<span class="overdue">Due: ' + new Date(t.deadline).toLocaleDateString() + '</span>' +
            '</div>';
    }).join('') || '<p class="text-success">No overdue tasks!</p>';
}

async function updateTaskStatus(taskID, newStatus) {
    try {
        await api.put('/tasks/' + taskID, { status: newStatus });
        window.location.reload();
    } catch (err) { console.error(err); }
}

function setupFormHandlers() {
    var projectForm = document.getElementById('projectForm');
    if (projectForm) {
        projectForm.onsubmit = async function (e) {
            e.preventDefault();
            var formData = new FormData(projectForm);
            var body = Object.fromEntries(formData.entries());
            try {
                await api.post('/projects', body);
                window.location.reload();
            } catch (err) { console.error(err); }
        };
    }
}
