<?php
/**
 * Author: helen
 * CreateTime: 2016/06/07 16:27
 * Description: 微信文案控制器
 */
namespace Home\Controller\Weixin;

use Home\Controller\CommonController;

class ArticleController extends CommonController
{
    //创建文案（编辑文案）
    public function editArticle()
    {
        $article_id = $_GET['article_id'];  //获取文案的id号
        $type = empty($_GET['type']) ? '0' : $_GET['type'];              //获取文案的类型，假如未设置默认为0（即业务无关）
        $type = intval($type);      //类型转换为int型
        if (is_null($article_id)) {   //新建文案
            //根据业务类型选择输出的标签，实例化标签表，其中随机取标签进行展示（后期实现）
            $labels = M('labels');
            $table_data = $labels->where('type=1')->limit(10)->select();
            $topic_data = $labels->where('type=2')->limit(20)->select();
            $common_topic_data = $labels->where('type=2')->limit(5)->select();
            $content_data = $labels->where('type=3')->limit(20)->select();
            $common_content_data = $labels->where('type=3')->limit(5)->select();
            $this->assign('table_data', $table_data);
            $this->assign('topic_data', $topic_data);
            $this->assign('common_topic_data', $common_topic_data);
            $this->assign('content_data', $content_data);
            $this->assign('common_content_data', $common_content_data);
            //页面基本信息
            $page = array(
                'title' => '蜜枣网',
            );

            $this->assign('huitiantable', []);
            $this->assign('huitiantopic', []);
            $this->assign('huitiancontent', []);

            $this->assign('page', $page);
            $this->theme('MeeZao')->display();
        } else {      //编辑文案
            //实例化文案表
            $local_article = M('local_articles');
            $res = $local_article->where("id='$article_id'")->find();
            $thumb_pic_url = $local_article->where("id='$article_id'")->getField('thumb_pic_url');
            $this->assign('thumb_pic_url', $thumb_pic_url);
            $this->assign('article', $res);
            //标签原始数据添加
            //根据业务类型选择输出的标签，实例化标签表，其中随机取标签进行展示（后期实现）
            $labels = M('labels');
            $table_data = $labels->where('type=1')->limit(10)->select();
            $topic_data = $labels->where('type=2')->limit(20)->select();
            $common_topic_data = $labels->where('type=2')->limit(5)->select();
            $content_data = $labels->where('type=3')->limit(20)->select();
            $common_content_data = $labels->where('type=3')->limit(5)->select();
            $this->assign('table_data', $table_data);
            $this->assign('topic_data', $topic_data);
            $this->assign('common_topic_data', $common_topic_data);
            $this->assign('content_data', $content_data);
            $this->assign('common_content_data', $common_content_data);

            //将标签数据组织好进行展示。
            $table_labels = $res['table_labels'];
            $topic_labels = $res['topic_labels'];
            $content_labels = $res['content_labels'];
            //实例化文案标签中间表、标签表
            $article_labels = M('article_labels');
            $labels = M('labels');
            //组织标题标签数据
            $table_data = explode(';', $table_labels);
            $table_labels_data = array();
            foreach ($table_data as $key => $value) {
                $id = $labels->where("name='$value'")->getField('id');
                $table_labels_data[$id] = $value;
            }
            //组织题目标签数据
            $topic_data = explode(';', $topic_labels);
            $topic_labels_data = array();
            foreach ($topic_data as $key => $value) {
                if (!empty($value)) {
                    $pos = strpos($value, '-');
                    $id = substr($value, 0, $pos);
                    $name = substr($value, $pos + 1);
                    $topic_labels_data[$id] = $name;
                } else {
                    break;
                }
            }

            //组织内容标签数据
            $content_data = explode(';', $content_labels);
            $content_labels_data = array();
            foreach ($content_data as $key => $value) {
                if (!empty($value)) {
                    $pos = strpos($value, '-');
                    $id = substr($value, 0, $pos);
                    $name = substr($value, $pos + 1);
                    $content_labels_data[$id] = $name;
                } else {
                    break;
                }

            }

            $this->assign('huitiantable', json_encode($table_labels_data, JSON_UNESCAPED_UNICODE));
            $this->assign('huitiantopic', json_encode($topic_labels_data, JSON_UNESCAPED_UNICODE));
            $this->assign('huitiancontent', json_encode($content_labels_data, JSON_UNESCAPED_UNICODE));

            //页面基本信息
            $page = array(
                'title' => '蜜枣网',
            );
            $this->assign('page', $page);
            $this->theme('MeeZao')->display();

        }

    }

    //组合文案
    public function combineArticle()
    {

        //实例化文案表
        $local_articles = M('local_articles');
        $articles = $local_articles->order('update_time desc')->select();
        $this->assign('data', $articles);
        //根据文案的状态进行显示
        $map['status'] = 1;
        $p = empty($_GET['p']) ? 0 : $_GET['p'];
        // 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
        $articles = $local_articles->where($map)->order('update_time desc')->page($_GET['p'], 10)->select();
        $this->assign('data', $articles);        // 赋值数据集
        //数据分页
        $count = $local_articles->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
        $Page->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条记录&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('last', '末页');
        $Page->setConfig('first', '首页');
        $Page->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
        $Page->lastSuffix = false;//最后一页不显示为总页数

        $show = $Page->show();// 分页显示输出
        $this->assign('fenye', $show);// 赋值分页输出

        //页面基本信息
        $page = array(
            'title' => '蜜枣网',
        );
        $this->assign('page', $page);
        $this->theme('MeeZao')->display();
    }

    //单文案列表
    public function articleList()
    {
        //实例化文案表
        $local_articles = M('local_articles');
        //根据文案的状态进行显示
        $map['status'] = 1;
        $p = empty($_GET['p']) ? 0 : $_GET['p'];
        // 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
        $articles = $local_articles->where($map)->order('update_time desc')->page($_GET['p'], 10)->select();
        $this->assign('data', $articles);        // 赋值数据集
        //数据分页
        $count = $local_articles->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
        $Page->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条记录&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('last', '末页');
        $Page->setConfig('first', '首页');
        $Page->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
        $Page->lastSuffix = false;//最后一页不显示为总页数

        $show = $Page->show();// 分页显示输出
        $this->assign('fenye', $show);// 赋值分页输出

        //页面基本信息
        $page = array(
            'title' => '蜜枣网',
        );
        $this->assign('page', $page);
        $this->theme('MeeZao')->display();
    }

    //多文案列表
    public function articleListMore()
    {
        //实例化多文案表
        $local_articles = M('more_articles');
        //根据文案的状态进行显示
        $map['status'] = 1;
        $p = empty($_GET['p']) ? 0 : $_GET['p'];
        // 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
        $articles = $local_articles->where($map)->order('id desc')->page($_GET['p'], 10)->select();
        $this->assign('data', $articles);        // 赋值数据集
        //数据分页
        $count = $local_articles->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
        $Page->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条记录&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('last', '末页');
        $Page->setConfig('first', '首页');
        $Page->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
        $Page->lastSuffix = false;//最后一页不显示为总页数

        $show = $Page->show();// 分页显示输出
        $this->assign('fenye', $show);// 赋值分页输出

        //页面基本信息
        $page = array(
            'title' => '蜜枣网',
        );
        $this->assign('page', $page);
        $this->theme('MeeZao')->display();
    }

