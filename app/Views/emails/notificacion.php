<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title><?= esc($titulo) ?></title></head>
<body style="margin:0;padding:0;background:#f4f5f7;font-family:Arial,Helvetica,sans-serif;color:#3b3f4a;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f5f7;padding:32px 12px;">
    <tr><td align="center">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border-radius:12px;overflow:hidden;">
            <tr><td style="background:#ffffff;padding:22px 32px 18px;font-size:28px;font-weight:bold;letter-spacing:-1px;color:#111418;">
                inter<span style="color:#00818e;">graphics</span>
            </td></tr>
            <tr><td style="height:6px;line-height:6px;font-size:0;background:#00a3b6;background-image:linear-gradient(90deg,#00818e 0 28%,#00a3b6 28% 52%,#66c0bc 52% 72%,#72ba6e 72% 88%,#c6d32f 88% 100%);">&nbsp;</td></tr>
            <tr><td style="padding:32px;">
                <p style="margin:0 0 6px;color:#6b7280;">Hola, <?= esc($nombre) ?></p>
                <h1 style="font-size:20px;margin:0 0 16px;color:#14171f;"><?= esc($titulo) ?></h1>
                <?php foreach ($lineas as $l): ?>
                    <p style="margin:0 0 10px;line-height:1.6;"><?= esc($l) ?></p>
                <?php endforeach ?>
                <?php if ($boton && $url): ?>
                    <p style="text-align:center;margin:28px 0 8px;">
                        <a href="<?= esc($url, 'attr') ?>" style="background:#00818e;color:#ffffff;text-decoration:none;padding:13px 32px;border-radius:30px;font-weight:bold;display:inline-block;"><?= esc($boton) ?></a>
                    </p>
                <?php endif ?>
            </td></tr>
            <tr><td style="background:#f4f5f7;padding:16px 32px;font-size:12px;color:#9ca3af;text-align:center;">
                Sistema de Vacaciones · International Graphics Outsourcing S.R.L.
            </td></tr>
        </table>
    </td></tr>
</table>
</body>
</html>
