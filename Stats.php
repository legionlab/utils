<?php

namespace Llab\Utils;

/**
 * Created by PhpStorm.
 * User: leonardo
 * Date: 27/08/16
 * Time: 14:54
 */
class Stats
{

    public static function varianceGroupContinuous($middle, $fa)
    {
        $n = array_sum($fa);
        $calculation = "1 / ({$n} - 1) * [";
        $x = 0;
        $x2 = 0;
        foreach ($middle as $key => $classe) {
            $calculation .= "({$classe}² * {$fa[$key]}) + ";
            $x += (($classe * $classe) * $fa[$key]);
        }

        $calculation = mb_substr($calculation, 0, -3)."] - [";

        foreach ($middle as $key => $classe) {
            $calculation .= "({$classe} * {$fa[$key]}) + ";
            $x2 += ($classe * $fa[$key]);
        }
        $calculation = mb_substr($calculation, 0, -3)."]² / {$n} = <br>";

        $calculation .= "1 / ".($n - 1)." * ({$x}) - ({$x2}² / {$n}) =<br>";

        $x2 = Math::round($x2 * $x2, 3);
        $calculation .= "1 / ".($n - 1)." * ({$x}) - ({$x2}a / {$n}) =<br>";

        $x2 = Math::round($x2 / $n, 3);
        $n = $n -1;
        $calculation .= "1 / {$n} * [({$x}) - {$x2}a]=<br>";

        $n = 1/ $n;
        $calculation .= "{$n} * ({$x} - {$x2})=<br>";

        $x = $x - $x2;
        $calculation .= "{$n} * {$x} =<br>";

        $n = Math::round($n * $x, 3);
        $calculation .= "<strong>{$n}a</strong>";

        return array('calculation' => $calculation, 'result' => $n);
    }

    public static function varianceGroupDiscreet($classes, $fa)
    {
        $n = array_sum($fa);
        $calculation = "1 / ({$n} - 1) * [";
        $x = 0;
        $x2 = 0;
        foreach ($classes as $key => $classe) {
            $calculation .= "({$classe}² * {$fa[$key]}) + ";
            $x += (($classe * $classe) * $fa[$key]);
        }

        $calculation = mb_substr($calculation, 0, -3)."] - [";

        foreach ($classes as $key => $classe) {
            $calculation .= "({$classe} * {$fa[$key]}) + ";
            $x2 += ($classe * $fa[$key]);
        }
        $calculation = mb_substr($calculation, 0, -3)."]² / {$n} = <br>";

        $calculation .= "1 / ".($n - 1)." * ({$x}) - ({$x2}² / {$n}) =<br>";

        $x2 = Math::round($x2 * $x2, 3);
        $calculation .= "1 / ".($n - 1)." * ({$x}) - ({$x2}a / {$n}) =<br>";

        $x2 = Math::round($x2 / $n, 3);
        $n = $n -1;
        $calculation .= "1 / {$n} * [({$x}) - {$x2}a]=<br>";

        $n = 1/ $n;
        $calculation .= "{$n} * ({$x} - {$x2})=<br>";

        $x = $x - $x2;
        $calculation .= "{$n} * {$x} =<br>";

        $n = Math::round($n * $x, 3);
        $calculation .= "<strong>{$n}a</strong>";

        return array('calculation' => $calculation, 'result' => $n);
    }

    public static function variance(array $numbers)
    {
        $n = count($numbers);
        $x = array_sum($numbers);
        $x2 = 0;
        $c1 = "";
        foreach($numbers as $number) {
            $c1 .= "{$number}² + ";
            $x2 += ($number * $number);
        }

        $c1 = mb_substr($c1, 0, -3);
        $calculation = "[({$c1}) - ({$x}²)/{$n}] / ".($n - 1)." =<br>";
        $x = $x * $x;
        $calculation .= "[{$x2} - ({$x}/{$n})] / ".($n - 1)." =<br>";
        $x = Math::round($x/$n, 3);
        $calculation .= "({$x2} - {$x}a) / ".($n - 1)." =<br>";
        $x2 = $x2 - $x;
        $calculation .= "{$x2}  / ".($n - 1)." =<br>";
        $x2 = Math::round($x2 / ($n-1), 3);
        $calculation .= "<strong>{$x2}a</strong>";

        return array('calculation' => $calculation, 'result' => $x2);
    }

