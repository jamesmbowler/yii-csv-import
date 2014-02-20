<?php

/**
 * This module adds CSV Import support 
 *
 * @author James Bowler
 * @package yiiext.modules.import
 */
class ImportModule extends CWebModule
{
	/**
	 * @var array associative array of 'scopename' to commentable models 'modelclass'
	 *
	 *
	 * 'modelclass' is a class name of the importable AR
	 * this AR class must have the {@see CommentableBehavior} attached to it
	 */
	public $commentableModels = array();

	/**
	 * @var string allows you to extend comment class and use your extended one, set path alias here
	 */
	public $importModelClass = 'import.models.Import';

    /**
	 * @var array you can set filters that will be added to the comment controller {@see CController::filters()}
	 */
	public $controllerFilters = array();

	/**
	 * @var array you can set accessRules that will be added to the comment controller {@see CController::accessRules()}
	 */
	public $controllerAccessRules = array();
	
    public function init()
    {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application

        // import the module-level models and components
        $this->setImport(array(
	        'import.models.*',
	        'import.behaviors.*',
        ));
    }

    public function beforeControllerAction($controller, $action)
    {
        if(parent::beforeControllerAction($controller, $action))
        {
            // this method is called before any module controller action is performed
            // you may place customized code here
            return true;
        }
        else
            return false;
    }

	/**
	 * This event is raised after a new import has been added
	 *
	 * @param $comment
	 * @param $model
	 */
	public function onAfterImport($modelIds, $modelName)
	{
		$event = new ImportEvent();
		$event->modelIds = $modelIds;
		$event->modelName = $modelName;
		$this->raiseEvent('onAfterImport', $event);
	}
	
	/**
	 * This event is raised after a new comment has been added
	 *
	 * @param $comment
	 * @param $model
	 */
	
	public function onBeforeShowForm($model)
	{
	    if(method_exists(get_class($model), 'onBeforeShowForm'))
	    {
	        $model = $model->onBeforeShowForm($model);
	    }
	    return $model;
	
	}

}