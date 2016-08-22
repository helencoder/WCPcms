<?php
/**
 * Author：helen
 * CreateTime: 2016/08/20 15:44
 * Description：新闻服务控制器
 */
namespace Home\Controller\Service;

use Home\Controller\CommonController;

class NewsController extends CommonController
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
     * 新闻类型--指定新闻api
     */
    private function newsType($type)
    {
        switch ($type) {
            case 'social':
                $url = 'http://apis.baidu.com/txapi/social/social?num=10';
                break;
            case 'sports':
                $url = 'http://apis.baidu.com/txapi/tiyu/tiyu?num=10';
                break;
            case 'technology':
                $url = 'http://apis.baidu.com/txapi/keji/keji?num=10';
                break;
            case 'world':
                $url = 'http://apis.baidu.com/txapi/world/world?num=10';
                break;
            default:
                $url = 'http://apis.baidu.com/txapi/weixin/wxhot?num=10&rand=1';
                break;
        }
        return $url;
    }

    /**
     * 获取相应新闻资讯
     */
    private function getNewsData($type)
    {
        // 首先进行数据库查找
        $date = date('Y-m-d');
        $map = array(
            'date' => $date,
            'type' => $type
        );
        $apiNews = D('ApiNews');
        $newsList = $apiNews->getNewsDataByCondition($map);
        if (empty($newsList)) {
            $url = $this->newsType($type);
            $apikey = $this->getApiKey();
            $header = array(
                'apikey: ' . $apikey
            );
            $getNewsRes = request($url, '', $header);
            $data = (array)$getNewsRes;
            if ($data['code'] == 200) {
                $list = (array)$data['newslist'];
                $newsList = array();
                foreach($list as $key=>$value) {
                    $value->date = $date;
                    $value->type = $type;
                    array_push($newsList, (array)$value);
                }
                $addRes = $apiNews->addAll($newsList);
            }
        }
        //$data = json_encode($data, JSON_UNESCAPED_UNICODE );
        return $newsList;
    }

    /**
     * 新闻信息展示封装
     */
    public function newsData()
    {
        $type = $_GET['type'];
        if (empty($type)) {
            $type = 'wxhot';
        }
        $newsList = $this->getNewsData($type);
        if (empty($newsList)) {
            $apiNews = D('ApiNews');
            $newsList = $apiNews->getNewsDataByDate(date('Y-m-d'));
        }
        $this->assign('type', $type);
        $this->assign('date', date('Y-m-d'));
        $this->assign('newsList', $newsList);
        $this->display();
    }


}