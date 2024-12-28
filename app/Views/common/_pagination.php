<!-- <?php if (countItems($pager->links()) > 1): ?>
    <nav aria-label="<?= lang('Pager.pageNavigation') ?>">
        <ul class="pagination justify-content-end">
            <?php if ($pager->hasPreviousPage()) : ?>
                <li class="page-item">
                    <a href="<?= $pager->getFirst() ?>" class="page-link" aria-label="">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <li class="page-item">
                    <a href="<?= $pager->getPreviousPage() ?>" class="page-link" aria-label="">
                        <span aria-hidden="true">&lsaquo;</span>
                    </a>
                </li>
            <?php endif;
            foreach ($pager->links() as $link) : ?>
                <li class="page-item<?= $link['active'] ? ' active' : ''; ?>">
                    <a href="<?= $link['uri'] ?>" class="page-link">
                        <?= $link['title'] ?>
                    </a>
                </li>
            <?php endforeach;
            if ($pager->hasNextPage()) : ?>
                <li class="page-item">
                    <a href="<?= $pager->getNextPage() ?>" class="page-link" aria-label="">
                        <span aria-hidden="true">&rsaquo;</span>
                    </a>
                </li>
                <li class="page-item">
                    <a href="<?= $pager->getLast() ?>" class="page-link" aria-label="">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?> -->


<?php if (countItems($pager->links()) > 1): ?>
    <nav aria-label="<?= lang('Pager.pageNavigation') ?>">
        <ul class="pagination justify-content-center">
            <!-- Nút "First" và "Previous" -->
            <?php if ($pager->hasPreviousPage()) : ?>
                <li class="page-item">
                    <a href="<?= $pager->getFirst() ?>" class="page-link" aria-label="First">
                        <i class="bi bi-chevron-double-left"></i>
                    </a>
                </li>
                <li class="page-item">
                    <a href="<?= $pager->getPreviousPage() ?>" class="page-link" aria-label="Previous">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
            <?php endif; ?>

            <!-- Các nút số trang -->
            <?php foreach ($pager->links() as $link) : ?>
                <li class="page-item<?= $link['active'] ? ' active' : ''; ?>">
                    <a href="<?= $link['uri'] ?>" class="page-link">
                        <?= $link['title'] ?>
                    </a>
                </li>
            <?php endforeach; ?>

            <!-- Nút "Next" và "Last" -->
            <?php if ($pager->hasNextPage()) : ?>
                <li class="page-item">
                    <a href="<?= $pager->getNextPage() ?>" class="page-link" aria-label="Next">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="page-item">
                    <a href="<?= $pager->getLast() ?>" class="page-link" aria-label="Last">
                        <i class="bi bi-chevron-double-right"></i>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>
