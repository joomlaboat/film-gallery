<?php
/**
* Film Gallery Joomla! 3.x Native Component
* @version 1.1.2
* @author Ivan Komlev <support@joomlaboat.com>
* Copyright (C) 2009-2018 Ivan Komlev
* @link http://www.joomlaboat.com
* @license GNU/GPL **/


defined('_JEXEC') or die('Restricted access');

class FilmGalleryClass
{

	var $copyprotection;
	var $thumbstyle;

	var $bgimagefolder;

	var $scrollsize;
	var $thumbwidth;
	var $thumbheight;

	var $padding;

	function getFilmGallery($galleryparams,$count)
	{

		$opt=explode(',',$galleryparams);
		if(count($opt)<1)
			return '';

		// 0 - Folder
		// 1 - Width
		// 2 - Height
		// 3 - Scroll Position
		// 4 - File List
		// 5 - Thumb Background Image
		// 6 - Scroll Size
		// 7 - Thumb Width
		// 8 - Thumb Height
		// 9 - Distance between images, vertical or horizontal depending on navigation bar position



		$folder=$opt[0];

		$width=400;		if(count($opt)>1)	$width=$opt[1];
		$height=300;		if(count($opt)>2)	$height=(int)$opt[2];
		$scrollposition='';	if(count($opt)>3)	$scrollposition=$opt[3];
		$filelist='';		if(count($opt)>4)	$filelist=$opt[4];


		$this->thumbbgimage="";		if(count($opt)>5)	$this->thumbbgimage=$opt[5];

		$this->scrollsize=130;		if(count($opt)>6)	$this->scrollsize=$opt[6];
		if($this->scrollsize==0)
			$this->scrollsize=135;

		$this->thumbwidth=0;		if(count($opt)>7)	(int)$this->thumbwidth=$opt[7];
		$this->thumbheight=0;		if(count($opt)>8)	(int)$this->thumbheight=$opt[8];

		$this->padding='5';			if(count($opt)>9)	(int)$this->padding=$opt[9];

		//$this->cssstyle='padding:5px;border-style:none;';			if(count($opt)>9)	$this->cssstyle=$opt[9];



		$imagefiles=$this->getFileList($folder, $filelist);

		$result='';
		if(count($imagefiles)==0)
			return $result;

		$divName='filmegalleryplg_'.$count;

		switch($scrollposition)
		{
			case 'left' :
				$result=$this->drawGalleryLeft($imagefiles,$width,$height,$divName);
			break;


			case 'right' :
				$result=$this->drawGalleryRight($imagefiles,$width,$height,$divName);
			break;

			case 'top' :
				$result=$this->drawGalleryTop($imagefiles,$width,$height,$divName);
			break;

			case 'bottom' :
				$result=$this->drawGalleryBottom($imagefiles,$width,$height,$divName);
			break;

			default:

				$pair=explode(':',$scrollposition);

				if(isset($pair[1]))
					$rel=$pair[1];
				else
					$rel='shadowbox';

				if($pair[0]=='vertical')
					$result=$this->drawGalleryVertical($imagefiles,$width,$height,$divName,$rel);
				elseif($pair[0]=='horizontal')
					$result=$this->drawGalleryHorizontal($imagefiles,$width,$height,$divName,$rel);
				else
					$result=$this->drawGalleryRight($imagefiles,$width,$height,$divName);
		}

		return

		$result.'
		<!-- end of film gallery -->';
	}

	//Image Gallery
	function FileExtenssion($src)
	{
		$fileExtension='';
		$name = explode(".", strtolower($src));
		$currentExtensions = $name[count($name)-1];
		$allowedExtensions = 'jpg jpeg gif png';
		$extensions = explode(" ", $allowedExtensions);
		for($i=0; count($extensions)>$i; $i=$i+1){
			if($extensions[$i]==$currentExtensions)
			{
				$extensionOK=1;
				$fileExtension=$extensions[$i];

				return $fileExtension;
				break;
			}
		}

		return $fileExtension;
	}







