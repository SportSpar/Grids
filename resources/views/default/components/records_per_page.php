<?php /** @var SportSpar\Grids\Components\RecordsPerPage $component */ ?>
<label style="font-weight: normal">
    <?= __('Records per page') ?>
    <select class="form-control input-sm grids-control-records-per-page"
            style="display: inline; width: 80px; margin-right: 10px"
            onchange="submit()"
            name="<?php echo $component->getInputName() ?>">
        <?php foreach ($component->getVariants() as $key => $value): ?>
            <option value="<?php echo $key ?>" <?php echo $component->getValue() === $value ? 'selected="selected"' : '' ?>>
                <?php echo $value ?>
            </option>
        <?php endforeach; ?>
    </select>
</label>
