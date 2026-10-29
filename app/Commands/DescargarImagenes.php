<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Intergraphics;

/**
 * Descarga el logo y las imágenes del sitio oficial a public/assets/img/ig/
 * Uso:  php spark intergraphics:imagenes
 */
class DescargarImagenes extends BaseCommand
{
    protected $group       = 'Intergraphics';
    protected $name        = 'intergraphics:imagenes';
    protected $description = 'Descarga el logo e imágenes de intergraphics.dk a public/assets/img/ig/';

    public function run(array $params)
    {
        /** @var Intergraphics $cfg */
        $cfg     = config(Intergraphics::class);
        $destino = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'ig' . DIRECTORY_SEPARATOR;

        if (! is_dir($destino)) {
            mkdir($destino, 0775, true);
        }

        $client = service('curlrequest', ['timeout' => 30, 'http_errors' => false]);
        $ok = 0;

        foreach ($cfg->imagenes as $clave => $ruta) {
            $url     = $cfg->urlRemota . $ruta;
            $archivo = $destino . basename($ruta);

            try {
                $resp = $client->get($url, ['headers' => ['User-Agent' => 'Mozilla/5.0']]);

                if ($resp->getStatusCode() === 200) {
                    file_put_contents($archivo, $resp->getBody());
                    CLI::write("  OK  {$clave} -> " . basename($ruta), 'green');
                    $ok++;
                } else {
                    CLI::write("  ERROR {$resp->getStatusCode()}  {$url}", 'red');
                }
            } catch (\Throwable $e) {
                CLI::write("  ERROR  {$url}: " . $e->getMessage(), 'red');
            }
        }

        CLI::newLine();
        CLI::write("Descargadas {$ok} de " . count($cfg->imagenes) . ' imágenes en ' . $destino, 'yellow');
    }
}
