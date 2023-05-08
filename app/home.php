<?php
error_reporting(E_ALL & ~E_NOTICE);
session_start();
require_once '../assets/util/Session.php';
require_once '../controller/ControlSesion.php';
?>
<link rel="stylesheet" type="text/css" href="../assets/css/home.min.css">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800%7CShadows+Into+Light" rel="stylesheet" type="text/css">

<div role="main" class="main bdy mt-xlg" style="overflow:hidden">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 center">
                <div class="d-block d-sm-none mb-20" style="font-size:18px;">
                    <span>
                        En Confipetrol nos comprometemos en alcanzar la confiabilidad de los equipos definida,<br>
                        con la mejora continua de los costos previstos, controlando los riesgos e impactos<br>
                        ambientales para el cumplimiento de los objetivos operativos y contractuales.
                    </span>
                </div>
                <h2 class="word-rotator-title d-none d-sm-block">
                    <!--El hecho de
                    <strong>-->
                    <span class="word-rotate" data-plugin-options='{"delay": 6000, "animDelay": 300}' style="max-height:131px;">
                      <span class="word-rotate-items">
                        <span>
                            En Confipetrol nos comprometemos en alcanzar la confiabilidad de los equipos definida,<br>
                            con la mejora continua de los costos previstos, controlando los riesgos e impactos<br>
                            ambientales para el cumplimiento de los objetivos operativos y contractuales.
                        </span>

                          <!--<span>programar</span>-->
                          <!--<span>incredible</span>-->
                      </span>
                    </span>
                    <!--</strong>
                    los trabajos de Mantto de equipos-->
                </h2>
            </div>
        </div>
    </div>
    <section class="section section-default section-with-mockup mb-none">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-4 hidden-sm hidden-xs">
                    <div class="feature-box feature-box-style-2 reverse mb-xl appear-animation"
                         data-appear-animation="fadeInLeft" data-appear-animation-delay="300">
                        <div class="feature-box-icon">
                            <i class="fa fa-link"></i>
                        </div>
                        <div class="feature-box-info">
                            <h4 class="mb-sm">Confiabilidad</h4>
                            <p class="mb-lg">
                                Probabilidad de que un equipo cumpla una misión específica
                                bajo condiciones de uso determinadas en un período determinado
                            </p>
                        </div>
                    </div>
                    <div class="feature-box feature-box-style-2 reverse mt-xl appear-animation" data-appear-animation="fadeInLeft" data-appear-animation-delay="600">
                        <div class="feature-box-icon">
                            <i class="fa fa-calendar-check-o"></i>
                        </div>
                        <div class="feature-box-info">
                            <h4 class="mb-sm">Disponibilidad</h4>
                            <p class="mb-lg">Estimar en forma global el porcentaje de tiempo total en que se puede esperar que un equipo esté disponible para cumplir la función para la cual fue destinado</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <img src="../assets/img/home/miconfi.png" class="img-responsive mockup-landing-page mb-xl appear-animation" alt="Porto" data-appear-animation="zoomIn" data-appear-animation-delay="300">
                </div>
                <div class="col-lg-4 col-md-4 hidden-sm hidden-xs">
                    <div class="feature-box feature-box-style-2 mb-xl appear-animation" data-appear-animation="fadeInRight" data-appear-animation-delay="300">
                        <div class="feature-box-icon">
                            <i class="fa fa-puzzle-piece"></i>
                        </div>
                        <div class="feature-box-info">
                            <h4 class="mb-sm">Gestión de activos</h4>
                            <p class="mb-lg">Extender la Vida de Activos y Maximizar el retorno de los Activos</p>
                        </div>
                    </div>
                    <div class="feature-box feature-box-style-2 mt-xl appear-animation" data-appear-animation="fadeInRight" data-appear-animation-delay="600">
                        <div class="feature-box-icon">
                            <i class="fa fa-line-chart"></i>
                        </div>
                        <div class="feature-box-info">
                            <h4 class="mb-sm">Medición por indicadores</h4>
                            <p class="mb-lg">Pemitir evaluar de forma efectiva el comportamiento operacional de las instalaciones, sistemas, equipos, dispositivos y componentes.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


