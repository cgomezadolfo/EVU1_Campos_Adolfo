<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inicio de Sesión - Tech Solutions</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .login-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .login-header p {
            opacity: 0.9;
            font-size: 14px;
        }

        .login-form {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .alert {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .login-footer {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
        }

        .login-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        .loading {
            display: none;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid #ffffff;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Iniciar Sesión</h1>
            <p>Sistema de Gestión de Proyectos</p>
        </div>

        <form class="login-form" id="loginForm">
            <div id="alertContainer"></div>

            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn" id="loginBtn">
                <span class="btn-text">Iniciar Sesión</span>
                <div class="loading">
                    <div class="spinner"></div>
                    <span>Autenticando...</span>
                </div>
            </button>
        </form>

        <div class="login-footer">
            <p>¿No tienes cuenta? <a href="{{ route('auth.registro') }}">Regístrate aquí</a></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const btn = document.getElementById('loginBtn');
            const btnText = document.querySelector('.btn-text');
            const loading = document.querySelector('.loading');
            const alertContainer = document.getElementById('alertContainer');

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                // Mostrar loading
                btnText.style.display = 'none';
                loading.style.display = 'flex';
                btn.disabled = true;

                // Limpiar alertas anteriores
                alertContainer.innerHTML = '';

                const formData = {
                    email: document.getElementById('email').value,
                    password: document.getElementById('password').value
                };

                try {
                    const response = await fetch('/api/auth/login', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(formData)
                    });

                    const data = await response.json();

                    if (response.ok && data.exito) {
                        // Guardar token en localStorage
                        localStorage.setItem('jwt_token', data.datos.jwt_token);
                        localStorage.setItem('user_data', JSON.stringify(data.datos.usuario));
                        
                        // Mostrar éxito
                        alertContainer.innerHTML = `
                            <div class="alert alert-success">
                                ✅ ${data.mensaje}. Redirigiendo...
                            </div>
                        `;

                        // Redirigir al dashboard después de 2 segundos
                        setTimeout(() => {
                            window.location.href = '/dashboard';
                        }, 2000);
                    } else {
                        throw new Error(data.mensaje || 'Error en el inicio de sesión');
                    }
                } catch (error) {
                    alertContainer.innerHTML = `
                        <div class="alert alert-error">
                            ❌ ${error.message}
                        </div>
                    `;
                } finally {
                    // Ocultar loading
                    btnText.style.display = 'inline';
                    loading.style.display = 'none';
                    btn.disabled = false;
                }
            });

            // Rellenar campos con datos de prueba (solo para desarrollo)
            if (window.location.hostname === '127.0.0.1' || window.location.hostname === 'localhost') {
                document.getElementById('email').value = 'ana.garcia@techsolutions.com';
                document.getElementById('password').value = 'MiClave123!';
            }
        });
    </script>
</body>
</html>
