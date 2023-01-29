<?php
/**
 * Film Gallery Joomla! 3.x Native Component
 * @version 1.1.2
 * @author Ivan Komlev <support@joomlaboat.com>
 * Copyright (C) 2009-2018 Ivan Komlev
 * @link http://www.joomlaboat.com
 * @license GNU/GPL *
 */


defined('_JEXEC') or die('Restricted access');

class FilmGalleryClass
{
    var bool $copyProtection;
    var $bgimagefolder;
    var int $scrollSize;
    var $thumbwidth;
    var $thumbheight;
    var $padding;
    private string $thumbBackgroundImage;

    function getFilmGallery($galleryParams, $count): string
    {
        $opt = str_getcsv($galleryParams,",","\"");

        if (count($opt) < 1)
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

        $folder = $opt[0];
        $width = 400;
        if (count($opt) > 1) $width = $opt[1];
        $height = 300;
        if (count($opt) > 2) $height = (int)$opt[2];
        $scrollPosition = '';
        if (count($opt) > 3) $scrollPosition = $opt[3];
        $fileList = '';
        if (count($opt) > 4) $fileList = $opt[4];

        $this->thumbBackgroundImage = "";
        if (count($opt) > 5) $this->thumbBackgroundImage = $opt[5];

        $this->scrollSize = 130;
        if (count($opt) > 6) $this->scrollSize = intval($opt[6]);
        if ($this->scrollSize == 0)
            $this->scrollSize = 135;

        $this->thumbwidth = 0;
        if (count($opt) > 7) (int)$this->thumbwidth = $opt[7];
        $this->thumbheight = 0;
        if (count($opt) > 8) (int)$this->thumbheight = $opt[8];

        $this->padding = '5';
        if (count($opt) > 9) (int)$this->padding = $opt[9];

        $imageFiles = $this->getFileList($folder, $fileList);

        $result = '';
        if (count($imageFiles) == 0)
            return $result;

        $divName = 'filmegalleryplg_' . $count;

        switch ($scrollPosition) {
            case 'left' :
                $result = $this->drawGalleryLeft($imageFiles, $width, $height, $divName);
                break;
            case 'right' :
                $result = $this->drawGalleryRight($imageFiles, $width, $height, $divName);
                break;
            case 'top' :
                $result = $this->drawGalleryTop($imageFiles, $width, $height, $divName);
                break;
            case 'bottom' :
                $result = $this->drawGalleryBottom($imageFiles, $width, $height, $divName);
                break;
            default:
                $pair = explode(':', $scrollPosition);

                $rel = $pair[1] ?? 'shadowbox';

                if ($pair[0] == 'vertical')
                    $result = $this->drawGalleryVertical($imageFiles, $width, $height, $divName, $rel);
                elseif ($pair[0] == 'horizontal')
                    $result = $this->drawGalleryHorizontal($imageFiles, $width, $height, $divName, $rel);
                else
                    $result = $this->drawGalleryRight($imageFiles, $width, $height, $divName);
        }
        return $result . '
		<!-- end of film gallery -->';
    }

    //Image Gallery
    function getFileList(string $dirPath, string $fileList): array
    {
        if ($dirPath[0] == '/') {
            $dirPath = substr($dirPath, 1, strlen($dirPath) - 1);
        }

        $sys_path = JPATH_SITE . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $dirPath);

