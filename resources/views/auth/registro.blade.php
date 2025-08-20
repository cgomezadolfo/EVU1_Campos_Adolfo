<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registro - Tech Solutions</title>
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

        .register-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
        }

        .register-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .register-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .register-header p {
            opacity: 0.9;
            font-size: 14px;
        }

        .register-form {
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

        .form-group.error input {
            border-color: #dc3545;
        }

        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: none;
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

        .register-footer {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
        }

        .register-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .register-footer a:hover {
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

        .password-strength {
            margin-top: 5px;
            font-size: 12px;
        }

        .strength-weak { color: #dc3545; }
        .strength-medium { color: #ffc107; }
        .strength-strong { color: #28a745; }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>Crear Cuenta</h1>
            <p>Únete al Sistema de Gestión de Proyectos</p>
        </div>

        <form class="register-form" id="registerForm">
            <div id="alertContainer"></div>

            <div class="form-group">
                <label for="name">Nombre Completo</label>
                <input type="text" id="name" name="name" required>
                <div class="error-message" id="nameError"></div>
            </div>

            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" required>
                <div class="error-message" id="emailError"></div>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
                <div class="password-strength" id="passwordStrength"></div>
                <div class="error-message" id="passwordError"></div>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmar Contraseña</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
                <div class="error-message" id="confirmError"></div>
            </div>

            <button type="submit" class="btn" id="registerBtn">
                <span class="btn-text">Crear Cuenta</span>
                <div class="loading">
                    <div class="spinner"></div>
                    <span>Registrando...</span>
                </div>
            </button>
        </form>

        <div class="register-footer">
            <p>¿Ya tienes cuenta? <a href="{{ route('auth.login') }}">Inicia sesión aquí</a></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registerForm');
            const btn = document.getElementById('registerBtn');
            const btnText = document.querySelector('.btn-text');
            const loading = document.querySelector('.loading');
            const alertContainer = document.getElementById('alertContainer');
            
            // Elementos del formulario
            const nameInput = document.getElementById('name');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');
            
            // Validación de fortaleza de contraseña
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                const strengthElement = document.getElementById('passwordStrength');
                
                if (password.length === 0) {
                    strengthElement.textContent = '';
                    return;
                }
                
                let strength = 0;
                const checks = [
                    password.length >= 8,
                    /[a-z]/.test(password),
                    /[A-Z]/.test(password),
                    /\d/.test(password),
                    /[!@#$%^&*]/.test(password)
                ];
                
                strength = checks.filter(check => check).length;
                
                if (strength < 3) {
                    strengthElement.textContent = 'Contraseña débil';
                    strengthElement.className = 'password-strength strength-weak';
                } else if (strength < 4) {
                    strengthElement.textContent = 'Contraseña media';
                    strengthElement.className = 'password-strength strength-medium';
                } else {
                    strengthElement.textContent = 'Contraseña fuerte';
                    strengthElement.className = 'password-strength strength-strong';
                }
            });

            // Validación de confirmación de contraseña
            confirmPasswordInput.addEventListener('input', function() {
                const confirmError = document.getElementById('confirmError');
                const confirmGroup = this.closest('.form-group');
                
                if (this.value !== passwordInput.value) {
                    confirmGroup.classList.add('error');
                    confirmError.style.display = 'block';
                    confirmError.textContent = 'Las contraseñas no coinciden';
                } else {
                    confirmGroup.classList.remove('error');
                    confirmError.style.display = 'none';
                }
            });

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                // Validaciones básicas
                let hasErrors = false;
                
                // Limpiar errores anteriores
                document.querySelectorAll('.form-group').forEach(group => group.classList.remove('error'));
                document.querySelectorAll('.error-message').forEach(error => error.style.display = 'none');
                alertContainer.innerHTML = '';

                // Validar nombre
                if (nameInput.value.trim().length < 2) {
                    showFieldError('name', 'El nombre debe tener al menos 2 caracteres');
                    hasErrors = true;
                }

                // Validar contraseña
                if (passwordInput.value.length < 8) {
                    showFieldError('password', 'La contraseña debe tener al menos 8 caracteres');
                    hasErrors = true;
                }

                // Validar confirmación
                if (passwordInput.value !== confirmPasswordInput.value) {
                    showFieldError('password_confirmation', 'Las contraseñas no coinciden');
                    hasErrors = true;
                }

                if (hasErrors) return;
                
                // Mostrar loading
                btnText.style.display = 'none';
                loading.style.display = 'flex';
                btn.disabled = true;

                const formData = {
                    name: nameInput.value.trim(),
                    email: emailInput.value.trim(),
                    password: passwordInput.value,
                    password_confirmation: confirmPasswordInput.value
                };

                try {
                    const response = await fetch('/api/auth/registro', {
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
                        // Manejar errores de validación
                        if (data.errores) {
                            for (const [field, messages] of Object.entries(data.errores)) {
                                showFieldError(field, messages[0]);
                            }
                        } else {
                            throw new Error(data.mensaje || 'Error en el registro');
                        }
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

            function showFieldError(fieldName, message) {
                const field = document.getElementById(fieldName);
                const errorElement = document.getElementById(fieldName + 'Error');
                const formGroup = field.closest('.form-group');
                
                formGroup.classList.add('error');
                errorElement.style.display = 'block';
                errorElement.textContent = message;
            }
        });
    </script>
</body>
</html>
