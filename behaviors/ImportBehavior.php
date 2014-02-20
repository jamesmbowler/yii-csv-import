<?php
/**
 * Add this behavior to AR Models that are importable
 *
 * The below is an example config in an AR Model
 * <pre>
 * public function behaviors() {
 *     return array(
 *       'import' => array(
                'class' => 'ext.import.behaviors.ImportBehavior',
                //name of model
                'model'=>'Deals',
                //name of the controller
                'controller'=>'Deals',
                'fields'=>array(
                    'title'=>array('displayName'=>'Title', 'sample'=>'Oranges'),
                    'itemName'=>array('displayName'=>'Item Name', 'sample'=>'Oranges'),
                    'price'=>array('displayName'=>'Price', 'sample'=>'4 for $1'),
                    'description'=>array('displayName'=>'Description', 'sample'=>'really good oranges'),
                ),
                //url that the user is returned to after successful import
                'returnUrl'=> '/deals/index',
                //the "title" field of the model
                'titleField'=> 'title',
                //do you want the user to see the data in form view
                'showImportForm'=> false,
                //only used if "showImportForm" is set to true
                //the view that must exist in the model's view folder
                'importView'=>'show_import_stores_ext'
            ),
 *     );
 * }
 * </pre>
 *
 * @property ImportModule $module
 *
 * @author James Bowler
 * @package yiiext.modules.import
 */
class ImportBehavior extends CActiveRecordBehavior
{
	/**
	 * @var string name of the model
	 */
	public $model;
	/**
	 * @var string template for import
	 */
	public $templateInfo;
	
	/**
	 * @var string template headers for import
	 */
	public $templateHeaders;
	
	/**
	 * @var string template Values for import
	 */
	public $templateValues;
	
	/**
	 * @var string title for record
	 */
	public $titleField;
	
	/**
	 * @var boolean show import form
	 */
	public $showImportForm;
	
	/**
	 * @var string controller name
	 */
	public $controller;
	
	/**
	 * @var string view file for import form
	 */
	public $importView;
	
	/**
	 * @var array model field names for CSV Template
	 * In the form of array('title'=>array('displayName'=>'Title', 'required'=>true,'sample'=>'Oranges'),)
	 */
	public $fields;
	
	/**
	 * @var array of model names
	 */
	public $modelFields;
	
	/**
	 * @var array of model display names
	 */
	public $displayNames;
	
	/**
	 * @var string view file for import form
	 */
	public $sampleValues;
	
	/**
	 * @var string csv text that the user will download when clicking on 
	 * "view template" link
	 */
	public $template;

	/**
	 * @var string url to return to after import
	 */
	public $returnUrl;
	/**
	 * @var string name of the table column holding related Objects Id in mapTable
	 */
	public $mapRelatedColumn = null;

   /**
    * Do some preprocessing
    */
	public function attach($owner)
	{
		parent::attach($owner);
		// make sure import module is loaded so views can be rendered properly
		Yii::app()->getModule('import');
		if($this->fields)
		{
            foreach($this->fields as $k => $field)
            {
                $this->modelFields[$k] = $k;
                
                    $this->displayNames[$k] = $field['displayName'];
                    $this->sampleValues[] = $field['sample'];
            }
                $this->template = implode(',',$this->displayNames)."\n".
                    implode(',',$this->sampleValues);
        }        
	}

	/**
	 * @return CommentModule
	 */
	public function getModule()
	{
		return Yii::app()->getModule('import');
	}
}