document.addEventListener('DOMContentLoaded', function () {
    // Sombra del navbar al hacer scroll
    const nav = document.getElementById('navPrincipal');
    const alScroll = () => nav && nav.classList.toggle('reducida', window.scrollY > 40);
    window.addEventListener('scroll', alScroll);
    alScroll();

    // Cerrar el menú móvil al elegir una opción
    const menu = document.getElementById('menuPrincipal');
    if (menu) {
        menu.querySelectorAll('.nav-link').forEach(function (link) {
            link.addEventListener('click', function () {
                const c = bootstrap.Collapse.getInstance(menu);
                if (c) c.hide();
            });
        });
    }

    // Filtro de proyectos
    document.querySelectorAll('#filtrosProyectos .ig-filtro').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('#filtrosProyectos .ig-filtro').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const f = btn.dataset.filtro;
            document.querySelectorAll('.ig-proyecto').forEach(function (p) {
                const cats = p.dataset.cat.split(' ');
                p.classList.toggle('oculto', f !== 'todos' && !cats.includes(f));
            });
        });
    });

    // Formulario de contacto (solo validación visual por ahora)
    const form = document.getElementById('formContacto');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const aviso = document.getElementById('contactoAviso');
            aviso.classList.remove('d-none', 'alert-success', 'alert-danger');
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                aviso.classList.add('alert-danger');
                aviso.textContent = 'Por favor complete los campos obligatorios.';
                return;
            }
            aviso.classList.add('alert-success');
            aviso.textContent = 'El envío en línea se habilitará pronto. Mientras tanto, escríbanos al correo indicado en esta sección.';
            form.reset();
            form.classList.remove('was-validated');
        });
    }
});
