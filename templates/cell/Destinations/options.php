<?php foreach ($destinations as $d) : ?>
    <option value="<?= $d->id ?>"><?= $d->name ?></option>
<?php endforeach; ?>