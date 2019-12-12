<?php

class IndexController extends adminBaseController{

    public $layout = 'main/layout';

    // 构造方法
    public function init(){
    }

    public function index(){
        
        echo '<img src="data:image/png;base64,'.base::app('verify')->setCodeType(1)->setCodeLen(4)->setHard(3)->verifi($base64=true).'" width="'.base::app('verify')->getImageW().'" height="'.base::app('verify')->getImageH().'">';

        echo base::app('verify')->getCode();

        picVerify::set();
        picVerify::verify();


    }


    // 列表页
    public function list(){
        $getArray = array();
        $getArray['page'] = isset($_GET['page']) ? (intval($_GET['page'])>0 ? intval($_GET['page']) : 1) : 1;
        $getArray['limit'] = 15;
        
        // 获取总数
        $count = base::app('mysql')->table('art_article')->field('COUNT(*) AS counts')->limit(1)->selectOne();
        // 分页类
        $offset = base::app('page')->clear()->setNowpage($getArray['page'])->setLimit($getArray['limit'])->offset();
        $dataList = base::app('mysql')->table('art_article')->limit($getArray['limit'])->offset($offset)->order('id')->by('desc')->setParamkey('id')->select();
        $page = base::app('page')->setTotal($count['counts'])->page();

        // 筛选
        $cateArr = array();
        $cateOne = base::app('mysql')->table('art_tags')->where(array('pid'=>0))->order('id')->by('desc')->select();
        foreach ($cateOne as $key => $one) {
            $cateArr[$one['id']] = $one;
            $cateArr[$one['id']]['children'] = base::app('mysql')->table('art_tags')->where(array('pid'=>$one['id']))->order('id')->by('desc')->select();
        }

        $this->renderout('list', array('dataList'=>$dataList, 'cateArr'=>$cateArr, 'page'=>$page));
    }


    // 修改
    public function edit(){
        $getArray = array();
        $getArray['id'] = intval($_GET['id']);
        $data = base::app('mysql')->table('art_article')->findByPk($getArray['id']);

        $this->renderout('edit', array('data'=>$data));
    }

    // 更新
    public function update(){
        echo "<pre>";
        // $postArray = $_POST;
        print_r($_FILES);
        print_r($_POST);
        base::app('upload')->upload('');
        // $update = base::app('mysql')->table('art_article')->where(array('id'=>$postArray['id']))->update($postArray);
        // if ($update===false) {
        //     echo "error";
        // }else{
        //     echo "success";
        // }
    }

    // 删除
    






}

