<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Sistema de Gestión de Proyectos</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header h1 {
            text-align: center;
            font-size: 2rem;
            font-weight: 300;
        }

        .nav {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .nav ul {
            list-style: none;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .nav a {
            text-decoration: none;
            color: #667eea;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .nav a:hover {
            background-color: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        .card {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .card h2 {
            color: #667eea;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #555;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e1e5e9;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .btn-primary {
            background-color: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background-color: #5a6fd8;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-info {
            background-color: #17a2b8;
            color: white;
        }

        .btn-info:hover {
            background-color: #138496;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }

        .table tbody tr:hover {
            background-color: #f5f5f5;
        }

        .alert {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .alert-error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .badge {
            padding: 0.25rem 0.5rem;
            border-radius: 3px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .badge-pendiente {
            background-color: #ffc107;
            color: #212529;
        }

        .badge-en_progreso {
            background-color: #17a2b8;
            color: white;
        }

        .badge-completado {
            background-color: #28a745;
            color: white;
        }

        .actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .monto {
            font-weight: 600;
            color: #28a745;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .nav ul {
                flex-direction: column;
                align-items: center;
            }
            
            .actions {
                flex-direction: column;
            }
            
            .table {
                font-size: 0.875rem;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>Sistema de Gestión de Proyectos - Tech Solutions</h1>
        </div>
    </div>

    <div class="container">
        <nav class="nav">
            <ul>
                <li><a href="{{ route('proyectos.index') }}">📋 Lista de Proyectos</a></li>
                <li><a href="{{ route('proyectos.create') }}">➕ Crear Proyecto</a></li>
            </ul>
        </nav>

        @yield('content')
    </div>

    <script>
        // Configurar CSRF token para peticiones AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Función para formatear números como moneda
        function formatCurrency(amount) {
            return new Intl.NumberFormat('es-CL', {
                style: 'currency',
                currency: 'CLP'
            }).format(amount);
        }

        // Función para confirmar eliminación
        function confirmarEliminacion(nombre) {
            return confirm(`¿Está seguro que desea eliminar el proyecto "${nombre}"?\n\nEsta acción no se puede deshacer.`);
        }
    </script>
    
    @yield('scripts')
</body>
</html>
