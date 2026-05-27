<?php /** @var \App\Model\Entity\Tag $tag */ ?>
<div id="app">
    <?= $this->Form->create($tag, ['ref' => 'form']); ?>
    <fieldset>
        <legend><?= __('Edit {0}', ['Tag']) ?></legend>
        <?php
            echo $this->Form->control('label', ['label' => 'Nome tag']);
            echo $this->Form->control('slug', ['label' => 'URL slug (generato automaticamente)', 'readonly' => true]);
            echo $this->Form->control('tags_enhancement.alt_name', ['label' => 'Nome ciclovia']);
            echo $this->Form->control('tags_enhancement.color', [
                'label' => 'Colore',
                'type'  => 'color',
            ]);        ?>
        <div class="card card-info mt-3">
        <div class="card-body">
            <h3 class="card-title"><i class="bi bi-image"></i> Immagine tag</h3>
            <?= $this->element('upload', [
                'model' => 'TagsEnhancements',
                'field' => 'tag',
                'multiple' => false,
                'temp' => false,
                'convert' => false,
                'filetype' => 'image/*',
            ] + (!empty($tag->id) ? [
                'destination' => $tag->slug,
                'id' => $tag->id,
                'files' => !empty($tag->tags_enhancement?->image) ? [$tag->tags_enhancement->image] : [],
            ] : [])); ?>
        </div>
    </div>
    </fieldset>
  
    <?= $this->Form->button(__("Save")); ?>
    <?= $this->Form->end() ?>
</div>