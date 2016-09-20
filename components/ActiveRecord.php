<?php
namespace app\components;

/**
 * Base active record class for project models
 * @package dantux\project\components
 */
class ActiveRecord extends \yii\db\ActiveRecord
{

    /** @var string  */
    public static $SLUG_PATTERN = '/^[0-9a-z-]{0,128}$/';

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Check if database is writable
            if($this->attemptDBWrite())
                return true;
            else
            {
                \Yii::$app->getSession()->setFlash('error', 'Database is not writable by the application user');
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Formats all model errors into a single string
     * @return string
     */
    public function formatErrors()
    {
        $result = '';
        foreach($this->getErrors() as $attribute => $errors) {
            $result .= implode(" ", $errors)." ";
        }
        return $result;
    }

    /**
     * Write in sessions alert messages
     * @param string $type error or success
     * @param string $message alert body
     */
    public function flash($type, $message)
    {
        if($type == 'danger')
            $type = 'error';
        if($type == 'notice')
            $type = 'info';

        \Yii::$app->getSession()->setFlash($type, $message);
    }

    public function attemptDBWrite()
    {
        try {
            $connection = \Yii::$app->db;
            return true;

        } catch (\yii\db\Exception $e) {
            \Yii::$app->getSession()->setFlash('error', var_dump($e));
            return false;
        }
    }

}
