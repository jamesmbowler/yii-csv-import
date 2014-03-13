<?php
/* @var $this DealsController */


$form = $this->beginWidget(
    'CActiveForm',
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
<?php

echo $form->label($model, 'csv_file');
echo $form->fileField($model, 'csv_file');
echo $form->error($model, 'csv_file');

echo $form->hiddenField($model, 'model', array('value'=>get_class($m)));
echo $form->hiddenField($model, 'returnUrl', array('value'=>$m->import->returnUrl));
?>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit'); ?>
	</div>
	
	<?php
$this->endWidget();
