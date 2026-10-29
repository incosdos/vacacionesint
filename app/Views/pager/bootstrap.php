<?php
/** @var \CodeIgniter\Pager\PagerRenderer $pager */
$pager->setSurroundCount(2);
?>
<nav aria-label="Paginación">
    <ul class="pagination pagination-sm justify-content-center flex-wrap mb-0 ig-paginacion">
        <?php if ($pager->hasPrevious()): ?>
            <li class="page-item"><a class="page-link" href="<?= $pager->getFirst() ?>" aria-label="Primera"><i class="bi bi-chevron-double-left"></i></a></li>
            <li class="page-item"><a class="page-link" href="<?= $pager->getPrevious() ?>" aria-label="Anterior"><i class="bi bi-chevron-left"></i></a></li>
        <?php endif ?>
        <?php foreach ($pager->links() as $link): ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a class="page-link" href="<?= $link['uri'] ?>" <?= $link['active'] ? 'aria-current="page"' : '' ?>><?= $link['title'] ?></a>
            </li>
        <?php endforeach ?>
        <?php if ($pager->hasNext()): ?>
            <li class="page-item"><a class="page-link" href="<?= $pager->getNext() ?>" aria-label="Siguiente"><i class="bi bi-chevron-right"></i></a></li>
            <li class="page-item"><a class="page-link" href="<?= $pager->getLast() ?>" aria-label="Última"><i class="bi bi-chevron-double-right"></i></a></li>
        <?php endif ?>
    </ul>
</nav>
