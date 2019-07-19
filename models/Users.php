<?php

namespace app\models;

use Yii;
use yii\db\StaleObjectException;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $login
 * @property string $email
 * @property int $created
 * @property int $updated
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['login'], 'required'],
            [['created', 'updated'], 'integer'],
            [['login', 'email'], 'string', 'max' => 255],
            [['login'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login' => 'Login',
            'email' => 'Email',
            'created' => 'Created',
            'updated' => 'Updated',
        ];
    }


    public static function UpdateUsers($datapage, $i)
    {
        $user_count = Users::find()->count();
        if ($i * 20 <= $user_count) { // при условии, что строк таблице не становится меньше
            $user_for_delete = Users::find()->offset(($i - 1) * 20)->limit(20)->all();
            foreach ($user_for_delete as $user) try {
                $user->delete();
            } catch (StaleObjectException $e) {
            } catch (\Throwable $e) {
            }
        }

        foreach ($datapage['items'] as $item) {
            $values = [
                    'login' => $item['login'],
                    'email' => $item['email'],
                    'created' => $item['created'],
                    'updated' => $item['updated'],
                ];

                $user = new Users();
                $user->attributes = $values;
                try {
                    $user->insert();
                } catch (\Throwable $e) {

                }
        }
    }

}
