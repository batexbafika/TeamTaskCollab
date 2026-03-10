const API_BASE_URL = 'http://localhost:8000/api';

const api = {
    async request(endpoint, options = {}) {
        const token = localStorage.getItem('token');
        const headers = {
            'Content-Type': 'application/json',
            ...options.headers
        };

        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        try {
            const response = await fetch(`${API_BASE_URL}${endpoint}`, {
                ...options,
                headers
            });

            if (response.status === 401) {
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                window.location.href = 'login.html';
                return;
            }

            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || 'Something went wrong');
            }
            return data;
        } catch (err) {
            if (err.name === 'TypeError' && err.message === 'Failed to fetch') {
                console.warn('Backend offline. Using mock data.');
                return handleMockRequest(endpoint, options);
            }
            throw err;
        }
    },

    get(endpoint) {
        return this.request(endpoint, { method: 'GET' });
    },

    post(endpoint, body) {
        return this.request(endpoint, {
            method: 'POST',
            body: JSON.stringify(body)
        });
    },

    put(endpoint, body) {
        return this.request(endpoint, {
            method: 'PUT',
            body: JSON.stringify(body)
        });
    },

    delete(endpoint) {
        return this.request(endpoint, { method: 'DELETE' });
    }
};

function handleMockRequest(endpoint, options) {
    const method = options.method || 'GET';
    const body = options.body ? JSON.parse(options.body) : null;

    if (endpoint === '/login') {
        const user = { userID: 1, name: body.email.split('@')[0], email: body.email, address: null };
        return { user, token: 'mock-jwt-token' };
    }

    if (endpoint === '/register') {
        const user = { userID: Date.now(), name: body.name, email: body.email, address: body.address || null };
        return { user, token: 'mock-jwt-token' };
    }

    if (endpoint === '/logout') {
        return { message: 'Logged out successfully' };
    }

    if (endpoint.match(/^\/projects\/\d+\/tasks/)) {
        var projId = parseInt(endpoint.split('/')[2]);
        return getMockTasks().filter(function (t) { return t.projectID === projId; });
    }

    if (endpoint === '/projects') {
        if (method === 'POST') return { projectID: Date.now(), name: body.name, description: body.description, createdBy: 1, created_at: new Date().toISOString() };
        return [
            { projectID: 1, name: 'Core System Migration', description: 'Migrate legacy data to new platform', createdBy: 1 },
            { projectID: 2, name: 'Frontend Redesign', description: 'Implement new UI components', createdBy: 1 }
        ];
    }

    if (endpoint.includes('/projects/') && method === 'DELETE') {
        return { success: true };
    }

    if (endpoint === '/tasks') {
        if (method === 'POST') return { taskID: Date.now(), description: body.description, status: 'open', projectID: body.projectID, createdBy: 1 };
        return getMockTasks();
    }

    if (endpoint.includes('/tasks/') && (method === 'PUT' || method === 'DELETE')) {
        return { success: true };
    }

    if (endpoint === '/task-assignments') {
        if (method === 'POST') return { assignmentID: Date.now(), taskID: body.taskID, userID: body.userID };
        return [
            { assignmentID: 1, taskID: 1, userID: 2 },
            { assignmentID: 2, taskID: 2, userID: 3 },
            { assignmentID: 3, taskID: 3, userID: 3 }
        ];
    }

    if (endpoint.match(/^\/projects\/\d+\/members/)) {
        if (method === 'POST') return { membershipID: Date.now(), projectID: body.projectID, userID: body.userID, role: body.role };
        return [
            { membershipID: 1, projectID: 1, userID: 2, role: 'projectManager', joinedAt: '2026-03-01' },
            { membershipID: 2, projectID: 1, userID: 3, role: 'teamMember', joinedAt: '2026-03-02' }
        ];
    }

    if (endpoint.match(/^\/tasks\/\d+\/comments/)) {
        if (method === 'POST') return { commentID: Date.now(), message: body.message, taskID: body.taskID, createdBy: 1 };
        return [
            { commentID: 1, message: 'Started working on this', taskID: 1, createdBy: 2, createdAt: '2026-03-05' }
        ];
    }

    if (endpoint === '/admin/stats') {
        return { total_users: 5, total_projects: 2, total_tasks: 12 };
    }

    if (endpoint === '/users') {
        if (method === 'PUT' || method === 'POST') return { success: true };
        if (method === 'DELETE') return { success: true };
        return [
            { userID: 1, name: 'Admin Chief', email: 'admin@system.com', address: 'Bamenda' },
            { userID: 2, name: 'Lead PM', email: 'pm@system.com', address: 'Douala' },
            { userID: 3, name: 'Dev Ops Agent', email: 'dev@system.com', address: 'Yaounde' }
        ];
    }

    if (endpoint.includes('/users/')) {
        if (method === 'PUT') return { success: true };
        if (method === 'DELETE') return { success: true };
    }

    return { message: 'Mock endpoint not available: ' + endpoint };
}

