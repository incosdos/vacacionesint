<?= $this->extend('layouts/main') ?>

<?= $this->section('contenido') ?>

<!-- ===================== HERO ===================== -->
<section id="inicio" class="ig-hero">
    <div id="heroCarousel" class="carousel slide carousel-fade h-100" data-bs-ride="carousel" data-bs-interval="6000">
        <div class="carousel-inner h-100">
            <?php foreach (['banner1', 'banner2', 'banner3'] as $i => $b): ?>
                <div class="carousel-item h-100 <?= $i === 0 ? 'active' : '' ?>">
                    <div class="ig-hero-bg" style="background-image:url('<?= ig_img($b) ?>')"></div>
                </div>
            <?php endforeach ?>
        </div>
    </div>

    <div class="ig-hero-overlay"></div>

    <div class="container ig-hero-contenido">
        <div class="row">
            <div class="col-lg-8 col-xl-7">
                <span class="ig-pill mb-3">Diseño gráfico · Outsourcing · Producción digital</span>
                <h1 class="display-4 fw-bold text-white mb-3">
                    Hola, somos <span class="text-ig">INTERGRAPHICS</span>
                </h1>
                <p class="lead text-white-50 mb-4"><?= esc($empresa->eslogan) ?></p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="#servicios" class="btn btn-ig btn-lg px-4">Nuestros servicios</a>
                    <a href="#contacto" class="btn btn-outline-light btn-lg px-4">Contáctenos</a>
                </div>
            </div>
        </div>
    </div>

    <a href="#cifras" class="ig-scroll" aria-label="Bajar"><i class="bi bi-chevron-down"></i></a>
</section>

<!-- ===================== CIFRAS ===================== -->
<section id="cifras" class="ig-cifras">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3">
                <div class="ig-cifra">2005</div>
                <div class="ig-cifra-texto">Año de fundación en Cochabamba</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="ig-cifra">+15</div>
                <div class="ig-cifra-texto">Años de experiencia</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="ig-cifra">2</div>
                <div class="ig-cifra-texto">Países: Bolivia y Dinamarca</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="ig-cifra">8</div>
                <div class="ig-cifra-texto">Líneas de servicio</div>
            </div>
        </div>
    </div>
</section>

<!-- ===================== NOSOTROS ===================== -->
<section id="nosotros" class="ig-seccion">
    <div class="container">
        <div class="row g-4 g-lg-5 align-items-center">
            <div class="col-lg-6">
                <div class="ig-img-marco">
                    <img src="<?= ig_img('magasin') ?>" class="img-fluid rounded-4 shadow" alt="Equipo de diseño INTERGRAPHICS" loading="lazy">
                    <div class="ig-img-badge">
                        <i class="bi bi-globe-americas"></i>
                        <div><strong>Cochabamba</strong><br><small>Bolivia · Dinamarca</small></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <span class="ig-subtitulo">Quiénes somos</span>
                <h2 class="ig-titulo">Un equipo internacional de diseñadores y desarrolladores</h2>
                <p>
                    <strong><?= esc($empresa->razonSocial) ?></strong> nació en 2005 en Cochabamba como
                    una colaboración entre Bolivia y Dinamarca. Lo que comenzó como un proyecto DANIDA es hoy
                    una empresa independiente y rentable, especializada en producción gráfica para periódicos,
                    agencias y el sector digital.
                </p>
                <p>
                    Desde julio de 2022, JFM-grafisk (Dinamarca) e Inter-Graphics (Bolivia) trabajan juntos
                    bajo el nombre <strong>INTERGRAPHICS</strong>, uniendo la experiencia creativa danesa con el
                    talento y la capacidad de producción de nuestro equipo en Cochabamba.
                </p>

                <div class="row g-3 mt-2">
                    <div class="col-sm-6">
                        <div class="ig-valor">
                            <i class="bi bi-bullseye"></i>
                            <div>
                                <h6 class="mb-1">Misión</h6>
                                <small>Crear soluciones gráficas bien pensadas, con alto orgullo profesional y en estrecha colaboración con nuestros clientes.</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="ig-valor">
                            <i class="bi bi-eye"></i>
                            <div>
                                <h6 class="mb-1">Visión</h6>
                                <small>Ser el socio internacional de referencia en producción gráfica y digital para los sectores de medios y publicidad.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================== SERVICIOS ===================== -->