    //文案提交、保存（此处最主要的问题是图片的上传问题）(此处不进行文案的交互，在预览和群发的过程中才进行最终的交互)
    public function articleUpload()
    {
        set_time_limit(0);

        //封面图片1M限制
        if (empty($_FILES) || $_FILES["article"]["size"]['image'] > 1048576) {
            $this->error('封面图片大小大于1M');
            die;
        }
        $type = empty($_GET['type']) ? '1' : $_GET['type'];              //获取文案的类型，假如未设置默认为0（即业务无关）
        $data['type'] = intval($type);
        //获取创建文案列表上传的数据
        $article = $_POST['article'];
        $data['title'] = $article['title'];                              //标题
        $data['author'] = $article['author'];                            //作者
        $data['digest'] = $article['digest'];                            //摘要
        $data['show_cover_pic'] = $article['show_cover_pic'];            //值为1，表示图片显示在正文中；值为0，表示不显示（处理）
        $data['content'] = $article['content'];                          //正文
        $data['meng_content'] = $article['meng_content'];                //蒙版
        if (empty($data['meng_content'])) {
            $data['is_mask'] = 'N';
        } else {
            $data['is_mask'] = 'Y';                  //是否有蒙版，Y为有；N为没有
        }
        $data['status'] = 1;            //设置文案的状态，为1，表明还可以显示
        //$data['combined_state'] =                                             //组合状态
        //组合文案标题标签
        $title = $_POST['title'];
        $table_labels = '';
        foreach ($title as $key => $value) {
            $table_labels .= $value;
            $table_labels .= ';';
        }
        $data['table_labels'] = $table_labels;                         //标题标签
        $topic_labels = $_POST['topic_labels'];
        $data['topic_labels'] = $topic_labels['names'];                //题目标签
        $content_labels = $_POST['content_labels'];
        $data['content_labels'] = $content_labels['names'];            //内容标签

        /*//实例化标签表
        $labels = M('labels');
        //标签处理(自定义标签首先添加到数据库中)
        //题目标签
        $topic_labels_array = explode(';', $data['topic_labels']);
        foreach ($topic_labels_array as $key => $value) {
            //新增标签进行交互
            if (substr($value, 0, 1) == 'u') {
                $pos = strpos($value, '-');
                $label = substr($value, $pos + 1);
                $topic_label_data['name'] = $label;
                $topic_label_data['type'] = 2;
                $topic_label_data['create_time'] = date('Y-m-d H:i:s', time());
                $res = $labels->data($topic_label_data)->add();
                $topic_labels_array[$key] = $res . '-' . $label;
            }
        }
        $data['topic_labels'] = implode($topic_labels_array, ';');
        //内容标签
        $content_labels_array = explode(';', $data['content_labels']);
        foreach ($content_labels_array as $key => $value) {
            //新增标签进行交互
            if (substr($value, 0, 1) == 'u') {
                $pos = strpos($value, '-');
                $label = substr($value, $pos + 1);
                $content_label_data['name'] = $label;
                $content_label_data['type'] = 3;
                $content_label_data['create_time'] = date('Y-m-d H:i:s', time());
                $res = $labels->data($content_label_data)->add();
                $content_labels_array[$key] = $res . '-' . $label;
            }
        }
        $data['content_labels'] = implode($content_labels_array, ';');*/

        //实例化文案数据表
        $local_articles = M('local_articles');
        //假如是通过ajax传过来的数据，则进行保存操作
        //假如为生成链接，进入此通道
        if (!empty($_POST['route'])) {
            //判断进入来源
            $url_msg = $this->get_url_msg($_SERVER['HTTP_REFERER']);
            $article_id = $url_msg['article_id'];
            if (empty($article_id)) {
                //对于封面图片应该要进行二次处理
                $cover_img_path = $this->storageImg($_FILES);
                $url_pos = strlen($_SERVER['DOCUMENT_ROOT']);
                $data['wgw_content'] = $data['content'];
                if (!empty($cover_img_path)) {
                    $data['thumb_pic_url'] = substr($cover_img_path, $url_pos);   //图片(thumb后期需要进行缩略图处理 数据库中此不可为空，)                   //图片(thumb后期需要进行缩略图处理 数据库中此不可为空，)

                    //添加微官网展示字段。
                    if ($data['show_cover_pic'] == 1) {
                        $data['wgw_content'] = '<p><img alt="" floatstyle=""  style="max-width: 100%;height: auto !important;" src="' . $data['thumb_pic_url'] . '"/></p>' . $data['content'];
                    } else {
                        $data['wgw_content'] = $data['content'];
                    }

                }

                $data['create_time'] = date('Y-m-d H:i:s', time());                      //创建时间
                $data['update_time'] = date('Y-m-d H:i:s', time());                      //更新时间
                $res = $local_articles->data($data)->add();
                $url_data = 'http://m.' . $_SERVER['HTTP_HOST'] . '/Mall/Pay/getCode.html?copywriters=' . $res;
                $jump_url = 'http://m.' . $_SERVER['HTTP_HOST'] . '/MeeZao/Weixin/WeixinArticle/EditArticle.html?article_id=' . $res;
                //将新式原文链接添加到数据库
                $souce_url_data['content_source_url'] = $url_data;
                $local_articles->where("id=$res")->save($souce_url_data);


                //更新标签表等相关信息
                //实例化标签中间表
                $article_labels = M('article_labels');
                //实例化标签表
                $labels = M('labels');
                $title_labels_data = $_POST['title'];                       //标题标签
                $topic_labels = $_POST['topic_labels'];
                $topic_labels_data = $topic_labels['names'];                //题目标签
                $content_labels = $_POST['content_labels'];
                $content_labels_data = $content_labels['names'];            //内容标签
                $aid = $res;
                //删除原有标签
                $article_labels->where("aid=$aid")->delete();
                //标题标签添加到数据表中
                foreach ($title_labels_data as $key => $value) {
                    $title_data['tid'] = $key;
                    $title_data['aid'] = $aid;
                    $title_data['create_time'] = date('Y-m-d H:i:s', time());
                    $title_result = $article_labels->data($title_data)->add();
                    if (empty($title_result)) {
                        $article_labels->data($title_data)->add();
                    }
                }
                $topic_labels_data = explode(';', $topic_labels_data);
                //题目标签加入到数据表中
                foreach ($topic_labels_data as $key => $value) {
                    //对于value值的首字母进行判断，假如其为u，即为新增标签，所以需要同时更新标签表
                    $tip = substr($value, 0, 1);
                    $pos = strpos($value, '-');  //获取字符串中间-的位置，用于获取信息。
                    if ($tip == 'u') {
                        $label_data['name'] = substr($value, $pos + 1);
                        $label_data['type'] = 2;  //题目标签的类型对应2
                        $label_data['create_time'] = date('Y-m-d H:i:s', time());
                        $tid = $labels->data($label_data)->add();     //此处返回的值即为新建标签的id值
                        $topic_data['tid'] = $tid;
                    } else {
                        $topic_data['tid'] = substr($value, 0, $pos);
                    }
                    $topic_data['aid'] = $aid;
                    $topic_data['create_time'] = date('Y-m-d H:i:s', time());
                    $topic_result = $article_labels->data($topic_data)->add();
                    if (empty($topic_result)) {
                        $article_labels->data($topic_data)->add();
                    }
                }
                $content_labels_data = explode(';', $content_labels_data);
                foreach ($content_labels_data as $key => $value) {
                    //对于value值的首字母进行判断，假如其为u，即为新增标签，所以需要同时更新标签表
                    $tip = substr($value, 0, 1);
                    $pos = strpos($value, '-');  //获取字符串中间-的位置，用于获取信息。
                    if ($tip == 'u') {
                        $content_data['name'] = substr($value, $pos + 1);
                        $content_data['type'] = 3;  //题目标签的类型对应2
                        $content_data['create_time'] = date('Y-m-d H:i:s', time());
                        $tid = $labels->data($content_data)->add();     //此处返回的值即为新建标签的id值
                        $content_data['tid'] = $tid;
                    } else {
                        $content_data['tid'] = substr($value, 0, $pos);
                    }
                    $content_data['aid'] = $aid;
                    $content_data['create_time'] = date('Y-m-d H:i:s', time());
                    $content_result = $article_labels->data($content_data)->add();
                    if (empty($content_result)) {
                        $article_labels->data($content_data)->add();
                    }
                }

                $rec_url_data['url_data'] = $url_data;
                $rec_url_data['jump_url'] = $jump_url;
                $rec_url_data['id'] = $res;
                $this->ajaxReturn($rec_url_data, 'JSON');

            } else {

                //对于封面图片应该要进行二次处理
                $cover_img_path = $this->storageImg($_FILES);
                $url_pos = strlen($_SERVER['DOCUMENT_ROOT']);
                $data['wgw_content'] = $data['content'];
                if (!empty($cover_img_path)) {
                    $data['thumb_pic_url'] = substr($cover_img_path, $url_pos);    //图片(thumb后期需要进行缩略图处理 数据库中此不可为空，)                  //图片(thumb后期需要进行缩略图处理 数据库中此不可为空，)

                    //添加微官网展示字段。
                    if ($data['show_cover_pic'] == 1) {
                        $data['wgw_content'] = '<p><img alt="" floatstyle=""  style="max-width: 100%;height: auto !important;" src="' . $data['thumb_pic_url'] . '"/></p>' . $data['content'];
                    } else {
                        $data['wgw_content'] = $data['content'];
                    }


                }

                $data['create_time'] = date('Y-m-d H:i:s', time());                      //创建时间
                $data['update_time'] = date('Y-m-d H:i:s', time());                      //更新时间
                $url_data = 'http://m.' . $_SERVER['HTTP_HOST'] . '/Mall/Pay/getCode.html?copywriters=' . $article_id;
                $data['content_source_url'] = $url_data;
                $res = $local_articles->where("id=$article_id")->save($data);

                //更新标签表等相关信息
                //实例化标签中间表
                $article_labels = M('article_labels');
                //实例化标签表
                $labels = M('labels');
                $title_labels_data = $_POST['title'];                       //标题标签
                $topic_labels = $_POST['topic_labels'];
                $topic_labels_data = $topic_labels['names'];                //题目标签
                $content_labels = $_POST['content_labels'];
                $content_labels_data = $content_labels['names'];            //内容标签
                $aid = $article_id;
                //删除原有标签
                $article_labels->where("aid=$aid")->delete();
                //标题标签添加到数据表中
                foreach ($title_labels_data as $key => $value) {
                    $title_data['tid'] = $key;
                    $title_data['aid'] = $aid;
                    $title_data['create_time'] = date('Y-m-d H:i:s', time());
                    $title_result = $article_labels->data($title_data)->add();
                    if (empty($title_result)) {
                        $article_labels->data($title_data)->add();
                    }
                }
                $topic_labels_data = explode(';', $topic_labels_data);
                //题目标签加入到数据表中
                foreach ($topic_labels_data as $key => $value) {
                    //对于value值的首字母进行判断，假如其为u，即为新增标签，所以需要同时更新标签表
                    $tip = substr($value, 0, 1);
                    $pos = strpos($value, '-');  //获取字符串中间-的位置，用于获取信息。
                    if ($tip == 'u') {
                        $label_data['name'] = substr($value, $pos + 1);
                        $label_data['type'] = 2;  //题目标签的类型对应2
                        $label_data['create_time'] = date('Y-m-d H:i:s', time());
                        $tid = $labels->data($label_data)->add();     //此处返回的值即为新建标签的id值
                        $topic_data['tid'] = $tid;
                    } else {
                        $topic_data['tid'] = substr($value, 0, $pos);
                    }
                    $topic_data['aid'] = $aid;
                    $topic_data['create_time'] = date('Y-m-d H:i:s', time());
                    $topic_result = $article_labels->data($topic_data)->add();
                    if (empty($topic_result)) {
                        $article_labels->data($topic_data)->add();
                    }
                }
                $content_labels_data = explode(';', $content_labels_data);
                foreach ($content_labels_data as $key => $value) {
                    //对于value值的首字母进行判断，假如其为u，即为新增标签，所以需要同时更新标签表
                    $tip = substr($value, 0, 1);
                    $pos = strpos($value, '-');  //获取字符串中间-的位置，用于获取信息。
                    if ($tip == 'u') {
                        $content_data['name'] = substr($value, $pos + 1);
                        $content_data['type'] = 3;  //题目标签的类型对应2
                        $content_data['create_time'] = date('Y-m-d H:i:s', time());
                        $tid = $labels->data($content_data)->add();     //此处返回的值即为新建标签的id值
                        $content_data['tid'] = $tid;
                    } else {
                        $content_data['tid'] = substr($value, 0, $pos);
                    }
                    $content_data['aid'] = $aid;
                    $content_data['create_time'] = date('Y-m-d H:i:s', time());
                    $content_result = $article_labels->data($content_data)->add();
                    if (empty($content_result)) {
                        $article_labels->data($content_data)->add();
                    }
                }

                $rec_url_data['url_data'] = $url_data;
                $rec_url_data['id'] = $res;
                $this->ajaxReturn($rec_url_data, 'JSON');
            }
        } else {
            //通过前端是否传过来article_id字段确定是更新还是新建
            $article_id = $_POST['article_id'];
            //开始需要判断到底是文案的编辑还是文案的创建
            $pos = strpos($_SERVER['HTTP_REFERER'], '=');    //看url中是否带有此参数，假如有，即为编辑文案;否则，即为新建文案
            $url_msg = $this->get_url_msg($_SERVER['HTTP_REFERER']);
            $article_id = $url_msg['article_id'];
            $type = $url_msg['type'];
            //新建文案的条件：article_id为空
            if (empty($article_id) && empty($_POST['article_id'])) {
                //图片上传
                $cover_img_path = $this->storageImg($_FILES);
                $url_pos = strlen($_SERVER['DOCUMENT_ROOT']);
                $data['thumb_pic_url'] = substr($cover_img_path, $url_pos);              //图片(thumb后期需要进行缩略图处理 数据库中此不可为空，)

                //添加微官网展示字段。
                if ($data['show_cover_pic'] == 1) {
                    $data['wgw_content'] = '<p><img alt="" floatstyle=""  style="max-width: 100%;height: auto !important;" src="' . $data['thumb_pic_url'] . '"/></p>' . $data['content'];
                } else {
                    $data['wgw_content'] = $data['content'];
                }


                $data['create_time'] = date('Y-m-d H:i:s', time());                      //创建时间
                $data['update_time'] = date('Y-m-d H:i:s', time());                      //更新时间
                $url_msg = $this->get_url_msg($_POST['url_data']);
                $article_id = $url_msg['copywriters'];
                if (!empty($_POST['url_data'])) {
                    $data['content_source_url'] = $_POST['url_data'];                   //原文链接
                    $res = $local_articles->where("id=$article_id")->data($data)->save();         //thinkphp的add方法的返回值就是id值
                } else {
                    $res = $local_articles->data($data)->add();
                }

                if ($res) {

                    //更新标签表等相关信息
                    //实例化标签中间表
                    $article_labels = M('article_labels');
                    //实例化标签表
                    $labels = M('labels');
                    $title_labels_data = $_POST['title'];                       //标题标签
                    $topic_labels = $_POST['topic_labels'];
                    $topic_labels_data = $topic_labels['names'];                //题目标签
                    $content_labels = $_POST['content_labels'];
                    $content_labels_data = $content_labels['names'];            //内容标签
                    $aid = $res;
                    //标题标签添加到数据表中
                    foreach ($title_labels_data as $key => $value) {
                        $title_data['tid'] = $key;
                        $title_data['aid'] = $aid;
                        $title_data['create_time'] = date('Y-m-d H:i:s', time());
                        $title_result = $article_labels->data($title_data)->add();
                        if (empty($title_result)) {
                            $article_labels->data($title_data)->add();
                        }
                    }
                    $topic_labels_data = explode(';', $topic_labels_data);
                    //题目标签加入到数据表中
                    foreach ($topic_labels_data as $key => $value) {
                        //对于value值的首字母进行判断，假如其为u，即为新增标签，所以需要同时更新标签表
                        $tip = substr($value, 0, 1);
                        $pos = strpos($value, '-');  //获取字符串中间-的位置，用于获取信息。
                        if ($tip == 'u') {
                            $label_data['name'] = substr($value, $pos + 1);
                            $label_data['type'] = 2;  //题目标签的类型对应2
                            $label_data['create_time'] = date('Y-m-d H:i:s', time());
                            $tid = $labels->data($label_data)->add();     //此处返回的值即为新建标签的id值
                            $topic_data['tid'] = $tid;
                        } else {
                            $topic_data['tid'] = substr($value, 0, $pos);
                        }
                        $topic_data['aid'] = $aid;
                        $topic_data['create_time'] = date('Y-m-d H:i:s', time());
                        $topic_result = $article_labels->data($topic_data)->add();
                        if (empty($topic_result)) {
                            $article_labels->data($topic_data)->add();
                        }
                    }
                    $content_labels_data = explode(';', $content_labels_data);
                    foreach ($content_labels_data as $key => $value) {
                        //对于value值的首字母进行判断，假如其为u，即为新增标签，所以需要同时更新标签表
                        $tip = substr($value, 0, 1);
                        $pos = strpos($value, '-');  //获取字符串中间-的位置，用于获取信息。
                        if ($tip == 'u') {
                            $content_data['name'] = substr($value, $pos + 1);
                            $content_data['type'] = 3;  //题目标签的类型对应2
                            $content_data['create_time'] = date('Y-m-d H:i:s', time());
                            $tid = $labels->data($content_data)->add();     //此处返回的值即为新建标签的id值
                            $content_data['tid'] = $tid;
                        } else {
                            $content_data['tid'] = substr($value, 0, $pos);
                        }
                        $content_data['aid'] = $aid;
                        $content_data['create_time'] = date('Y-m-d H:i:s', time());
                        $content_result = $article_labels->data($content_data)->add();
                        if (empty($content_result)) {
                            $article_labels->data($content_data)->add();
                        }
                    }

                    //在此处添加判断，假如是点击保存离开的话，则不进行跳转
                    $save_leave = $_POST['save_to_new'];
                    $msg = $this->get_url_msg($save_leave);
                    $leixin_type = $msg['type'];
                    if ($leixin_type == 0 || empty($leixin_type)) {
                        $leixin_type = 0;
                    } else {
                        $leixin_type = 1;
                    }
                    if (!empty($save_leave)) {
                        /*$rec_data['rec_msg'] = '保存成功';
                        $this->ajaxReturn($rec_data,'JSON');*/
                        //直接跟微信进行交互
                        $media_id = $this->uploadArticle($res);
                        //将media_id存入数据库中
                        $local_articles->where("id='$res'")->setField('media_id', $media_id);

                        $this->success('文案保存成功', 'EditArticle.html?type=' . $leixin_type);
                    } else {
                        //直接跟微信进行交互
                        $media_id = $this->uploadArticle($res);
                        //将media_id存入数据库中
                        $local_articles->where("id='$res'")->setField('media_id', $media_id);

                        //添加判断content_source_url字段
                        $content_source_url = $local_articles->where("id='$res'")->getField('content_source_url');
                        if(empty($content_source_url)){
                            $content_source_url_data = 'http://m.' . $_SERVER['HTTP_HOST'] . '/Mall/Pay/getCode.html?copywriters=' . $res;
                            $local_articles->where("id='$res'")->setField('content_source_url', $content_source_url_data);
                        }

                        $this->success('文案新增成功', 'ArticleList.html');
                    }
                } else {
                    $this->error('文案新增失败，请重新提交');
                }
            } else {
                //更新文案信息
                $data['update_time'] = date('Y-m-d H:i:s', time());                      //更新时间
                //此处对article_id加上判断
                $url_msg = $this->get_url_msg($_SERVER['HTTP_REFERER']);
                $article_id = $url_msg['article_id'];

                //$article_id = empty($_POST['article_id'])?substr($_SERVER['HTTP_REFERER'],$pos+1):$_POST['article_id'];
                $article_id = empty($article_id) ? $_POST['article_id'] : $article_id;
                //对于封面图片应该要进行二次处理，假如不存在的话，就更新此字段
                $cover_img_path = $this->storageImg($_FILES);

                $url_pos = strlen($_SERVER['DOCUMENT_ROOT']);

                $data['wgw_content'] = $data['content'];
                if (!empty($cover_img_path)) {
                    $data['thumb_pic_url'] = substr($cover_img_path, $url_pos);          //图片(thumb后期需要进行缩略图处理 数据库中此不可为空，)

                    //添加微官网展示字段。
                    if ($data['show_cover_pic'] == 1) {
                        $data['wgw_content'] = '<p><img alt="" floatstyle=""  style="max-width: 100%;height: auto !important;" src="' . $data['thumb_pic_url'] . '"/></p>' . $data['content'];
                    } else {
                        $data['wgw_content'] = $data['content'];
                    }

                }

                if (!empty($article['content_source_url'])) {
                    $data['content_source_url'] = $article['content_source_url'];    //原文链接
                }else{
                    $data['content_source_url'] = 'http://m.' . $_SERVER['HTTP_HOST'] . '/Mall/Pay/getCode.html?copywriters=' . $article_id;
                }

                $local_articles->where("id='$article_id'")->save($data);
                //更新文案以后，需要将原先跟微信交互的东西清除
                $clear_wx_data['media_id'] = '';
                $clear_wx_data['thumb_media_id'] = '';
                $clear_wx_data['wx_content'] = '';
                $clear_wx_data['update_time'] = date('Y-m-d H:i:s', time());                      //更新时间
                $local_articles->where("id='$article_id'")->save($clear_wx_data);

                //更新标签表等相关信息
                //实例化标签中间表
                $article_labels = M('article_labels');
                //实例化标签表
                $labels = M('labels');
                $title_labels_data = $_POST['title'];                       //标题标签
                $topic_labels = $_POST['topic_labels'];
                $topic_labels_data = $topic_labels['names'];                //题目标签
                $content_labels = $_POST['content_labels'];
                $content_labels_data = $content_labels['names'];            //内容标签
                $aid = $article_id;
                //删除原有标签
                $article_labels->where("aid=$aid")->delete();
                //标题标签添加到数据表中
                foreach ($title_labels_data as $key => $value) {
                    $title_data['tid'] = $key;
                    $title_data['aid'] = $aid;
                    $title_data['create_time'] = date('Y-m-d H:i:s', time());
                    $title_result = $article_labels->data($title_data)->add();
                    if (empty($title_result)) {
                        $article_labels->data($title_data)->add();
                    }
                }
                $topic_labels_data = explode(';', $topic_labels_data);
                //题目标签加入到数据表中
                foreach ($topic_labels_data as $key => $value) {
                    //对于value值的首字母进行判断，假如其为u，即为新增标签，所以需要同时更新标签表
                    $tip = substr($value, 0, 1);
                    $pos = strpos($value, '-');  //获取字符串中间-的位置，用于获取信息。
                    if ($tip == 'u') {
                        $label_data['name'] = substr($value, $pos + 1);
                        $label_data['type'] = 2;  //题目标签的类型对应2
                        $label_data['create_time'] = date('Y-m-d', time());
                        $tid = $labels->data($label_data)->add();     //此处返回的值即为新建标签的id值
                        $topic_data['tid'] = $tid;
                    } else {
                        $topic_data['tid'] = substr($value, 0, $pos);
                    }
                    $topic_data['aid'] = $aid;
                    $topic_data['create_time'] = date('Y-m-d H:i:s', time());
                    $topic_result = $article_labels->data($topic_data)->add();
                    if (empty($topic_result)) {
                        $article_labels->data($topic_data)->add();
                    }
                }
                $content_labels_data = explode(';', $content_labels_data);
                foreach ($content_labels_data as $key => $value) {
                    //对于value值的首字母进行判断，假如其为u，即为新增标签，所以需要同时更新标签表
                    $tip = substr($value, 0, 1);
                    $pos = strpos($value, '-');  //获取字符串中间-的位置，用于获取信息。
                    if ($tip == 'u') {
                        $content_data['name'] = substr($value, $pos + 1);
                        $content_data['type'] = 3;  //题目标签的类型对应2
                        $content_data['create_time'] = date('Y-m-d H:i:s', time());
                        $tid = $labels->data($content_data)->add();     //此处返回的值即为新建标签的id值
                        $content_data['tid'] = $tid;
                    } else {
                        $content_data['tid'] = substr($value, 0, $pos);
                    }
                    $content_data['aid'] = $aid;
                    $content_data['create_time'] = date('Y-m-d H:i:s', time());
                    $content_result = $article_labels->data($content_data)->add();
                    if (empty($content_result)) {
                        $article_labels->data($content_data)->add();
                    }
                }

                //直接跟微信进行交互
                $media_id = $this->uploadArticle($article_id);
                //将media_id存入数据库中
                $local_articles->where("id='$article_id'")->setField('media_id', $media_id);

                $this->success('文案修改成功', 'ArticleList.html');
            }
        }

    }

