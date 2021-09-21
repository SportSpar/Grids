<?php
/** @var SportSpar\Grids\Filter $filter */

$style = $filter->getStyle();
?>

<input
    class="<?= implode(', ', $style->getCssClasses()) ?>"
    name="<?= $filter->getInputName() ?>"
    value="<?= $filter->getValue() ?>"
    />
<?php if($label): ?>
    <span><?= $label ?></span>
<?php endif ?>
