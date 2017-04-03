<?php
/**
 *  Dolumar engine, php/html MMORTS engine
 *  Copyright (C) 2009 Thijs Van der Schaeghe
 *  CatLab Interactive bvba, Gent, Belgium
 *  http://www.catlab.eu/
 *  http://www.dolumar.com/
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License along
 *  with this program; if not, write to the Free Software Foundation, Inc.,
 *  51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

class Neuron_Core_RSSParser
{
    private $sUrl;
    private $oCache;
    private $iCacheLifespan;

    public function __construct ($sUrl)
    {
        $this->sUrl = $sUrl;
    }

    public function setCache ($cache, $iCacheLifespan = 3600)
    {
        $this->oCache = $cache;
        $this->iCacheLifespan = $iCacheLifespan;
    }

    public function getItems ($amount)
    {
        $name = md5 ($this->sUrl . '|' . $amount);

        if (isset ($this->oCache))
        {
            if ($out = $this->oCache->getCache ($name, $this->iCacheLifespan))
            {
                return unserialize ($out);
            }

            $data = $this->getFreshItems ($amount);
            $this->oCache->setCache ($name, serialize ($data));

            return $data;
        }

        return $this->getFreshItems ($amount);
    }

    private function getFreshItems ($amount)
    {
        if (empty($this->sUrl)) {
            return [];
        }

        $out = array ();

        $dom = new DOMDocument();
        $dom = $dom->load($this->sUrl);

        if ($dom)
        {
            $rss = $dom->getElementsByTagName ('rss');

            if ($rss->length > 0)
            {
                $channel = $rss->item (0)->getElementsByTagName ('channel');
                if ($channel->length > 0)
                {
                    $items = $channel->item (0)->getElementsByTagName ('item');

                    for ($i = 0; $i < $items->length && $i < $amount; $i ++)
                    {
                        $item = $items->item ($i);

                        $out[] = array
                        (
                            'title' => $this->fetchValue ($item, 'title'),
                            'url' => $this->fetchValue ($item, 'link'),
                            'description' => $this->fetchValue ($item, 'description'),
                            'date' => strtotime ($this->fetchValue ($item, 'pubDate'))
                        );
                    }
                }
            }
        }

        return $out;
    }

    private function fetchValue ($item, $name)
    {
        $element = $item->getElementsByTagName ($name);
        if ($element->length > 0)
        {
            return $element->item (0)->nodeValue;
        }
        return null;
    }
}