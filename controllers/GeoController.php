<?php
namespace app\controllers;

use yii\rest\ActiveController;
use Yii;

/**
 * Class GeoController
 * @package app\controllers
 */
class GeoController extends ActiveController
{
    public $modelClass = 'geo';

    /**
     * Возвращает страну, город, долготу, широту
     * Кэширует результат на 30 минут
     * @param string $ip
     * @return string
     */
    public function actionGet(string $ip): string
    {
        $result = Yii::$app->cache->get($ip);

        if (false === $result) {
            $result = new \stdClass();

            try {
                $geoip = Yii::$app->geoip->ip($ip);
                $result->lng = $geoip->location->lng;
                $result->lat = $geoip->location->lat;
                $result->city = $geoip->city;
                $result->country = $geoip->country;

                Yii::$app->cache->set($ip, $result, 1800);
            } catch (\Exception $e) {
                $result->error = '404';
            }
        }

        return json_encode($result);
    }
}
