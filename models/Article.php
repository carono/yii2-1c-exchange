<?php

namespace carono\exchange1c\models;

use carono\exchange1c\models\query\ArticleQuery;
use Yii;
use \carono\exchange1c\models\base\Article as BaseArticle;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "article".
 *
 * @property Article parent
 * @property Article[] articles
 */
class Article extends BaseArticle
{
    /**
     * @return ArticleQuery
     */
    public function getParent()
    {
        return $this->hasOne(Article::className(), ['id' => 'parent_id']);
    }

    /**
     * @return ArticleQuery
     */
    public function getArticles()
    {
        return $this->hasMany(Article::className(), ['parent_id' => 'id']);
    }

    public static function getDb()
    {
        return Yii::$app->get('exchangeDb');
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('CURRENT_TIMESTAMP')
            ]
        ];
    }

    public function formForMenu()
    {
        $item = ['label' => $this->name, 'url' => ['article/view', 'id' => $this->id]];
        foreach ($this->articles as $subGroup) {
            $item['items'][] = $subGroup->formForMenu();
        }
        return $item;
    }

    public static function formMenuItems($parent = null)
    {
        $items = [];
        foreach (self::findAll(['parent_id' => $parent]) as $group) {
            $items[] = $group->formForMenu();
        }
        return $items;
    }

    public function delete()
    {
        foreach ($this->articles as $article) {
            $article->delete();
        }
        return parent::delete();
    }

}