function getMockTasks() {
    return [
        { taskID: 1, title: 'Database Export', description: 'Export all user records from legacy database', status: 'inProgress', projectID: 1, createdBy: 1, deadline: '2026-03-15' },
        { taskID: 2, title: 'UI Update', description: 'Create responsive header with mobile nav toggle', status: 'completed', projectID: 2, createdBy: 1, deadline: '2026-03-10' },
        { taskID: 3, title: 'Scripts', description: 'Write automated migration scripts for data transfer', status: 'open', projectID: 1, createdBy: 2, deadline: '2026-03-20' }
    ];
}

function mapStatusToDisplay(backendStatus) {
    var map = { 'open': 'TO DO', 'inProgress': 'IN PROGRESS', 'completed': 'DONE' };
    return map[backendStatus] || backendStatus;
}

function mapStatusToBadge(backendStatus) {
    var map = { 'open': 'badge-info', 'inProgress': 'badge-warning', 'completed': 'badge-success' };
    return map[backendStatus] || 'badge-info';
}

document.addEventListener('DOMContentLoaded', function () {
    var userJson = localStorage.getItem('user');
    var token = localStorage.getItem('token');
    var currentPath = window.location.pathname;

    if (token && userJson && (currentPath.includes('login.html') || currentPath.includes('register.html') || currentPath.endsWith('/') || currentPath.endsWith('index.html'))) {
        var params = new URLSearchParams(window.location.search);
        var redirect = params.get('redirect');
        if (redirect) {
            window.location.href = redirect;
        } else if (currentPath.includes('/html/')) {
            window.location.href = 'dashboard.html';
        } else {
            window.location.href = 'html/dashboard.html';
        }
        return;
    }

    if (!token || !userJson) {
        if (!currentPath.includes('login.html') &&
            !currentPath.includes('register.html') &&
            !currentPath.includes('dashboard.html') &&
            !currentPath.endsWith('/') &&
            !currentPath.endsWith('index.html')) {
            window.location.href = 'login.html';
        }
        return;
    }

    var user = JSON.parse(userJson);
    handleGlobalNavigation(user, currentPath);
    setupLogout();
});

function handleGlobalNavigation(user, path) {
    if (path.endsWith('/html/')) {
        window.location.href = 'dashboard.html';
        return;
    }

    var toggle = document.querySelector('.menu-toggle');
    var sidebar = document.querySelector('.sidebar');
    if (toggle && sidebar) {
        toggle.onclick = function () {
            var isHidden = sidebar.style.transform === 'translateX(-100%)' || !sidebar.style.transform;
            sidebar.style.transform = isHidden ? 'translateX(0)' : 'translateX(-100%)';
        };
    }
}

function setupLogout() {
    var logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.onclick = function (e) {
            e.preventDefault();
            api.post('/logout').catch(function () { });
            localStorage.clear();
            window.location.href = 'login.html';
        };
    }
}
