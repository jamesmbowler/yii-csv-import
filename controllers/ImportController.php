<?php

/**
 *
 * @property ImportModule $module
 *
 */
class ImportController extends Controller
{
    
    public $file;
    public $model;
    /**
     * @return array action filters
     */
    public function filters()
    {
        return $this->module->controllerFilters;
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
      return $this->module->controllerAccessRules;
    }


    public function import($form = false)
    {
      // check there are no errors
      if($this->file['error']['csv_file'] != 0){
        throw Exception('there was a problem importing the file');
      }
      
      $name = $this->file['name']['csv_file'];
      $ext = pathinfo($this->file['tmp_name']['csv_file'], PATHINFO_EXTENSION);
      
      $tmpName = $this->file['tmp_name']['csv_file'];
      
      // necessary if a large csv file
      set_time_limit(0);
  
      $row = 1;
      
      //count how many Records we save
      $i = 1;
      $error = '';
      $saved = array();
        
      if(($handle = fopen($tmpName, 'r')) == FALSE){
        Throw new Exception("could not open file");
      }
      
      $modelInstance = new $this->model;
      $titleField = $modelInstance->import->titleField;
      //put array keys (display names) in $attributes so we can
      //check if all csv headers are correct
      $attributes = $modelInstance->import->displayNames;
      $modelKeys = $modelInstance->import->modelFields;
      $controller = $modelInstance->import->controller;
          
      while(($data = fgetcsv($handle, 1000, ',')) !== FALSE){
        $model[$i] = new $this->model;
        $model[$i]->scenario = 'import';
            
        //if we're in the first row, set the fields
        if($row == 1){
          //we need to check if there are any invalid attributes in the header
          
          $fields = array_filter(array_map('trim', $data));
          
          //if any fields are not in the model attributes list,
          //just end process and let the user know
          if($diff = array_diff($fields, $attributes)){
              $error.="<b class='lp30 d-b' >The CSV Header row has invalid attributes:
             ".implode(',',$diff)."</b>"; 
              
              Yii::app()->user->setFlash('warning', $error);
              $this->redirect(array('/'.$model[$i]->import->controller.'/import'));
          } 
          else {
            //ArjLog::add("no problem with fields: ".implode(',', $fields));
          }
       
          //get array of fields that the user is importing
          $presentFields = array_intersect($attributes, $fields);
          //get intersection of model fields and present fields
          //as the user does not have to import all fields in the model
          //"fields" array. Your model validation will validate
          //the fields as usual.
          $modelKeys = array_intersect_key($modelKeys,$presentFields);
          $row++;
          continue;
        }
        
        //remove space from data elements
        $data = array_map('trim', $data);
        
        //if count of csv columns does not equal count of fields in csv,
        //show error
        if(count($data) != count($fields)){
          $error.="<b class='lp30 d-b' >Number of columns does not match header for row
          $row </b>"; 
          
          ArjLog::add($error);
          $i++;
          //end script
          continue;
        }
        
        //mass assign model attributes
        $model[$i]->attributes = array_combine($modelKeys, $data);
        
        //ArjLog::add($model[$i]->attributes);
        try{
            $model[$i] = Yii::app()->getModule('import')->onBeforeShowForm($model[$i]);
        } catch (Exception $e) {
          Yii::app()->user->setFlash('warning', $e->getMessage());
          $this->redirect($model[$i]->import->returnUrl);
        }
       
        //if in non "form" mode, save models
        if(!$form){
          try{
            //ArjLog::add(json_encode($model[$i]->attributes));
            
            if($model[$i]->save()){
              //save array of Records saved, so we can check if they have images
              $saved[$model[$i]->id] = $model[$i]->attributes[$titleField];
              $i++;
            } else {
              ArjLog::add(json_encode($model[$i]->errors));
              //if model doesn't save, show errors
              $error.="<b class='lp30' >Row $row: ".$model[$i]->$titleField."</b>
              <ul>";
              
              foreach($model[$i]->errors as $err){
                foreach($err as $e){
                  $error .="<li>".$e."</li>";
                }
              }
              $error.="</ul>";
              $i++;
            }
          } catch (Exception $e){
            //ArjLog::add($e->getMessage());
            //if model doesn't save, show errors
            $error.="<b class='lp30' >Row $row: ".$model[$i]->$titleField."</b><ul>";
        
            $error .="<li>could not import: ".$e->getMessage()."</li>";
            
            $error.="</ul>";
            $i++;
          }
        }
        
        $row++;
      }
      fclose($handle);
        

    //if in form mode, return $model, which contains all models
    if($form){return $model;}

    //if there are any errors, set error Flash
    if($error != ''){
      ArjLog::add("set error: ".$error);
      $error =  "<b> Some of the rows in your CSV file were not imported: </b><br>".$error;
      Yii::app()->user->setFlash('warning', '<span class="ss_sprite ss_error">'. $error .'</span>');
      
    }
    //set success flash
    if($saved){
      $msg = '';
      foreach($saved as $k=>$v){
        $msg.= "<a target='_blank' style='color:white' href='/".$controller."/view/$k'>$v</a><br>";
      }
      Yii::app()->user->setFlash('success', 
        '<strong>'.count($saved).' Records saved.</strong><br>'.$msg);

      Yii::app()->getModule('import')->onAfterImport(array_keys($saved), $this->model);
    }
  }

