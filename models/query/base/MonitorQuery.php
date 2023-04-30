<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace carono\exchange1c\models\query\base;

use yii\data\ActiveDataProvider;
use yii\data\Sort;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for \carono\exchange1c\models\Monitor
 * @see \carono\exchange1c\models\Monitor
 * @method \yii\db\BatchQueryResult|\carono\exchange1c\models\Monitor[] each($batchSize = 100, $db = null)
 * @method \yii\db\BatchQueryResult|\carono\exchange1c\models\Monitor[] batch($batchSize = 100, $db = null)
 */
class MonitorQuery extends ActiveQuery
{
	/**
	 * @inheritdoc
	 * @return \carono\exchange1c\models\Monitor[]
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}


	/**
	 * @inheritdoc
	 * @return \carono\exchange1c\models\Monitor
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
	 * @var array|\yii\db\ActiveRecord $model
	 * @return $this
	 */
	public function filter($model = null)
	{
		return $this;
	}
}
