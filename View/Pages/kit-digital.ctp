<?php echo $this->Html->css(
    array(
        'pages/kit-digital.css?' . date('YmdHis')
    )
); ?>

<?php echo $this->Html->script(
    array(
        'pages/kit-digital.js?' . date('YmdHis')
    )
); ?>


<article role="main">
    <header class="jumbotron text-center">
        <h1 class="main text-center">
            <?= $this->Html->image('/images/kit-digital/banner_kit_digital.png', [
                    'class' => 'img-fluid',
                    'style' => "max-height: 100px;"
            ]) ?>
        </h1>
        <div class="bg-primary">
            <div class="container">
                <p class="lead">
                    Para PYMES y autónomos Somos Agentes Digitalizadores y podemos gestionar tus ayudas
                </p>
            </div>
        </div>
    </header>
    <div class="theme-showcase" role="main">
        <div class="section container my-5 pb-5 text-center kit-digital-program">
            <p class="mb-4">
                Programa Kit Digital cofinanciado por los fondos Next Generation (EU) del Mecanismo de
                Recuperación y Resilencia
            </p>
            <picture class="d-none d-md-block">
                <?= $this->Html->image('/images/kit-digital/logos_gobierno.jpg', [
                        'class' => 'mw-100 h-auto',
                ]) ?>
            </picture>

            <?= $this->Html->image('/images/kit-digital/programa-kit-digital-gobierno-de-espana.jpg', [
                    'class' => 'd-md-none mw-100 mx-auto mb-3',
            ]) ?>
            <?= $this->Html->image('/images/kit-digital/programa-kit-digital-red-es.jpg', [
                    'class' => 'd-md-none mw-100 mx-auto mb-3',
            ]) ?>
            <?= $this->Html->image('/images/kit-digital/programa-kit-digital-kit-digital.jpg', [
                    'class' => 'd-md-none mw-100 mx-auto mb-3',
            ]) ?>
            <?= $this->Html->image('/images/kit-digital/programa-kit-digital-plan-de-recuperacion-transformacion-y-resiliencia.jpg', [
                    'class' => 'd-md-none mw-100 mx-auto mb-3',
            ]) ?>
            <?= $this->Html->image('/images/kit-digital/programa-kit-digital-next-generation-eu.jpg', [
                    'class' => 'd-md-none mw-100 mx-auto mb-0',
            ]) ?>
        </div>

        <div class="section bg-light my-5 py-5 whats-kid-digital">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-lg-6 pr-lg-5">
                        <h2 class="section__title">¿Qué es Kit Digital?</h2>
                        <p>
                            Kit Digital es el programa de ayudas a pymes y autónomos, lanzado por el
                            <strong>Ministerio de Asuntos Económicos y Transformación Digital</strong>,
                            financiado por la Unión Europea (NextGenerationEU), con el que se pretende
                            incentivar e
                            impulsar su transformación digital.
                        </p>
                        <h3 class="h5">Paso 1</h3>
                        <p>
                            Comunícanos que estás interesado en solicitar el bono para que podamos informarte y
                            ayudarte en lo que requieras (avisarnos por el formulario no supone ningún
                            compromiso más).
                        </p>
                        <h3 class="h5">Paso 2</h3>
                        <p>
                            Registrarse en acelera pymes: regístrate <a target="_blank" href="https://www.acelerapyme.gob.es/registro-pyme">aquí</a>.
                        </p>
                        <h3 class="h5">Paso 3</h3>
                        <p>
                            Hacer test autodiagnóstico para evaluar digitalización de la empresa: <a target="_blank" href="https://www.acelerapyme.gob.es/quieres-conocer-el-grado-de-digitalizacion-de-tu-pyme">haz
                                el test</a>.
                        </p>
                        <h3 class="h5">Paso 4</h3>
                        <p>
                            Te informaremos de la apertura de la convocatoria para tu tamaño de empresa para que
                            puedas presentar la solicitud.
                        </p>
                        <p>
                            <a target="_blank" href="https://sede.red.gob.es/es/procedimientos/convocatoria-de-ayudas-destinadas-la-digitalizacion-de-empresas-del-segmento-i-entre">Solicita
                                el bono aquí</a>.
                        </p>
                        <h3 class="h5">Paso 5</h3>
                        <p>
                            Una vez concedida la ayuda, realizar acuerdo con agentes digitalizadores con los que
                            se
                            van a utilizar las ayudas del bono (kit digital).
                        </p>
                        <p>
                            <a target="_blank" href="https://www.acelerapyme.gob.es/kit-digital/soluciones-digitales">Ve las
                                soluciones aquí</a>.
                        </p>
                        <h3 class="h5">Paso 6</h3>
                        <p>
                            Se realiza el proyecto y se presenta factura al cliente, de la cuál solo pagará la
                            parte
                            correspondiente a descontar la cantidad del bono que vaya a usar.
                        </p>
                    </div>
                    <div class="col-12 col-lg-6 pl-lg-5">
                        <div class="iframe-video__container mb-4" style="padding-top: 56.25%; position: relative;">
                            <iframe frameborder="0" allowfullscreen="" src="https://www.youtube.com/embed/LbkzY4PzoZg?enablejsapi=1&amp;amp=1&amp;playsinline=1" title="YouTube video" allow="autoplay;" style="position: absolute;top: 0;left: 0;bottom: 0;right: 0;height: 100%;width: 100%;"></iframe>

                        </div>
                        <h2 class="section__title">¿Cuáles son los requisitos para
                            solicitar la Ayuda?</h2>
                        <p class="font-weight-bold">Si cumples con las condiciones establecidas en
                            las bases de la
                            convocatoria de la
                            ayuda del Kit Digital, podrás disponer de un bono digital que te permitirá acceder a
                            las soluciones de digitalización.</p>
                        <div class="main_features">
                            <ul class="pl-4 text-left">
                                <li>Ser una pequeña empresa, microempresa o autónomo.</li>
                                <li>No estar sujeta a una orden de recuperación pendiente de la Comisión Europea
                                    que
                                    haya declarado una ayuda ilegal e incompatible con el mercado común.</li>
                                <li>Cumplir los límites financieros y efectivos que definen las categorías de
                                    empresas.</li>
                                <li>Estar al corriente de las obligaciones tributarias y frente a la Seguridad
                                    Social.</li>
                                <li>Estar en situación de alta y tener la antigüedad mínima que se establece por
                                    convocatoria.</li>
                                <li>No superar el límite de ayudas mínimas (de pequeña cuantía).</li>
                                <li>No tener consideración de empresa en crisis.</li>
                                <li>No incurrir en ninguna de las prohibiciones previstas en el artículo 13.2 de
                                    la
                                    Ley 38/2003, de 17 de noviembre, General de Subvenciones.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section container my-5 py-5 kid-digital-solutions">
            <h2 class="section__title text-center">Soluciones del Kit Digital</h2>
            <h3 class="text-center mb-5">Nuestros servicios</h3>

            <div class="services__container card-deck flex-column flex-lg-row">
                <div class="card mb-4 mb-lg-0">
                    <div class="card-body text-center bg-light pb-5">
                        <a href="#" class="description p-4 position-absolute text-center text-decoration-none text-white">
                            <ul class="pl-4 text-left">
                                <li>Diseño Web responsive, en WordPress, con
                                    posibilidad de autogestionar los contenidos</li>
                                <li>Contratación del hosting, dominio e emails,
                                    durante un año</li>
                                <li>Diseño original personalizado</li>
                                <li>Incluye formulario, datos de contacto y enlace a
                                    RRSS</li>
                                <li>Adaptada a los principales navegadores</li>
                                <li>Optimizada para el Posicionamiento SEO Básico</li>
                            </ul>
                        </a>
                        <h2 class="card-title">Sitio web y presencia<br> en internet</h2>
                        <h3 class="card-subtitle font-weight-normal mb-3">Página web corporativa</h3>
                        <p class="lead">Consigue una página web autogestionable y responsive para que tu negocio
                            tenga visibilidad.</p>
                        <div class="logo text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 20 20" style="-ms-transform: rotate(360deg); transform: rotate(360deg);">
                                <path fill="currentColor" d="M12 1c-.18 0-.36.007-.538.02a.5.5 0 1 0 .076.998a6 6 0 0 1 6.445 6.445a.5.5 0 1 0 .997.075A7 7 0 0 0 12 1Zm0 2c-.187 0-.373.01-.555.03a.5.5 0 0 0 .11.994a4 4 0 0 1 4.42 4.42a.5.5 0 1 0 .995.11A5 5 0 0 0 12 3Zm0 2c-.205 0-.405.02-.6.06a.5.5 0 0 0 .2.98a2 2 0 0 1 2.36 2.36a.5.5 0 0 0 .98.2A3 3 0 0 0 12 5Zm-1.92-2.999H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h3v2H5.5a.5.5 0 1 0 0 1h9a.5.5 0 0 0 0-1H13v-2h3a2 2 0 0 0 2-2v-3.08a1.494 1.494 0 0 1-.523-.307c-.139.125-.3.224-.477.29v3.097a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-9a1 1 0 0 1 1-1h6.095c.066-.177.166-.339.29-.478a1.495 1.495 0 0 1-.306-.522Zm1.92 13v2H8v-2h4ZM13 8a1 1 0 1 1-2 0a1 1 0 0 1 2 0Z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <div class="card-footer bg-light text-center">
                        <span class="price h3 mb-0 d-block">1.500 € - 7.000 €</span>
                    </div>
                </div>
                <div class="card mb-4 mb-lg-0">
                    <div class="card-body text-center bg-light pb-5">
                        <a href="#" class="description p-4 position-absolute text-center text-decoration-none text-white">
                            <ul class="pl-4 text-left">
                                <li>WooCommerce como plataforma principal de comercio
                                    electrónico</li>
                                <li>Integración de métodos de pago en la web</li>
                                <li>Subida de productos a la web (hasta 100
                                    referencias)</li>
                                <li>Formación al cliente para administrar su propio
                                    comercio electrónico a través de una video-llamada o un video-tutorial</li>
                                <li>Alojamiento web en hosting seguro</li>
                                <li>Actualización y mantenimiento web mensual</li>
                            </ul>
                        </a>
                        <h2 class="card-title">Comercio electrónico</h2>
                        <h3 class="card-subtitle font-weight-normal mb-3">Página web con tienda online</h3>
                        <p class="lead">Web con pasarela de pago que te permitirá vender tus productos y
                            servicios en Internet.</p>
                        <div class="logo text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 16 16" style="-ms-transform: rotate(360deg); transform: rotate(360deg);">
                                <path fill="currentColor" d="M2.97 1.35A1 1 0 0 1 3.73 1h8.54a1 1 0 0 1 .76.35l2.609 3.044A1.5 1.5 0 0 1 16 5.37v.255a2.375 2.375 0 0 1-4.25 1.458A2.371 2.371 0 0 1 9.875 8A2.37 2.37 0 0 1 8 7.083A2.37 2.37 0 0 1 6.125 8a2.37 2.37 0 0 1-1.875-.917A2.375 2.375 0 0 1 0 5.625V5.37a1.5 1.5 0 0 1 .361-.976l2.61-3.045zm1.78 4.275a1.375 1.375 0 0 0 2.75 0a.5.5 0 0 1 1 0a1.375 1.375 0 0 0 2.75 0a.5.5 0 0 1 1 0a1.375 1.375 0 1 0 2.75 0V5.37a.5.5 0 0 0-.12-.325L12.27 2H3.73L1.12 5.045A.5.5 0 0 0 1 5.37v.255a1.375 1.375 0 0 0 2.75 0a.5.5 0 0 1 1 0zM1.5 8.5A.5.5 0 0 1 2 9v6h1v-5a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v5h6V9a.5.5 0 0 1 1 0v6h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1V9a.5.5 0 0 1 .5-.5zM4 15h3v-5H4v5zm5-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-3zm3 0h-2v3h2v-3z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <div class="card-footer bg-light text-center">
                        <span class="price h3 mb-0 d-block">3.000 € - 7.000 €</span>
                    </div>
                </div>
                <div class="card mb-4 mb-lg-0">
                    <div class="card-body text-center bg-light pb-5">
                        <a href="#" class="description p-4 position-absolute text-center text-decoration-none text-white">
                            <ul class="pl-4 text-left">
                                <li>Social Media Plan: definir la estrategia que se va
                                    a implementar en las redes sociales</li>
                                <li>Control y motorización de las métricas de las
                                    redes sociales</li>
                                <li>Gestión y mantenimiento de la red social</li>
                                <li>Creación de creatividades y medios que ayuden al
                                    usuario a interactuar con la red social</li>
                                <li>Publicaciones programadas en torno al plan elegido
                                    por el cliente</li>
                            </ul>
                        </a>
                        <h2 class="card-title">Gestión de redes sociales</h2>
                        <h3 class="card-subtitle font-weight-normal mb-3">Redes sociales</h3>
                        <p class="lead">Cuenta en RRSS para promocionar tu empresa y tus productos en Instagram
                            y Facebook.</p>
                        <div class="logo text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 32 32" style="-ms-transform: rotate(360deg); transform: rotate(360deg);">
                                <circle cx="21" cy="26" r="2" fill="currentColor"></circle>
                                <circle cx="21" cy="6" r="2" fill="currentColor"></circle>
                                <circle cx="4" cy="16" r="2" fill="currentColor"></circle>
                                <path fill="currentColor" d="M28 12a3.996 3.996 0 0 0-3.858 3h-4.284a3.966 3.966 0 0 0-5.491-2.643l-3.177-3.97A3.963 3.963 0 0 0 12 6a4 4 0 1 0-4 4a3.96 3.96 0 0 0 1.634-.357l3.176 3.97a3.924 3.924 0 0 0 0 4.774l-3.176 3.97A3.96 3.96 0 0 0 8 22a4 4 0 1 0 4 4a3.962 3.962 0 0 0-.81-2.387l3.176-3.97A3.966 3.966 0 0 0 19.858 17h4.284A3.993 3.993 0 1 0 28 12ZM6 6a2 2 0 1 1 2 2a2.002 2.002 0 0 1-2-2Zm2 22a2 2 0 1 1 2-2a2.002 2.002 0 0 1-2 2Zm8-10a2 2 0 1 1 2-2a2.002 2.002 0 0 1-2 2Zm12 0a2 2 0 1 1 2-2a2.002 2.002 0 0 1-2 2Z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <div class="card-footer bg-light text-center">
                        <span class="price h3 mb-0 d-block">2.350 € - 7.000 €</span>
                        <small class="font-italic">Presupuesto anual</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="section form__container py-5">
            <div class="container my-5">
                <div class="content p-3 p-lg-5 position-relative">
                    <h2 class="section__title mb-4 text-center">Contacto</h2>
                    <form id="kit-digital-form" method="post" class="main-form form contact-form" "<?php echo Router::url("/", false); ?>kit-digital" autocomplete="off">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">Nombre*</label>
                                    <input type="text" name="name" id="name" class="form-control" pattern=".{3,100}" required="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-6 ">
                                <div class="form-group">
                                    <label for="email">Email*</label>
                                    <input type="email" name="email" id="email" class="form-control" pattern=".{5,100}" required="">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-6 ">
                                <div class="form-group">
                                    <label for="phone">Número de teléfono móvil*</label>
                                    <input type="tel" name="phone" id="phone" class="form-control" pattern=".{5,100}" required="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-6 ">
                                <div class="form-group">
                                    <label for="empresa">Nombre empresa*</label>
                                    <input type="text" name="empresa" id="empresa" class="form-control" pattern=".{5,100}" required="">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-6 ">
                                <div class="form-group">
                                    <label for="employees">Trabajadores*</label>
                                    <select id="employees" name="employees" class="form-select form-control" aria-label="Employees">
                                        <option selected="selected" disabled="disabled">- Select -</option>
                                        <option value="One or two (or freelance)">De 1 a 2 trabajadores (o freelance)</option>
                                        <option value="From 3 to 9 employees">De 3 a 9 trabajadores</option>
                                        <option value="From 10 to 49 employees">De 10 a 49 trabajadores</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="message">Mensaje*</label>
                                    <textarea name="message" id="message" class="form-control" style="resize: vertical; min-height: 150px;" required=""></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="custom-control custom-checkbox">
                                    <input id="data-protection-police" name="data-protection-police" class="custom-control-input" type="checkbox" required="">
                                    <label class="custom-control-label" for="data-protection-police">Acepto la <a class="data-protection-police-link" href="#dpp">Protección de datos</a>*</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="g-recaptcha" style="position: relative; float: left;" data-sitekey="6LeNxKAUAAAAAGHlDeqliG7-9wDsvvQhv8a6i3Cw"></div>
                                </div>
                            </div>
                            <div class="col-md-6 text-center text-md-right">
                                <button type="submit" class="btn btn-primary">Enviar</button>
                            </div>
                        </div>
                    </form>
                    <div id="dpp" class="dpp bg-white h-100 pb-4 position-absolute pt-3 px-4 w-100">
                        <button class="close-dpp align-items-center bg-transparent border-0 d-flex justify-content-center mb-2 ml-auto text-primary">
                            <i class="retina-theessentials-2544"></i>
                        </button>
                        <p>En cumplimiento de lo dispuesto en la normativa vigente en materia de Protección de
                            Datos de Carácter Personal, el Responsable del Tratamiento es DevmanExtensions
                            con NIF: ES74521153A, Dirección: C/ Primera Literatura 11, 3ºA, 02008, Albacete. Email: <a href="mailto:info@devmanextensions.com?subject=Contacto%20web%20-%20Kit%20Digital">info@devmanextensions.com</a>.</p>
                        <p>La base para el tratamiento de los datos es el consentimiento del usuario, y la
                            finalidad del tratamiento es la de atender su consulta o sugerencia. Asimismo, sus
                            datos serán utilizados para proporcionarle por cualquier medio (electrónico o no),
                            información periódica acerca de nuestros productos y servicios.</p>
                        <p>Los datos se conservarán mientras se mantenga la relación y no se solicite su
                            supresión, y en cualquier caso en cumplimiento de plazos legales de prescripción que
                            le resulten de aplicación.</p>
                        <p>No se cederán datos a terceros, salvo obligación legal, ni están previstas
                            transferencias internacionales de dichos datos.</p>
                        <p>Puede ejercitar sus derechos de acceso, rectificación, supresión, portabilidad y
                            limitación u oposición dirigiéndose por escrito a C/ Primera Literatura 11, 3ºA, 02008, Albacete, aportando copia de su documento
                            nacional de identidad o documento equivalente, o mediante el envío de un correo
                            electrónico a la siguiente dirección <a href="mailto:info@devmanextensions.com?subject=Contacto%20web%20-%20Kit%20Digital">info@devmanextensions.com</a>,
                            así como reclamar ante la Autoridad de Control (Agencia Española de Protección de
                            Datos: <a href="https://www.aepd.es/es" target="_blank" rel="noopener">www.agpd.es</a>).</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>