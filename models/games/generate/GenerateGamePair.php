<?php

namespace app\models\games\generate;

class GenerateGamePair
{
    /**
     * @var int Размер игрового поля
     */
    public $gameCountBox = 20;
    /**
     * @var array Игровое поле
     */
    public $gameBox = [];
    /**
     * @var int Количество уникальных пар, которые будут использованы в текущей игре
     */
    public $pair;
    /**
     * @var int Максимальное количество уникальных пар для текущей игры
     */
    public $pairMax;
    /**
     * @var int Минимальное количество уникальных пар для текущей игры
     */
    public $pairMin;
    /**
     * @var array Массив с идентификаторами используемых пар
     */
    public $pairUsed = [];
    /**
     * @var int Количество повторений пар на игровом поле
     */
    public $pairRepeat = 0;
    /**
     * @var int Максимальное количество повторений пар на игровом поле
     */
    public $pairRepeatMax;

    /**
     * Массив с идентификаторами всех пар в игре
     * @return array
     */
    public function setPairs()
    {
        return [
            'apple', 'ball', 'bear', 'bee', 'bird', 'bus', 'butterfly', 'cake', 'caramel', 'cat',
            'cherry', 'cock', 'cow', 'cucumber', 'dog', 'dragon', 'elephant', 'fir', 'fish', 'fox',
            'frog', 'giraffe', 'grasshopper', 'heart', 'hedgehog', 'hippopotamus', 'horse', 'house',
            'lion', 'mouse', 'mushroom', 'penguin', 'pig', 'star', 'plane', 'rabbit', 'raspberry',
            'rocket', 'rose', 'sheep', 'ship', 'sun', 'train', 'turtle', 'watermelon', 'wood', 'zebra'
        ];
    }

    /**
     * Формирование количества пар и повторов пар для текущей игры исходя из размера игрового поля
     * @param $gameCountBox
     */
    public function countPairs($gameCountBox)
    {
        $this->gameCountBox = $gameCountBox;
        $this->pairMax = $this->gameCountBox / 2;
        $this->pairMin = $this->pairMax / 2;
        $this->pair = rand($this->pairMin, $this->pairMax);
        $this->pairRepeatMax = $this->pairMax - $this->pair;
    }

    /**
     * Формирование массива со случайными парами для текущей игры
     */
    public function setPairsUsed()
    {
        // Запрос к базе данных используя Active Record
        $pairGetDB = $this->setPairs();

        $randomPairKeys = array_rand($pairGetDB, $this->pair);

        foreach($randomPairKeys as $randomPairKey){
            $this->pairUsed[] = $pairGetDB[$randomPairKey];
        }
    }

    /**
     * Создание игрового поля со случайно размещенными на нем парами
     */
    public function createGameBox()
    {
        foreach ($this->pairUsed as $pair){
            $repeat = $this->repeatPair();

            $countCell = ($repeat + 1) * 2;

            $this->fillGameBox($countCell, $pair);
        }

        $emptyCell = $this->gameCountBox - count($this->gameBox);

        if ($emptyCell > 0){
            $countAllowedPair = $emptyCell / 2;

            for ($i = 1; $i <= $countAllowedPair; $i ++){
                $this->fillGameBox(2, $this->pairUsed[array_rand($this->pairUsed)]);
            }
        }

        shuffle($this->gameBox);
        shuffle($this->gameBox);
        shuffle($this->gameBox);
    }

    /**
     * Наполнение поля парами
     * @param $countCell
     * @param $pair
     */
    public function fillGameBox($countCell, $pair)
    {
        for ($i = 1; $i <= $countCell; $i ++){
            $this->gameBox[] = $pair;
        }
    }

    /**
     * Повтор пары на поле
     * @return int
     */
    public function repeatPair()
    {
        if ($this->pairRepeatMax > 0 && $this->pairRepeat < $this->pairRepeatMax){
            $repeat = rand(0, $this->pairRepeatMax - $this->pairRepeat);

            $this->pairRepeat += $repeat;

            return $repeat;
        }

        return 0;
    }

    public function getPair()
    {
        return $this->pairMax;
    }

    public function runGame($gameCountBox)
    {
        $this->countPairs($gameCountBox);

        $this->setPairsUsed();

        $this->createGameBox();

        return implode(';', $this->gameBox);
    }
}