<?php
// 执行时间不限制
set_time_limit(0);

// 可读文件
$allowRead = array('php', 'conf', 'py', 'txt', 'go', 'lua', 'sql');

// 主目录 
define("ROOT", dirname(__FILE__));  // dir ->  /Users/baidu/Desktop/PHPTEST

// 脚本路径
$script = $_SERVER['PHP_SELF'];

// 用户访问目录
$path = substr($script, strlen("/index.php"));  // 去掉index.php 

// 用户上传内容
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(empty($_FILES) || empty($_FILES['upload'])) { // 没有选择文件
        header("location:/");
        exit();
    } else { // 上传文件
        $filename = ROOT . $path .   $_FILES['upload']['name'];
        if(move_uploaded_file($_FILES['upload']['tmp_name'] , $filename) ) {
            echo "<h1>upload success</h1>";
        };
        echo "<h2><a href='/'>回到首页 </a> <h2>";
        exit();
    } 
}
// 用户访问完整目录
$filename = ROOT . $path;
if (is_dir($filename)) {
    // 访问某一个目录 
    if (substr($path, -1 ) != '/') {
        header("location: " . $script . '/');
    }
    $path  = empty($path) ? "/" : $path;
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        echo "<h1>Directory listing for {$path}</h1>";
    } 
} else if (is_file($filename)) {
    // 访问某一个文件
    $fileinfo = pathinfo($filename);
    if(strpos($fileinfo['basename'], '.') == 0 || in_array($fileinfo['extension'], $allowRead)) {
        $content = file_get_contents($filename);
        echo "<pre>";
        echo htmlspecialchars($content);
        exit();
    } else {
        header("location:" . $path);
        exit();
    }
} else { 
    // 未知文件
    header("location:/");
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>文件服务器</title>
</head>
<body>
<hr>
<form action="/index.php<?php echo $path; ?>" method="post" enctype="multipart/form-data">
<input type="file" name="upload" id="" >
<input type="submit" value="upload">
</form>
<hr>
<?php
//列出文件列表
$filename_arr = array();
if($dh = @opendir($filename)){
    //读取
        while(($file = readdir($dh)) !== false){
        if($file != '.' && $file != '..'){
                $filename_arr[] = $file;
        }
    }
    //关闭
    closedir($dh);
}

echo "<ul>";
foreach ($filename_arr as $v) {
    echo "<li><a href= '/index.php{$path}{$v}'>$v </a></li>";
}
echo "</ul>";
?>

</body>
</html>