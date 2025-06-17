<ul class="list-inline">
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