	function drawGalleryRight(&$imagefiles,$width,$height,$divName)
	{
		$count=0;
		if($this->thumbwidth==0)
			$this->thumbwidth=90;

		if($this->thumbbgimage=='')
			$this->thumbbgimage=$this->bgimagefolder.'film_v.gif';

		if($this->thumbbgimage=='none')
			$this->thumbbgimage='';

		$htmlresult='';
		$htmlresult.='



        <!-- Film Gallery (Right Scroll)-->

		<table width="'.($width+$this->scrollsize+15).'" height="'.$height.'" border="0" align="center" cellpadding="0" cellspacing="0" style="border:none;padding:0;margin:0;">
		<tr>
		<td align="center" width="'.$width.'" style="margin:0;padding:0;border:none;">
		<div style="width:'.$width.'px; height:'.$height.'px;position: relative;overflow:hidden;">
        <img src="'.$imagefiles[0].'" width="'.$width.'" height="'.$height.'" style="z-index:4;padding:0;margin:0;" id="'.$divName.'_Main" name="'.$divName.'_Main">';

		if($this->copyprotection)
			$htmlresult.='<div style="position: absolute;top: 0;left:0;width:'.$width.'px;height:'.$height.'px;background-image: url(plugins/content/filmgalleryfiles/glass.png);background-repeat: repeat;"></div>';

		$htmlresult.='</div>
		</td>
		<td valign="top" style="padding-top:0px;margin:0;padding:0;text-align:left;border:none;" align="center">';

		$htmlresult.=$this->VerticalNavigation($imagefiles,$height,$divName,'',true);

		$htmlresult.='</td>
		</tr>
		</table>
';

    return $htmlresult;

	}
	function HorizontalNavigation(&$imagefiles,$width,$divName,$rel='')
	{
		$htmlresult='
		<div style="

			width:'.$width.'px;
			overflow: -moz-scrollbars-horizontal;
			overflow-x: auto;
			overflow-y: hidden;
			padding: 0;
			margin: 0;
			position:relative;
			">

			<table border="0" height="'.$this->scrollsize.'" cellpadding="0" cellspacing="0"
			style="border-style:none;padding:0;margin:0;';

			if($this->thumbbgimage!='')
			{
				$htmlresult.='background-image: url('.$this->thumbbgimage.');background-repeat: repeat-x; background-position:center center; ';
			}

			$htmlresult.='"><tbody>

			<tr height="'.$this->scrollsize.'" >';

	    //List of Images

		foreach($imagefiles as $imagefile)
        {
			$margintop=(int)(($this->scrollsize-$this->thumbheight)/2);


			$htmlresult.='<td height="'.$this->scrollsize.'" width="110" align="center" valign="top" style="width:110px !important;position:relative;border:none;margin:0;padding:0;">';

				if($this->copyprotection and $rel=='')
				{

					$htmlresult.='

				<div style="margin-right:'.$this->padding.'px;height:'.$this->thumbheight.'px;
				width:110px !important;
				margin-top:'.$margintop.'px;
				position:relative;
				cursor:pointer;"
				onMouseOver=\'document.getElementById("'.$divName.'_Main").src="'.$imagefile.'";\'	onMouseOver=\'document.getElementById("'.$divName.'_Main").src="'.$imagefile.'";\'>
				<img src="'.$imagefile.'" height="'.$this->thumbheight.'" style="padding:0;height:'.$this->thumbheight.'px;margin:0;border:none;">';
					$htmlresult.='<div style="position: absolute;width:110px !important;top: 0;left:0;right:0;bottom:0;background-image: url('.$this->bgimagefolder.'glass.png);background-repeat: repeat;"></div>';


					$htmlresult.='</div>
				';

				}
				elseif(!$this->copyprotection and $rel=='')
				{


					$htmlresult.='<div style="margin-right:'.$this->padding.'px;height:'.$this->thumbheight.'px;margin-top:'.$margintop.'px;">
					<img src="'.$imagefile.'" ';

					if($this->thumbwidth!=0)
						$htmlresult.=' width="'.$this->thumbwidth.'"';

					$htmlresult.=' height="'.$this->thumbheight.'" style="border:none;margin:0;padding:0;max-width:none !important;width:110px !important;';

					if($this->thumbwidth!=0)
						$htmlresult.='width:'.$this->thumbwidth.'px;';

					$htmlresult.='height:'.$this->thumbheight.'px;" onMouseOver=\'document.getElementById("'.$divName.'_Main").src="'.$imagefile.'";\'>
					</div>';
				}
				elseif(!$this->copyprotection and $rel!='')
				{

					$alt='';

					$htmlresult.='<div style="width:110px !important;margin-right:'.$this->padding.'px;height:'.$this->thumbheight.'px;margin-top:'.$margintop.'px;">';

					if($rel=='jcepopup')
						$htmlresult.='<a href="'.$imagefile.'" class="jcepopup" rel="title['.$alt.'];caption['.$alt.'];group[filmgallery];">';
					else
						$htmlresult.='<a href="'.$imagefile.'" rel="'.$rel.'">';

					$htmlresult.='<img src="'.$imagefile.'" height="'.$this->thumbheight.'" style="
					width:110px !important;border:none;margin:0;padding:0;height:'.$this->thumbheight.'px;" alt="'.$alt.'" /></a>
					</div>';
				}
				elseif($this->copyprotection and $rel!='')
				{
					$alt='';

					if($rel=='jcepopup')
						$htmlresult.='<a href="'.$imagefile.'" class="jcepopup" rel="title['.$alt.'];caption['.$alt.'];group[filmgallery];">';
					else
						$htmlresult.='<a href="'.$imagefile.'" rel="'.$rel.'">';

					$htmlresult.='

				<div style="margin-right:'.$this->padding.'px;height:'.$this->thumbheight.'px;
				margin-top:'.$margintop.'px;
				position:relative;
				cursor:pointer;">
				<img src="'.$imagefile.'" height="'.$this->thumbheight.'" style="padding:0;height:'.$this->thumbheight.'px;margin:0;border:none;">';
					$htmlresult.='<div style="position: absolute;top: 0;left:0;right:0;bottom:0;background-image: url('.$this->bgimagefolder.'glass.png);background-repeat: repeat;"></div>';


					$htmlresult.='</div></a>
				';

				}


				$htmlresult.='</td>';

        }
		$htmlresult.='
			</tr></tbody></table>
		</div>';

		return $htmlresult;

	}

