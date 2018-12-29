<?php

/**
 * @Author: CraspHB彬
 * @Date:   2018-12-28 11:36:11
 * @Email:   646054215@qq.com
 * @Last Modified time: 2018-12-29 14:54:45
 */
namespace Crasphb;
use Crasphb\upload\Uploader;

class Ueditor{
	/**
	 * 上传入口
	 * @return [type] [description]
	 */
	public function index(){

		date_default_timezone_set("Asia/chongqing");
		error_reporting(E_ERROR);
		header("Content-Type: text/html; charset=utf-8");

		$CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents(__DIR__."/config.json")), true);
		$action = $_GET['action'];
        //halt($CONFIG);
		switch ($action) {
		    case 'config':
		        $result =  json_encode($CONFIG);
		        break;

		    /* 上传图片 */
		    case 'uploadimage':
		    /* 上传涂鸦 */
		    case 'uploadscrawl':
		    /* 上传视频 */
		    case 'uploadvideo':
		    /* 上传文件 */
		    case 'uploadfile':
		        $result = $this->upload($CONFIG);
		        break;

		    /* 列出图片 */
		    case 'listimage':
		        $result = $this->listSource($CONFIG);
		        break;
		    /* 列出文件 */
		    case 'listfile':
		        $result = $this->listSource($CONFIG);
		        break;

		    /* 抓取远程文件 */
		    case 'catchimage':
		        $result = $this->catchimage($CONFIG);
		        break;

		    default:
		        $result = json_encode(array(
		            'state'=> '请求地址出错'
		        ));
		        break;
		}

		/* 输出结果 */
		if (isset($_GET["callback"])) {
		    if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
		        echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
		    } else {
		        echo json_encode(array(
		            'state'=> 'callback参数不合法'
		        ));
		    }
		} else {
		    echo $result;
		}
	}
	/**
	 * 资源上传
	 * @param  string $CONFIG [description]
	 * @return [type]         [description]
	 */
	protected function upload($CONFIG = ''){
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

		/* 生成上传实例对象并完成上传 */
		$up = new Uploader($fieldName, $config, $base64);

		/* 返回数据 */
		return json_encode($up->getFileInfo());
	}
	/**
	 * 列出图片，文件资源
	 * @param  string $CONFIG [description]
	 * @return [type]         [description]
	 */
	protected function listSource($CONFIG = ''){
		/* 判断类型 */
		switch ($_GET['action']) {
		    /* 列出文件 */
		    case 'listfile':
		        $allowFiles = $CONFIG['fileManagerAllowFiles'];
		        $listSize = $CONFIG['fileManagerListSize'];
		        $path = $CONFIG['fileManagerListPath'];
		        break;
		    /* 列出图片 */
		    case 'listimage':
		    default:
		        $allowFiles = $CONFIG['imageManagerAllowFiles'];
		        $listSize = $CONFIG['imageManagerListSize'];
		        $path = $CONFIG['imageManagerListPath'];
		}
		$allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);

		/* 获取参数 */
		$size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
		$start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : 0;
		$end = $start + $size;

		/* 获取文件列表 */
		$path = $_SERVER['DOCUMENT_ROOT'] . (substr($path, 0, 1) == "/" ? "":"/") . $path;
		$files = getfiles($path, $allowFiles);
		if (!count($files)) {
		    return json_encode(array(
		        "state" => "no match file",
		        "list" => array(),
		        "start" => $start,
		        "total" => count($files)
		    ));
		}

		/* 获取指定范围的列表 */
		$len = count($files);
		for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
		    $list[] = $files[$i];
		}

		/* 返回数据 */
		$result = json_encode(array(
		    "state" => "SUCCESS",
		    "list" => $list,
		    "start" => $start,
		    "total" => count($files)
		));

		return $result;
	}
	/**
	 * ueditor资源安装到根目录下
	 * @return [type] [description]
	 */
	static public function install(){
		
		cpfiles(__DIR__.DS.'ueditor',ROOT_PATH.'public'.DS.'tp-ueditor');
	}
}