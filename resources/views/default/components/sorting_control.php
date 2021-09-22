<small class="grid-sorter" style="white-space: nowrap">
    <?php if ($column->isSortedAsc()): ?>
        <span class="text-success">&#x25B2;</span>
    <?php else: ?>
        <a title="<?= __('Sort ascending') ?>" href="<?= $grid->getSorter()->link($column, 'ASC') ?>">
            &#x25B2;
        </a>
    <?php endif; ?>

    <?php if ($column->isSortedDesc()): ?>
        <span class="text-success">&#x25BC;</span>
    <?php else: ?>
        <a title="<?= __('Sort descending') ?>" href="<?= $grid->getSorter()->link($column, 'DESC') ?>">
            &#x25BC;
        </a>
    <?php endif; ?>
</small>
