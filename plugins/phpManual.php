<?php

/*
	This file is part of Nimda - An advanced event-driven IRC Bot written in PHP with a nice plugin system
	Copyright (C) 2009  noother [noothy@gmail.com]

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
*/

class phpManual extends Plugin {
	
	function isTriggered() {
		if (!isset($this->info['text'])) {
            $output = sprintf($this->CONFIG['help'], $this->info['triggerUsed']);
			$this->sendOutput($output);
            return;
        }
		
		
        $res = libHTTP::GET($this->CONFIG['language'].'.php.net', '/'.$this->info['text']);
    
        if (preg_match('/<span class=\"refname\">(.*?)<\/span> &mdash; <span class=\"dc\-title\">(.*?)<\/span>/si', $res['raw'], $match)) {
            $match[2] = str_replace(array("\n", "\r"), ' ', $match[2]);
            preg_match('/<div class=\"methodsynopsis dc\-description\">(.*?)<\/div>/si', $res['raw'], $descmatch);
            $decl = isset($descmatch[1])?'; '.strip_tags($descmatch[1]):'';
            $decl = str_replace(array("\n", "\r"), ' ', $decl);
            $this->sendOutput($match[1].' - '.utf8_encode(html_entity_decode($match[2])).utf8_encode(html_entity_decode($decl)));
        } else {    // if several possibilities
            $output = '';
            // FIXME nothing in $res['raw'] ... server sends 302 found.. stupid libhttp :/
            
            var_dump($res);
            
            if (preg_match_all('/<a href=\"\/manual\/[a-z]+\/(?:.*?)\.php\">(?:<b>)?(.*?)(?:<\/b>)?<\/a><br/i', $res['raw'], $matches, PREG_SET_ORDER)) {
                $output = 'several possibilities: ';
                for ($i=0; $i<count($matches); ++$i)
                    $output .= $matches[$i][1].(($i < count($matches)-1)?', ':'');
                
            } else {
                $output = $this->CONFIG['notfound_text'].' http://'.$this->CONFIG['language'].'.php.net/search.php?show=wholesite&pattern='.$this->info['text'];
            }
            $this->sendOutput($output);
        }
                
	}

}
?>

