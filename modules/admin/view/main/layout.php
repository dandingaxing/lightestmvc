<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <title>文章-首页</title>
    <link rel="stylesheet" type="text/css" href="/static/admin/css/bootstrap.css">
    <script type="text/javascript" src="/static/admin/js/jquery.min.js"></script>
    <script type="text/javascript" src="/static/admin/js/bootstrap.js"></script>
</head>
<body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <a class="navbar-brand" href="<?php echo $this->createUrl('/article/index/index'); ?>">文章管理首页</a>
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav">
            <li  class="dropdown">
                <a href="<?php echo $this->createUrl('/article/index/list'); ?>">文章管理</a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="<?php echo $this->createUrl('/article/index/list'); ?>">文章列表</a>
                        <a href="<?php echo $this->createUrl('/article/index/add'); ?>">添加文章</a>
                        <a href="<?php echo $this->createUrl('/article/index/list', array('status'=>1)); ?>">文章黑名单</a>
                    </li>
                </ul>
            </li>
            <li  class="dropdown">
                <a href="<?php echo $this->createUrl('/article/tags/list'); ?>">栏目管理</a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="<?php echo $this->createUrl('/article/tags/list'); ?>">栏目列表</a>
                        <a href="<?php echo $this->createUrl('/article/tags/add'); ?>">添加栏目</a>
                    </li>
                </ul>
            </li>
            <li  class="dropdown">
                <a href="<?php echo $this->createUrl('/article/advert/list'); ?>">广告位管理</a>
                <ul class="dropdown-menu">
                    <li>
                      <a href="<?php echo $this->createUrl('/article/advert/list'); ?>">广告位列表</a>
                      <a href="<?php echo $this->createUrl('/article/advert/add'); ?>">添加广告位</a>
                    </li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="<?php echo $this->createUrl('/article/words/list'); ?>">标签管理</a>
                <ul class="dropdown-menu">
                    <li>
                      <a href="<?php echo $this->createUrl('/article/words/list'); ?>">标签列表</a>
                      <a href="<?php echo $this->createUrl('/article/words/add'); ?>">添加标签</a>
                    </li>
                </ul>
            </li>
            <li  class="dropdown">
                <a href="<?php echo $this->createUrl('/article/pinglun/list'); ?>">评论管理</a>
                <ul class="dropdown-menu">
                    <li>
                      <a href="<?php echo $this->createUrl('/article/pinglun/list'); ?>">评论列表</a>
                    </li>
                    <li>
                      <a href="<?php echo $this->createUrl('/article/pinglun/list', array('status'=>'0')); ?>">评论待审核列表</a>
                    </li>
                </ul>
            </li>

          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="javascript:void(0)">欢迎：xxx</a></li>
            <li>
                <li><a href="<?php echo $this->createUrl('/article/default/logout'); ?>">登出</a></li>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <script style="text/javascript">
        $('li.dropdown').mouseover(function() {
            $(this).addClass('open');    }).mouseout(function() {        $(this).removeClass('open');
        }); 
    </script>
    
    <div class="container" style="margin-top:50px"></div>

    <?php echo $content; ?>


</body>
</html>