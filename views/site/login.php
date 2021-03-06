<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\form\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>

    <?=$form->field($model, 'email')->textInput()->label('Email')?>
    <?=$form->field($model, 'password')->passwordInput()->label('Пароль')?>
    <?=$form->field($model, 'remember')->checkbox([
        'template' => "<div class=\"col-lg-offset-1 col-lg-3\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
    ])->label('Запомнить меня')?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('Авторизация', ['class' => 'btn btn-success', 'name' => 'login-button']) ?>
        </div>
    </div>

    <h3>Нет аккаунта? </h3>
    <div class="col-lg-offset-1 col-lg-11">
        <a href="/register" class="btn btn-primary">Регистрация</a>
    </div>

    <?php ActiveForm::end(); ?>
</div>