  /**
   * Import Products from csv file
   */
  public function actionUpload()
  {   
    if(isset($_FILES['ImportFile'])){
      $this->file = $_FILES['ImportFile'];
      $this->model = $_POST['ImportFile']['model'];
      
      $model = new $this->model;

      try{
        if($model->import->showImportForm){
          //populate models based on csv data
          $models = $this->import(true);
          //render import view, which must be in the model 
          //views folder
          //check to see if view exists
          
          if(!is_readable(Yii::app()->basePath.'/views/'.$model->import->controller.'/'.$model->import->importView.'.php'))
          {
              Throw new Exception('View '.$model->import->importView .' does not exist.');
          }
          $this->render('//'.$model->import->controller.'/'.$model->import->importView, 
              array('models'=>$models));
          app()->end();
        }
      
        $this->import();
      } catch (Exception $e) {
        //if any exceptions are caught during import, set error
        //and redirect to returnUrl
        ArjLog::add($e->getMessage());
        
        Yii::app()->user->setFlash('error',$e->getMessage());
        $this->redirect($model->import->returnUrl);
      }
    $this->redirect($_POST['ImportFile']['returnUrl']);
        
    //if the import file was not posted, but something was posted
    //we are in "display form" mode, so we save any models that
    //pass validation, and display the form again with the 
    //remaining models (those with errors), so the user can correct
    //them and resubmit
    } elseif (isset($_POST)) {
      $modelName = key($_POST);
      $errors = false;
      
      if($model = new $modelName){
        $i = 0;
        $titleField = $model->import->titleField;
        //put array keys (display names) in $attributes so we can
        //check if all csv headers are correct
        $attributes = $model->import->displayNames;
        $modelKeys = $model->import->modelFields;
        $controller = $model->import->controller;
        
        foreach($_POST[$modelName] as $m){   
        
          $instance[$i] = new $modelName;
          $instance[$i]->attributes=$m;
          
          try{
            if(!$instance[$i]->save()){
              ArjLog::add(json_encode($instance[$i]->errors));
              $errors = true;
              Yii::app()->user->setFlash('warning',
                '&nbsp;Some '.$modelName.' were not imported due to errors, please see below.');
            } else {
              $saved[$instance[$i]->id] = $instance[$i]->attributes[$instance[$i]->import->titleField];
              //unset variable
              unset($instance[$i]);
            }
          } catch (Exception $e){
            ArjLog::add($e->getMessage());
          }
          $i++;
        }
            
        if(isset($saved)){
          $msg = "<strong>Well done!</strong> The following ".$modelName." were saved:<br>";
          foreach($saved as $k=>$v){
              $msg.= "<a target='_blank' href='/".$model->import->controller."/view/$k'>$v</a><br>";
          }
          Yii::app()->user->setFlash('success',$msg);
        
        }
        //if there are no errors, redirect to returnUrl. Otherwise,
        //return to form view with remaining records, with 
        //error messages
        if(!$errors){
            $this->redirect(array($model->import->returnUrl));
        } else {
           ArjLog::add("there were errors");
            $this->render('//'.$model->import->controller.'/'.$model->import->importView, 
                array('models'=>$instance));
            app()->end();
        }
        
      }
    }
  }
}
