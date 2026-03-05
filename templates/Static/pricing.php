<!--templetae custom per la pagina di pricing con margini della pagina ridotti-->

<?php

$this->assign('title', $title);
if (isset($keywords)) {
    $this->Html->meta(
        'keywords',
        $keywords,
        ['block' => true]
    );
}

if (isset($description)) {
    $this->Html->meta(
        'description',
        $description,
        ['block' => true]
    );
}
?>

<div class="container-fluid px-0">
    <?php if (isset($copertina)): ?>
        <div class="entry-media">
            <figure>
                <?php if (strpos($copertina, '.jpg') || strpos($copertina, '.png')): ?>
                    <img src="<?= "/images/$copertina?w=800&h=600" ?>" alt="<?= $description ?>">
                <?php endif; ?>

                <?php if (strpos($copertina, 'youtube')): ?>
                    <iframe width="560" height="315" src="<?= $copertina ?>" frameborder="0"
                        allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen></iframe>
                <?php endif; ?>
            </figure>
        </div><!-- End .entry-media -->
    <?php endif ?>

    <?php
    $first = substr(strip_tags($body), 0, 1);
    $body_first = strpos($body, $first);
    $body[$body_first] = ' ';
    ?>
    <p>
        <span class="dropcap"><?= $first; ?></span>
        <?= $body ?>
    </p>


</div><!-- End .container-fluid -->