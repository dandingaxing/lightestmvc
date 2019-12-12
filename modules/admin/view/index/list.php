

<div class="container">
  <form class="form-horizontal" enctype="multipart/form-data" id="myform" method="post" action="/index.php?r=article/index/update" style="">
    <div class="row">
      <label class="control-label col-md-1" for="thumbs">标签：</label>
      <div class="col-md-10">
        <?php foreach ($cateArr as $key => $catOne): ?>
          <div class="col-md-1"><?php echo $catOne['tagsname'] ?></div>
          <div class="col-md-11">
            <?php foreach ($catOne['children'] as $k => $catTwo): ?>
              <span class="col-md-2">
                <input type="checkbox" name="tags[<?php $catOne['id'] ?>][<?php echo $catTwo['id'] ?>]" value="<?php echo $catTwo['id'] ?>">
                <?php echo $catTwo['tagsname'] ?>
              </span>
            <?php endforeach ?>
          </div>
        <?php endforeach ?>
      </div>
    </div>

    <div class="row">
      <label class="control-label col-md-1" for="thumbs">关键字：</label>
      <div class="col-md-2">
        <input name="keywords" class="form-control">
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-primary">提交</button>
      </div>
    </div>

  </form>


</div>


<div class="container">
  <table class="table table-hover table-bordered">
    <thead>
      <tr class="info">
        <th><input type="checkbox" id="checkAll"></th>
        <th>ID</th>
        <th>文章名称</th>
        <th>标签</th>
        <th>描述</th>
        <th>更新时间</th>
        <th>操作</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($dataList as $key => $data): ?>
      <tr>
        <td><input type="checkbox" name="changestatus[]" value="<?php echo $data['id']; ?>"></td>
        <td><?php echo $data['id'] ?></td>
        <td><?php echo $data['title'] ?></td>
        <td><?php echo $data['keywords'] ?></td>
        <td><?php echo strip_tags( $data['description'] ); ?></td>
        <td><?php echo date('Y-m-d H:i:s', $data['updatetime']); ?></td>
        <td>
          <a href="<?php echo $this->createUrl('edit', array('id'=>$data['id'])); ?>" class="btn btn-xs btn-primary">修改</a>
          <a href="<?php echo $this->createUrl('delete', array('id'=>$data['id'])); ?>" class="btn btn-xs btn-danger">删除</a>
        </td>
      </tr>
    <?php endforeach ?>
    </tbody>
  </table>
</div>


<div class="container">
  <?php echo $page; ?>
</div>

<?php // print_r($dataList); ?>

