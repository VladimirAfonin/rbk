<?php
namespace frontend\helpers;

class ImageHelper
{
    const IMG_DIR_NAME = 'uploads';

    /**
     * get image from url
     * @param $url
     * @return string
     * @throws \yii\base\Exception
     */
    public static function getImageFromUrl($url)
    {
        if($url) {
            $hash = \Yii::$app->security->generateRandomString(8);
            $path = \Yii::getAlias('@webroot');
            $fullPath = $path . DIRECTORY_SEPARATOR . ImageHelper::IMG_DIR_NAME . DIRECTORY_SEPARATOR . $hash . '.jpeg';
            $image = file_get_contents($url);
            file_put_contents($fullPath, $image);

            return $fullPath;
        }
        return '';
    }
}