<?php

// Жеребъевка для тестирования алгоритма

class Toss
{

    private $data = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function start()
    {
        $games = [];
        $stop = false;
        do {
            if (count($this->data) < 2) {
                return $games;
            }
            $coms = array_rand($this->data, 2);

            $games[] = $coms;

            unset($this->data[$coms[0]]);
            unset($this->data[$coms[1]]);

            if (count($this->data) <= 0) {
                $stop = true;
            }
        } while (!$stop);

        return $games;
    }

}