        $imList = array();
        if ($fileList !="") {
            $a = explode(';', $fileList);

            foreach ($a as $b) {
                $filename = $sys_path . DIRECTORY_SEPARATOR . trim($b);

                if (file_exists($filename))
                    $imList[] = '/' . $dirPath . '/' . trim($b);;
            }
        } else {
            if (file_exists($sys_path)) {
                if ($handle = opendir($sys_path)) {
                    $extensionList = array('jpg', 'gif', 'png', 'jpeg');

                    while (false !== ($file = readdir($handle))) {

                        $FileExt = $this->FileExtenssion($file);
                        if (in_array($FileExt, $extensionList)) {

                            if ($dirPath[0] == '/')
                                $imList[] = $dirPath . '/' . $file;
                            else
                                $imList[] = '/' . $dirPath . '/' . $file;
                        }
                    }
                }
                sort($imList);
            } else {
                JFactory::getApplication()->enqueueMessage('Path "' . $sys_path . '" not found', 'error');
            }
        }
        return $imList;
    }

    function FileExtenssion($src): string
    {
        $fileExtension = '';
        $name = explode(".", strtolower($src));
        $currentExtensions = $name[count($name) - 1];
        $allowedExtensions = 'jpg jpeg gif png';
        $extensions = explode(" ", $allowedExtensions);
        for ($i = 0; count($extensions) > $i; $i = $i + 1) {
            if ($extensions[$i] == $currentExtensions) {
                $fileExtension = $extensions[$i];

                return $fileExtension;
            }
        }
        return $fileExtension;
    }

    function drawGalleryLeft(&$imageFiles, $width, $height, $divName): string
    {
        if ($this->thumbwidth == 0)
            $this->thumbwidth = 90;

        if ($this->thumbBackgroundImage == '')
            $this->thumbBackgroundImage = $this->bgimagefolder . 'film_v.gif';

        if ($this->thumbBackgroundImage == 'none')
            $this->thumbBackgroundImage = '';

        $htmlresult = '
        <!-- Film Gallery (Left Scroll)-->
		<table width="' . ($width + $this->scrollSize) . '" height="' . $height . '" border="0" align="center" cellpadding="0" cellspacing="0" style="border:none;padding:0;margin:0;">
		<tr>
		<td valign="top" style="margin:0;padding:0;border:none;" align="center">';

        $htmlresult .= $this->VerticalNavigation($imageFiles, $height, $divName);
        $htmlresult .= '
		</td>
		<td align="center" width="' . $width . '" style="margin:0;padding:0;border:none;">
		<div style="width:' . $width . 'px; height:' . $height . 'px;position: relative;overflow:hidden;">
        <img src="' . $imageFiles[0] . '" width="' . $width . '" height="' . $height . '" style="z-index:4;padding:0;margin:0;" id="' . $divName . '_Main" name="' . $divName . '_Main">';

        if ($this->copyProtection)
            $htmlresult .= '<div style="position: absolute;top: 0;left:0;width:' . $width . 'px;height:' . $height . 'px;background-image: url(plugins/content/filmgalleryfiles/glass.png);background-repeat: repeat;"></div>';

        $htmlresult .= '</div>
		</td>
		</tr>
		</table>
';
        return $htmlresult;
    }

    function VerticalNavigation(&$imageFiles, $height, $divName, $rel = '', $add_15px = false): string
    {
        $htmlresult = '<div style="';

        if ($add_15px)
            $htmlresult .= 'width: ' . ($this->scrollSize + 15) . 'px;';

        $htmlresult .= 'height:' . $height . 'px;
			overflow: scroll -moz-scrollbars-vertical;
			overflow-x: hidden;
			overflow-y: auto;
			padding: 0;
			margin: 0 auto 0 0;
			position:relative;
			">

			<table border="0" width="' . $this->scrollSize . '" cellpadding="0" cellspacing="0"
			style="border:none;padding:0;margin:0;';

        if ($this->thumbBackgroundImage != '') {
            $htmlresult .= 'background-image: url(' . $this->thumbBackgroundImage . ');	background-repeat: repeat-y; background-position:center center;';
        }
        $htmlresult .= '">
';

        //List of Images

        foreach ($imageFiles as $imagefile) {
            $htmlresult .= '
            <tr>
			<td width="' . $this->scrollSize . '" valign="middle" align="center" style="position:relative;border:none;margin:0;padding:0;" >
';

            if ($this->copyProtection and $rel == '') {
                $htmlresult .= '
				<div style="margin-bottom:' . $this->padding . 'px;width:' . $this->thumbwidth . 'px;margin-left:auto;margin-right:auto;position:relative;cursor:pointer;" onMouseOver=\'document.getElementById("' . $divName . '_Main").src="' . $imagefile . '";\'	onMouseOver=\'document.getElementById("' . $divName . '_Main").src="' . $imagefile . '";\'>
				<img src="' . $imagefile . '" width="' . $this->thumbwidth . '" style="padding:0;width:' . $this->thumbwidth . 'px;margin:0;border:none;">';
                $htmlresult .= '<div style="position: absolute;top: 0;left:0;right:0;bottom:0;background-image: url(' . $this->bgimagefolder . 'glass.png);background-repeat: repeat;"></div>';
                $htmlresult .= '</div>
				';
            } elseif (!$this->copyProtection and $rel == '') {
                $htmlresult .= '
					<div style="margin-bottom:' . $this->padding . 'px;width:' . $this->thumbwidth . 'px;margin-left:auto;margin-right:auto;">
					<img src="' . $imagefile . '" width="' . $this->thumbwidth . '" style="border:none;padding:0;width:' . $this->thumbwidth . 'px;margin:0;"
					onMouseOver=\'document.getElementById("' . $divName . '_Main").src="' . $imagefile . '";\'>
					</div>';
            } elseif (!$this->copyProtection and $rel != '') {
                $alt = '';
                $htmlresult .= '<div style="margin-bottom:' . $this->padding . 'px;width:' . $this->thumbwidth . 'px;margin-left:auto;margin-right:auto;">';

                if ($rel == 'jcepopup')
                    $htmlresult .= '<a href="' . $imagefile . '" class="jcepopup" rel="title[' . $alt . '];caption[' . $alt . '];group[filmgallery];">';
                else
                    $htmlresult .= '<a href="' . $imagefile . '" rel="' . $rel . '">';

                $htmlresult .= '<img src="' . $imagefile . '" width="' . $this->thumbwidth . '" style="border:none;padding:0;width:' . $this->thumbwidth . 'px;margin:0;" /></a>
					</div>';
            } elseif ($this->copyProtection and $rel != '') {
                $alt = '';

                if ($rel == 'jcepopup')
                    $htmlresult .= '<a href="' . $imagefile . '" class="jcepopup" rel="title[' . $alt . '];caption[' . $alt . '];group[filmgallery];">';
                else
                    $htmlresult .= '<a href="' . $imagefile . '" rel="' . $rel . '">';

                $htmlresult .= '

				<div style="margin-bottom:' . $this->padding . 'px;width:' . $this->thumbwidth . 'px;margin-left:auto;margin-right:auto;position:relative;cursor:pointer;" >
				<img src="' . $imagefile . '" width="' . $this->thumbwidth . '" style="border:none;padding:0;width:' . $this->thumbwidth . 'px;margin:0;" />';
                $htmlresult .= '<div style="position: absolute;top: 0;left:0;right:0;bottom:0;background-image: url(' . $this->bgimagefolder . 'glass.png);background-repeat: repeat;"></div>';
                $htmlresult .= '</div></a>
';
            }

            $htmlresult .= '
			</td>
            </tr>
';
        }
        $htmlresult .= '</table></div>';
        return $htmlresult;
    }

    function drawGalleryRight(&$imageFiles, $width, $height, $divName): string
    {
        if ($this->thumbwidth == 0)
            $this->thumbwidth = 90;

        if ($this->thumbBackgroundImage == '')
            $this->thumbBackgroundImage = $this->bgimagefolder . 'film_v.gif';

        if ($this->thumbBackgroundImage == 'none')
            $this->thumbBackgroundImage = '';

        $htmlresult = '
        <!-- Film Gallery (Right Scroll)-->

		<table width="' . ($width + $this->scrollSize + 15) . '" height="' . $height . '" border="0" align="center" cellpadding="0" cellspacing="0" style="border:none;padding:0;margin:0;">
		<tr>
		<td align="center" width="' . $width . '" style="margin:0;padding:0;border:none;">
		<div style="width:' . $width . 'px; height:' . $height . 'px;position: relative;overflow:hidden;">
        <img src="' . $imageFiles[0] . '" width="' . $width . '" height="' . $height . '" style="z-index:4;padding:0;margin:0;" id="' . $divName . '_Main" name="' . $divName . '_Main">';

        if ($this->copyProtection)
            $htmlresult .= '<div style="position: absolute;top: 0;left:0;width:' . $width . 'px;height:' . $height . 'px;background-image: url(plugins/content/filmgalleryfiles/glass.png);background-repeat: repeat;"></div>';

        $htmlresult .= '</div>
		</td>
		<td valign="top" style="padding-top:0px;margin:0;padding:0;text-align:left;border:none;" align="center">';
        $htmlresult .= $this->VerticalNavigation($imageFiles, $height, $divName, '', true);
        $htmlresult .= '</td>
		</tr>
		</table>
';
        return $htmlresult;
    }

    function drawGalleryTop(&$imagefiles, $width, $height, $divName): string
    {
        if ($this->thumbheight == 0)
            $this->thumbheight = 90;

        if ($this->thumbBackgroundImage == '')
            $this->thumbBackgroundImage = $this->bgimagefolder . 'film_h.gif';

        if ($this->thumbBackgroundImage == 'none')
            $this->thumbBackgroundImage = '';

        $htmlresult = '
        <!-- Film Gallery (Top Scroll)-->
		';
        $htmlresult .= $this->HorizontalNavigation($imagefiles, $width, $divName);
        $htmlresult .= '
		<div style="position: relative;overflow:hidden;">
        <img src="' . $imagefiles[0] . '" width="' . $width . '" height="' . $height . '" style="z-index:4;padding:0;margin:0;" id="' . $divName . '_Main" name="' . $divName . '_Main">';
        if ($this->copyProtection)
            $htmlresult .= '<div style="position: absolute;top: 0;left:0;width:' . $width . 'px;height:' . $height . 'px;background-image: url(plugins/content/filmgalleryfiles/glass.png);background-repeat: repeat;"></div>';
        $htmlresult .= '</div>';
        return $htmlresult;
    }

    function HorizontalNavigation(&$imagefiles, $width, $divName, $rel = '')
    {
        $htmlresult = '
		<div style="
			width:' . $width . 'px;
			overflow: -moz-scrollbars-horizontal;
			overflow-x: auto;
			overflow-y: hidden;
			padding: 0;
			margin: 0;
			position:relative;
			">
			<table border="0" height="' . $this->scrollSize . '" cellpadding="0" cellspacing="0"
			style="border-style:none;padding:0;margin:0;';

        if ($this->thumbBackgroundImage != '') {
            $htmlresult .= 'background-image: url(' . $this->thumbBackgroundImage . ');background-repeat: repeat-x; background-position:center center; ';
        }

        $htmlresult .= '"><tbody>

			<tr height="' . $this->scrollSize . '" >';

        //List of Images
        foreach ($imagefiles as $imageFile) {
            $marginTop = (int)(($this->scrollSize - $this->thumbheight) / 2);

            $htmlresult .= '<td height="' . $this->scrollSize . '" width="110" align="center" valign="top" style="width:110px !important;position:relative;border:none;margin:0;padding:0;">';

            if ($this->copyProtection and $rel == '') {
                $htmlresult .= '
				<div style="margin-right:' . $this->padding . 'px;height:' . $this->thumbheight . 'px;
				width:110px !important;
				margin-top:' . $marginTop . 'px;
				position:relative;
				cursor:pointer;"
				onMouseOver=\'document.getElementById("' . $divName . '_Main").src="' . $imageFile . '";\'	onMouseOver=\'document.getElementById("' . $divName . '_Main").src="' . $imageFile . '";\'>
				<img src="' . $imageFile . '" height="' . $this->thumbheight . '" style="padding:0;height:' . $this->thumbheight . 'px;margin:0;border:none;">';
                $htmlresult .= '<div style="position: absolute;width:110px !important;top: 0;left:0;right:0;bottom:0;background-image: url(' . $this->bgimagefolder . 'glass.png);background-repeat: repeat;"></div>';
                $htmlresult .= '</div>
				';

            } elseif (!$this->copyProtection and $rel == '') {
                $htmlresult .= '<div style="margin-right:' . $this->padding . 'px;height:' . $this->thumbheight . 'px;margin-top:' . $marginTop . 'px;">
					<img src="' . $imageFile . '" ';

                if ($this->thumbwidth != 0)
                    $htmlresult .= ' width="' . $this->thumbwidth . '"';

                $htmlresult .= ' height="' . $this->thumbheight . '" style="border:none;margin:0;padding:0;max-width:none !important;width:110px !important;';

                if ($this->thumbwidth != 0)
                    $htmlresult .= 'width:' . $this->thumbwidth . 'px;';

                $htmlresult .= 'height:' . $this->thumbheight . 'px;" onMouseOver=\'document.getElementById("' . $divName . '_Main").src="' . $imageFile . '";\'>
					</div>';
            } elseif (!$this->copyProtection and $rel != '') {
                $alt = '';
                $htmlresult .= '<div style="width:110px !important;margin-right:' . $this->padding . 'px;height:' . $this->thumbheight . 'px;margin-top:' . $marginTop . 'px;">';

                if ($rel == 'jcepopup')
                    $htmlresult .= '<a href="' . $imageFile . '" class="jcepopup" rel="title[' . $alt . '];caption[' . $alt . '];group[filmgallery];">';
                else
                    $htmlresult .= '<a href="' . $imageFile . '" rel="' . $rel . '">';

                $htmlresult .= '<img src="' . $imageFile . '" height="' . $this->thumbheight . '" style="
					width:110px !important;border:none;margin:0;padding:0;height:' . $this->thumbheight . 'px;" alt="' . $alt . '" /></a>
					</div>';
            } elseif ($this->copyProtection and $rel != '') {
                $alt = '';

                if ($rel == 'jcepopup')
                    $htmlresult .= '<a href="' . $imageFile . '" class="jcepopup" rel="title[' . $alt . '];caption[' . $alt . '];group[filmgallery];">';
                else
                    $htmlresult .= '<a href="' . $imageFile . '" rel="' . $rel . '">';

                $htmlresult .= '

				<div style="margin-right:' . $this->padding . 'px;height:' . $this->thumbheight . 'px;
				margin-top:' . $marginTop . 'px;
				position:relative;
				cursor:pointer;">
				<img src="' . $imageFile . '" height="' . $this->thumbheight . '" style="padding:0;height:' . $this->thumbheight . 'px;margin:0;border:none;">';
                $htmlresult .= '<div style="position: absolute;top: 0;left:0;right:0;bottom:0;background-image: url(' . $this->bgimagefolder . 'glass.png);background-repeat: repeat;"></div>';
                $htmlresult .= '</div></a>
				';
            }
            $htmlresult .= '</td>';
        }
        $htmlresult .= '
			</tr></tbody></table>
		</div>';
        return $htmlresult;
    }

    function drawGalleryBottom(&$imageFiles, $width, $height, $divName)
    {
        if ($this->thumbheight == 0)
            $this->thumbheight = 90;

        if ($this->thumbBackgroundImage == '')
            $this->thumbBackgroundImage = $this->bgimagefolder . 'film_h.gif';

        if ($this->thumbBackgroundImage == 'none')
            $this->thumbBackgroundImage = '';

        $htmlresult = '
        <!-- Film Gallery (Bottom Scroll)-->
		<div style="width:' . $width . 'px; position: relative;overflow:hidden;">';

        $htmlresult .= '<img src="' . $imageFiles[0] . '" width="' . $width . '" height="' . $height . '" style="z-index:4;padding:0;margin:0;" id="' . $divName . '_Main" name="' . $divName . '_Main">';

        if ($this->copyProtection)
            $htmlresult .= '<div style="position: absolute;top: 0;left:0;width:' . $width . 'px;height:' . $height . 'px;background-image: url(plugins/content/filmgalleryfiles/glass.png);background-repeat: repeat;"></div>';

        $htmlresult .= $this->HorizontalNavigation($imageFiles, $width, $divName);
        $htmlresult .= '</div>';
        return $htmlresult;
    }

    function drawGalleryVertical(&$imagefiles, $width, $height, $divName, $rel = '')
    {
        if ($this->thumbwidth == 0)
            $this->thumbwidth = 90;

        if ($this->thumbBackgroundImage == '')
            $this->thumbBackgroundImage = $this->bgimagefolder . 'film_v.gif';

        if ($this->thumbBackgroundImage == 'none')
            $this->thumbBackgroundImage = '';

        $htmlresult = '
        <!-- Film Gallery (Vertical Scroll with Shadowbox)-->
';
        $htmlresult .= $this->VerticalNavigation($imagefiles, $height, $divName, $rel, true);
        return $htmlresult;
    }

    function drawGalleryHorizontal(&$imageFiles, $width, $height, $divName, $rel = '')
    {
        if ($this->thumbheight == 0)
            $this->thumbheight = 90;

        if ($this->thumbBackgroundImage == '')
            $this->thumbBackgroundImage = $this->bgimagefolder . 'film_h.gif';

        if ($this->thumbBackgroundImage == 'none')
            $this->thumbBackgroundImage = '';

        $htmlresult = '
        <!-- Film Gallery (Horizontal Scroll with Shadowbox)-->
		';

        $htmlresult .= $this->HorizontalNavigation($imageFiles, $width, $divName, $rel);
        return $htmlresult;
    }

    function getListToReplace($par, &$options, &$text, $qtype)
    {
        $fList = array();
        $l = strlen($par) + 2;

        $offset = 0;
        do {
            if ($offset >= strlen($text))
                break;

            $ps = strpos($text, $qtype[0] . $par . '=', $offset);
            if ($ps === false)
                break;

            if ($ps + $l >= strlen($text))
                break;

            $pe = strpos($text, $qtype[1], $ps + $l);

            if ($pe === false)
                break;

            $noteString = substr($text, $ps, $pe - $ps + 1);

            $options[] = trim(substr($text, $ps + $l, $pe - $ps - $l));
            $fList[] = $noteString;
            $offset = $ps + $l;
        } while (!($pe === false));

        //for these with no parameters
        $ps = strpos($text, $qtype[0] . $par . $qtype[1]);
        if (!($ps === false)) {
            $options[] = '';
            $fList[] = $qtype[0] . $par . $qtype[1];
        }
        return $fList;
    }

    function strip_html_tags_textarea($text)
    {
        return preg_replace(
            array(
                // Remove invisible content
                '@<textarea[^>]*?>.*?</textarea>@siu',
            ),
            array(
                ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', "$0", "$0", "$0", "$0", "$0", "$0", "$0", "$0",), $text);
    }
}
