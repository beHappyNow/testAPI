<?php

//test view. Was used to check work of actions that provide possibility of uploading images to server
use yii\widgets\ActiveForm;
?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <input type="hidden" name="image" value="">
    <input type="file" id="uploadform-image" name="image">
<?= $form->field($model, 'first_name')->textInput()->hint('Пожалуйста, введите имя')->label('Имя') ?>
    <button>Отправить</button>

<?php ActiveForm::end() ?>