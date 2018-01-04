<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL   ^ E_NOTICE);
set_time_limit(30 * 30);
ini_set("memory_limit","5M");

if ( isset($_POST) && count($_POST)>0 )
{
    if( is_uploaded_file($_FILES["imgFile"]["tmp_name"]) )
    {
        $ext = "";
        switch ($_FILES["imgFile"]["type"])
        {
            case "image/gif":
                $ext = ".gif";
                $type_ext = "gif";
            break;
            case "image/pjpeg":
            case "image/pjpg":
            case "image/jpg":
            case "image/jpeg":
                $ext = ".jpg";
                $type_ext = "gif";
            break;

            default:
                $errs[count($errs)] = "Only gif or jpeg";
            break;
        }
        if (trim($ext) != "")
        {
            if (floor($_FILES["imgFile"]["size"]) > 1048576)
            {
                $errs[count($errs)] = "Image size too large";
            }
            else
            {
                $img = $_FILES["imgFile"]['name'];
                if (! ( move_uploaded_file($_FILES["imgFile"]["tmp_name"], $img) ) )
                {
                    $errs[count($errs)] = "Can't uplaod";;
                }
                else
                {
                    $con_path =  "convert";
                    exec("./morphology -m grayscale -t open -i 2 $img  2$img");
                    exec("$con_path $img 2$img -compose difference -composite 3$img");
                    exec("$con_path 3$img  -threshold 25% 4$img");
                    exec("./morphology -m binary -t dilate 4$img  5$img");
                    exec("$con_path $img 2$img 5$img  -composite last$img");
                    unlink($img);
                    unlink("2$img");
                    unlink("3$img");
                    unlink("4$img");
                    unlink("5$img");
                    header("Content-Type: image/$type_ext");
                    readfile("last$img");
                    unlink("last$img");
                }
            }
        }
    }
    else
    {
        $errs[count($errs)] = "Uplaod image";
    }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>

<body>
<?
    if(count($errs))
    foreach($errs as $k => $v)
{?>
<tr valign="top">
<td width="6%">&nbsp;<? echo $k+1;?>.</td>
<td width="94%"><? echo  $v; ?></td>
</tr>
<?
}
?>
<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data">
<p>
<input name="imgFile" type="file" />
</p>
<p>
<label>
<input type="submit" name="Submit" value="Submit" />
</label>
</p>
</form>
</body>
</html>