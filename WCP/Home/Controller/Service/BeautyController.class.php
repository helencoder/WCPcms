<?php
/**
 * Author：helen
 * CreateTime: 2016/08/22 23:11
 * Description：美女图片控制器
 */
namespace Home\Controller\Service;

use Home\Controller\CommonController;

class BeautyController extends CommonController
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
     * 获取相应新闻资讯
     */
    private function getBeautyData()
    {
        $url = 'http://apis.baidu.com/txapi/mvtp/meinv?num=20';
        $apikey = $this->getApiKey();
        $header = array(
            'apikey: ' . $apikey
        );
        $getBeautyRes = request($url, '', $header);
        $data = (array)$getBeautyRes;
        if ($data['code'] == 200) {
            $list = (array)$data['newslist'];
            $newsList = array();
            foreach($list as $key=>$value) {
                array_push($newsList, (array)$value);
            }
        }
        //$data = json_encode($data, JSON_UNESCAPED_UNICODE );
        return $newsList;
    }

    /**
     * 新闻信息展示封装
     */
    public function beautyData()
    {
        $beautyList = $this->getBeautyData();
        $this->assign('date', date('Y-m-d'));
        $this->assign('beautyList', $beautyList);
        $this->display();
    }
}