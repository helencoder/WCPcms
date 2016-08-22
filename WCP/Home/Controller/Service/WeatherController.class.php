<?php
/**
 * Author：helen
 * CreateTime: 2016/08/20 15:43
 * Description：天气服务控制器
 */
namespace Home\Controller\Service;

use Home\Controller\CommonController;

class WeatherController extends CommonController
{
    /**
     * 获取接口调用的apikey
     */
    private function getApiKey()
    {
        $account_json = dirname($_SERVER['DOCUMENT_ROOT']) . '/WCP/Home/Conf/account.json';
        $account_data = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents($account_json)), true);
        $apiKey = $account_data['baidu']['apikey'];
        return $apiKey;
    }

    /**
     * 获取指定城市的天气预报信息
     */
    public function getWeatherData($city)
    {
        $apikey = $this->getApiKey();
        $header = array(
            'apikey: ' . $apikey
        );
        $url = 'http://apis.baidu.com/heweather/weather/free?city=' . $city;
        $getWeatherRes = request($url, '', $header);
        $data = (array)$getWeatherRes;
        //$data = json_encode($data, JSON_UNESCAPED_UNICODE );
        return $data;
    }

    /**
     * 城市天气预报信息封装
     */
    public function weatherData()
    {
        $city = $_GET['city'];
        $weatherData = $this->getWeatherData($city);
        $allData = (array)$weatherData['HeWeather data service 3.0'][0];
        $basicData = $allData['basic'];
        $nowData = $allData['now'];
        $environmentData = $allData['aqi']->city;
        $dailyForecastData = $allData['daily_forecast'];

        $forecastData = array();
        $date = array();
        $tmp_max = array();
        $tmp_min = array();
        foreach ($dailyForecastData as $key=>$value) {
            $dailyData = array(
                'date'          => $value->date,
                'tmp_max'       => $value->tmp->max,
                'tmp_min'       => $value->tmp->min,
                'wind'          => $value->wind->dir . $value->wind->sc . '级',
                'pcpn'          => $value->pcpn     //降水量
            );
            array_push($date, $value->date);
            array_push($tmp_max, $value->tmp->max);
            array_push($tmp_min, $value->tmp->min);
            array_push($forecastData, $dailyData);
        }

        $weatherMsg = array(
            "basic" => array(
                'city'          => $basicData->city,
                'country'       => $basicData->cnty,
                'update_time'   => $basicData->update->loc
            ),
            "environment" => array(
                'now'           => $nowData->cond->txt,
                'wind'          => $nowData->wind->dir . $nowData->wind->sc . '级',
                'aqi'           => $environmentData->aqi,   //空气质量指数
                'pm2.5'         => $environmentData->pm25,  //pm2.5
                'quality'       => $environmentData->qlty   //空气质量类别
            ),
            "forecast"          => $forecastData
        );

        $this->assign('date', $date);
        $this->assign('tmp_max', $tmp_max);
        $this->assign('tmp_min', $tmp_min);
        dump($date);
        $this->assign('weatherMsg', $weatherMsg);
        $this->display();
    }
}