    //文案组织
    public function articleOrganize()
    {
        //实例化多文案表
        $more_articles = M('more_articles');
        $data['name'] = $_POST['articles_title'];       //组合文章标注
        //获取组合文案的顺序等信息
        $articles = $_POST['articles'];
        $tip = array();
        //将被选中的文案进行组合，形式为id-顺序（30-2，意为id为30的文案排在第2个文案的位置）
        foreach ($articles as $key => $value) {
            if ($value != null) {
                array_push($tip, "$key-$value");
            }
        }
        //文案顺序组织，将文案顺序排列成数组的形式（$key为文案的顺序，$value为文案的id号）
        $new_tip = array();
        foreach ($tip as $key => $value) {
            $pos = strpos($value, '-');
            $id = substr($value, 0, $pos);    //获取文案的id号，
            $order = substr($value, $pos + 1);    //获取文案的顺序
            $new_tip["$order"] = $id;       //对数组的形式进行重新组合
        }
        //根据key值进行排序
        ksort($new_tip);
        //将数组变为字符串,获取组合状态(不同组合之间利用，连接)
        $combine_state = implode($new_tip, ',');
        //组合文案的条目数
        $count = count($tip);
        $data['wid'] = $combine_state;                  //文案排列顺序 ,连接
        $data['combined_state'] = '1';                  //组合状态
        $data['create_time'] = date('Y-m-d H:i:s', time());    //创建时间
        $data['count'] = $count;                        //文案的组合条目数
        $data['status'] = 1;                            //设置文案显示状态
        //直接进行多文案的上传(此处要进行判断是否上传成功)
        /*$media_id = $this->uploadMoreMpnews($tip);
        if(empty($media_id)){
            $media_id = $this->uploadMoreMpnews($tip);  //重新进行调取
        }
        $data['media_id'] = $media_id;*/
        $res = $more_articles->data($data)->add();
        if ($res) {
            $this->success('文案组合成功', 'ArticleListMore.html');
        } else {
            $this->error('文案组合失败，请重新提交');
        }

    }

