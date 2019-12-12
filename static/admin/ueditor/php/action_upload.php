<?php
/**
 * 上传附件和上传视频
 * User: Jinqn
 * Date: 14-04-09
 * Time: 上午10:17
 */
include "Uploader.class.php";
include "upyun.class.php";

$upyunConfig = array(
    'hostName' => 'youlipinpic.b0.upaiyun.com',
    'bucketname' => 'youlipinpic',
    'operator_name' => 'ctm',
    'operator_pwd' => 'YOUlipin2014',
    );

/* 上传配置 */
$base64 = "upload";
switch (htmlspecialchars($_GET['action'])) {
    case 'uploadimage':
        $config = array(
            "pathFormat" => $CONFIG['imagePathFormat'],
            "maxSize" => $CONFIG['imageMaxSize'],
            "allowFiles" => $CONFIG['imageAllowFiles']
        );
        $fieldName = $CONFIG['imageFieldName'];
        break;
    case 'uploadscrawl':
        $config = array(
            "pathFormat" => $CONFIG['scrawlPathFormat'],
            "maxSize" => $CONFIG['scrawlMaxSize'],
            "allowFiles" => $CONFIG['scrawlAllowFiles'],
            "oriName" => "scrawl.png"
        );
        $fieldName = $CONFIG['scrawlFieldName'];
        $base64 = "base64";
        break;
    case 'uploadvideo':
        $config = array(
            "pathFormat" => $CONFIG['videoPathFormat'],
            "maxSize" => $CONFIG['videoMaxSize'],
            "allowFiles" => $CONFIG['videoAllowFiles']
        );
        $fieldName = $CONFIG['videoFieldName'];
        break;
    case 'uploadfile':
    default:
        $config = array(
            "pathFormat" => $CONFIG['filePathFormat'],
            "maxSize" => $CONFIG['fileMaxSize'],
            "allowFiles" => $CONFIG['fileAllowFiles']
        );
        $fieldName = $CONFIG['fileFieldName'];
        break;
}

/* 百度原生类 生成上传实例对象并完成上传 (将上传屏蔽验证图片并返回信息) */
$up = new Uploader($fieldName, $config, $base64);
$baiduBack = $up->getFileInfo();

// 又拍云上传
$upyun = new UpYun($upyunConfig['bucketname'], $upyunConfig['operator_name'], $upyunConfig['operator_pwd']);
$fh = fopen($_FILES[$fieldName]["tmp_name"], 'r');
$aa =  $upyun->writeFile($baiduBack["url"], $fh, true);
fclose($fh);


$upyunBack = array(
    "state" => $baiduBack['state'],
    "url" => 'http://' . $upyunConfig['hostName'] . $baiduBack['url'] . '!mark',
    "title" => $baiduBack['title'],
    "original" => $baiduBack['original'],
    "type" => $baiduBack['type'],
    "size" => $baiduBack['size']
    );

return json_encode($upyunBack);


/**
 * 得到上传文件所对应的各个参数,数组结构
 * array(
 *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
 *     "url" => "",            //返回的地址
 *     "title" => "",          //新文件名
 *     "original" => "",       //原始文件名
 *     "type" => ""            //文件类型
 *     "size" => "",           //文件大小
 * )
 */

/* 返回数据 */
// return json_encode($up->getFileInfo());
