<?php /** @var array<string, string|null> $migas  etiqueta => url (null = página actual) */ ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb small mb-2">
        <?php foreach ($migas as $etiqueta => $url): ?>
            <?php if ($url === null): ?>
                <li class="breadcrumb-item active" aria-current="page"><?= esc($etiqueta) ?></li>
            <?php else: ?>
                <li class="breadcrumb-item"><a href="<?= site_url($url) ?>"><?= esc($etiqueta) ?></a></li>
            <?php endif ?>
        <?php endforeach ?>
    </ol>
</nav>
