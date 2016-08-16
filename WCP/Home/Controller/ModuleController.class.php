<?php
/**
 * Author：helen
 * CreateTime: 2016/08/16 15:27
 * Description: 模块控制器
 */
namespace Home\Controller;

use Think\Controller;

class ModuleController extends Controller
{
    protected $modules              = [];
    protected $module_type          = [];
    protected $module_type_sequence = [];
    protected $module               = [];
    protected $module_sequence      = [];
    protected $fun                  = [];

    /**
     * 初始化方法
     */
    public function __construct()
    {
        $modules_json = dirname($_SERVER['DOCUMENT_ROOT']) . '/WCP/Home/Conf/modules.json';
        $modules = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents($modules_json)), true);
        $this->modules              = $modules;
        $this->module_type          = $modules['module_type'];
        $this->module_type_sequence = $modules['module_type_sequence'];
        $this->module               = $modules['module'];
        $this->module_sequence      = $modules['module_sequence'];
        $this->fun                  = $modules['fun'];
    }

    /**
     * 获取所有的模型数据
     * 数据返回格式
     * [
     *     [
     *         "key":
     *         "name":
     *         "desc":
     *         "modules": [
     *             "key":
     *             "module": [
     *                 "name":
     *                 "type":
     *                 "desc":
     *                 "child_modules": []
     *                 "free_child_modules": []
     *                 "capi_list": []
     *                 "funs": []
     *                 "free_funs": []
     *             ]
     *         ]
     *     ],
     *     ...
     * ]
     */
    public function getModuleData()
    {
        $module_type = $this->module_type;
        $module_type_sequence = $this->module_type_sequence;
        $module = $this->module;
        $module_sequence = $this->module_sequence;

        $data_map = [];
        foreach ($module_sequence as &$key) {
            $module_data = $module[$key];
            $type = $module_data['type'];
            $tmp_data = array(
                "key"   => $key,
                "module"  => $module_data
            );
            if ($data_map[$type]) {
                array_push($data_map[$type], $tmp_data);
            } else {
                $data_map[$type] = [];
                array_push($data_map[$type], $tmp_data);
            }
        }

        $data = [];
        foreach ($module_type_sequence as &$key) {
            $tmp_data = array(
                'key'       => $key,
                'name'      => $module_type[$key]['name'],
                'desc'      => $module_type[$key]['desc'],
                'modules'   => $data_map[$key]
            );
            array_push($data, $tmp_data);
        }
        return $data;
    }

    /**
     * 获取关联用户权限的模型数据
     */
    public function getModuleDataWithAuthority($openid)
    {
        $module_data = $this->getModuleData();
    }

    /**
     * 获取用户有权调用的接口列表
     */
    public function getCapiListWithAuthority($openid)
    {
        $module_data = $this->getModuleData();
    }
}