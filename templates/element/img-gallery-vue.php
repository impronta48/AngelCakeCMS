<?php $this->Html->script('https://cdn.jsdelivr.net/npm/vue-tinybox', ['block' => true]) ?>

<?php $this->Html->scriptStart(['block'=>true]); ?>
    $images = <?= json_encode($images) ?>;
<?php $this->Html->scriptEnd(['block'=>true]); ?>

<img
    v-for="(img, idx) in fullImages"
    :src="img.thumbnail"        
    class="open-tinybox img-thumbnail"
    @click="index = idx"
>

<Tinybox
    v-model="index"
    :images="fullImages"
/>
