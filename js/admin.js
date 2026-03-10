document.addEventListener('DOMContentLoaded', async function () {
    try {
        var results = await Promise.all([
            api.get('/admin/stats'),
            api.get('/users')
        ]);
        renderStats(results[0]);
        renderUsers(results[1]);
    } catch (err) {
        console.error('Failed to fetch admin data:', err);
    }
});

function renderStats(stats) {
    var list = document.getElementById('adminStats');
    if (!list) return;
    list.innerHTML =
        '<div class="stats-grid">' +
        '<div class="stat-card"><h3>Total Users</h3><p>' + (stats.total_users || 0) + '</p></div>' +
        '<div class="stat-card"><h3>Total Projects</h3><p>' + (stats.total_projects || 0) + '</p></div>' +
        '<div class="stat-card"><h3>Total Tasks</h3><p>' + (stats.total_tasks || 0) + '</p></div>' +
        '</div>';
}

function renderUsers(users) {
    var container = document.getElementById('userManagementList');
    if (!container) return;

    container.innerHTML = users.map(function (u) {
        return '<div class="card" style="display:flex;justify-content:space-between;align-items:center;">' +
            '<div>' +
            '<h3>' + u.name + '</h3>' +
            '<p class="text-muted">' + u.email + (u.address ? ' &bull; ' + u.address : '') + '</p>' +
            '</div>' +
            '<button class="btn btn-outline" style="color:#ef4444;border-color:#fecaca;font-size:0.75rem;padding:0.3rem 0.6rem;" onclick="deleteUser(' + u.userID + ')">Remove</button>' +
            '</div>';
    }).join('') || '<p class="text-muted">No users found.</p>';
}

async function deleteUser(id) {
    try {
        await api.delete('/users/' + id);
        window.location.reload();
    } catch (err) { console.error(err); }
}
