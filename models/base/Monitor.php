<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace carono\exchange1c\models\base;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the base-model class for table "{{%monitor}}".
 *
 * @property integer $id
 * @property string $url
 * @property string $route
 * @property string $headers
 * @property string $get
 * @property string $post
 * @property string $file
 * @property string $ip
 * @property string $user_id
 * @property string $response
 * @property integer $http_code
 * @property string $created_at
 */
class Monitor extends ActiveRecord
{
	protected $_relationClasses = [];


	/**
	 * @return \yii\db\Connection the database connection used by this AR class.
	 */
	public static function getDb()
	{
		return Yii::$app->get('exchangeDb');
	}


	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%monitor}}';
	}


	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
		            [['url', 'route', 'headers', 'get', 'post', 'file', 'ip', 'user_id', 'response'], 'string'],
		            [['http_code'], 'integer'],
		        ];
	}


	/**
	 * @inheritdoc
	 * @return \carono\exchange1c\models\Monitor|\yii\db\ActiveRecord
	 */
	public static function findOne($condition, $raise = false)
	{
		$model = parent::findOne($condition);
		if (!$model && $raise){
		    throw new \yii\web\HttpException(404, Yii::t('errors', "Model carono\\exchange1c\\models\\Monitor not found"));
		}else{
		    return $model;
		}
	}


	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
		    'id' => Yii::t('models', 'ID'),
		    'url' => Yii::t('models', 'Url'),
		    'route' => Yii::t('models', 'Route'),
		    'headers' => Yii::t('models', 'Headers'),
		    'get' => Yii::t('models', 'Get'),
		    'post' => Yii::t('models', 'Post'),
		    'file' => Yii::t('models', 'File'),
		    'ip' => Yii::t('models', 'Ip'),
		    'user_id' => Yii::t('models', 'User ID'),
		    'response' => Yii::t('models', 'Response'),
		    'http_code' => Yii::t('models', 'Http Code'),
		    'created_at' => Yii::t('models', 'Created At')
		];
	}


	/**
	 * @inheritdoc
	 * @return \carono\exchange1c\models\query\MonitorQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return new \carono\exchange1c\models\query\MonitorQuery(get_called_class());
	}


	/**
	 * @param string $attribute
	 * @return string|null
	 */
	public function getRelationClass($attribute)
	{
		return ArrayHelper::getValue($this->_relationClasses, $attribute);
	}
}
