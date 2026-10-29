<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Datos de la empresa usados en toda la web.
 * Modifique aquí nombres, contactos e imágenes sin tocar las vistas.
 */
class Intergraphics extends BaseConfig
{
    public string $nombreComercial = 'INTERGRAPHICS';
    public string $razonSocial     = 'International Graphics Outsourcing S.R.L.';
    public string $eslogan         = 'Nos dedicamos a crear soluciones bien pensadas que tienen un impacto duradero en su empresa.';

    /**
     * Origen remoto de las imágenes (sitio oficial www.intergraphics.dk).
     * Si la imagen existe en public/assets/img/ig/ se usa la copia local.
     */
    public string $urlRemota = 'https://www-static.intergraphics.dk/wp-content/uploads/';

    public array $imagenes = [
        'banner1'    => '2023/05/1600x1200_banner_01.jpg',
        'banner2'    => '2023/05/1600x1200_banner_02.jpg',
        'banner3'    => '2023/05/1600x1200_banner_03.jpg',
        'marketing'  => '2023/05/1600x1200_marketing_01.jpg',
        'some'       => '2023/05/1600x1200_SoMe_01.jpg',
        'web'        => '2023/05/1600x1200_Hjemmeisde_01.jpg',
        'apps'       => '2023/05/1600x1200_Apps_01.jpg',
        'magasin'    => '2023/05/1600x1200_magasin_01.jpg',
        'anuncios'   => '2023/05/1600x1200_Annoncer_01.jpg',
        'impresos'   => '2023/05/1600x1200_Tryksager_01.jpg',
        'video'      => '2023/05/1600x1200_Video_01.jpg',
        'case_dyrskue'   => '2023/06/soenderjysk-dyrskue_ombrydning-960x720.jpg',
        'case_postnord'  => '2023/06/postDanmarkRundt_ombrydning2022-960x720.jpg',
        'case_viborg'    => '2023/06/viborg_kommune_snapsting_skraa-960x720.jpg',
        'case_samson'    => '2023/06/mockup_Samson1600x1200-960x720.jpg',
        'case_broen'     => '2023/06/Broen_shopping_skraa-960x720.jpg',
        'case_autofyn'   => '2023/06/AutoFyn-DIA_topscrollannonce_skraa_GRAA-960x720.jpg',
        'case_pindle'    => '2023/05/1600x1200_Apps_01-960x720.jpg',
        'case_julefrokost' => '2023/06/julefrokostMagasin-960x720.jpg',
    ];

    public array $oficinas = [
        [
            'pais'      => 'Bolivia',
            'ciudad'    => 'Cochabamba',
            'direccion' => 'Cochabamba, Bolivia', // TODO: completar dirección exacta
            'telefono'  => '72251168',                    // TODO: completar teléfono
            'correo'    => 'rrhh.intergraphics@gmail.com',                    // TODO: completar correo
        ],
        [
            'pais'      => 'Dinamarca',
            'ciudad'    => 'Vejle',
            'direccion' => 'Bugattivej 8, 7100 Vejle, Dinamarca',
            'telefono'  => '(+45) 4070 3517',
            'correo'    => 'mail@intergraphics.dk',
        ],
    ];

    public array $redes = [
        'facebook'  => 'https://www.facebook.com/',
        'instagram' => 'https://www.instagram.com/',
        'linkedin'  => 'https://www.linkedin.com/',
    ];
}
