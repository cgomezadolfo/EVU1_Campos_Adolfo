<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - Tech Solutions</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar h1 {
            font-size: 24px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .logout-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .welcome-card {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .welcome-card h2 {
            color: #333;
            margin-bottom: 1rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #666;
            font-size: 14px;
        }

        .projects-section {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .projects-section h3 {
            margin-bottom: 1rem;
            color: #333;
        }

        .project-item {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .project-item:last-child {
            border-bottom: none;
        }

        .project-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-pendiente {
            background: #fff3cd;
            color: #856404;
        }

        .status-en_progreso {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-completado {
            background: #d4edda;
            color: #155724;
        }

        .loading {
            text-align: center;
            padding: 2rem;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Sistema de Gestión de Proyectos</h1>
        <div class="user-info">
            <div class="user-avatar" id="userAvatar"></div>
            <div>
                <div id="userName">Cargando...</div>
                <div style="font-size: 12px; opacity: 0.8;" id="userEmail"></div>
            </div>
            <button class="logout-btn" onclick="logout()">Cerrar Sesión</button>
        </div>
    </div>

    <div class="container">
        <div class="welcome-card">
            <h2 id="welcomeMessage">¡Bienvenido al Sistema!</h2>
            <p>Gestiona tus proyectos de manera eficiente y colaborativa.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number" id="totalProjects">-</div>
                <div class="stat-label">Total de Proyectos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="activeProjects">-</div>
                <div class="stat-label">Proyectos Activos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="completedProjects">-</div>
                <div class="stat-label">Proyectos Completados</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="totalBudget">-</div>
                <div class="stat-label">Presupuesto Total</div>
            </div>
        </div>

        <div class="projects-section">
            <h3>Proyectos Recientes</h3>
            <div id="projectsList">
                <div class="loading">Cargando proyectos...</div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            checkAuth();
            loadUserData();
            loadProjects();
        });

        function checkAuth() {
            const token = localStorage.getItem('jwt_token');
            if (!token) {
                window.location.href = '/login';
                return;
            }

            // Verificar si el token es válido (opcional - podría hacer una llamada al servidor)
            try {
                const payload = JSON.parse(atob(token.split('.')[1]));
                if (payload.exp < Date.now() / 1000) {
                    localStorage.removeItem('jwt_token');
                    localStorage.removeItem('user_data');
                    window.location.href = '/login';
                }
            } catch (error) {
                console.error('Error verificando token:', error);
                logout();
            }
        }

        function loadUserData() {
            const userData = JSON.parse(localStorage.getItem('user_data') || '{}');
            
            if (userData.nombre) {
                document.getElementById('userName').textContent = userData.nombre;
                document.getElementById('userEmail').textContent = userData.email;
                document.getElementById('userAvatar').textContent = userData.nombre.charAt(0).toUpperCase();
                document.getElementById('welcomeMessage').textContent = `¡Bienvenido, ${userData.nombre}!`;
            }
        }

        async function loadProjects() {
            const token = localStorage.getItem('jwt_token');
            
            try {
                const response = await fetch('/api/proyectos', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    displayProjects(data.datos || data);
                    updateStats(data.datos || data);
                } else if (response.status === 401) {
                    logout();
                } else {
                    document.getElementById('projectsList').innerHTML = '<div class="loading">Error al cargar proyectos</div>';
                }
            } catch (error) {
                console.error('Error cargando proyectos:', error);
                document.getElementById('projectsList').innerHTML = '<div class="loading">Error al cargar proyectos</div>';
            }
        }

        function displayProjects(projects) {
            const projectsList = document.getElementById('projectsList');
            
            if (!projects || projects.length === 0) {
                projectsList.innerHTML = '<div class="loading">No hay proyectos disponibles</div>';
                return;
            }

            projectsList.innerHTML = projects.slice(0, 5).map(project => `
                <div class="project-item">
                    <div>
                        <strong>${project.nombre}</strong>
                        <br>
                        <small style="color: #666;">Responsable: ${project.responsable}</small>
                    </div>
                    <div style="text-align: right;">
                        <span class="project-status status-${project.estado}">
                            ${getStatusLabel(project.estado)}
                        </span>
                        <br>
                        <small style="color: #666;">$${formatNumber(project.monto)}</small>
                    </div>
                </div>
            `).join('');
        }

        function updateStats(projects) {
            if (!projects) return;

            const total = projects.length;
            const active = projects.filter(p => p.estado === 'en_progreso').length;
            const completed = projects.filter(p => p.estado === 'completado').length;
            const totalBudget = projects.reduce((sum, p) => sum + parseFloat(p.monto || 0), 0);

            document.getElementById('totalProjects').textContent = total;
            document.getElementById('activeProjects').textContent = active;
            document.getElementById('completedProjects').textContent = completed;
            document.getElementById('totalBudget').textContent = '$' + formatNumber(totalBudget);
        }

        function getStatusLabel(status) {
            const labels = {
                'pendiente': 'Pendiente',
                'en_progreso': 'En Progreso',
                'completado': 'Completado'
            };
            return labels[status] || status;
        }

        function formatNumber(num) {
            return new Intl.NumberFormat('es-ES').format(num);
        }

        function logout() {
            localStorage.removeItem('jwt_token');
            localStorage.removeItem('user_data');
            window.location.href = '/login';
        }
    </script>
</body>
</html>
