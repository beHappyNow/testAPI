<?php

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

/**
* UploadForm is the model behind the upload form.
*/
class UploadForm extends Model
{
    /**
    * @var UploadedFile file attribute
    */
    public $image;
    public $first_name;
    public $last_name;
    public $city;
    public $country;
    public $gender;

    /**
    * @return array the validation rules.
    */
    public function rules()
    {
        return [
            [['image'], 'file'],
        ];
    }
}