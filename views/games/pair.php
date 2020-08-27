<?php
$this->params['breadcrumbs'][] = ['label' => 'Мой аккаунт', 'url' => '/account'];
$this->params['breadcrumbs'][] = ['label' => 'Игры', 'url' => '/account/games'];
$this->params['breadcrumbs'][] = $this->title;
?>

<div id="game-info" class="game-info">
    <span id="message"></span>
    <span>
        <i id="active"></i>
    </span>
    <span>
        Общее количество пар: <i id="pair"></i>
    </span>
    <span>
        Найдено пар: <i id="pair-found"></i> <span id="pair-player"></span>
    </span>
    <span id="give" class="btn btn-danger mt-5">Сдаться</span>
</div>
<div class="pairs-layout"></div>

<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script>
    $(document).ready(function() {

        var socket = new WebSocket("ws://192.168.83.137:2346/?user_key=<?=$user->security_key?>&game_key=<?=$pair->security_key?>&game=pair&game_action=render");
        var player = <?=$user->id?>;
        var timer = null;

        function timeActive(sec = 0)
        {
            timer = setInterval(function(){
                if (sec > 0){
                    sec--;

                    $('#active').parent().html('До конца хода осталось: <i id="active">' + sec + '</i>');
                }else{
                    clearInterval(timer);
                }
            }, 1000);
        }

        function message(str, player){
            var data = JSON.parse(str);

            if (data.status === 'active'){
                if (data.player_active === player){
                    data.message = 'Ваш ход'
                }else{
                    data.message = 'Ожидайте хода противника'
                }

                if (data.type !== 'solo'){
                    timeActive(data.time_active)
                }
            }else{
                if (data.status === 'end'){
                    if (data.winner === null){
                        data.message = 'Ничья'
                    }else if (data.winner === player){
                        if (data.message === 'Игрок исключен за бездействие'){
                            data.message = 'Вы победили. ' + data.message;
                        }else if (data.message === 'Игрок сдался'){
                            data.message = 'Вы победили. ' + data.message;
                        }else{
                            data.message = 'Вы победили';
                        }
                    }else{
                        if (data.message === 'Игрок исключен за бездействие'){
                            data.message = 'Вы проиграли. Вас исключили за бездействие';
                        }else if (data.message === 'Игрок сдался'){
                            data.message = 'Вы проиграли. Вы сдались';
                        }else{
                            data.message = 'Вы проиграли';
                        }
                    }
                }

                $('#active').parent().html('<i id="active"></i>');
            }

            if (data.type !== 'solo'){
                if (data.player_one === player){
                    $('#pair-player').text('Вы: ' + data.cell_player_one + ' Противник: ' + data.cell_player_two)
                }else{
                    $('#pair-player').text('Вы: ' + data.cell_player_two + ' Противник: ' + data.cell_player_one)
                }
            }

            $('#message').text(data.message)
            $('#pair').text(data.sum_pair)
            $('#pair-found').text(data.found_pair)

            var box = '';
            for (i = 0; i < data.sum_pair * 2; i ++){
                if (data.box[i] !== ''){
                    box += '<img class="pair" src="/image/pair/' + data.box[i] + '.jpg" data-pair="' + i + '"  alt="">'
                }else{
                    box += '<div class="pair" data-pair="' + i + '"></div>'
                }
            }

            $('.pairs-layout').html(box).addClass('pairs-layout-' + data.sum_pair)
        }

        socket.onopen = function() {

        };

        socket.onerror = function(error) {

        }

        socket.onclose = function() {
        }

        socket.onmessage = function(evt) {
            clearInterval(timer);
            message(evt.data, player)
        }

        $(document).on('click', '.pair', function (){
            var message = {
                user_key: "<?=$user->security_key?>",
                game_key: "<?=$pair->security_key?>",
                game: "pair",
                game_action: "cell",
                cell: $(this).attr('data-pair')
            }

            socket.send(JSON.stringify(message));
        })

        $(document).on('click', '#give', function (){
            var message = {
                user_key: "<?=$user->security_key?>",
                game_key: "<?=$pair->security_key?>",
                game: "pair",
                game_action: "give",
            }

            socket.send(JSON.stringify(message));
        })
    });
</script>

