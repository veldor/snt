<?php


namespace app\models\utils;


use Yii;
use yii\db\Exception;
use yii\db\Transaction;

class DbTransaction
{
    /**
     * @var Transaction
     */
    private $transaction;

    public function __construct()
    {
        $db = Yii::$app->db;
        $this->transaction = $db->beginTransaction();
    }

    /**
     */
    public function commitTransaction(){
        try {
            $this->transaction->commit();
        } catch (Exception $e) {

        }
    }

    /**
     *
     */
    public function rollbackTransaction(){
        $this->transaction->rollBack();
    }
}