	function VerticalNavigation(&$imagefiles,$height,$divName,$rel='',$add_15px=false)
	{

		$htmlresult='<div style="';

			//if($rel!='')
			if($add_15px)
				$htmlresult.='width: '.($this->scrollsize+15).'px;';

			$htmlresult.='height:'.$height.'px;
			overflow: scroll -moz-scrollbars-vertical;
			overflow-x: hidden;
			overflow-y: auto;
			padding: 0;
			margin: 0 auto 0 0;
			position:relative;
			"

			>

			<table border="0" width="'.$this->scrollsize.'" cellpadding="0" cellspacing="0"
			style="border:none;padding:0;margin:0;';
			//border-style:none
			if($this->thumbbgimage!='')
			{
				$htmlresult.='background-image: url('.$this->thumbbgimage.');	background-repeat: repeat-y; background-position:center center;';
			}
			$htmlresult.='">
			';

	    //List of Images

        foreach($imagefiles as $imagefile)
        {
			// height="'.$filmheight.'"<div style="height: '.$filmheight.'px;overflow: hidden;"></div>
            $htmlresult.='
            <tr>
			<td width="'.$this->scrollsize.'" valign="middle" align="center" style="position:relative;border:none;margin:0;padding:0;" >
            ';

			if($this->copyprotection and $rel=='')
			{
				$htmlresult.='

				<div style="margin-bottom:'.$this->padding.'px;width:'.$this->thumbwidth.'px;margin-left:auto;margin-right:auto;position:relative;cursor:pointer;" onMouseOver=\'document.getElementById("'.$divName.'_Main").src="'.$imagefile.'";\'	onMouseOver=\'document.getElementById("'.$divName.'_Main").src="'.$imagefile.'";\'>
				<img src="'.$imagefile.'" width="'.$this->thumbwidth.'" style="padding:0;width:'.$this->thumbwidth.'px;margin:0;border:none;">';
					$htmlresult.='<div style="position: absolute;top: 0;left:0;right:0;bottom:0;background-image: url('.$this->bgimagefolder.'glass.png);background-repeat: repeat;"></div>';


					$htmlresult.='</div>
				';
			}
			elseif(!$this->copyprotection and $rel=='')
			{
				$htmlresult.='
					<div style="margin-bottom:'.$this->padding.'px;width:'.$this->thumbwidth.'px;margin-left:auto;margin-right:auto;">
					<img src="'.$imagefile.'" width="'.$this->thumbwidth.'" style="border:none;padding:0;width:'.$this->thumbwidth.'px;margin:0;"
					onMouseOver=\'document.getElementById("'.$divName.'_Main").src="'.$imagefile.'";\'>
					</div>'
					;

			}
			elseif(!$this->copyprotection and $rel!='')
			{

					$alt='';

					$htmlresult.='<div style="margin-bottom:'.$this->padding.'px;width:'.$this->thumbwidth.'px;margin-left:auto;margin-right:auto;">';

					if($rel=='jcepopup')
						$htmlresult.='<a href="'.$imagefile.'" class="jcepopup" rel="title['.$alt.'];caption['.$alt.'];group[filmgallery];">';
					else
						$htmlresult.='<a href="'.$imagefile.'" rel="'.$rel.'">';

					$htmlresult.='<img src="'.$imagefile.'" width="'.$this->thumbwidth.'" style="border:none;padding:0;width:'.$this->thumbwidth.'px;margin:0;" /></a>
					</div>';
			}
			elseif($this->copyprotection and $rel!='')
			{
					$alt='';

					if($rel=='jcepopup')
						$htmlresult.='<a href="'.$imagefile.'" class="jcepopup" rel="title['.$alt.'];caption['.$alt.'];group[filmgallery];">';
					else
						$htmlresult.='<a href="'.$imagefile.'" rel="'.$rel.'">';

					$htmlresult.='

				<div style="margin-bottom:'.$this->padding.'px;width:'.$this->thumbwidth.'px;margin-left:auto;margin-right:auto;position:relative;cursor:pointer;" >
				<img src="'.$imagefile.'" width="'.$this->thumbwidth.'" style="border:none;padding:0;width:'.$this->thumbwidth.'px;margin:0;" />';
					$htmlresult.='<div style="position: absolute;top: 0;left:0;right:0;bottom:0;background-image: url('.$this->bgimagefolder.'glass.png);background-repeat: repeat;"></div>';


					$htmlresult.='</div></a>
				';

			}


			$htmlresult.='
			</td>
            </tr>
            ';
        }
		$htmlresult.='</table></div>';


		return $htmlresult;
	}

