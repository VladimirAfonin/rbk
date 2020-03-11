<?php
namespace frontend\repositories;

use frontend\models\News;

class NewsRepository
{
    public static function createNews(array $data)
    {
        $db = \Yii::$app->db;
        $transaction = $db->beginTransaction();

        try {
            \Yii::$app->db->createCommand()->truncateTable(News::tableName())->execute();
            foreach($data as $k => $item) {
                $date = NewsRepository::getDate(trim($item['date_parse']));
                $news = new News();
                $news->title = $item['title'] ?? $item['short_title'];
                $news->description = $item['fullText'];
                $news->image_path = $item['image'];
                $news->date_parse = $date;
                $news->category_id = CategoryRepository::getCategoryId($item['category']);
                $news->updated_at = time();
                $news->created_at = time();
                $news->save(false);
            }
            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->session->setFlash('error', 'Ошибка создания категорий: ' . $e->getMessage());
            \Yii::$app->errorHandler->logException($e);
        }
    }

    /**
     * @param $date
     * @return false|int
     */
    public static function getDate($date)
    {
        $date = htmlentities($date, null, 'utf-8');
        $date = str_replace("&nbsp;", "", $date);
        $date = html_entity_decode($date);
       return $date;
    }

}