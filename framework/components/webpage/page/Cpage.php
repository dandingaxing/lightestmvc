<?php

// 自制PHP分页类
// 灵活返回

class Cpage {
    public $total;                         //总记录数
    public $size = 8;                      //一页显示的记录数
    public $nowpage = 1;                   //当前页
    public $pagecount;                     //总页数
    public $limit = 15;                    // 分页查询的limit
    public $offset;                        // 根据当前页数, 分页查询, offset 计算
    public $pageArr;                       // 获取分页返回数据
    public $pageParam='page';              // page 默认参数
    public $pagepre='{page}';              // 分页url模版替换
    public $urlmodel;                      // 分页url 模版
    public $firstmodel;                    // 分页url 的特殊第一页/首页URL，一般首页的URL 和其他带有参数的页面略有不同
    public $pagei;                         // 起头页数
    public $paged;                         // 结尾页数

    // 构造方法
    public function __construct($config = array()){
    }

    // 重新初始化
    public function clear(){
        return $this;
    }

    public function setTotal($total){
        $this->total = $total;
        return $this;
    }

    public function getTotal(){
        return $this->total;
    }

    public function setSize($size){
        $this->size = intval($size)<1 ? 1 : intval($size);
        return $this;
    }

    public function getSize(){
        return $this->size;
    }

    public function setNowpage($nowpage){
        $this->nowpage = intval($nowpage)<1 ? 1 : intval($nowpage);
        return $this;
    }

    public function getNowpage(){
        return $this->nowpage;
    }

    public function setLimit($limit){
        $this->limit = $limit;
        return $this;
    }

    public function getLimit(){
        return $this->limit;
    }

    public function offset(){
        $this->offset = ($this->nowpage-1) * $this->limit;
        return $this->offset;
    }

    public function setOffset($offset){
        $this->offset = $offset;
        return $this;
    }

    public function getOffset(){
        return $this->offset;
    }

    public function setUrlModel($urlmodel){
        $this->urlmodel = $urlmodel;
        return $this;
    }

