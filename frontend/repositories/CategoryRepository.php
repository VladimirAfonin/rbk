<?php
namespace frontend\repositories;

use frontend\models\Category;

class CategoryRepository
{
    /**
     * @param $data
     */
    public static function createCategory(array $data)
    {
        $db = \Yii::$app->db;
        $transaction = $db->beginTransaction();

        try {
            foreach($data as $k => $item) {
                if($item['category']) {
                    $isCategoryExist = Category::find()->where(['title' => $item['category']])->one();
                    if(!$isCategoryExist) {
                        $category = new Category();
                        $category->category_id = null;
                        $category->title = $item['category'];
                        $category->updated_at = time();
                        $category->created_at = time();
                        $category->save(false);
                    }
                }
            }
            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->session->setFlash('error', 'Ошибка создания категорий: ' . $e->getMessage());
            \Yii::$app->errorHandler->logException($e);
        }
    }

    /**
     * @param $category
     * @return array|Category|null|\yii\db\ActiveRecord
     */
    public static function getCategoryId($category)
    {
        $category = Category::find()->where(['title' => $category])->one();
        return $category->id ?? null;
    }
}