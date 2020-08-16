<?php
$this->params['breadcrumbs'][] = ['label' => 'Мой аккаунт', 'url' => '/account'];
$this->params['breadcrumbs'][] = ['label' => 'Игры', 'url' => '/account/games'];
$this->params['breadcrumbs'][] = $this->title;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

?>

<div class="row">
    <div class="site-login">
        <h1><?= Html::encode($this->title) ?></h1>

        <?php $form = ActiveForm::begin([
            'id' => 'game-pair-form',
            'layout' => 'horizontal',
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
                'labelOptions' => ['class' => 'col-lg-1 control-label'],
            ],
        ]); ?>

        <?=$form->field($model, 'game_box')->dropDownList([20 => 20, 40 => 40, 60 => 60], ['options' => ['1' => ['Selected' => true]]])->label('Поле')?>

        <?=$form->field($model, 'type')->dropDownList([
            'solo' => 'одиночная', 'ai' => 'против компьютера', 'player' => 'против игрока'
        ], ['options' => ['1' => ['Selected' => true]]])->label('Тип игры')?>

        <div class="form-group">
            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::submitButton('Начать игру', ['class' => 'btn btn-success', 'name' => 'login-button']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>
