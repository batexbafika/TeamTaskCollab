document.addEventListener('DOMContentLoaded', async function () {
    var userJson = localStorage.getItem('user');
    if (!userJson) return;
    var user = JSON.parse(userJson);

    try {
        var projects = await api.get('/projects');
        renderProjects(projects, user);
    } catch (err) {
        console.error('Failed to fetch projects:', err);
    }
});

function renderProjects(projects, user) {
    var list = document.getElementById('projectsList');
    if (!list) return;

    list.innerHTML = projects.map(function (p) {
        return '<div class="card project-card">' +
            '<div class="section-header">' +
            '<h3>' + p.name + '</h3>' +
            '<button class="btn btn-outline" style="padding:0.25rem 0.5rem;font-size:0.75rem;color:#ef4444;border-color:#fecaca;" onclick="deleteProject(' + p.id + ')">Delete</button>' +
            '</div>' +
            '<p class="text-muted">' + (p.description || 'No description provided.') + '</p>' +
            '<div class="flex-end mt-4">' +
            '<a href="tasks.html?projectId=' + p.id + '" class="btn btn-primary" style="text-decoration:none;padding:0.4rem 0.8rem;font-size:0.8rem;">View Tasks</a>' +
            '</div>' +
            '</div>';
    }).join('') || '<p class="text-muted">No projects available.</p>';
}

async function deleteProject(id) {
    try {
        await api.delete('/projects/' + id);
        window.location.reload();
    } catch (err) { console.error(err); }
}