	function drawGalleryLeft(&$imagefiles,$width,$height,$divName)
	{

		$count=0;

		if($this->thumbwidth==0)
			$this->thumbwidth=90;

		if($this->thumbbgimage=='')
			$this->thumbbgimage=$this->bgimagefolder.'film_v.gif';

		if($this->thumbbgimage=='none')
			$this->thumbbgimage='';

		$htmlresult='';

		$htmlresult.='


        <!-- Film Gallery (Left Scroll)-->

		<table width="'.($width+$this->scrollsize).'" height="'.$height.'" border="0" align="center" cellpadding="0" cellspacing="0" style="border:none;padding:0;margin:0;">
		<tr>
		<td valign="top" style="margin:0;padding:0;border:none;" align="center">';
		//padding-top:0px;padding-bottom:0px;

		$htmlresult.=$this->VerticalNavigation($imagefiles,$height,$divName);


		$htmlresult.='
		</td>
		<td align="center" width="'.$width.'" style="margin:0;padding:0;border:none;">
		<div style="width:'.$width.'px; height:'.$height.'px;position: relative;overflow:hidden;">
        <img src="'.$imagefiles[0].'" width="'.$width.'" height="'.$height.'" style="z-index:4;padding:0;margin:0;" id="'.$divName.'_Main" name="'.$divName.'_Main">';

		if($this->copyprotection)
			$htmlresult.='<div style="position: absolute;top: 0;left:0;width:'.$width.'px;height:'.$height.'px;background-image: url(plugins/content/filmgalleryfiles/glass.png);background-repeat: repeat;"></div>';

		$htmlresult.='</div>
		</td>
		</tr>

		</table>

';

    return $htmlresult;

	}

	function drawGalleryTop(&$imagefiles,$width,$height,$divName)
	{
		$count=0;

		if($this->thumbheight==0)
			$this->thumbheight=90;


		if($this->thumbbgimage=='')
			$this->thumbbgimage=$this->bgimagefolder.'film_h.gif';

		if($this->thumbbgimage=='none')
			$this->thumbbgimage='';

		$htmlresult='

        <!-- Film Gallery (Top Scroll)-->
		';

		$htmlresult.=$this->HorizontalNavigation($imagefiles,$width,$divName);

		$htmlresult.='
		<div style="position: relative;overflow:hidden;">
        <img src="'.$imagefiles[0].'" width="'.$width.'" height="'.$height.'" style="z-index:4;padding:0;margin:0;" id="'.$divName.'_Main" name="'.$divName.'_Main">';
		if($this->copyprotection)
			$htmlresult.='<div style="position: absolute;top: 0;left:0;width:'.$width.'px;height:'.$height.'px;background-image: url(plugins/content/filmgalleryfiles/glass.png);background-repeat: repeat;"></div>';
		$htmlresult.='</div>';

    return $htmlresult;

	}


