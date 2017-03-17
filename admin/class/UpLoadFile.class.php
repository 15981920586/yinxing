<?php
// print_r($_FILES);
// $TempPath=$_FILES['PicPath']['tmp_name'];
// echo $TempPath;
// move_uploaded_file($TempPath,'./uploadpath/1.jpg');

//我们的图片上传四种数据过滤
//一、图片格式	jpg gif jpeg png
//二、图片尺寸	500*1024 KB
//三、图片上传位置	uploadpath/20160315
//四、图片具体名称	图片命名机制
if(!defined('VISITSTATE')){echo 'visit error';exit();}
class UpLoadFile
{
	public $picFormat;	//允许上传图片的格式	以数组格式传递值
	public $picSize;	//允许接收图片的大小
	public $picCommon;	//图片上传公共的位置	不用以 / 格式结尾 如：../uploadfile
	public $thumbnailStart;//是否启用缩略图功能		开启的话，请设置值为：ON
	public $thumbnailWidth;//开启缩略图之后所生成图片的宽度
	public $thumbnailHeight;//开启缩略图之后所生成图片的高度
	public $watermarkStart;//是否开启水印状态
	public $watermarkPath;//水印图片的路径

	public function ImgSave($Path)
	{
		if(empty($Path)) return;
		$ResultArr=array();
		if(!is_uploaded_file($Path['tmp_name']))
		{
			return $ResultArr['State']=$this->MsgShow(1);//图片上传失败
		}
		$FileExtension=$this->FileExtension($Path['name']);//获取文件的具体格式
		//判断当前获取过来的图片格式是否再我们设定的范围内
		if(!in_array($FileExtension,$this->picFormat))
		{			
			return $ResultArr['State']=$this->MsgShow(2);//图片格式有误
		}
		//对图片尺寸为空值的验证
		if(empty($Path['size']))
		{
			return $ResultArr['State']=$this->MsgShow(3);	//图片尺寸为0
		}
		//判断图片尺寸是否在系统设定的范围内
		if($Path['size']>$this->picSize)
		{
			return $ResultArr['State']=$this->MsgShow(4);	//图片尺寸上传过大
		}
		//判断系统是否设定了图片上传的根目录
		if(empty($this->picCommon))
		{
			return $ResultArr['State']=$this->MsgShow(5);	//您的上传跟目录不存在
		}
		$PicPath=$this->SaveFile($Path['tmp_name'],$FileExtension);
		if(!$PicPath)
		{
			return $ResultArr['State']=$this->MsgShow(6);	//文件上传中失败
		}
		
		$ResultArr['State']=$this->MsgShow(0);
		$ResultArr['PicPath']=$PicPath;
		return $ResultArr;
	}

	//错误信息返回
	private function MsgShow($State=0)
	{
		return $State;
	}

	//获取文件名称的扩展名
	private function FileExtension($Name='')
	{
		if(!empty($Name))
		{
			$Base=pathinfo($Name);
			return $Base['extension'];
		}
		return false;
	}

	private function SetDirPath()
	{
		//格式 当前跟目录下的子文件夹（20160315）
		$NewSavePath=$this->picCommon.'/'.date('Ymd').'/';
		if(!is_dir($NewSavePath))
		{
			@mkdir($NewSavePath);
		}

		return $NewSavePath;
	}

	private function SaveFile($TempPath,$FileExtension)
	{
		$DirPath=$this->SetDirPath();

		$Name=date('YmdHis').'_'.mt_rand(100,999);
		$NewPicName=$Name.'.'.$FileExtension;

		$NewPicNameSmall=$DirPath.$Name.'small.'.$FileExtension;
		if(move_uploaded_file($TempPath,$this->SetDirPath().$NewPicName))
		{
			$Path=array();
			//$DirPath=str_replace('./','',$DirPath);
			$Path['big']=$DirPath.$NewPicName;

			if($this->thumbnailStart=='ON')
			{
				$this->ThumbnailSave($Path['big'],$NewPicNameSmall,$FileExtension);
				$NewPicNameSmall=str_replace('../','',$NewPicNameSmall);
				$Path['small']=$NewPicNameSmall;
			}

			if($this->watermarkStart=='ON')
			{
				$this->SetWatermark($Path['big'],$FileExtension);
			}

			$Path['big']=str_replace('../','',$Path['big']);
			return $Path;
		}else{
			return false;
		}
	}

