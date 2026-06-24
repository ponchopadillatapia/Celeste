<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHAT SUPER-PONCHO</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #050508; color: #e0e0e8; min-height: 100vh; }
        .container { max-width: 900px; margin: 0 auto; padding: 20px; }
        h1 { text-align: center; margin-bottom: 4px; color: #00f0ff; font-size: 1.8rem; text-shadow: 0 0 20px #00f0ff66, 0 0 40px #00f0ff22; }
        .subtitle { text-align: center; color: #555; margin-bottom: 24px; font-size: 0.9rem; }
        .card { background: #0a0a12; border-radius: 12px; padding: 24px; border: 1px solid #1a1a2e; margin-bottom: 16px; box-shadow: 0 0 15px #00f0ff08; }
        .card h2 { color: #00f0ff; margin-bottom: 16px; font-size: 1.1rem; display: flex; align-items: center; gap: 8px; text-shadow: 0 0 10px #00f0ff44; }
        .form-group { margin-bottom: 14px; }
        label { display: block; font-size: 0.82rem; color: #777; margin-bottom: 4px; font-weight: 500; }
        input, select, textarea { width: 100%; padding: 10px 14px; background: #08080f; border: 1px solid #1a1a2e; border-radius: 8px; color: #e0e0e8; font-size: 0.9rem; transition: all 0.3s; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #00f0ff; box-shadow: 0 0 10px #00f0ff33; }
        textarea { resize: vertical; min-height: 60px; }
        .btn { padding: 11px 20px; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 0.9rem; width: 100%; margin-top: 6px; font-weight: 500; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 6px; }
        .btn-primary { background: linear-gradient(135deg, #6e00ff, #9b30ff); box-shadow: 0 0 15px #6e00ff44; }
        .btn-primary:hover { box-shadow: 0 0 25px #6e00ff88; transform: translateY(-1px); }
        .btn-success { background: linear-gradient(135deg, #00cc6a, #00ff88); color: #050508; box-shadow: 0 0 15px #00ff8844; }
        .btn-success:hover { box-shadow: 0 0 25px #00ff8888; transform: translateY(-1px); }
        .btn-danger { background: linear-gradient(135deg, #ff0055, #ff3377); box-shadow: 0 0 15px #ff005544; }
        .btn-danger:hover { box-shadow: 0 0 25px #ff005588; transform: translateY(-1px); }
        .btn-outline { background: transparent; border: 1px solid #1a1a2e; color: #777; }
        .btn-outline:hover { border-color: #00f0ff; color: #00f0ff; box-shadow: 0 0 10px #00f0ff33; }
        .btn:disabled { opacity: 0.4; cursor: not-allowed; box-shadow: none; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .toast { position: fixed; top: 20px; right: 20px; padding: 14px 20px; border-radius: 10px; font-size: 0.9rem; z-index: 1000; animation: slideIn 0.3s ease; max-width: 400px; display: flex; align-items: center; gap: 8px; border: 1px solid transparent; }
        .toast.success { background: #0a0a12; color: #00ff88; border-color: #00ff8855; box-shadow: 0 0 20px #00ff8833; }
        .toast.error { background: #0a0a12; color: #ff3377; border-color: #ff005555; box-shadow: 0 0 20px #ff005533; }
        .toast.info { background: #0a0a12; color: #00f0ff; border-color: #00f0ff55; box-shadow: 0 0 20px #00f0ff33; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes slideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }

        /* Navbar */
        .navbar { display: flex; align-items: center; justify-content: space-between; background: #0a0a12; border-radius: 12px; padding: 12px 20px; margin-bottom: 20px; border: 1px solid #1a1a2e; box-shadow: 0 0 20px #00f0ff08; }
        .navbar .user-info { display: flex; align-items: center; gap: 10px; font-size: 0.85rem; }
        .navbar .user-info .avatar { width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #6e00ff, #00f0ff); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; color: #fff; box-shadow: 0 0 12px #6e00ff55; }
        .navbar .user-info .name { color: #e0e0e8; }
        .navbar .user-info .role { color: #555; font-size: 0.75rem; }
        .nav-tabs { display: flex; gap: 4px; }
        .nav-tab { padding: 8px 16px; background: transparent; border: none; color: #555; border-radius: 8px; cursor: pointer; font-size: 0.85rem; transition: all 0.3s; }
        .nav-tab:hover { color: #00f0ff; background: #0f0f1a; }
        .nav-tab.active { background: linear-gradient(135deg, #6e00ff, #9b30ff); color: white; box-shadow: 0 0 12px #6e00ff44; }

        /* Auth screens */
        .auth-screen { max-width: 420px; margin: 60px auto; }
        .auth-screen .card { padding: 32px; }
        .auth-toggle { text-align: center; margin-top: 16px; color: #555; font-size: 0.85rem; }
        .auth-toggle a { color: #00f0ff; cursor: pointer; text-decoration: none; text-shadow: 0 0 8px #00f0ff44; }
        .auth-toggle a:hover { text-decoration: underline; text-shadow: 0 0 12px #00f0ff88; }
        .divider { border: none; border-top: 1px solid #1a1a2e; margin: 16px 0; }

        /* Notification cards */
        .notif-card { background: #08080f; border: 1px solid #1a1a2e; border-radius: 10px; padding: 14px 16px; margin-bottom: 10px; transition: all 0.3s; cursor: pointer; }
        .notif-card:hover { border-color: #1a1a3e; box-shadow: 0 0 10px #00f0ff0a; }
        .notif-card.unread { border-left: 3px solid #00f0ff; box-shadow: 0 0 8px #00f0ff11; }
        .notif-card .notif-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
        .notif-card .notif-type { font-size: 0.7rem; padding: 2px 8px; border-radius: 4px; text-transform: uppercase; font-weight: 600; }
        .type-asamblea { background: #6e00ff22; color: #b388ff; text-shadow: 0 0 6px #6e00ff44; }
        .type-pago_atrasado { background: #ff005522; color: #ff6699; text-shadow: 0 0 6px #ff005544; }
        .type-multa { background: #ff880022; color: #ffaa44; text-shadow: 0 0 6px #ff880044; }
        .type-mensaje { background: #00f0ff22; color: #00f0ff; text-shadow: 0 0 6px #00f0ff44; }
        .notif-card .notif-title { font-weight: 600; font-size: 0.9rem; color: #e0e0e8; }
        .notif-card .notif-body { font-size: 0.82rem; color: #777; margin-top: 4px; }
        .notif-card .notif-time { font-size: 0.7rem; color: #444; }

        /* Chat */
        .chat-container { background: #08080f; border: 1px solid #1a1a2e; border-radius: 12px; height: 400px; display: flex; flex-direction: column; }
        .chat-messages { flex: 1; overflow-y: auto; padding: 16px; }
        .chat-bubble { max-width: 75%; padding: 10px 14px; border-radius: 12px; margin-bottom: 8px; font-size: 0.85rem; }
        .chat-bubble.mine { background: linear-gradient(135deg, #6e00ff, #9b30ff); color: white; margin-left: auto; border-bottom-right-radius: 4px; box-shadow: 0 0 12px #6e00ff33; }
        .chat-bubble.other { background: #0f0f1a; border: 1px solid #1a1a2e; color: #e0e0e8; border-bottom-left-radius: 4px; }
        .chat-bubble .chat-sender { font-size: 0.7rem; opacity: 0.6; margin-bottom: 2px; }
        .chat-input-bar { display: flex; gap: 8px; padding: 12px; border-top: 1px solid #1a1a2e; }
        .chat-input-bar input { flex: 1; }
        .chat-input-bar button { width: auto; margin: 0; padding: 10px 20px; }

        /* Surveys */
        .survey-card { background: #08080f; border: 1px solid #1a1a2e; border-radius: 12px; padding: 20px; margin-bottom: 14px; }
        .survey-card h3 { color: #e0e0e8; font-size: 1rem; margin-bottom: 4px; }
        .survey-card .survey-desc { color: #555; font-size: 0.82rem; margin-bottom: 14px; }
        .survey-card .survey-creator { font-size: 0.75rem; color: #444; margin-bottom: 12px; }
        .survey-option { display: flex; align-items: center; gap: 10px; padding: 10px 14px; background: #0a0a12; border: 1px solid #1a1a2e; border-radius: 8px; margin-bottom: 6px; cursor: pointer; transition: all 0.3s; }
        .survey-option:hover { border-color: #00f0ff55; box-shadow: 0 0 8px #00f0ff11; }
        .survey-option.voted { border-color: #00ff8855; background: #00ff8808; box-shadow: 0 0 10px #00ff8822; }
        .survey-option .option-text { flex: 1; font-size: 0.85rem; }
        .survey-option .option-votes { font-size: 0.8rem; color: #555; white-space: nowrap; }
        .survey-bar { height: 4px; background: #1a1a2e; border-radius: 2px; margin-top: 6px; overflow: hidden; }
        .survey-bar .fill { height: 100%; background: linear-gradient(90deg, #00f0ff, #6e00ff); border-radius: 2px; transition: width 0.4s; box-shadow: 0 0 8px #00f0ff44; }
        .empty-state { text-align: center; padding: 40px; color: #444; font-size: 0.9rem; }

        .section { display: none; }
        .section.active { display: block; }
        .hidden { display: none; }

        @media (max-width: 700px) {
            .grid-2 { grid-template-columns: 1fr; }
            .nav-tabs { flex-wrap: wrap; }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>🏢 CHAT SUPER-PONCHO</h1>
    <p class="subtitle">Sistema de gestión residencial</p>

    <!-- ========== AUTH SCREENS ========== -->
    <div id="auth-wrapper">
        <!-- LOGIN -->
        <div id="screen-login" class="auth-screen">
            <div class="card">
                <h2>👋 Bienvenido</h2>
                <div class="form-group">
                    <label>Correo electrónico</label>
                    <input type="email" id="loginEmail" value="admin@superponcho.com" placeholder="tu@correo.com">
                </div>
                <div class="form-group">
                    <label>Contraseña</label>
                    <input type="password" id="loginPassword" value="password123" placeholder="••••••••">
                </div>
                <button class="btn btn-success" onclick="doLogin()" id="btnLogin">Iniciar Sesión</button>
                <hr class="divider">
                <p class="auth-toggle">¿No tienes cuenta? <a onclick="showAuth('register')">Regístrate aquí</a></p>
                <p class="auth-toggle" style="margin-top:8px;"><a onclick="showAuth('forgot')">¿Olvidaste tu contraseña?</a></p>
            </div>

        </div>

        <!-- REGISTER -->
        <div id="screen-register" class="auth-screen hidden">
            <div class="card">
                <h2>📝 Crear Cuenta</h2>
                <div class="form-group">
                    <label>Nombre completo</label>
                    <input type="text" id="regName" placeholder="Juan Pérez">
                </div>
                <div class="form-group">
                    <label>Correo electrónico</label>
                    <input type="email" id="regEmail" placeholder="tu@correo.com">
                </div>
                <div class="form-group">
                    <label>Contraseña</label>
                    <input type="password" id="regPassword" placeholder="Mínimo 8 caracteres">
                </div>
                <div class="form-group">
                    <label>Confirmar contraseña</label>
                    <input type="password" id="regPasswordConfirm" placeholder="Repetir contraseña">
                </div>
                <div class="form-group">
                    <label>Departamento / Torre</label>
                    <input type="text" id="regDept" placeholder="Ej: Torre A">
                </div>
                <button class="btn btn-primary" onclick="doRegister()">Crear Cuenta</button>
                <hr class="divider">
                <p class="auth-toggle"><a onclick="showAuth('login')">← Volver al login</a></p>
            </div>
        </div>

        <!-- VERIFY EMAIL -->
        <div id="screen-verify" class="auth-screen hidden">
            <div class="card" style="text-align:center;">
                <h2 style="justify-content:center;">✉️ Verifica tu correo</h2>
                <p style="color:#94a3b8;font-size:0.85rem;margin-bottom:20px;">Ingresa el código de 6 dígitos que recibiste en <strong id="verifyEmailDisplay" style="color:#00f0ff;"></strong></p>
                <div style="display:flex;gap:8px;justify-content:center;margin-bottom:16px;">
                    <input type="text" id="code1" maxlength="1" style="width:48px;height:56px;text-align:center;font-size:1.4rem;font-weight:700;" oninput="codeNext(1)">
                    <input type="text" id="code2" maxlength="1" style="width:48px;height:56px;text-align:center;font-size:1.4rem;font-weight:700;" oninput="codeNext(2)">
                    <input type="text" id="code3" maxlength="1" style="width:48px;height:56px;text-align:center;font-size:1.4rem;font-weight:700;" oninput="codeNext(3)">
                    <input type="text" id="code4" maxlength="1" style="width:48px;height:56px;text-align:center;font-size:1.4rem;font-weight:700;" oninput="codeNext(4)">
                    <input type="text" id="code5" maxlength="1" style="width:48px;height:56px;text-align:center;font-size:1.4rem;font-weight:700;" oninput="codeNext(5)">
                    <input type="text" id="code6" maxlength="1" style="width:48px;height:56px;text-align:center;font-size:1.4rem;font-weight:700;" oninput="codeNext(6)">
                </div>
                <button class="btn btn-success" onclick="doVerify()">Verificar</button>
                <hr class="divider">
                <p class="auth-toggle"><a onclick="showAuth('login')">← Volver al login</a></p>
            </div>
        </div>

        <!-- FORGOT PASSWORD -->
        <div id="screen-forgot" class="auth-screen hidden">
            <div class="card">
                <h2>🔒 Recuperar Contraseña</h2>
                <div id="forgot-step1">
                    <p style="color:#94a3b8;font-size:0.85rem;margin-bottom:16px;">Ingresa tu correo y te enviaremos un código de recuperación.</p>
                    <div class="form-group">
                        <label>Correo electrónico</label>
                        <input type="email" id="forgotEmail" placeholder="tu@correo.com">
                    </div>
                    <button class="btn btn-primary" onclick="doForgot()">Enviar Código</button>
                </div>
                <div id="forgot-step2" class="hidden">
                    <p style="color:#94a3b8;font-size:0.85rem;margin-bottom:16px;">Ingresa el código y tu nueva contraseña.</p>
                    <div class="form-group">
                        <label>Código (6 dígitos)</label>
                        <input type="text" id="resetCode" placeholder="123456" maxlength="6">
                    </div>
                    <div class="form-group">
                        <label>Nueva contraseña</label>
                        <input type="password" id="resetPassword" placeholder="Mínimo 8 caracteres">
                    </div>
                    <div class="form-group">
                        <label>Confirmar nueva contraseña</label>
                        <input type="password" id="resetPasswordConfirm" placeholder="Repetir contraseña">
                    </div>
                    <button class="btn btn-success" onclick="doReset()">Cambiar Contraseña</button>
                </div>
                <hr class="divider">
                <p class="auth-toggle"><a onclick="showAuth('login')">← Volver al login</a></p>
            </div>
        </div>
    </div>

    <!-- ========== MAIN APP (after login) ========== -->
    <div id="app-wrapper" class="hidden">
        <div class="navbar">
            <div class="user-info">
                <div class="avatar" id="userAvatar">A</div>
                <div>
                    <div class="name" id="userName">Admin</div>
                    <div class="role" id="userRole">admin · Administración</div>
                </div>
            </div>
            <div class="nav-tabs">
                <button class="nav-tab active" onclick="showTab('notifications', this)">🔔 Notificaciones</button>
                <button class="nav-tab" onclick="showTab('chat', this)">💬 Chat</button>
                <button class="nav-tab" onclick="showTab('surveys', this)">📊 Encuestas</button>
                <button class="nav-tab" id="adminTab" onclick="showTab('admin', this)" style="display:none;">⚙️ Admin</button>
            </div>
            <button class="btn btn-outline" style="width:auto;padding:8px 14px;margin:0;font-size:0.8rem;" onclick="doLogout()">Salir</button>
        </div>

        <!-- NOTIFICATIONS TAB -->
        <div id="tab-notifications" class="section active">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                <h2 style="color:#e0e0e8;font-size:1rem;">🔔 Notificaciones <span id="unreadBadge" style="background:linear-gradient(135deg,#6e00ff,#9b30ff);color:white;padding:2px 8px;border-radius:10px;font-size:0.75rem;"></span></h2>
                <button class="btn btn-outline" style="width:auto;padding:6px 14px;margin:0;font-size:0.8rem;" onclick="markAllRead()">Marcar todas leídas</button>
            </div>
            <div id="notifList"></div>
        </div>

        <!-- CHAT TAB -->
        <div id="tab-chat" class="section">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                <div class="grid-2" style="flex:1;margin:0;">
                    <div class="form-group" style="margin:0;">
                        <label>Chatear con departamento:</label>
                        <select id="chatDept" onchange="loadChat()" style="margin-top:4px;">
                            <option value="">Cargando departamentos...</option>
                        </select>
                    </div>
                </div>
                <div id="liveIndicator" style="display:flex;align-items:center;gap:6px;font-size:0.78rem;color:#00ff88;margin-left:12px;">
                    <span style="width:8px;height:8px;background:#00ff88;border-radius:50%;display:inline-block;animation:pulse 1.5s infinite;box-shadow:0 0 6px #00ff88;"></span> En vivo
                </div>
            </div>
            <style>@keyframes pulse { 0%,100% { opacity:1; } 50% { opacity:0.3; } }</style>
            <div class="chat-container">
                <div class="chat-messages" id="chatMessages">
                    <div class="empty-state">Selecciona un departamento para ver los mensajes</div>
                </div>
                <div class="chat-input-bar">
                    <input type="text" id="chatInput" placeholder="Escribe un mensaje..." onkeydown="if(event.key==='Enter')sendChat()">
                    <button class="btn btn-primary" onclick="sendChat()">Enviar</button>
                </div>
            </div>
        </div>

        <!-- SURVEYS TAB -->
        <div id="tab-surveys" class="section">
            <h2 style="color:#e0e0e8;font-size:1rem;margin-bottom:14px;">📊 Encuestas Activas</h2>
            <div id="surveyList"></div>
        </div>

        <!-- ADMIN TAB -->
        <div id="tab-admin" class="section">
            <div class="grid-2">
                <div class="card">
                    <h2>📤 Enviar Notificación</h2>
                    <div class="form-group">
                        <label>Usuario destino (ID)</label>
                        <input type="number" id="notifUserId" value="2" min="1">
                    </div>
                    <div class="form-group">
                        <label>Tipo</label>
                        <select id="notifType">
                            <option value="mensaje">Mensaje</option>
                            <option value="multa">Multa</option>
                            <option value="asamblea">Asamblea</option>
                            <option value="pago_atrasado">Pago Atrasado</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Título</label>
                        <input type="text" id="notifTitle" value="Aviso importante">
                    </div>
                    <div class="form-group">
                        <label>Mensaje</label>
                        <textarea id="notifBody">Este es un mensaje de prueba.</textarea>
                    </div>
                    <button class="btn btn-primary" onclick="createNotification()">Enviar Notificación</button>
                </div>
                <div class="card">
                    <h2>➕ Crear Encuesta</h2>
                    <div class="form-group">
                        <label>Título</label>
                        <input type="text" id="surveyTitle" value="¿Instalar cámaras de seguridad?">
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea id="surveyDesc">Votación para instalar cámaras en áreas comunes.</textarea>
                    </div>
                    <div class="form-group">
                        <label>Opciones (una por línea)</label>
                        <textarea id="surveyOptions" style="min-height:80px;">Sí, instalar
No, no es necesario
Necesito más información</textarea>
                    </div>
                    <button class="btn btn-primary" onclick="createSurvey()">Crear Encuesta</button>
                </div>
            </div>
        </div>
    </div>

<script>
const BASE = '/api';
let token = '';
let currentUser = null;

// ========== TOAST ==========
function toast(msg, type = 'info') {
    const t = document.createElement('div');
    t.className = 'toast ' + type;
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(() => { t.style.animation = 'slideOut 0.3s ease forwards'; setTimeout(() => t.remove(), 300); }, 3000);
}

// ========== AUTH SCREENS ==========
function showAuth(screen) {
    ['login','register','verify','forgot'].forEach(s => {
        document.getElementById('screen-' + s).classList.add('hidden');
    });
    document.getElementById('screen-' + screen).classList.remove('hidden');
}

function codeNext(n) {
    const val = document.getElementById('code' + n).value;
    if (val && n < 6) document.getElementById('code' + (n + 1)).focus();
    if (n === 6 && val) doVerify();
}

// ========== API HELPER ==========
async function api(method, url, body = null) {
    const opts = { method, headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' } };
    if (token) opts.headers['Authorization'] = 'Bearer ' + token;
    if (body) opts.body = JSON.stringify(body);
    const res = await fetch(BASE + url, opts);
    const data = await res.json();
    return { ok: res.ok, status: res.status, data };
}

// ========== AUTH ==========
async function doLogin() {
    const btn = document.getElementById('btnLogin');
    btn.disabled = true; btn.textContent = 'Ingresando...';
    const { ok, data } = await api('POST', '/auth/login', {
        email: document.getElementById('loginEmail').value,
        password: document.getElementById('loginPassword').value,
        device_name: 'Navegador Web',
    });
    btn.disabled = false; btn.textContent = 'Iniciar Sesión';
    if (!ok) { toast(data.message || Object.values(data.errors || {}).flat().join(', '), 'error'); return; }
    token = data.token;
    currentUser = data.user;
    enterApp();
}

let pendingEmail = '';
let pendingCode = '';

async function doRegister() {
    const { ok, data } = await api('POST', '/auth/register', {
        name: document.getElementById('regName').value,
        email: document.getElementById('regEmail').value,
        password: document.getElementById('regPassword').value,
        password_confirmation: document.getElementById('regPasswordConfirm').value,
        department: document.getElementById('regDept').value,
    });
    if (!ok) { toast(data.message || Object.values(data.errors || {}).flat().join(', '), 'error'); return; }
    if (data.requires_verification) {
        pendingEmail = document.getElementById('regEmail').value;
        document.getElementById('verifyEmailDisplay').textContent = pendingEmail;
        toast('Cuenta creada. Revisa tu correo para el código.', 'success');
        showAuth('verify');
    } else {
        toast('Cuenta creada. Ya puedes iniciar sesión.', 'success');
        document.getElementById('loginEmail').value = document.getElementById('regEmail').value;
        document.getElementById('loginPassword').value = document.getElementById('regPassword').value;
        showAuth('login');
    }
}

async function doVerify() {
    const code = [1,2,3,4,5,6].map(i => document.getElementById('code' + i).value).join('');
    if (code.length < 6) { toast('Ingresa el código completo', 'error'); return; }
    const { ok, data } = await api('POST', '/auth/verify-email', { email: pendingEmail, code });
    if (!ok) { toast(data.message || 'Código inválido', 'error'); return; }
    if (data.token) {
        token = data.token;
        currentUser = data.user;
        toast('Correo verificado. Bienvenido.', 'success');
        enterApp();
    } else {
        toast('Correo verificado. Ya puedes iniciar sesión.', 'success');
        document.getElementById('loginEmail').value = pendingEmail;
        showAuth('login');
    }
}

async function doForgot() {
    const email = document.getElementById('forgotEmail').value;
    if (!email) { toast('Ingresa tu correo', 'error'); return; }
    const { ok, data } = await api('POST', '/auth/forgot-password', { email });
    pendingEmail = email;
    if (data.mail_sent) {
        toast('Código enviado a tu correo. Revisa tu bandeja.', 'success');
        document.getElementById('forgot-step1').classList.add('hidden');
        document.getElementById('forgot-step2').classList.remove('hidden');
    } else {
        toast(data.message || 'No se pudo enviar el correo. Configura SMTP en .env', 'error');
    }
}

async function doReset() {
    const { ok, data } = await api('POST', '/auth/reset-password', {
        email: pendingEmail,
        code: document.getElementById('resetCode').value,
        password: document.getElementById('resetPassword').value,
        password_confirmation: document.getElementById('resetPasswordConfirm').value,
    });
    if (!ok) { toast(data.message || 'Error al cambiar contraseña', 'error'); return; }
    toast('Contraseña cambiada. Inicia sesión.', 'success');
    showAuth('login');
}

async function doLogout() {
    stopChatPolling();
    await api('POST', '/auth/logout');
    token = ''; currentUser = null;
    document.getElementById('auth-wrapper').classList.remove('hidden');
    document.getElementById('app-wrapper').classList.add('hidden');
    toast('Sesión cerrada', 'info');
}
</script>

<script>
// ========== ENTER APP ==========
function enterApp() {
    document.getElementById('auth-wrapper').classList.add('hidden');
    document.getElementById('app-wrapper').classList.remove('hidden');
    document.getElementById('userName').textContent = currentUser.name;
    document.getElementById('userRole').textContent = currentUser.role + ' · ' + (currentUser.department || 'Sin depto.');
    document.getElementById('userAvatar').textContent = currentUser.name.charAt(0).toUpperCase();
    if (currentUser.role === 'admin') document.getElementById('adminTab').style.display = '';
    else document.getElementById('adminTab').style.display = 'none';
    loadNotifications();
    loadSurveys();
    loadDepartments();
}

// ========== TABS ==========
function showTab(name, btn) {
    document.querySelectorAll('#app-wrapper .section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    if (btn) btn.classList.add('active');
    stopChatPolling();
    if (name === 'notifications') loadNotifications();
    if (name === 'chat') { loadChat(); startChatPolling(); }
    if (name === 'surveys') loadSurveys();
}

// ========== NOTIFICATIONS ==========
async function loadNotifications() {
    const { ok, data } = await api('GET', '/notifications');
    const countRes = await api('GET', '/notifications/unread-count');
    document.getElementById('unreadBadge').textContent = countRes.data.unread_count > 0 ? countRes.data.unread_count + ' nuevas' : '';
    const list = document.getElementById('notifList');
    const items = data.data || [];
    if (!items.length) { list.innerHTML = '<div class="empty-state">No tienes notificaciones</div>'; return; }
    list.innerHTML = items.map(n => `
        <div class="notif-card ${n.read ? '' : 'unread'}" onclick="markNotifRead(${n.id}, this)">
            <div class="notif-header">
                <span class="notif-type type-${n.type}">${n.type.replace('_',' ')}</span>
                <span class="notif-time">${new Date(n.created_at).toLocaleString('es')}</span>
            </div>
            <div class="notif-title">${n.title}</div>
            <div class="notif-body">${n.body}</div>
        </div>
    `).join('');
}

async function markNotifRead(id, el) {
    await api('PATCH', '/notifications/' + id + '/read');
    el.classList.remove('unread');
    const countRes = await api('GET', '/notifications/unread-count');
    document.getElementById('unreadBadge').textContent = countRes.data.unread_count > 0 ? countRes.data.unread_count + ' nuevas' : '';
}

async function markAllRead() {
    await api('PATCH', '/notifications/read-all');
    toast('Todas marcadas como leídas', 'success');
    loadNotifications();
}

// ========== CHAT ==========
let chatInterval = null;

async function loadDepartments() {
    const { ok, data } = await api('GET', '/departments');
    const select = document.getElementById('chatDept');
    if (!ok || !data.length) { select.innerHTML = '<option value="">No hay departamentos</option>'; return; }
    select.innerHTML = data.map(d => '<option value="' + d + '">' + d + '</option>').join('');
}

function startChatPolling() {
    stopChatPolling();
    chatInterval = setInterval(loadChat, 3000);
}

function stopChatPolling() {
    if (chatInterval) { clearInterval(chatInterval); chatInterval = null; }
}

async function loadChat() {
    const dept = document.getElementById('chatDept').value;
    if (!dept) return;
    const { ok, data } = await api('GET', '/chat?department=' + encodeURIComponent(dept));
    const container = document.getElementById('chatMessages');
    const items = data.data || [];
    const wasAtBottom = container.scrollHeight - container.scrollTop - container.clientHeight < 50;
    if (!items.length) { container.innerHTML = '<div class="empty-state">No hay mensajes con ' + dept + '. Envía el primero.</div>'; return; }
    container.innerHTML = items.map(m => `
        <div class="chat-bubble ${m.user_id === currentUser.id ? 'mine' : 'other'}">
            ${m.user_id !== currentUser.id ? '<div class="chat-sender">' + (m.user ? m.user.name : 'Usuario') + '</div>' : ''}
            ${m.message}
            <div style="font-size:0.65rem;opacity:0.5;margin-top:4px;text-align:right;">${new Date(m.created_at).toLocaleTimeString('es', {hour:'2-digit',minute:'2-digit'})}</div>
        </div>
    `).join('');
    if (wasAtBottom) container.scrollTop = container.scrollHeight;
}

async function sendChat() {
    const msg = document.getElementById('chatInput').value.trim();
    if (!msg) return;
    const dept = document.getElementById('chatDept').value;
    const { ok, data } = await api('POST', '/chat', { department_to: dept, message: msg });
    if (!ok) { toast(data.message || 'Error al enviar', 'error'); return; }
    document.getElementById('chatInput').value = '';
    loadChat();
}

// ========== SURVEYS ==========
async function loadSurveys() {
    const { ok, data } = await api('GET', '/surveys');
    const list = document.getElementById('surveyList');
    const items = data.data || [];
    if (!items.length) { list.innerHTML = '<div class="empty-state">No hay encuestas activas</div>'; return; }
    list.innerHTML = items.map(s => {
        const total = s.options.reduce((sum, o) => sum + (o.votes_count || 0), 0);
        return `
        <div class="survey-card">
            <h3>${s.title}</h3>
            ${s.description ? '<div class="survey-desc">' + s.description + '</div>' : ''}
            <div class="survey-creator">Creada por ${s.creator ? s.creator.name : 'Admin'}</div>
            ${s.options.map(o => {
                const pct = total > 0 ? Math.round((o.votes_count / total) * 100) : 0;
                return `
                <div class="survey-option" onclick="voteSurvey(${s.id}, ${o.id}, this)">
                    <span class="option-text">${o.option_text}</span>
                    <span class="option-votes">${o.votes_count || 0} votos (${pct}%)</span>
                </div>
                <div class="survey-bar"><div class="fill" style="width:${pct}%"></div></div>`;
            }).join('')}
            ${currentUser && currentUser.role === 'admin' ? '<button class="btn btn-danger" style="margin-top:12px;" onclick="closeSurvey(' + s.id + ')">Cerrar Encuesta</button>' : ''}
        </div>`;
    }).join('');
}

async function voteSurvey(surveyId, optionId, el) {
    const { ok, data } = await api('POST', '/surveys/' + surveyId + '/vote', { option_id: optionId });
    if (!ok) { toast(data.message || 'Error al votar', 'error'); return; }
    el.classList.add('voted');
    toast('Voto registrado', 'success');
    loadSurveys();
}

async function closeSurvey(id) {
    const { ok } = await api('PATCH', '/surveys/' + id + '/close');
    if (ok) { toast('Encuesta cerrada', 'success'); loadSurveys(); }
}

// ========== ADMIN ==========
async function createNotification() {
    const { ok, data } = await api('POST', '/notifications', {
        user_id: parseInt(document.getElementById('notifUserId').value),
        type: document.getElementById('notifType').value,
        title: document.getElementById('notifTitle').value,
        body: document.getElementById('notifBody').value,
    });
    if (!ok) { toast(data.message || Object.values(data.errors || {}).flat().join(', '), 'error'); return; }
    toast('Notificación enviada', 'success');
}

async function createSurvey() {
    const options = document.getElementById('surveyOptions').value.split('\n').filter(o => o.trim());
    const { ok, data } = await api('POST', '/surveys', {
        title: document.getElementById('surveyTitle').value,
        description: document.getElementById('surveyDesc').value,
        options,
    });
    if (!ok) { toast(data.message || Object.values(data.errors || {}).flat().join(', '), 'error'); return; }
    toast('Encuesta creada', 'success');
    loadSurveys();
}
</script>
</div>
</body>
</html>
