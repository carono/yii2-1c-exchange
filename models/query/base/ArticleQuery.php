<?php
namespace carono\exchange1c\models\query\base;

use carono\yii2helpers\QueryHelper;
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
	public function all($db = null)
	{
		return parent::all($db);
	}


	/**
	 * @inheritdoc
	 * @return \carono\exchange1c\models\Article
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}


	/**
	 * @var mixed $filter
	 * @var array $options Options for ActiveDataProvider
	 * @return ActiveDataProvider
	 */
	public function search($filter = null, $options = [])
	{
		$query = clone $this;
		$query->filter($filter);
		$sort = new Sort();
		    return new ActiveDataProvider(
		    array_merge([
		        'query' => $query,
		        'sort'  => $sort
		    ], $options)
		);
	}


	/**
	 * @var mixed $model
	 * @return $this
	 */
	public function filter($model = null)
	{
		if ($model){
		    QueryHelper::regular($model, $this);
		}
		return $this;
	}
}
