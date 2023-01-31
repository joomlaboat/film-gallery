<?php
/**
 * Film Gallery Joomla! 3.x/4.x Native Component
 * @version 1.1.3
 * @author Ivan Komlev <support@joomlaboat.com>
 * Copyright (C) 2009-2023 Ivan Komlev
 * @link http://www.joomlaboat.com
 * @license GNU/GPL *
 */

defined('_JEXEC') or die('Restricted access');

require_once('render.php');

class plgContentFilmGallery extends JPlugin
{
    public function onContentPrepare($context, &$row, &$params, $page = 0)
    {
        if (is_object($row)) {
            return $this->plgFilmGallery($row->text);
        }
        return $this->plgFilmGallery($row);
    }

    function plgFilmGallery(&$text_original): int
    {
        $fgc = new FilmGalleryClass;

        if ($this->params->def('avoidtextarea'))
            $text = $fgc->strip_html_tags_textarea($text_original);
        else
            $text = $text_original;

        $options = array();
        $fgc->copyProtection = $this->params->def('copyprotection');
        $fgc->backgroundImageFolder = 'plugins/content/filmgallery/files/';
        $fList = $fgc->getListToReplace('filmgallery', $options, $text, '{}','=');

        for ($i = 0; $i < count($fList); $i++) {
            $replaceWith = $fgc->getFilmGallery($options[$i], $i);
            $text_original = str_replace($fList[$i], $replaceWith, $text_original);
        }
        return count($fList);
    }
}