    public static function medianGroupDiscreet($classes, $fas)
    {
        $calculation = "";
        $n = count($classes);
        if($n % 2) {
            $calculation .= "X({$n} / 2) + 1 =<br>";
            $n = $n / 2;
            $calculation .= "X{$n} + 1 =<br>";
            $n = $n + 1;
            $calculation .= "<strong>X{$n}</strong> =<br>";

            $result = "C: {$classes[$n - 1]} | Fa: {$fas[$n - 1]}";
            $calculation .= "<strong>{$result}</strong><br><br>";
        }
        else {
            $calculation .= "{X({$n} / 2) + X[({$n} / 2) +1]} / 2 =<br>";
            $n = $n/2;
            $calculation .= "[X{$n} + X({$n} +1)] / 2 =<br>";
            $n2 = $n - 1;
            $calculation .= "(X{$n} + X{$n2}) / 2 =<br>";
            $n1 = $fas[$n-1];
            $n2 = $fas[$n2-1];
            $calculation .= "({$n1} + {$n2}) / 2 =<br>";
            $r = $n1 + $n2;
            $calculation .= "{$r} / 2 =<br>";
            $r = $r /2;
            $calculation .= "<strong>{$r}</strong>";
        }

        return array("calculation" => $calculation, "class" => $classes[$n - 1], "fa" => $fas[$n - 1]);
    }

    public static function setsGroupContinuous($limo, $famo, $faant, $fapos, $range)
    {
        $calculation = "";

        $calculation .= "{$limo} + [({$famo} - {$faant}) / ({$famo} - {$faant}) + ({$famo} - {$fapos})] * {$range} =<br>";
        $tmp1 = $famo - $faant;
        $tmp2 = $famo - $fapos;
        $calculation .= "{$limo} + [{$tmp1} / ({$tmp1} + {$tmp2})] * {$range} =<br>";
        $tmp2 = $tmp1 + $tmp2;
        $calculation .= "{$limo} + ({$tmp1} / {$tmp2}) * {$range} =<br>";
        $tmp1 = Math::round($tmp1 / $tmp2, 3);
        $calculation .= "{$limo} + ({$tmp1}a * {$range}) =<br>";
        $tmp1 = Math::round($tmp1 * $range, 3);
        $calculation .= "{$limo} + {$tmp1}a =<br>";
        $tmp1 = $limo + $tmp1;
        $calculation .= "<strong>{$tmp1}</strong>";

        return array("calculation" => $calculation, "result" => $tmp1);
    }

    public static function medianGroupContinuous($limd, $n, $facant, $famd, $cmd)
    {
        $calculation = "";

        $calculation .= "{$limd} + { [({$n}/2) + $facant] / {$famd}} * {$cmd} =<br>";
        $n = Math::round($n/2, 3);
        $calculation .= "{$limd} + [ ({$n}a + $facant) / {$famd}] * {$cmd} =<br>";
        $n = $n + $facant;
        $calculation .= "{$limd} + ({$n} / {$famd}) * {$cmd} =<br>";
        $n = Math::round($n/$famd, 3);
        $calculation .= "{$limd} + ({$n}a * {$cmd}) =<br>";
        $n = Math::round($n * $cmd, 3);
        $calculation .= "{$limd} + {$n}a =<br>";
        $n = $limd + $n;
        $calculation .= "<strong>{$n}</strong>";

        return array("calculation" => $calculation, "result" => $n);
    }


    public static function summation(array $numbers, array $fa, $round = 2)
    {
        $sum = 0;
        $calculation = "";
        foreach ($numbers as $key => $value) {
            $sum += ($value * $fa[$key]);
            $calculation .= "({$value} * $fa[$key]) + ";
        }
        $calculation = mb_substr($calculation, 0, -3);
        $sumFa = self::sum($fa);
        $totally = Math::round($sum/$sumFa, $round);
        $calculation .= " / {$sumFa}";
        $calculation .= " =<br>{$sum} / {$sumFa} =<br><strong>{$totally}a</strong>";

        return array("calculation" => $calculation, "result" => $totally);
    }

    public static function sum(array $numbers)
    {
        return array_sum($numbers);
    }

}