	function drawGalleryBottom(&$imagefiles,$width,$height,$divName)
	{
		$jpgcanverter='';
		$count=0;


		if($this->thumbheight==0)
			$this->thumbheight=90;


		if($this->thumbbgimage=='')
			$this->thumbbgimage=$this->bgimagefolder.'film_h.gif';

		if($this->thumbbgimage=='none')
			$this->thumbbgimage='';

		$topoffset=(int)(($this->scrollsize-$this->thumbheight)/2);

		$htmlresult='';
		$htmlresult.='

        <!-- Film Gallery (Bottom Scroll)-->

		<div style="width:'.$width.'px; position: relative;overflow:hidden;">';

        $htmlresult.='<img src="'.$imagefiles[0].'" width="'.$width.'" height="'.$height.'" style="z-index:4;padding:0;margin:0;" id="'.$divName.'_Main" name="'.$divName.'_Main">';


		if($this->copyprotection)
			$htmlresult.='<div style="position: absolute;top: 0;left:0;width:'.$width.'px;height:'.$height.'px;background-image: url(plugins/content/filmgalleryfiles/glass.png);background-repeat: repeat;"></div>';


		$htmlresult.=$this->HorizontalNavigation($imagefiles,$width,$divName);
		$htmlresult.='</div>';

    return $htmlresult;

	}



	function drawGalleryHorizontal(&$imagefiles,$width,$height,$divName,$rel='')
	{
		$count=0;


		if($this->thumbheight==0)
			$this->thumbheight=90;


		if($this->thumbbgimage=='')
			$this->thumbbgimage=$this->bgimagefolder.'film_h.gif';

		if($this->thumbbgimage=='none')
			$this->thumbbgimage='';

		$topoffset=(int)(($this->scrollsize-$this->thumbheight)/2);

		$htmlresult='
        <!-- Film Gallery (Horizontal Scroll with Shadowbox)-->
		';


		$htmlresult.=$this->HorizontalNavigation($imagefiles,$width,$divName,$rel);

    return $htmlresult;

	}



	function drawGalleryVertical(&$imagefiles,$width,$height,$divName,$rel='')
	{
		$jpgcanverter='';
		$count=0;
		if($this->thumbwidth==0)
			$this->thumbwidth=90;

		if($this->thumbbgimage=='')
			$this->thumbbgimage=$this->bgimagefolder.'film_v.gif';

		if($this->thumbbgimage=='none')
			$this->thumbbgimage='';

		$htmlresult='
        <!-- Film Gallery (Vertical Scroll with Shadowbox)-->

';
		$htmlresult.=$this->VerticalNavigation($imagefiles,$height,$divName,$rel,true);


    return $htmlresult;

	}



	function getFileList($dirpath, $filelist)
	{
		$sys_path=JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DS,$dirpath);

		$imList= array();
		if($filelist)
		{

			$a=explode(';',$filelist);
			foreach($a as $b)
			{
				$filename=$sys_path.DIRECTORY_SEPARATOR.trim($b);

				if(file_exists($filename))
					$imList[]='/'.$dirpath.'/'.trim($b);;
			}

		}
		else
		{
			if(file_exists($sys_path))
			{
				if ($handle = opendir($sys_path))
				{
					$extlist=array('jpg','gif','png','jpeg');

					while (false !== ($file = readdir($handle)))
					{

						$FileExt=$this->FileExtenssion($file);
						if(in_array($FileExt,$extlist))
							$imList[]='/'.$dirpath.'/'.$file;

					}
				}
				sort($imList);
			}
			else
			{
				JFactory::getApplication()->enqueueMessage('Path "'.$sys_path.'" not found', 'error');
			}

	    }
		return $imList;
	}



	function getListToReplace($par,&$options,&$text,$qtype)
	{
		$fList=array();
		$l=strlen($par)+2;

		$offset=0;
		do{
			if($offset>=strlen($text))
				break;

			$ps=strpos($text, $qtype[0].$par.'=', $offset);
			if($ps===false)
				break;


			if($ps+$l>=strlen($text))
				break;

		$pe=strpos($text, $qtype[1], $ps+$l);

		if($pe===false)
			break;

		$notestr=substr($text,$ps,$pe-$ps+1);

			$options[]=trim(substr($text,$ps+$l,$pe-$ps-$l));
			$fList[]=$notestr;


		$offset=$ps+$l;


		}while(!($pe===false));

		//for these with no parameters
		$ps=strpos($text, $qtype[0].$par.$qtype[1]);
		if(!($ps===false))
		{
			$options[]='';
			$fList[]=$qtype[0].$par.$qtype[1];
		}

		return $fList;
	}




	function strip_html_tags_textarea( $text )
	{
		$text = preg_replace(
		array(
		// Remove invisible content
		'@<textarea[^>]*?>.*?</textarea>@siu',
		),
		array(
		' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',"$0", "$0", "$0", "$0", "$0", "$0","$0", "$0",), $text );

		return $text ;
	}
}




?>