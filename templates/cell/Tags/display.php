<ul class="list-inline">
    <li class="list-inline-item">
        <span class="badge badge-success">
            <a href="/blog" class="text-white">
                Tutti gli articoli
            </a>
        </span>
    </li>

    <?php foreach ($tags as $tag): ?>

        <li class="list-inline-item">
            <span class="badge badge-primary">
                <a href="/blog?tag=<?= $tag->id ?>" class="text-white">
                    <?= htmlspecialchars($tag->title) ?>
                </a>
            </span>
        </li>
    <?php endforeach; ?>
</ul>