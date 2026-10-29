<?php /* Modales compartidos: confirmar acción y asignar rol. Requiere $roles. */ ?>
<div class="modal fade" id="modalConfirmar" tabindex="-1" aria-labelledby="modalConfirmarTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <?= form_open('', ['id' => 'formConfirmar']) ?>
                <div class="modal-header border-0">
                    <h2 class="modal-title h5" id="modalConfirmarTitulo"></h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body pt-0" id="modalConfirmarTexto"></div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn rounded-pill px-3" id="modalConfirmarBoton"></button>
                </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRol" tabindex="-1" aria-labelledby="modalRolTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <?= form_open('', ['id' => 'formRol']) ?>
                <div class="modal-header border-0">
                    <h2 class="modal-title h5" id="modalRolTitulo"><i class="bi bi-person-badge text-ig-osc me-2"></i>Asignar rol</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body pt-0">
                    <p class="mb-3">Usuario: <strong id="modalRolNombre"></strong></p>
                    <label for="modalRolSelect" class="form-label">Nuevo rol</label>
                    <select class="form-select" name="idrol" id="modalRolSelect" required>
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= (int) $r['idrol'] ?>"><?= esc($r['nombre']) ?></option>
                        <?php endforeach ?>
                    </select>
                    <div class="form-text">El cambio se aplica de inmediato, incluso si la persona tiene la sesión abierta.</div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-ig px-3"><i class="bi bi-check2 me-1"></i>Asignar</button>
                </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('modalConfirmar').addEventListener('show.bs.modal', function (e) {
        const d = e.relatedTarget.dataset;
        document.getElementById('formConfirmar').action = d.url;
        document.getElementById('modalConfirmarTitulo').textContent = d.titulo;
        document.getElementById('modalConfirmarTexto').textContent = d.texto;
        const b = document.getElementById('modalConfirmarBoton');
        b.textContent = d.boton;
        b.className = 'btn rounded-pill px-3 ' + (d.estilo || 'btn-ig');
    });
    document.getElementById('modalRol').addEventListener('show.bs.modal', function (e) {
        const d = e.relatedTarget.dataset;
        document.getElementById('formRol').action = d.url;
        document.getElementById('modalRolNombre').textContent = d.nombre;
        document.getElementById('modalRolSelect').value = d.rolActual === '4' ? '3' : d.rolActual; // Invitado -> sugiere Colaborador
    });
});
</script>
