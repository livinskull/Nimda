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

define('MOVIE_HOST', 'www.moviemaze.de');

class movie extends Plugin {

	function isTriggered() {
		if(!isset($this->info['text'])) {
			//$this->sendOutput("Usage: ".$this->info['triggerUsed']." <movie>");
            $this->sendOutput(sprintf($this->CONFIG['help'],$this->info['triggerUsed']));
			return;
		}
		
		$term = $this->info['text'];
		
        $result = libHTTP::POST(MOVIE_HOST, '/suche/result.phtml', 'searchword='.$term);
        //print_r($result);
        if (preg_match_all('/<a href=\"\/filme\/(.*)?\.html\">\s*<b style=\"(.*)?\">(.*)?<\/b>\s*<\/a>/i', $result['raw'], $movies, PREG_SET_ORDER)) {
            print_r($movies);
            $output = '';
            if (count($movies) > 1 && strtolower($movies[0][3]) != strtolower($term)) {
                // several results
                $this->sendOutput($this->CONFIG['several_text']);
                foreach ($movies as $movie)
                    $output .= $movie[3].'; ';
                    
                if (count($movies))
                    $output = substr($output, 0, strlen($output)-2);
                
            } else {
                // one result
                $link = '/filme/'.$movies[0][1].'.html';
                
                $result = libHTTP::GET(MOVIE_HOST, $link);
                
                //name & year
                if (preg_match('/<span class=\"fn\"><h1>.*?<\/h1><\/span><\/span><h2>\((.*?)\)/i', $result['raw'], $arr))
                    $output .= html_entity_decode($arr[1]).': ';
                   
                // genre
                //if (preg_match('/<span class=\"fett\">Genre:<\/span><\/td>\s*<td class="\standard\" valign=\"top\">\s*(.*?)\s*<\/td>/i', $result['raw'], $arr))
                //    $output .= '( '.html_entity_decode($arr[1]).' ) : ';
                //print_r($arr);
                    
                // description
                if (preg_match('/<div id=\"plot\">\s*<!--.*-->(.*?)<\/div>/i', $result['raw'], $arr))
                    $output .= html_entity_decode($arr[1]);
                
                $link = '( http://'.MOVIE_HOST.$link.' )';
                $output = substr($output,0,$this->CONFIG['max_length']-(strlen($link)+8)).'... '.$link;
                
            }
            
            $this->sendOutput($output);
        } else {
            $this->sendOutput($this->CONFIG['notfound_text']);
        }

        return;
	}
	

}

?>
