<?php
/* @var $this DealsController */

$form = $this->beginWidget('booster.widgets.TbActiveForm',
    array(
        'id' => 'csv-form',
        'enableAjaxValidation' => false,
        'htmlOptions' => array('enctype' => 'multipart/form-data'),
        'action'=>array('/import/import/upload'),
        'enableClientValidation'=>true,
        'clientOptions'=>array(
			'validateOnSubmit'=>true,
		),
    )
);
	?>

	<p><a class="u" href="<?php echo $this->createUrl($m->import->returnUrl);?>"><< back</a></p>

	<h1>Import <?php echo get_class($m);?></h1>

	<p><a class="u" target="_blank" href="template?model=<?php echo get_class($m);?>">View Template</a></p>
	<?php echo $form->fileFieldGroup($model, 'csv_file');

	echo $form->hiddenField($model, 'model', array('value'=>get_class($m)));
	echo $form->hiddenField($model, 'returnUrl', array('value'=>$m->import->returnUrl));
	?>
  
	<?php $this->widget('booster.widgets.TbButton',
		array(
			'label'=>'Import',
			'buttonType'=>'submit'
	));

$this->endWidget();