    //文案删除
    public function articleDelete()
    {
        $article_id = $_GET['article_id'];              //单文案
        $more_article_id = $_GET['more_article_id'];    //多文案
        //实例化文案数据表
        if ($article_id) {    //单文案表
            $local_articles = M('local_articles');
            $res = $local_articles->where("id='$article_id'")->setField('status', 0);
            if ($res) {
                $this->success('文案删除成功', 'ArticleList.html');
            } else {
                $this->error('文案删除失败');
            }
        }
        if ($more_article_id) {       //多文案表
            $more_articles = M('more_articles');
            $res = $more_articles->where("id='$more_article_id'")->setField('status', 0);
            if ($res) {
                $this->success('文案删除成功', 'ArticleListMore.html');
            } else {
                $this->error('文案删除失败');
            }
        }
    }

    //封面图片转存，传入文件信息(array)，返回存储地址
    public function storageImg($photo)
    {
        //文件处理
        if ($photo["article"]["error"]['image'] > 0) {
            #echo '错误:'.$photo["article"]["error"]['image'].'<br />';
        } else {
            //文件上传路径
            $dir = $_SERVER['DOCUMENT_ROOT'] . '/Upload/weixin/article/thumb';
            //获取文件后缀
            $ext = strpos($photo["article"]["name"]['image'], '.');
            $ext = substr($photo["article"]["name"]['image'], $ext + 1);
            if ($ext == 'gif') {
                $this->error('封面图不能为gif图片');
            }
            //利用时间戳作为图片的新名字，避免重复
            $timestamp = time();
            $newname = $timestamp . '.' . $ext;
            //文件处理
            if (file_exists("$dir/" . $photo["article"]["name"]['image'])) {
                echo $photo["article"]["name"] . '文件已经存在.';
            } else {
                move_uploaded_file($photo["article"]["tmp_name"]['image'], "$dir/" . $newname);
            }
            $img_url = $dir . '/' . $newname;
            return $img_url;

        }
    }

