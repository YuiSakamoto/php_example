<?php

/**
 * @param  array $words   抽出対象の配列($weightsと要素数は同数)
 * @param  array $weights 重みの配列(要素はint型)
 *
 * @return mixed $word    抽出された要素
 *
 * @throws Exception      Validationエラー時に投げられる例外
 */
function lotteryWeighted($words, $weights)
{
    //Validation.
    try {
        foreach ($weights as $weight) {
            if (!is_int($weight)) {
                throw new Exception("Weights only type int.");
            }
        }

        $targets = array_combine($words, $weights);
        if (!$targets) {
            throw new Exception("The number of elements does not match.");
        }
    } catch (Exception $e) {
        echo $e->getMessage();
        exit;
    }

    //抽出
    $sum = array_sum($targets);
    $judgeVal = rand(1, $sum);

    foreach ($targets as $word => $weight) {
        if (($sum -= $weight) < $judgeVal) {
            return $word;
        }
    }
}

// 抽出対象と重みの配列
$words = [
    "a",
    "b",
    "c",
    "d"
];
$weights = [
    1.1,
    1,
    2,
    3
];

// 実行&出力
$result = lotteryWeighted($words, $weights);
echo $result;
