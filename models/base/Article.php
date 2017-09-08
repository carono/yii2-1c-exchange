<?php

namespace carono\exchange1c\models\base;

use Yii;

/**
 * This is the base-model class for table "article".
 *
 * @property integer $id
 * @property string $name
 * @property integer $parent_id
 * @property string $content
 * @property string $created_at
 * @property string $updated_at
 */
class Article extends \yii\db\ActiveRecord
{

protected $_relationClasses = [];


    /**
    * @inheritdoc
    * @return \carono\exchange1c\models\Article    */
    public static function findOne($condition, $raise = false)
    {
        $model = parent::findOne($condition);
        if (!$model && $raise){
            throw new \yii\web\HttpException(404,'Model carono\exchange1c\models\Article not found');
        }else{
            return $model;
        }
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%article}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id'], 'integer'],
            [['content'], 'string'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('models', 'ID'),
            'name' => Yii::t('models', 'Name'),
            'parent_id' => Yii::t('models', 'Parent ID'),
            'content' => Yii::t('models', 'Content'),
            'created_at' => Yii::t('models', 'Created At'),
            'updated_at' => Yii::t('models', 'Updated At'),
        ];
    }
    public function getRelationClass($attribute)
    {
        return isset($this->_relationClasses[$attribute]) ? $this->_relationClasses[$attribute] : null;
    }

    
    /**
     * @inheritdoc
     * @return \carono\exchange1c\models\query\ArticleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \carono\exchange1c\models\query\ArticleQuery(get_called_class());
    }


}
