<?php
/**
 *
 * @author James Bowler
 * 
 * File model. 
 */
class ImportFile extends CFormModel
{
    public $csv_file;
    
 
    public function rules()
    {
        return array(
            array('csv_file', 'file', 'types'=>'csv'),
            array('csv_file', 'required'),
            array('model', 'safe'),
        );
    }
    
    
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'csv_file' => 'Please upload a CSV file',
		);
	}
	
}