    //上传封面图片获取thumb_media_id(必须为永久素材)
    public function uploadCoverPics()
    {
//        $cover_img_path =  M('more_articles');
        $local_articles = M('local_articles');
        $thumb_pic_url = $local_articles->order('create_time desc')->getField('thumb_pic_url');
        $cover_img_path = $_SERVER['DOCUMENT_ROOT'] . $thumb_pic_url;
        //加上蒙版的判断，假如和微信交互的是蒙版，则显示封面图片限制为0，否则为1即可。
        $img_data = array(
            'type' => 'thumb',
            'media' => '@' . $cover_img_path
        );
        //获取access_token
        $access_token = $this->getToken();
        //$type = 'thumb';
        dump($access_token);
        //$url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.$access_token.'&type='.$type;
        $url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=' . $access_token;
        $result = request_post($url, $img_data);
        //此为永久图文素材的media_id
        $thumb_media_id = $result->media_id;
        dump($result);
        dump($thumb_media_id);
        die;
    }

    //上传封面图片获取thumb_media_id(必须为永久素材)
    public function uploadCoverPic($cover_img_path)
    {
        $img_data = array(
            'type' => 'thumb',
            'media' => '@' . $cover_img_path
        );
        //获取access_token
        $access_token = $this->getToken();
        //$type = 'thumb';
        //$url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.$access_token.'&type='.$type;
        $url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=' . $access_token;
        $result = request_post($url, $img_data);
        //此为永久图文素材的media_id
        $thumb_media_id = $result->media_id;
        return $thumb_media_id;
    }
    //上传正文或蒙版中的图片获取对应的url并替换保存(参数为正文的html代码的字符串)
    //利用simple_html_dom进行img的src地址的选取，并进行替换、保存操作
    public function uploadImg($content)
    {
        //获取access_token
        $access_token = $this->getToken();
        //引入simple_html_dom
        vendor('simplehtmldom.simple_html_dom');
        $html = str_get_html($content);
        foreach ($html->find('img') as $element) {
            $url_data = parse_url($element->src);
            if ($url_data['scheme'] == 'http' || $url_data['scheme'] == 'https') {

                //进行下一步判断，假如是微信图片则不进行交互
                if ($url_data['host'] == 'mmbiz.qlogo.cn') {
                    //微信图片不做处理
                    continue;
                } elseif ($url_data['host'] == $_SERVER['HTTP_HOST']) {
                    //本地图片直接交互
                    //$img_path = $element->src;
                    $img_path = $_SERVER['DOCUMENT_ROOT'].$url_data['path'];
                } else {
                    //对于远程图片进行判断，是否存在判断符
                    $pos = strpos($element->src, '?');
                    if (!empty($pos)) {
                        $remote_img_path = substr($element->src, 0, $pos);
                    } else {
                        $remote_img_path = $element->src;
                    }
                    //图片远程路径
                    //$remote_img_path = $element->src;
                    $save_dir = $_SERVER['DOCUMENT_ROOT'] . '/Upload/weixin/article/image';
                    $res = $this->getImage($remote_img_path, $save_dir);
                    $img_path = $res['save_path'];
                    //
                    $pos = strpos($img_path, '.');
                    $ext = substr($img_path, $pos + 1);
                    if ($ext == 'gif') {
                        $new_src = str_replace($ext, 'jpg', $img_path);
                        rename($img_path, $new_src);
                        $img_path = $new_src;
                    }
                }

            } else {
                //对于本地上传的文件需要加以判断，假如存在gif图则上传失败
                /*$ext = strrchr($element->src,'.');
                if($ext=='gif'){
                    $this->error('正文中不能上传gif图片，请检查后重新提交');
                }*/
                $pos = strpos($element->src, '.');
                $ext = substr($element->src, $pos + 1);
                $new_src = str_replace($ext, 'jpg', $element->src);
                copy($_SERVER['DOCUMENT_ROOT'] . $element->src, $_SERVER['DOCUMENT_ROOT'] . $new_src);
                //$img_path = $_SERVER['DOCUMENT_ROOT'].$element->src;
                $img_path = $_SERVER['DOCUMENT_ROOT'] . $new_src;
            }
            //$cfile = curl_file_create($img_path);   //use the CURLFile Class 替换@的使用方法。
            /*$img_data = array(
                'media'=>$cfile
            );*/
            if (file_exists_case($img_path)) {

                $img_data = array(
                    'media' => '@' . $img_path
                );
                $url = 'https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token=' . $access_token;
                $result = request_post($url, $img_data);
                $img_url = $result->url;
                //二次获取
                if (empty($img_url)) {
                    $result = request_post($url, $img_data);
                    $img_url = $result->url;
                }
                $element->src = $img_url;
            }
        }
        $wx_content = $html->save();
        $html->clear();
        return $wx_content;
    }

