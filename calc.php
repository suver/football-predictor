<?php


class Calc
{

    private $data = [];
    private $comandsCount = 0;
    private $meanAttackForce = 0;
    private $meanProtectionForce = 0;
    private $unknownForceIndex = 0;
    private $influenceUnknownForce = 0;

    public $debug = false;

    public function __construct($data)
    {

        $this->data = $data;
        $this->comandsCount = count($this->data);

        $this->calcMeanAttackForce();

        $this->calcAttackPower();
        $this->calcProtectionForce();
        $this->calcFocusVictory();

        $this->geneateUnknownForce();
//
//        var_dump($this->data);

    }

    // среднее значение силы атаки
    private function calcMeanAttackForce()
    {
        $goalsCount = 0;
        $gamesCount = 0;
        $skipedCount = 0;
        foreach ($this->data as $k => $data) {
            $goalsCount += $data['goals']['scored'];
            $skipedCount += $data['goals']['skiped'];
            $gamesCount += $data['games'];
        }

//        echo "ВСЕГО ИГР: " . $gamesCount . "\n";
//        echo "ВСЕГО ГОЛОВ: " . $goalsCount . "\n";
//        echo "ВСЕГО КОМАНД: " . $this->comandsCount . "\n";

        $this->meanAttackForce = $goalsCount / $gamesCount;
        $this->meanProtectionForce = $skipedCount / $gamesCount * 1;

//        echo "\n\nСРЕДНЕЕ ЧИСЛО ЗАБИТЫХ ГОЛОВ: " . $this->meanAttackForce . "\n";
//        echo "\n\nСРЕДНЕЕ ЧИСЛО ПРОПУЩЕННЫХ ГОЛОВ: " . $this->meanProtectionForce . "\n";
//        echo "--------------\n\n";
    }

    // сила атаки команды
    private function calcAttackPower()
    {
        foreach ($this->data as $k => $data) {
            $this->data[$k]['attackPower'] = $data['goals']['scored'] / $data['games'] / $this->meanAttackForce;
        }
    }

    // сила защиты команды
    private function calcProtectionForce()
    {
        foreach ($this->data as $k => $data) {
            $this->data[$k]['protectionForce'] = $data['goals']['skiped'] / $data['games'] / $this->meanProtectionForce;
        }
    }

    /**
     * Стремление к победе
     */
    private function calcFocusVictory()
    {
        foreach ($this->data as $k => $data) {
            $this->data[$k]['focusVictory'] = $data['win'] / ($data['defeat'] + $data['draw']);
        }
    }

    /**
     * Неведомая сила
     */
    private function geneateUnknownForce()
    {
        $this->unknownForceIndex = rand(0, 40) / 10;
        $this->influenceUnknownForce = rand(0, 10) / 10;
    }

    private function getUnknownForce($commandIndex)
    {
        if ($this->unknownForceIndex >= 1 && $this->unknownForceIndex < 3 && $commandIndex == 1) {
            return $this->influenceUnknownForce;
        }
        else if ($this->unknownForceIndex >= 2 && $this->unknownForceIndex < 4 && $commandIndex == 2) {
            return $this->influenceUnknownForce;
        }
        return 0;
    }

    private function appendUnknownForce($unknownForce, $koef)
    {
        if ($unknownForce > 0) {
            return $koef * $unknownForce;
        }
        return $koef;
    }

    public function match($c1, $c2)
    {

        $this->geneateUnknownForce();

        $command1 = $this->data[$c1];
        $command2 = $this->data[$c2];

        $command1UnknownForce = $this->getUnknownForce(1);
        $command2UnknownForce = $this->getUnknownForce(2);

        $t1 = ($this->appendUnknownForce($command1UnknownForce, $command1['attackPower'] * $command1['focusVictory'])) /
                ($this->appendUnknownForce($command2UnknownForce, $command2['protectionForce'] * $command2['focusVictory'])) /
                $this->meanAttackForce;
        $t2 = ($this->appendUnknownForce($command2UnknownForce, $command2['attackPower'] * $command2['focusVictory'])) /
                ($this->appendUnknownForce($command1UnknownForce,$command1['protectionForce'] * $command1['focusVictory'])) /
                $this->meanAttackForce;

        if ($this->debug) {
            echo "Играет '{$command1['name']}' VS '{$command2['name']}' \n";
            echo "\tГОЛОВ + '{$command1['goals']['scored']}' VS '{$command2['goals']['scored']}' \n";
            echo "\tГОЛОВ - '{$command1['goals']['skiped']}' VS '{$command2['goals']['skiped']}' \n";
            echo "\tПОБЕД '{$command1['win']}' VS '{$command2['win']}' \n";
            echo "\tПОРАЖЕНИЙ '{$command1['defeat']}' VS '{$command2['defeat']}' \n";
            echo "\tНИЧЬИХ '{$command1['draw']}' VS '{$command2['draw']}' \n";
            echo "\tАТАКА '{$command1['attackPower']}' VS '{$command2['attackPower']}' \n";
            echo "\tЗАЩИТА '{$command1['protectionForce']}' VS '{$command2['protectionForce']}' \n";
            echo "\tВОЛЯ '{$command1['focusVictory']}' VS '{$command2['focusVictory']}' \n";
            echo "\tНЕВЕДОМАЯ СИЛА [{$this->unknownForceIndex}/{$this->influenceUnknownForce}] '{$command1UnknownForce}' VS '{$command2UnknownForce}' \n";
            echo "\tКОЭФИЦИЕНТ ГОЛА '{$t1}' VS '{$t2}' \n";
            echo "\t-----------\n";
        }

        // переводим вероятности гола в голы
        return [
            round($t1),
            round($t2),
        ];
    }

    private function getData()
    {
        return $this->data;
    }

}