	private function ThumbnailSave($Path,$NewPicNameSmall,$FileExtension)
	{
		list($width,$height)=getimagesize('./'.$Path);
		$newWidth=$this->thumbnailWidth;
		$newHeight=$this->thumbnailHeight;

		$newImg=imagecreatetruecolor($newWidth,$newHeight);
		$bg=imagecolorallocate($newImg,255,255,255);
		imagefill($newImg, 0, 0, $bg);
		if($FileExtension=='jpeg' || $FileExtension=='jpg')
		{
			$oldImg=imagecreatefromjpeg('./'.$Path);
		}
		elseif($FileExtension=='gif')
		{
			$oldImg=imagecreatefromgif('./'.$Path);
		}
		elseif($FileExtension=='png')
		{
			$oldImg=imagecreatefrompng('./'.$Path);
		}

		//按比例计算缩略图大小
//		原图：高度500
//		原图：宽度300
//
//		缩略图：高度 200
//		缩略图：宽度 200

		/*
		状态一：高度满足：
		300-200>500-200
		100>300

		新的高度：200
		新的宽度：120=(200/500)*300

		坐标值：
		40=（200-120）/2
		0=(200-200)/2
		*/

        if ($width - $newWidth > $height - $newHeight) {
            $newHeight = ($newWidth / $width) * $height;
        } else {
            $newWidth = ($newHeight / $height) * $width;
        }

        $x=0;$y=0;
		$x = round(($this->thumbnailWidth - $newWidth) / 2);
		$y = round(($this->thumbnailHeight - $newHeight) / 2);
		imagecopyresampled($newImg,$oldImg, $x, $y, 0, 0, $newWidth, $newHeight, $width, $height);

		//header('Content-Type:image/jpeg');
		if($FileExtension=='jpeg' || $FileExtension=='jpg')
		{
			imagejpeg($newImg,$NewPicNameSmall);
		}
		elseif($FileExtension=='gif')
		{
			imagegif($newImg,$NewPicNameSmall);
		}
		elseif($FileExtension=='png')
		{
			imagepng($newImg,$NewPicNameSmall);
		}

		imagedestroy($newImg);
		imagedestroy($oldImg);
	}

	private function SetWatermark($Path,$FileExtension)
	{
		list($width,$height)=getimagesize('./'.$Path);
		if($FileExtension=='jpeg' || $FileExtension=='jpg')
		{
			$oldImg=imagecreatefromjpeg('./'.$Path);
		}
		elseif($FileExtension=='gif')
		{
			$oldImg=imagecreatefromgif('./'.$Path);
		}
		elseif($FileExtension=='png')
		{
			$oldImg=imagecreatefrompng('./'.$Path);
		}

		$logoFileExt=$this->FileExtension($this->watermarkPath);
		if($logoFileExt=='jpeg' || $logoFileExt=='jpg')
		{
			$logonImg=imagecreatefromjpeg($this->watermarkPath);
		}
		elseif($logoFileExt=='gif')
		{
			$logonImg=imagecreatefromgif($this->watermarkPath);
		}
		elseif($logoFileExt=='png')
		{
			$logonImg=imagecreatefrompng($this->watermarkPath);
		}

		list($logoWidth,$logoHeight)=getimagesize($this->watermarkPath);

		imagecopy($oldImg, $logonImg, $width-$logoWidth, $height-$logoHeight, 0, 0, $logoWidth, $logoHeight);

		//header('Content-Type:image/jpeg');
		if($FileExtension=='jpeg' || $FileExtension=='jpg')
		{
			imagejpeg($oldImg,'./'.$Path);
		}
		elseif($FileExtension=='gif')
		{
			imagegif($oldImg,'./'.$Path);
		}
		elseif($FileExtension=='png')
		{
			imagepng($oldImg,'./'.$Path);
		}
		imagedestroy($oldImg);
		imagedestroy($logonImg);
	}
}
?>