    //上传图文消息素材(其中相关数据直接从数据库中读取)，获取返回的media_id值
    public function uploadMpnews($article_id)
    {
        //实例化文案数据表
        $local_articles = M('local_articles');
        $article_details = $local_articles->where("id='$article_id'")->find();
        //加上蒙版的判断，假如和微信交互的是蒙版，则显示封面图片限制为0，否则为1即可。
        if (!empty($article_details['meng_content'])) {
            $article_details['show_cover_pic'] = 0;
        }
        //组织文案数据
        $articles = array(
            'articles' => array(
                array(
                    "thumb_media_id" => $article_details['thumb_media_id'],           /*图文消息的封面图片素材id（必须是永久mediaID）*/
                    "author" => $article_details['author'],
                    "title" => $article_details['title'],
                    "content_source_url" => $article_details['content_source_url'],   /*点击阅读原文的链接*/
                    "content" => $article_details['wx_content'],
                    "digest" => $article_details['digest'],
                    "show_cover_pic" => $article_details['show_cover_pic']
                )
            )
        );
        $articles = json_encode($articles, JSON_UNESCAPED_UNICODE);
        //获取access_token
        $access_token = $this->getToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/material/add_news?access_token=' . $access_token;
        $result = request_post($url, $articles);
        $media_id = $result->media_id;
        return $media_id;   //返回media_id
    }

