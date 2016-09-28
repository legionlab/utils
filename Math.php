<?php

namespace LegionLab\Utils;

/**
 * Created by PhpStorm.
 * User: leonardo
 * Date: 27/08/16
 * Time: 01:36
 */
class Math
{

    public static function inputToArray($post)
    {
        $result = array();
        $input = str_replace(',', '.', $post);
        $numbers = explode(Session::get('sdata'), $input);
        foreach ($numbers as $key => $num)
            $numbers[$key] = trim($num);

        $result['numbers'] = $numbers;
        asort($numbers);

        $result['numbersOrder'] = array();

        $order = "";
        foreach ($numbers as $key => $num) {
            $order .= $num . " - ";
            array_push($result['numbersOrder'], $num);
        }


        $result['order'] = $order;

        return $result;
    }

    public static function round($number, $precision)
    {
        return intval(strval($number * pow(10, $precision))) / pow(10, $precision);
    }

    /**
     * Calcula a media de um array de numeros
     * @param array $a Array de numeros
     * @return array Retorna a media dos valores do array
     */
    public static function arithmetics(array $numbers, $round = 3)
    {
        $calculation = "(";
        foreach ($numbers as $number)
            $calculation .= "{$number} + ";
        $calculation = mb_substr($calculation, 0, -2);
        $calculation .= ") / ".count($numbers)
        ;
        $totally = array_sum($numbers) / count($numbers);

        $calculation .= " = <br>".array_sum($numbers)."/".count($numbers)." = <br><strong>".self::round($totally, $round)."</strong>";

        return array("calculation" => $calculation, "result" => $totally);
    }

    /**
     * Retorna o valor que mais aparece no array (moda estatistica)
     * @param array $a Array de valores
     * @param int $quantidade Quantidade de vezes que a moda foi observada
     * @return array Valores mais observados no array
     */
    public static function sets(array $a, &$quantidade = 0) {
        $moda = array();
        if (empty($a)) {
            return $moda;
        }

        $ocorrencias = array();
        foreach ($a as $valor) {
            $valor_str = var_export($valor, true);
            if (!isset($ocorrencias[$valor_str])) {
                $ocorrencias[$valor_str] = array(
                    'valor' => $valor,
                    'ocorrencias' => 0
                );
            }
            $ocorrencias[$valor_str]['ocorrencias'] += 1;
        }

        // Determinar maior ocorrencia
        $quantidade = null;
        foreach ($ocorrencias as $item) {
            if ($quantidade === null || $item['ocorrencias'] >= $quantidade) {
                $quantidade = $item['ocorrencias'];
            }
        }

        // Obter valores com a maior ocorrencia
        foreach ($ocorrencias as $item) {
            if ($item['ocorrencias'] == $quantidade) {
                $moda[] = $item['valor'];
            }
        }
        return $moda;
    }

    /**
     * Obtem a mediana de um array de numeros.
     * @param array $a Array de numeros
     * @param callback $comparacao Funcao de comparacao para ordenar o array (ou null para usar a funcao sort para ordenar)
     * @return array|bool Mediana do array ou false, caso seja passado um array vazio
     */
    public static function median(array $a) {
        if (empty($a)) {
            return false;
        }
        $tamanho = count($a);
        $calculation = "";
        // Tamanho impar: obter valor mediano
        if ($tamanho % 2) {
            $median = $a[(($tamanho + 1) / 2) - 1];
            $calculation .= Language::get('math', 'middle')." ".Language::get('math', 'impar') . " " . Language::get('math', 'entao')." "
                . Language::get('math', 'median')." ".Language::get('math', 'equals') . ": <br><strong>{$median}</strong>";
            // Tamanho par: obter a media simples entre os dois valores medianos
        } else {
            $v1 = $a[($tamanho / 2) - 1];
            $v2 = $a[$tamanho / 2];
            $median = ($v1 + $v2) / 2;
            $calculation .= Language::get('math', 'middle')." ".Language::get('math', 'par') . " " . Language::get('math', 'entao')." "
                . Language::get('math', 'median')." ".Language::get('math', 'equals')  . ": <br>({$v1} + {$v2}) / 2 = <br><strong>{$median}</strong>";
        }
        return array("calculation" => $calculation, "result" => $median);
    }

}
