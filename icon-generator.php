<?php


if(isset($argv[1]) && !empty($argv[1]) && file_exists($argv[1])) {
    $file = $argv[1];
}
else {
    echo "\n";
    echo "USAGE: icon-generator.php <icon>";
    echo "\n\n";
    die();
}

//formats based on ios human-interface-guidelines
$formats = array(
    array('px' => 180, 'pt' => 60, 'scale' => '@3x'),
    array('px' => 120, 'pt' => 60, 'scale' => '@2x'),
    array('px' => 167, 'pt' => 83.5, 'scale' => '@2x'),
    array('px' => 152, 'pt' => 76, 'scale' => '@2x'),
    array('px' => 76, 'pt' => 76, 'scale' => '@1x'),
    array('px' => 120, 'pt' => 40, 'scale' => '@3x'),
    array('px' => 80, 'pt' => 40, 'scale' => '@2x'),
    array('px' => 40, 'pt' => 40, 'scale' => '@1x'),
    array('px' => 87, 'pt' => 29, 'scale' => '@3x'),
    array('px' => 58, 'pt' => 29, 'scale' => '@2x'),
    array('px' => 29, 'pt' => 29, 'scale' => '@1x'),
    array('px' => 60, 'pt' => 20, 'scale' => '@3x'),
    array('px' => 40, 'pt' => 20, 'scale' => '@2x'),
    array('px' => 20, 'pt' => 20, 'scale' => '@1x')
    //array('px' => 1024, 'pt' => null, 'scale' => null), //for appstore
);

$file_name = pathinfo($file, PATHINFO_FILENAME);
$ext = pathinfo($file, PATHINFO_EXTENSION);

if(!is_dir(__DIR__.'/icons')) {
    mkdir(__DIR__.'/icons');
}

foreach($formats as $format) {
    $out_file = __DIR__.'/icons/'.$file_name.'-'.$format['pt'].$format['scale'].'.'.$ext;
    createIcon($file, $out_file, $format['px'], $format['px']);
}

function createIcon($in_file, $out_file, $new_width, $new_height) {

    $result = false;
    $mime = getimagesize($in_file);

    switch($mime['mime']) {
        case 'image/png':
            $src_img = imagecreatefrompng($in_file);
            break;
        case 'image/jpg':
        case 'image/jpeg':
            $src_img = imagecreatefromjpeg($in_file);
            break;
    }

    $old_x = imageSX($src_img);
    $old_y = imageSY($src_img);

    if($old_x > $old_y) {
        $icon_w = $new_width;
        $icon_h = $old_y * ($new_height / $old_x);
    }

    if($old_x < $old_y) {
        $icon_w = $old_x * ($new_width / $old_y);
        $icon_h = $new_height;
    }

    if($old_x == $old_y) {
        $icon_w = $new_width;
        $icon_h = $new_height;
    }

    $dst_img = ImageCreateTrueColor($icon_w,$icon_h);

    imagecopyresampled($dst_img, $src_img, 0,0,0,0,$icon_w, $icon_h, $old_x, $old_y);

    switch($mime['mime']) {
        case 'image/png':
            $result = imagepng($dst_img, $out_file, 8);
            break;
        case 'image/jpg':
        case 'image/jpeg':
            $result = imagejpeg($dst_img,$out_file, 80);
            break;
    }

    imagedestroy($dst_img);
    imagedestroy($src_img);

    return $result;
}