    //上传多文案素材,获取多文案的media_id(传入的参数为多图文的条数和文案的组合顺序(array)。)
    public function uploadMoreMpnews($tip)
    {
        //实例化文案数据表
        $local_articles = M('local_articles');
        //处理传来的数组数据，组织文案数据
        $articles_details = array();
        foreach ($tip as $key => $id) {
            //对于每篇文章进行上传操作
            $article_media_id = $local_articles->where("id='$id'")->getField('media_id');
            if (empty($article_media_id)) {
                $article_media_id = $this->uploadArticle($id);
                if (!empty($article_media_id)) {
                    $local_articles->where("id='$id'")->setField('media_id', $article_media_id);
                }
            }
            $thumb_pic_url = $local_articles->where("id='$id'")->getField('thumb_pic_url');
            //将封面图片的url改为可以跟微信进行交互的url
            $thumb_pic_url = $_SERVER['DOCUMENT_ROOT'] . $thumb_pic_url;
            //对于微信交互，所有的都进行两次判断和交互。
            $thumb_media_id = $local_articles->where("id='$id'")->getField('thumb_media_id');
            if (empty($thumb_media_id)) {
                $thumb_media_id = $this->uploadCoverPic($thumb_pic_url);
            }

            $article_details = $local_articles->where("id='$id'")->find();
            //加上蒙版的判断，假如和微信交互的是蒙版，则显示封面图片限制为0，否则为1即可。
            if (!empty($article_details['meng_content'])) {
                $article_details['show_cover_pic'] = 0;
            }
            $details = array(
                "thumb_media_id" => $thumb_media_id ,           /*图文消息的封面图片素材id（必须是永久mediaID）*/
                "author" => $article_details['author'],
                "title" => $article_details['title'],
                "content_source_url" => $article_details['content_source_url'],   /*点击阅读原文的链接*/
                "content" => $article_details['wx_content'],
                "digest" => $article_details['digest'],
                "show_cover_pic" => $article_details['show_cover_pic']
            );
            array_push($articles_details, $details);
        }
        $articles = array(
            'articles' => $articles_details
        );
        $articles = json_encode($articles, JSON_UNESCAPED_UNICODE);
        //获取access_token
        $access_token = $this->getToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/material/add_news?access_token=' . $access_token;
        $result = request_post($url, $articles);
        $media_id = $result->media_id;
        return $media_id;   //返回media_id
    }



