

<div class="container">
  <form class="form-horizontal" enctype="multipart/form-data" method="post" action="<?php echo $this->createUrl('update'); ?>">
    <input type="hidden" name="_csrf" value="<?php echo base::app('csrf')->formCsrf(); ?>">
    <input type="hidden" name="id" value="<?php echo $data['id'] ?>">


    <div class="row list-group">

      <div class="list-group-item form-group">
        <label class="control-label col-md-1">多文件：</label>
        <div class="col-md-5">
          <input type="file" multiple="multiple" name="uploads[]">
        </div>
      </div>

      <div class="list-group-item form-group">
        <label class="control-label col-md-1">单文件：</label>
        <div class="col-md-5">
          <input type="file" name="up">
        </div>
      </div>

      <div class="list-group-item form-group">
        <label class="control-label col-md-1">文章标题：</label>
        <div class="col-md-5">
          <input class="form-control" name="title" type="text" value="<?php echo $data['title']; ?>">
        </div>
      </div>

      <div class="list-group-item form-group">
        <label class="control-label col-md-1">副标题：</label>
        <div class="col-md-5">
          <input class="form-control" name="subtitle" type="text" value="<?php echo $data['subtitle']; ?>">
        </div>
      </div>

      <div class="list-group-item form-group" style="margin-top:-2px">
        <input type="submit" class="col-md-offset-5 right10 btn btn-primary" value="提交">
        <input type="reset" class="btn btn-danger left10" value="重置">
      </div>

    </div>



  </form>



</div>