    public function getUrlModel(){
        if (!empty($this->urlmodel)) {
            return $this->urlmodel;
        }
        $scriptName = empty($_SERVER['SCRIPT_NAME']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $_GET[$this->pageParam] = $this->pagepre;
        $this->urlmodel = $scriptName . '?' . urldecode(http_build_query($_GET));
        return $this->urlmodel;
    }

    public function setFirstModel($firstmodel){
        $this->firstmodel = $firstmodel;
        return $this;
    }

    public function getFirstModel(){
        if (!empty($this->firstmodel)) {
            return $this->firstmodel;
        }else{
            $this->firstmodel = $this->getUrlModel();
        }
        return $this->firstmodel;
    }

    public function setPagepre($pagepre){
        $this->pagepre = $pagepre;
        return $this;
    }

    public function getPagepre(){
        return $this->pagepre;
    }


    public function getPagecount(){
        $this->pagecount = ceil($this->total / $this->limit);
        return $this;
    }


    // 获取分页 数据(核心)
    public function getPage(){
        $urlmodel = $this->getUrlModel();
        $firstmodel = $this->getFirstModel();

        if (empty($this->pagecount)) {
            $this->getPagecount();
        }

        $show_pages = intval($this->size/2);

        $this->pagei = $this->nowpage - $show_pages;
        $this->paged = $this->nowpage + $show_pages;
        // 偶数页数时候多一页的问题
        if( $this->size%2==0 )
            $this->paged = $this->paged - 1;

        if ($this->pagei < 1) {
            $this->paged = $this->paged + (1 - $this->pagei);
            $this->pagei = 1;
        }
        if ($this->paged > $this->pagecount) {
            $this->pagei = $this->pagei - ($this->paged - $this->pagecount);
            $this->paged = $this->pagecount;
        }
        if ($this->pagei < 1)
            $this->pagei = 1;

        $pageArr = array();
        $pageArr['pagecount'] = $this->pagecount;

        // 上一页
        $pageArr['pre']['pagenum'] = intval( $this->nowpage-1 ) < 1 ? 1 : intval( $this->nowpage-1 );
        $pageArr['pre']['isfirst'] = $pageArr['pre']['pagenum'] == 1 ? 1 : 0;

        $pageArr['pre']['pageurl'] = empty( $pageArr['pre']['isfirst'] ) ? ( empty( $this->urlmodel ) ? '' : str_replace($this->pagepre, $pageArr['pre']['pagenum'], $this->urlmodel) ) : ( empty( $this->firstmodel ) ? (empty( $this->urlmodel ) ? '' : str_replace($this->pagepre, $pageArr['pre']['pagenum'], $this->urlmodel)) : str_replace($this->pagepre, $pageArr['pre']['pagenum'], $this->firstmodel)  );

        // 下一页
        $pageArr['next']['pagenum'] = intval( $this->nowpage+1 ) > $this->pagecount ? $this->nowpage : intval( $this->nowpage+1 );
        $pageArr['next']['isend'] = $pageArr['next']['pagenum'] >= $this->pagecount ? 1 : 0;
        $pageArr['next']['pageurl'] = empty( $this->urlmodel ) ? '' : ( empty($pageArr['next']['isend']) ? str_replace($this->pagepre, $pageArr['next']['pagenum'], $this->urlmodel) : '' ) ;

        // 首页
        $pageArr['first']['pagenum'] = 1;
        $pageArr['first']['pageurl'] = empty($this->firstmodel) ? ( empty($this->urlmodel) ? '' : str_replace($this->pagepre, '1', $this->urlmodel) ) : str_replace($this->pagepre, $pageArr['pre']['pagenum'], $this->firstmodel);

        // 尾页
        $pageArr['end']['pagenum'] = $this->pagecount;
        $pageArr['end']['pageurl'] = empty($this->urlmodel) ? '' : str_replace($this->pagepre, $this->pagecount, $this->urlmodel);

        for ( $i=$this->pagei; $i<=$this->paged; $i++ ) { 
            $pageArr['num'][$i]['page'] = $i;
            $pageArr['num'][$i]['url'] = empty($this->urlmodel) ? '' : str_replace($this->pagepre, $i, $this->urlmodel);
            if($i==1){
                $pageArr['num'][$i]['url'] = empty($this->firstmodel) ? (empty($this->urlmodel) ? '' : str_replace($this->pagepre, $i, $this->urlmodel)) :str_replace($this->pagepre, $pageArr['pre']['pagenum'], $this->firstmodel);
            }

            $pageArr['num'][$i]['isnowpage'] = ($i==$this->nowpage) ? 1 : 0;
        }


        return $pageArr;
    }

    // 根据分页数据显示页面
    public function page( $ulclass="pagination pagination", $nowclass="active" ){
        $pageArr = $this->getPage();
        // 首页
        $home = '<li><a href="' . $pageArr['first']['pageurl'] . '">首页</a></li>';
        // 上一页
        $prev = '<li><a href="' . $pageArr['pre']['pageurl'] . '">上一页</a></li>';
        // 下一页
        $next = '<li><a href="' . $pageArr['next']['pageurl'] . '">下一页</a></li>';
        // 尾页
        $end = '<li><a href="' . $pageArr['end']['pageurl'] . '">尾页</a></li>';
        $numpage = '';
        foreach ($pageArr['num'] as $key => $num) {
            $numpage .= '<li'.( empty($num['isnowpage']) ? '' : ' class="'.$nowclass.'"' ).'><a href="' . $num['url'] . '">' . $num['page'] . '</a></li>';
        }
        $total = '<li class="disabled"><a href="">共 '.$this->total.' 条 / '.$this->pagecount.'页 </a></li>';
        // 以 bootstrap 的页面方式来进行构造
        $retstr = '<ul class="'.$ulclass.'">' . $home . $prev . $numpage . $next . $end . $total . '</ul>';
        return $retstr;
    }

}


?>