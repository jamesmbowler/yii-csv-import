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
class ImportModels extends CAction
{
    public $model;
    
    public function run()
    {
        $m = new $this->model;
        $model = new ImportFile;
        $controller=$this->getController();
        $controller->render('import.views.import.upload', array(
            'model'=>$model,
            'm'=>$m
        ));
    }
}
