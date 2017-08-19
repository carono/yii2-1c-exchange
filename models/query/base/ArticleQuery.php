<?php
namespace carono\exchange1c\models\query\base;

use yii\data\ActiveDataProvider;
use yii\data\Sort;

/**
 * This is the ActiveQuery class for \carono\exchange1c\models\Article
 * @see \carono\exchange1c\models\Article
 */
class ArticleQuery extends \yii\db\ActiveQuery
{

	/**
	 * @inheritdoc
	 * @return \carono\exchange1c\models\Article[]
	 */
	public function all($db = NULL)
	{
		return parent::all($db);
	}


	/**
	 * @inheritdoc
	 * @return \carono\exchange1c\models\Article
	 */
	public function one($db = NULL)
	{
		return parent::one($db);
	}


	/**
	 * @var mixed $filter
	 * @var array $options Options for ActiveDataProvider
	 * @return ActiveDataProvider
	 */
	public function search($filter = NULL, $options = [])
	{
		$this->filter($filter);
		$sort = new Sort();
		    return new ActiveDataProvider(
		    array_merge([
		        'query' => $this,
		        'sort'  => $sort
		    ], $options)
		);
	}


	/**
	 * @var mixed $model
	 * @return $this
	 */
	public function filter($model = NULL)
	{
		if ($model){
		//
		}
		return $this;
	}

}
