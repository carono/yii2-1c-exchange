<?php

namespace carono\exchange1c\models;

use carono\exchange1c\models\query\ArticleQuery;
use Yii;
use \carono\exchange1c\models\base\Article as BaseArticle;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "article".
 *
 * @property Article parent
 * @property Article[] articles
 */
class Article extends BaseArticle
{
    /**
     * @return ArticleQuery|ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Article::class, ['id' => 'parent_id']);
    }

    /**
     * @return ArticleQuery|ActiveQuery
     */
    public function getArticles()
    {
        return $this->hasMany(Article::class, ['parent_id' => 'id']);
    }

    /**
     * @return null|object|\yii\db\Connection|mixed
     */
    public static function getDb()
    {
        return Yii::$app->get('exchangeDb');
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'value' => new Expression('CURRENT_TIMESTAMP')
            ]
        ];
    }

    /**
     * @return array
     */
    public function formForMenu()
    {
        /**
         * @var Article $subGroup
         */
        $item = ['label' => $this->name, 'url' => ['article/view', 'id' => $this->id]];
        foreach ($this->getArticles()->orderBy(['[[pos]]' => SORT_ASC])->each() as $subGroup) {
            $item['items'][] = $subGroup->formForMenu();
        }
        return $item;
    }

    /**
     * @param int|null $parent
     * @return array
     */
    public static function formMenuItems($parent = null)
    {
        /**
         * @var Article $group
         */
        $items = [];
        $query = self::find()->andWhere(['parent_id' => $parent])->orderBy(['[[pos]]' => SORT_ASC]);
        foreach ($query->each() as $group) {
            $items[] = $group->formForMenu();
        }
        return $items;
    }

    /**
     * @param int $deep
     * @return array
     */
    public function formForTitle($deep = 0)
    {
        /**
         * @var Article $subGroup
         */
        $item = [];
        $item[] = str_repeat("\t", $deep) . '* ' . "[{$this->name}](#{$this->id})";
        foreach ($this->getArticles()->orderBy(['[[pos]]' => SORT_ASC])->each() as $subGroup) {
            $item[] = $subGroup->formForTitle($deep + 1);
        }
        return implode("\n", $item);
    }

    /**
     * @param int|null $parent
     * @return array
     */
    public static function formTitleItems($parent = null)
    {
        /**
         * @var Article $group
         */
        $items = [];
        $query = self::find()->andWhere(['parent_id' => $parent])->orderBy(['[[pos]]' => SORT_ASC]);
        foreach ($query->each() as $group) {
            $items[] = $group->formForTitle();
        }
        return $items;
    }

    /**
     * @return false|int
     */
    public function delete()
    {
        $files = self::extractFilesFromString($this->content);
        foreach ($files as $file) {
            $uploadFile = Yii::$app->controller->module->redactor->uploadDir . '/' . $file;
            FileHelper::unlink($uploadFile);
        }
        foreach ($this->articles as $article) {
            $article->delete();
        }
        return parent::delete();
    }

    /**
     * @param $content
     * @return mixed
     */
    public static function extractFilesFromString($content)
    {
        preg_match_all('#/file/article\?file=([\w\-\/\.]+)"#ui', $content, $m);
        return $m[1];
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($content = ArrayHelper::getValue($changedAttributes, 'content')) {
            $old = self::extractFilesFromString($content);
            $new = self::extractFilesFromString($this->content);
            foreach (array_diff($old, $new) as $file) {
                $uploadFile = Yii::$app->controller->module->redactor->uploadDir . '/' . $file;
                FileHelper::unlink($uploadFile);
            }
        }
        parent::afterSave($insert, $changedAttributes);
    }
}
