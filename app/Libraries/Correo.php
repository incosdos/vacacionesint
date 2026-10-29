<?php

namespace App\Libraries;

use Config\Email as EmailConfig;

/**
 * Envío de correos del sistema.
 */
class Correo
{
    /** Hay credenciales SMTP configuradas en app/Config/Email.php */
    public static function configurado(): bool
    {
        $cfg = config(EmailConfig::class);

        return $cfg->protocol !== 'smtp' || ($cfg->SMTPUser !== '' && $cfg->SMTPPass !== '');
    }

    public static function enviarVerificacion(array $usuario, string $token): bool
    {
        $enlace = site_url('registro/verificar/' . $token);

        $html = view('emails/verificacion', [
            'nombre' => $usuario['nombres'],
            'enlace' => $enlace,
            'horas'  => 24,
        ]);

        return self::enviar($usuario['email'], 'Confirme su registro - INTERGRAPHICS', $html);
    }

    /**
     * Notificación con el diseño corporativo.
     *
     * @param list<string> $lineas
     */
    public static function notificar(string $para, string $nombre, string $asunto, string $titulo, array $lineas, ?string $boton = null, ?string $url = null): bool
    {
        $html = view('emails/notificacion', compact('nombre', 'titulo', 'lineas', 'boton', 'url'));

        return self::enviar($para, $asunto, $html);
    }

    public static function enviar(string $para, string $asunto, string $html): bool
    {
        if (! self::configurado()) {
            log_message('warning', "Correo no enviado a {$para}: SMTP sin configurar.");

            return false;
        }

        $cfg   = config(EmailConfig::class);
        $email = service('email');
        $email->setFrom($cfg->fromEmail !== '' ? $cfg->fromEmail : $cfg->SMTPUser, $cfg->fromName);
        $email->setTo($para);
        $email->setSubject($asunto);
        $email->setMessage($html);

        if (! $email->send(false)) {
            log_message('error', 'Error al enviar correo a ' . $para . ': ' . $email->printDebugger(['headers']));

            return false;
        }

        return true;
    }
}
