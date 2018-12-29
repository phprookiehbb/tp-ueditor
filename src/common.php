<?php

/**
 * @Author: CraspHB彬
 * @Date:   2018-12-28 13:10:42
 * @Email:   646054215@qq.com
 * @Last Modified time: 2018-12-29 14:59:03
 */
use think\Route;
Route::rule('Ueditor/upload/index','\\Crasphb\\Ueditor@index');

function ueditor($params = []){
    Crasphb\Ueditor::install();
    $ueditor = new Crasphb\upload\Ueditor();
	call_user_func([$ueditor,'index'],$params);
}

/**
 * 遍历获取目录下的指定类型的文件
 * @param $path
 * @param array $files
 * @return array
 */
function getfiles($path, $allowFiles, &$files = array())
{
    if (!is_dir($path)) return null;
    if(substr($path, strlen($path) - 1) != '/') $path .= '/';
    $handle = opendir($path);
    while (false !== ($file = readdir($handle))) {
        if ($file != '.' && $file != '..') {
            $path2 = $path . $file;
            if (is_dir($path2)) {
                getfiles($path2, $allowFiles, $files);
            } else {
                if (preg_match("/\.(".$allowFiles.")$/i", $file)) {
                    $files[] = array(
                        'url'=> substr($path2, strlen($_SERVER['DOCUMENT_ROOT'])),
                        'mtime'=> filemtime($path2)
                    );
                }
            }
        }
    }
    return $files;
}

/**
 * 移动资源到根目录
 * @param  [type] $target [description]
 * @param  [type] $path   [description]
 * @return [type]         [description]
 */
function cpfiles($target , $path){

    $handle = opendir($target);
    while(false !== ($file = readdir($handle))){
        if($file != '.' && $file != '..'){
            if(!is_dir($path)){
               mkdir($path,0777,true);
            }            
            $target2 = $target .DS. $file;
            $path2 = $path .DS. $file;
            if(is_dir($target2) && !is_dir($path2)){
                cpfiles($target2,$path2);
            }else{
                if(file_exists($path2)) continue;
                copy($target2 , $path2);
            }
        }
    }
}