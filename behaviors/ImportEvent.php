<?php

/**
 *
 * @author James Bowler
 * 
 * For those who need to trigger a model function after 
 * import.
 */
class ImportEvent extends CEvent
{
    /**
     * @var array ids that were successfully imported
     */
    public $modelIds = null;

    /**
     * @var string the model into which the records were imported
     */
    public $modelName = null;
    
    /**
     * @var object model instance
     */
    public $model;
    
   /**
    * Triggered after import. If your model contains a method called
    * "afterImport", it will be run, with model Id's passed to it.
    * Call it like this: public function afterImport($modelIds)
    */
    public function onAfterImport($event)
    {
        $model = new $event->modelName;
        if(method_exists($model, 'afterImport'))
        {
            $model->afterImport($event->modelIds);
        }
    }   
}
