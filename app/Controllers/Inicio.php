<?php

namespace App\Controllers;

use Config\Intergraphics;

class Inicio extends BaseController
{
    protected $helpers = ['url', 'ig'];

    public function index(): string
    {
        $empresa = config(Intergraphics::class);

        $servicios = [
            ['icono' => 'bi-badge-ad',          'img' => 'banner1',   'titulo' => 'Banners digitales',
             'texto' => 'Producimos banners en todos los formatos, con soluciones creativas a medida, entrega a cualquier ad server y cumplimiento de requisitos de tamaño y formato.'],
            ['icono' => 'bi-megaphone',         'img' => 'marketing', 'titulo' => 'Marketing',
             'texto' => 'Comunicación visual creativa para redes sociales, sitios web, anuncios impresos, volantes y más, enfocada en que usted alcance sus objetivos.'],
            ['icono' => 'bi-instagram',         'img' => 'some',      'titulo' => 'Redes sociales',
             'texto' => 'Contenido de imagen y video para redes sociales, adaptado a la voz de su empresa y a su público objetivo.'],
            ['icono' => 'bi-window-desktop',    'img' => 'web',       'titulo' => 'Sitios web',
             'texto' => 'Sitios web modulares y a medida, fáciles de usar y totalmente responsivos en cualquier dispositivo.'],
            ['icono' => 'bi-journal-richtext',  'img' => 'magasin',   'titulo' => 'Revistas',
             'texto' => 'Diseño, diagramación, montaje de textos e imágenes, anuncios y corrección de revistas, suplementos, catálogos y periódicos temáticos.'],
            ['icono' => 'bi-newspaper',         'img' => 'anuncios',  'titulo' => 'Anuncios',
             'texto' => 'Producción creativa de anuncios de principio a fin, adaptados a cada medio y entregados listos para imprenta.'],
            ['icono' => 'bi-printer',           'img' => 'impresos',  'titulo' => 'Impresos',
             'texto' => 'Material impreso y de presentación de la más alta calidad: folletos, volantes, tarjetas de presentación y afiches.'],
            ['icono' => 'bi-camera-reels',      'img' => 'video',     'titulo' => 'Video',
             'texto' => 'Videos cortos y animaciones de texto para el marketing de su empresa o marca.'],
        ];

        $proyectos = [
            ['img' => 'case_dyrskue',     'titulo' => 'Det Sønderjyske Fællesdyrskue', 'cat' => 'impresos', 'etiqueta' => 'Impresos'],
            ['img' => 'case_postnord',    'titulo' => 'PostNord Danmark Rundt',        'cat' => 'revistas', 'etiqueta' => 'Revista'],
            ['img' => 'case_viborg',      'titulo' => 'Viborg Snapsting',              'cat' => 'banners',  'etiqueta' => 'Banner digital'],
            ['img' => 'case_samson',      'titulo' => 'Samson',                        'cat' => 'impresos', 'etiqueta' => 'Impresos'],
            ['img' => 'case_broen',       'titulo' => 'Broen Shopping',                'cat' => 'banners',  'etiqueta' => 'Banner digital'],
            ['img' => 'case_autofyn',     'titulo' => 'Auto Fyn',                      'cat' => 'anuncios banners', 'etiqueta' => 'Anuncios'],
            ['img' => 'case_pindle',      'titulo' => 'Pindle – App calendario',       'cat' => 'web',      'etiqueta' => 'Web / App'],
            ['img' => 'case_julefrokost', 'titulo' => 'Julefrokost Magasin',           'cat' => 'revistas', 'etiqueta' => 'Revista'],
        ];

        return view('inicio/index', [
            'titulo'    => $empresa->nombreComercial . ' | ' . $empresa->razonSocial,
            'empresa'   => $empresa,
            'servicios' => $servicios,
            'proyectos' => $proyectos,
        ]);
    }
}
