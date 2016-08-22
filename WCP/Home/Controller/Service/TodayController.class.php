<?php
/**
 * Author：helen
 * CreateTime: 2016/08/22 23:38
 * Description：历史上的今天
 */
namespace Home\Controller\Service;

use Home\Controller\CommonController;

class TodayController extends CommonController
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
     * 获取相应历史上的今天的信息
     */
    private function getTodayData()
    {
        $month = date('m');
        $day = date('d');
        $url = 'http://apis.baidu.com/avatardata/historytoday/lookup?yue=' . $month . '&ri=' . $day . '&type=1&page=1&rows=20&dtype=JOSN&format=false';
        $apikey = $this->getApiKey();
        $header = array(
            'apikey: ' . $apikey
        );
        $getBeautyRes = request($url, '', $header);
        $data = (array)$getBeautyRes;
        if ($data['error_code'] == 0) {
            $list = (array)$data['result'];
            $newsList = array();
            foreach($list as $key=>$value) {
                array_push($newsList, (array)$value);
            }
        } else {
            $newsList = array();
        }
        //$data = json_encode($data, JSON_UNESCAPED_UNICODE );
        return $newsList;
    }

    /**
     * 新闻信息展示封装
     */
    public function todayData()
    {
        $todayList = $this->getTodayData();
        $this->assign('date', date('Y-m-d'));
        $this->assign('todyList', $todayList);
        $this->display();
    }
}
