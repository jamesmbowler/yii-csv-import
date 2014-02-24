<?php
/**
 *
 * @author James Bowler
 * 
 * Action Class. This must be referenced in Controller, like this:
 
 public function actions()
    {
        return array(
            'import'=>array('class'=>'ext.import.components.ImportModels', 'model'=>'Deals'),
            'template'=>array('class'=>'ext.import.components.ImportTemplate', 'model'=>'Deals')
        );
    }
 */
class ImportTemplate extends CAction
{
    public $model;
    public function run($model)
	{
	    $m = new $this->model;
	    header("Content-type:text/csv");
        header("Content-Disposition:attachment; filename='".$model."-import-template.csv'");  
        $out = $m->import->template;
        
        echo $out;
	}
}