<?php

namespace carono\exchange1c\models;

use carono\exchange1c\models\query\ArticleQuery;
use Yii;
use \carono\exchange1c\models\base\Article as BaseArticle;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

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

    /**
     * @param null $parent
     * @return array
     */
    public static function formMenuItems($parent = null)
    {
        /**
         * @var Article $group
         */
        $items = [];
        foreach (self::find()->andWhere(['parent_id' => $parent])->orderBy(['[[pos]]' => SORT_ASC])->each() as $group) {
            $items[] = $group->formForMenu();
        }
        return $items;
    }

    public function delete()
    {
        $files = self::extractFilesFromString($this->content);
        foreach ($files as $file) {
            @unlink(Yii::getAlias(Yii::$app->getModule('redactor')->uploadDir . '/' . $file));
        }
        foreach ($this->articles as $article) {
            $article->delete();
        }
        return parent::delete();
    }

    public static function extractFilesFromString($content)
    {
        preg_match_all('#/file/article\?file=([\w\d\-\/\.]+)"#ui', $content, $m);
        return $m[1];
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($content = ArrayHelper::getValue($changedAttributes, 'content')) {
            $old = self::extractFilesFromString($content);
            $new = self::extractFilesFromString($this->content);
            foreach (array_diff($old, $new) as $file) {
                @unlink(Yii::getAlias(Yii::$app->getModule('redactor')->uploadDir . '/' . $file));
            }
        }
        parent::afterSave($insert, $changedAttributes);
    }
}
