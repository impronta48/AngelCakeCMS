
<?php $this->assign('title',"Iscrizione a {$event->title}" ); ?>

<!-- Validation -->
<?= $this->Html->script('/vendor/contact-form/validate.js',['block' => true]) ?>
<?= $this->Html->script('/vendor/contact-form/jquery.form.js',['block' => true]) ?>

    <div class="theme-inner-banner">
			<?= $this->Html->image('convegno.jpg') ?>
				<div class="">
					<h1 style="display: none">#convegno</h1>
				</div> <!-- /.opacity -->
			</div> <!-- /.theme-inner-banner -->

<div class="course-request-section">
	<div class="container">
		<div class="row">
						<div class="col-md-6 col-xs-12 float-right">
							<div class="course-request-text">

								<div class="top-title">
									<h2>Iscrizione a <?= $event->title ?></h2>
									<p><?= $event->place  ?></p>
								</div> <!-- /.top-title -->

								<div class="course-feature-list">
                  <p class="sottotitolo">
                    <?= $event->description  ?>
                  </p>
								</div> <!-- /.course-feature-list -->

							</div> <!-- /.course-request-text -->
						</div> <!-- /.col- -->
						<div class="col-md-6 col-xs-12">
							<div class="theme-form-style-one">
								<h3><?= $event->title ?></h3>

                    <?= $this->Form->create(null,[
												'url'=>\Cake\Routing\Router::url([
														'controller' => 'Participants',
														'action' => 'add'
												]),
												'id'=>'event-form'
												]); ?>

                    <?= $this->Form->control('name',['label'=>'Nome','required'=>'required']); ?>
                    <?= $this->Form->control('surname',['label'=>'Cognome','required'=>'required']); ?>
                    <?= $this->Form->control('email',['required'=>'required']); ?>
                    <?= $this->Form->control('tel',['required'=>'required']); ?>
                    <?= $this->Form->control('facebook'); ?>
                    <?= $this->Form->control('dob',['type'=>'date','dateFormat'=>'DMY','maxYear'=>2005,'minYear'=>1940,'label'=>'Data di Nascita']); ?>
                    <?= $this->Form->control('pob',['label'=>'Luogo di Nascita']); ?>
                    <?= $this->Form->control('destination_id',['label'=>'Sito di Appartenenza', 'options'=>$siti,'empty' => '-- Altro / Nessun Sito --']); ?>
                    <?= $this->Form->control('city',['label'=>'Città di Residenza']); ?>
                    <?= $this->Form->control('address',['label'=>'Indirizzo']); ?>
                    <?= $this->Form->control('diet',['label'=>'Intolleranze Alimentari o Regime Alimentare']); ?>
                    <?= $this->Form->hidden('event_id',['value'=>$event->id]); ?>
                    <?= $this->Form->hidden('referal',['value'=>\Cake\Routing\Router::url($this->request->getRequestTarget())]); ?>
                    <?= $this->Form->control('privacy',['type'=>'checkbox','label'=>'Autorizzo YEPP Italia al trattamento dei dati per le sole comunicazioni legate alla vita associativa','required'=>'required']); ?>

                    <?= $this->Form->submit('Mi Iscrivo',['class'=>"btn btn-success"]); ?>
                    <?= $this->Form->end(); ?>

							</div> <!-- /.theme-form-style-one -->
						</div> <!-- /.col- -->
        </div><!-- /.row- -->

	</div> <!-- /.container -->
</div> <!-- /.our-history -->