    //上传单文案（与微信进行交互，获取其media_id）
    public function uploadArticle($id)
    {
        /*
         * 1、获取封面图片的thumb_media_id
         * 2、获取正文中相关图片的url进行替换
         * 3、将相关信息存入数据库中
         * 4、上传相关信息
         */
        //实例化单文案表
        $local_articles = M('local_articles');
        //1、获取封面图片的thumb_media_id(获取封面图片的地址,为website下一固定路径)
        $thumb_pic_url = $local_articles->where("id='$id'")->getField('thumb_pic_url');
        //将封面图片的url改为可以跟微信进行交互的url
        $thumb_pic_url = $_SERVER['DOCUMENT_ROOT'] . $thumb_pic_url;
        //对于微信交互，所有的都进行两次判断和交互。
        $thumb_media_id = $local_articles->where("id='$id'")->getField('thumb_media_id');
        if (empty($thumb_media_id)) {
            $thumb_media_id = $this->uploadCoverPic($thumb_pic_url);
        }
//        dump($thumb_media_id);
        $data['thumb_media_id'] = $thumb_media_id;
        //2、获取正文中相关图片的url进行替换,换取wx_content;此处假如有蒙版，交互为蒙版内容
        $meng_content = $local_articles->where("id='$id'")->getField('meng_content');
        if (!empty($meng_content)) {
            $content = $local_articles->where("id='$id'")->getField('meng_content');
        } else {
            $content = $local_articles->where("id='$id'")->getField('content');
        }
        $wx_content = $this->uploadImg($content);
//        if (empty($wx_content)) {
//            $wx_content = $this->uploadImg($content);
//        }
        $data['wx_content'] = $wx_content;
        //3、将相关信息存入数据库中
        $res = $local_articles->where("id='$id'")->data($data)->save();
        if (empty($res)) {
            $local_articles->where("id='$id'")->data($data)->save();
        }
        //4、上传相关信息
        $media_id = $this->uploadMpnews($id);
        if (empty($media_id)) {
            $media_id = $this->uploadMpnews($id);
        }
        return $media_id;
    }
    //在文案预览和群发的过程中，对相应图文消息的media_id进行二次判断，假如不存在，重新获取一次！
    //在文案预览和文案群发的过程中进行文案的交互。
    //文案预览（分为单文案和多文案）
    public function articlePreview()
    {
        //接收需要预览的微信账号、文案id（ajax请求）
        $towxname = $_POST['name'];
        $id = substr($_POST['id'], 2);            //接受ajax传过来的文案id号
        $tip = substr($_POST['id'], 0, 1);         //（前缀为d表明为单文案，前缀为m表明为多文案）
        $fail['id'] = $id;
        $fail['tip'] = $tip;
        if ($tip == 'd') {  //单文案
            //实例化单文案数据表
            $local_articles = M('local_articles');
            $media_id = $local_articles->where("id='$id'")->getField('media_id');
            //假如文案的media_id不存在，重新获取。
            $fail['media'] = $media_id;

            if (empty($media_id)) {
                $media_id =  $this->uploadArticle($id);
                //将media_id存入数据库中
                $local_articles->where("id='$id'")->setField('media_id', $media_id);
            }
            $fail['media_id'] = $media_id;

        } else {          //多文案
            //实例化多文案数据表
            $more_articles = M('more_articles');
            $media_id = $more_articles->where("id='$id'")->getField('media_id');
            //假如文案的media_id不存在，重新获取。
            if (empty($media_id)) {
                $wid = $media_id = $more_articles->where("id='$id'")->getField('wid');
                $tip = explode(',', $wid);
                $media_id = $this->uploadMoreMpnews($tip);
                //存入数据库中
                $more_articles->where("id='$id'")->setField('media_id', $media_id);
            }
        }
        if(true){
            //获取access_token
            $access_token = $this->getToken();
            $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token=' . $access_token;
            //组织预览数据(图文消息)（根据微信号进行预览）
            $data = array(
                'towxname' => $towxname,       //填入需要预览的用户的openid
                'mpnews' => array(
                    'media_id' => $media_id  //填入图文消息的media_id
                ),
                'msgtype' => 'mpnews'
            );
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);
            $result = request_post($url, $data);
            if ($result->errcode == 0) {
                $msg = '预览发送成功，请到手机上查看';
                $fail['msg'] = $msg;
                //echo json_encode($success);
//            $this->ajaxReturn($success,'JSON');

            } else {
                $errcode = $result->errcode;
                $errMsg = errorMsg($errcode);
                $msg = '预览发送失败!';
                $fail['msg'] = $msg . $errMsg;
                //echo json_encode($fail);
            }
        }
        $this->ajaxReturn($fail, 'JSON');


    }

    //文案群发(接受ajax请求，有微信群发和发送到指定栏目上、利用参数进行判断)
    public function articleSendAll()
    {
        /*
         群发图文消息的过程如下：
            1、首先，预先将图文消息中需要用到的图片，使用上传图文消息内图片接口，上传成功并获得图片URL
            2、上传图文消息素材，需要用到图片时，请使用上一步获取的图片URL
            3、使用对用户分组的群发，或对OpenID列表的群发，将图文消息群发出去
            4、在上述过程中，如果需要，还可以预览图文消息、查询群发状态，或删除已群发的消息等
         */
        //获取文案的id(此处分辨是单文案还是多文案)(利用ajax触发)
        $id = substr($_POST['id'], 2);        //文案的id号
        $tip = substr($_POST['id'], 0, 1);     //（前缀为d表明为单文案，前缀为m表明为多文案）
        $place = $_POST['place'];
        switch ($place) {
            case 'GroupIssued':             //微信群发
                //群发文案的类型判断
                if ($tip == 'd') {    //单文案
                    //实例化文案数据表
                    $local_articles = M('local_articles');
                    $media_id = $local_articles->where("id='$id'")->getField('media_id');
                    //假如文案的media_id不存在，重新获取。
                    if (empty($media_id)) {
                        $media_id = $this->uploadMpnews($id);
                    }
                } else if ($tip == 'm') {  //多文案
                    //实例化多文案数据表
                    $more_articles = M('more_articles');
                    $media_id = $more_articles->where("id='$id'")->getField('media_id');
                    //假如文案的media_id不存在，重新获取。
                    if (empty($media_id)) {
                        $wid = $media_id = $more_articles->where("id='$id'")->getField('wid');
                        $tip = explode(',', $wid);
                        $media_id = $this->uploadMoreMpnews($tip);
                    }
                }
                //组织群发数据(图文消息)（根据分组进行群发）
                $news_data = array(
                    'filter' => array(
                        "is_to_all" => true, /*使用is_to_all为true且成功群发，会使得此次群发进入历史消息列表。设置is_to_all为false时是可以多次群发的，但每个用户只会收到最多4条，且这些群发不会进入历史消息列表。*/
                    ),
                    'mpnews' => array(
                        "media_id" => $media_id
                    ),
                    'msgtype' => 'mpnews'
                );
                $news_data = json_encode($news_data, JSON_UNESCAPED_UNICODE);    //后面参数为设置json_encode()不转义汉字。
                //获取access_token
                $access_token = $this->getToken();
                $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=' . $access_token;
                $result = request_post($url, $news_data);
                $send_records = M('send_records');

                if ($result->errcode == 0) {  /*接口返回值*/
                    $msg_id = $result->msg_id;
                    //实例化发送表
                    $data['type'] = $tip;         //发送文案类型(判断 d为单文案；m为多文案)
                    $data['wid'] = $id;          //发送文案编号
                    $data['msg_id'] = $msg_id;  //消息ID
                    $data['send_time'] = date('Y-m-d H:i:s', time());      //发送时间
                    $data['errcode'] = $result->errcode;      //发送时间
                    $data['errmsg'] = $result->errmsg;      //发送时间
                    $data['msg_data_id'] = $result->msg_data_id;      //发送时间
                    $res1 = $send_records->data($data)->add();

                    $msg = '群发成功，请到手机上查看';
                    $success['msg'] = $msg;
                    //echo json_encode($success);
                    $this->ajaxReturn($success, 'JSON');

                } else {  /*接口调用错误信息*/
                    $errcode = $result->errcode;
                    $errMsg = errorMsg($errcode);
                    $data['type'] = $tip;         //发送文案类型(判断 d为单文案；m为多文案)
                    $data['wid'] = $id;          //发送文案编号
                    $data['send_time'] = date('Y-m-d H:i:s', time());      //发送时间
                    $data['errcode'] = $result->errcode;      //发送时间
                    $data['errmsg'] = $result->errmsg;      //发送时间
                    $res1 = $send_records->data($data)->add();
                    $msg = '群发失败!';
                    $fail['msg'] = $msg . $errMsg;
                    //echo json_encode($fail);
                    $this->ajaxReturn($fail, 'JSON');
                }
                break;
            case 'LatestInformation':       //最新资讯
            case 'ThemeActivity':           //主题活动
            case 'NewProductRecommend':     //新品推荐
                //实例化发送记录表
                $type_send_records = M('article_send_records');
                $data['copywriter_id'] = $id;
                $data['copywirter_type'] = ($tip == 'd') ? 'Single' : 'More';
                //$data['wei_xin_appid'] = '';
                //$data['user_id'] = '';
                $data['send_place'] = $place;
                $data['created_at'] = date('Y-m-d H:i:s', time());
                $data['updated_at'] = date('Y-m-d H:i:s', time());
                $res = $type_send_records->data($data)->add();
                if (!empty($res)) {
                    $msg = '发送成功，请到手机上查看';
                    $success['msg'] = $msg;
                    //echo json_encode($success);
                    $this->ajaxReturn($success, "JSON");
                }
                break;
        }

    }

    //获取token
    public function getToken()
    {

        return getToken();
    }



    //url信息处理函数
    //定义处理函数
    function get_url_msg($str)
    {
        $data = array();
        $parameter = explode('&', end(explode('?', $str)));
        foreach ($parameter as $val) {
            $tmp = explode('=', $val);
            $data[$tmp[0]] = $tmp[1];
        }
        return $data;
    }

    /*
        *功能：php完美实现下载远程图片保存到本地
        *参数：文件url,保存文件目录,保存文件名称，使用的下载方式
        *当保存文件名称为空时则使用远程文件原来的名称
        */
    function getImage($url, $save_dir = '', $filename = '', $type = 0)
    {
        if (trim($url) == '') {
            return array('file_name' => '', 'save_path' => '', 'error' => 1);
        }
        if (trim($save_dir) == '') {
            $save_dir = './';
        }
        if (trim($filename) == '') {//保存文件名
            $ext = strrchr($url, '.');
            if ($ext != '.gif' && $ext != '.jpg' && $ext != '.png' && $ext != '.jpeg') {
                return array('file_name' => '', 'save_path' => '', 'error' => 3);
            }
            $filename = time() . rand(0, 10000) . $ext;
        }
        if (0 !== strrpos($save_dir, '/')) {
            $save_dir .= '/';
        }
        //创建保存目录
        if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
            return array('file_name' => '', 'save_path' => '', 'error' => 5);
        }
        //获取远程文件所采用的方法
        if ($type) {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $img = curl_exec($ch);
            curl_close($ch);
        } else {
            ob_start();
            readfile($url);
            $img = ob_get_contents();
            ob_end_clean();
        }
        //$size=strlen($img);
        //文件大小
        $fp2 = @fopen($save_dir . $filename, 'a');
        fwrite($fp2, $img);
        fclose($fp2);
        unset($img, $url);
        return array('file_name' => $filename, 'save_path' => $save_dir . $filename, 'error' => 0);
    }


}