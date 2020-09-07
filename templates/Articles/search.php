<div id="home">
<div class="well">
<h1 style="text-align: center;">Cerca articolo</h1>
        <?php

use PhpOffice\PhpSpreadsheet\Chart\Title;


echo $this->Form->create(
                    null,
                    [
                        'type'    => 'get',
                        'inputDefaults' => array(
                            'div' => 'form-group '
                        ),
                        'class' => 'form-inline',
                    ]
                ); ?>
        <div class="row" style="margin-bottom: 10px; margin:0 auto">
                <div style="margin:0 auto">
                    <?php echo $this->Form->control('q', ['label' => '', 'div' => 'col col-md-3']); ?>
                </div>
                <div style="margin-top: -15px;">
                    <?php echo $this->Form->submit('Filtra', ['class' => 'btn btn-filtra btn-primary mt-3']); ?>                            
                </div>            
                <div class="col-md-4">
                    
                </div>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
    <div class="md-50"></div>
    <div class="row">

        <section class="col-md-12">
            <article class="row" v-for="article in articles" v-cloak>
                <div class="col-md-5 col-lg-4 col-sm-12 text-center round" >
                    <a v-bind:href="'../' + article.slug">
                        <img v-bind:src="'/images' + article.copertina + '?w=170&h=170&fit=crop'" v-bind:alt="article.title" class="rounded-circle">
                    </a>
                </div>
                <div class="col-md-7 col-lg-8 col-sm-12 news">
                    <h2><a v-bind:href="'../' + article.slug">{{article.title}}</a></h2>
                    <p>{{article.subtitle}}</p>
                    <p><a v-bind:href="'../' + article.slug" role="button" class="leggi">Continua la lettura</a></p>
                </div>
            </article>

            <div>
                <ul class="pagination justify-content-center pagination-md">
                    <li class="page-item">
                        <a class="page-link" @click.prevent="changePage(pagination.page - 1)"
                            :disabled="pagination.page <= 1">«</a>
                    </li>
                    <li v-for="page in pages" class="page-item" :class="isCurrentPage(page) ? 'active' : ''">
                        <a class="page-link" :class="isCurrentPage(page) ? 'active' : ''"
                            @click.prevent="changePage(page)">{{ page }}</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" @click.prevent="changePage(pagination.page + 1)"
                            :disabled="pagination.page >= pagination.pageCount">»</a>
                    </li>
                </ul>
            </div>


        </section>

    </div>
</div>


<?= $this->Html->script('node_modules/axios/dist/axios.min.js', ['block'=>true]) ?>
<?= $this->Html->script('search.js',['block' => true]) ?>