<section id="servicios" class="ig-seccion ig-fondo-claro">
    <div class="container">
        <div class="text-center mb-5">
            <span class="ig-subtitulo">Lo que hacemos</span>
            <h2 class="ig-titulo">Nuestros servicios</h2>
            <p class="text-muted mx-auto" style="max-width:640px">
                Producción gráfica, diseño publicitario, marketing en línea, desarrollo web y estrategia digital.
            </p>
        </div>

        <div class="row g-4">
            <?php foreach ($servicios as $s): ?>
                <div class="col-sm-6 col-lg-3">
                    <article class="ig-servicio h-100">
                        <div class="ig-servicio-img">
                            <img src="<?= ig_img($s['img']) ?>" alt="<?= esc($s['titulo']) ?>" loading="lazy">
                            <span class="ig-servicio-icono"><i class="bi <?= esc($s['icono']) ?>"></i></span>
                        </div>
                        <div class="p-4">
                            <h5 class="fw-semibold"><?= esc($s['titulo']) ?></h5>
                            <p class="small text-muted mb-0"><?= esc($s['texto']) ?></p>
                        </div>
                    </article>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</section>

<!-- ===================== PROYECTOS ===================== -->
<section id="proyectos" class="ig-seccion">
    <div class="container">
        <div class="text-center mb-4">
            <span class="ig-subtitulo">Casos</span>
            <h2 class="ig-titulo">Proyectos destacados</h2>
        </div>

        <div class="d-flex flex-wrap justify-content-center gap-2 mb-4" id="filtrosProyectos">
            <button class="btn btn-sm ig-filtro active" data-filtro="todos">Todos</button>
            <button class="btn btn-sm ig-filtro" data-filtro="banners">Banners digitales</button>
            <button class="btn btn-sm ig-filtro" data-filtro="revistas">Revistas</button>
            <button class="btn btn-sm ig-filtro" data-filtro="impresos">Impresos</button>
            <button class="btn btn-sm ig-filtro" data-filtro="web">Web</button>
            <button class="btn btn-sm ig-filtro" data-filtro="anuncios">Anuncios</button>
        </div>

        <div class="row g-4">
            <?php foreach ($proyectos as $p): ?>
                <div class="col-sm-6 col-lg-3 ig-proyecto" data-cat="<?= esc($p['cat']) ?>">
                    <figure class="ig-caso">
                        <img src="<?= ig_img($p['img']) ?>" alt="<?= esc($p['titulo']) ?>" loading="lazy">
                        <figcaption>
                            <span class="badge bg-light text-dark mb-2"><?= esc($p['etiqueta']) ?></span>
                            <h6 class="text-white mb-0"><?= esc($p['titulo']) ?></h6>
                        </figcaption>
                    </figure>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</section>

<!-- ===================== CTA ===================== -->
<section class="ig-cta">
    <div class="container text-center">
        <h2 class="fw-bold text-white mb-3">¿Tiene un proyecto en mente?</h2>
        <p class="text-white-50 mb-4">Creamos conceptos creativos con alto orgullo profesional y en estrecha colaboración con nuestros clientes.</p>
        <a href="#contacto" class="btn btn-light btn-lg px-5">Hablemos</a>
    </div>
</section>

<!-- ===================== CONTACTO ===================== -->
<section id="contacto" class="ig-seccion">
    <div class="container">
        <div class="text-center mb-5">
            <span class="ig-subtitulo">Contacto</span>
            <h2 class="ig-titulo">Estamos para ayudarle</h2>
        </div>

        <div class="row g-4">
            <div class="col-lg-5">
                <?php foreach ($empresa->oficinas as $o): ?>
                    <div class="ig-oficina mb-4">
                        <h5 class="fw-semibold mb-3"><i class="bi bi-geo-alt-fill text-ig-osc me-2"></i>Oficina <?= esc($o['pais']) ?></h5>
                        <p class="mb-2"><i class="bi bi-building me-2 text-muted"></i><?= esc($o['direccion']) ?></p>
                        <?php if ($o['telefono'] !== ''): ?>
                            <p class="mb-2"><i class="bi bi-telephone me-2 text-muted"></i>
                                <a href="tel:<?= esc(preg_replace('/[^0-9+]/', '', $o['telefono'])) ?>"><?= esc($o['telefono']) ?></a></p>
                        <?php endif ?>
                        <?php if ($o['correo'] !== ''): ?>
                            <p class="mb-0"><i class="bi bi-envelope me-2 text-muted"></i>
                                <a href="mailto:<?= esc($o['correo']) ?>"><?= esc($o['correo']) ?></a></p>
                        <?php endif ?>
                    </div>
                <?php endforeach ?>
            </div>

            <div class="col-lg-7">
                <form id="formContacto" class="ig-form p-4 p-md-5" novalidate>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="cNombre">Nombre</label>
                            <input type="text" class="form-control" id="cNombre" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="cCorreo">Correo electrónico</label>
                            <input type="email" class="form-control" id="cCorreo" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="cAsunto">Asunto</label>
                            <input type="text" class="form-control" id="cAsunto">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="cMensaje">Mensaje</label>
                            <textarea class="form-control" id="cMensaje" rows="5" required></textarea>
                        </div>
                        <div class="col-12">
                            <div id="contactoAviso" class="alert d-none" role="alert"></div>
                            <button type="submit" class="btn btn-ig px-5">Enviar mensaje</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
