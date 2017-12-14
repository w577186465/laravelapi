<?php 

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class FilesystemController extends Controller {

    /**
     * 显示所给定的用户个人数据。
     *
     * @param  int  $id
     * @return Response
     */

    // 初始数据
    public function upload (Request $request) {
      $file = $request->file;
      // 第一个参数代表目录, 第二个参数代表我上方自己定义的一个存储媒介
      $day = date("Y-m-d");
      
      $src = $file->store('pictures/' . $day); // 保存源文件 返回路径
      
      $res = [
        'src' => $src,
      ];
      return $res;
    }

    public function uploader (Request $request) {
        $sizes = $request->input('sizes', []);
        $sizes[] = [120, 120];
        $file = $request->file;
        // 第一个参数代表目录, 第二个参数代表我上方自己定义的一个存储媒介
        $day = date("Y-m-d");
        
        $src = $file->store('pictures/' . $day); // 保存源文件 返回路径
        $path = public_path('uploads/pictures/' . $day . '/');
        $filepath = public_path('uploads/') . $file->store('pictures/' . $day); // 保存源文件 返回路径
        // 获取文件名
        preg_match('/\/([^\/]+)\.[a-z]*[^\/]*$/',$filepath,$filematch);
        $filename = $filematch[1];

        // 获取文件后缀
        $extension = $file->extension();

        // 生成缩略图
        $thumbs = [];
        foreach ($sizes as $v) {
            $img = Image::make($filepath)->resize(null, $v[1])->resizeCanvas($v[0], $v[1], 'center');
            $thumbname = sprintf('%s%s_%s_%s.%s', $path, $filename, $v[0], $v[1], $extension); // 缩略图保存路径
            $key = $v[0] . '_' . $v[1]; // 结果键值
            $thumburl = str_replace($filename, $filename . '_' . $key, $src);
            
            $thumbs[$key] = $thumburl;
            $img->save($thumbname);
        }
        
        $res = [
            'src' => $src,
            'thumbs' => $thumbs
        ];
        return $res;
    }
}