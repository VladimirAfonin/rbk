<?php
namespace frontend\services;

use frontend\helpers\ParseHelper;

class ParseService
{
    /**
     * @param $htmlDom
     * @return array
     */
    public function getParsedData($htmlDom)
    {
        $shortResultArray = [];
        $links = $htmlDom->query('//div[@class="js-news-feed-list"]/a[@class="news-feed__item js-news-feed-item js-yandex-counter"]')->length;

        for ($i = 0; $i <= $links - 1; $i++) {
            $item = $htmlDom->query('//div[@class="js-news-feed-list"]/a[@class="news-feed__item js-news-feed-item js-yandex-counter"]')->item($i)->nodeValue ?? null;
            $href = $htmlDom->query('//div[@class="js-news-feed-list"]/a[@class="news-feed__item js-news-feed-item js-yandex-counter"]/@href')->item($i)->nodeValue ?? null;
            $title = $htmlDom->query('//div[@class="js-news-feed-list"]/a[@class="news-feed__item js-news-feed-item js-yandex-counter"]/span[contains(@class, "news-feed__item__title")]/text()')->item($i)->nodeValue ?? null;
            $dateBlock = $htmlDom->query('//div[@class="js-news-feed-list"]/a[@class="news-feed__item js-news-feed-item js-yandex-counter"]/span[@class="news-feed__item__date"]/span[@class="news-feed__item__date-text"]/text()')->item($i)->nodeValue ?? null;
            $additionalData = explode(',', trim($dateBlock));
            $category = $additionalData[0] ?? 'нет данных';
            $time = $additionalData[1] ?? 'нет данных';

            $shortResultArray[$i] = [
                'item' => trim($item),
                'href' => trim($href),
                'title' => trim($title),
                'category' => trim($category),
                'time' => trim($time)
            ];
        }

        $fullDataResult = [];
        foreach($shortResultArray as $k => $item) {
            $response = ParseHelper::getRequestToUrl($item['href']);
            $htmlDom = ParseHelper::dom($response);

            $title = $htmlDom->query('//h1[@class="js-slide-title"]/text()')->item(0)->nodeValue ?? null;
            $image = $htmlDom->query('//div[@class="article__main-image__wrap"]/img/@src')->item(0)->nodeValue ?? null;
            $subTitle = $htmlDom->query('//div[@class="article__subtitle"]/text()')->item(0)->nodeValue ?? null;
            $fullTextsLength = $htmlDom->query('//div[@class="article__text article__text_free"]/p')->length ?? null;

            $p = '';
            for($i = 0; $i < $fullTextsLength - 1; $i++) {
                $p .= $htmlDom->query('//div[@class="article__text article__text_free"]/p/text()')->item($i)->nodeValue ?? null;
            }

            $fullDataResult[$k] = [
                'title' => trim($title),
                'short_title' => $item['title'],
                'image' => trim($image),
                'subTitle' => trim($subTitle),
                'fullText' => trim($p),
                'category' => $item['category'],
                'date_parse' => $item['time'],
            ];

        }
        return  $fullDataResult;
    }
}