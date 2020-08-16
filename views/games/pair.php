<?php
$this->params['breadcrumbs'][] = ['label' => 'Мой аккаунт', 'url' => '/account'];
$this->params['breadcrumbs'][] = ['label' => 'Игры', 'url' => '/account/games'];
$this->params['breadcrumbs'][] = $this->title;
?>

<div id="game-info" class="game-info">
    <span id="message"></span>
    <span>
        Общее количество пар: <i id="pair"></i>
    </span>
    <span>
        Найдено пар: <i id="pair-found"></i>
    </span>
</div>
<div class="pairs-layout"></div>


