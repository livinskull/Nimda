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
		
		
		$this->fetchDescription($this->info['text']);                
	}

	function fetchDescription($func) {
		$res = libHTTP::GET($this->CONFIG['language'].'.php.net', '/'.$func, 'LAST_LANG='.$this->CONFIG['language']);
	
		if (preg_match('/<span class=\"refname\">(.*?)<\/span> &mdash; <span class=\"dc\-title\">(.*?)<\/span>/si', $res['raw'], $match)) {
			$match[2] = str_replace(array("\n", "\r"), ' ', strip_tags($match[2]));
			preg_match('/<div class=\"methodsynopsis dc\-description\">(.*?)<\/div>/si', $res['raw'], $descmatch);
			$decl = isset($descmatch[1])?strip_tags($descmatch[1]):$match[1];
			$decl = html_entity_decode(str_replace(array("\n", "\r"), ' ', $decl));
			$decl = str_replace($func, "\x02".$func."\x02", $decl);
			$output =  $decl.' - '.html_entity_decode($match[2]).' ( http://'.$this->CONFIG['language'].'.php.net/'.$func.' )';
			$this->sendOutput(libString::isUTF8($output)?$output:utf8_encode($output));
		} else {    // if several possibilities
			$output = '';

			while (isset($res['header']['Location'])) {
				$urlparts = parse_url($res['header']['Location']);
				$res =  libHTTP::GET($urlparts['host'], $urlparts['path'].'?'.$urlparts['query'], 'LAST_LANG='.$this->CONFIG['language']);
			}
		
			if (preg_match_all('/<a href=\"\/manual\/[a-z]+\/(?:.*?)\.php\">(?:<b>)?(.*?)(?:<\/b>)?<\/a><br/i', $res['raw'], $matches, PREG_SET_ORDER))
				$this->fetchDescription($matches[0][1]);
			else
				$output = $this->CONFIG['notfound_text'].' http://'.$this->CONFIG['language'].'.php.net/search.php?show=wholesite&pattern='.$func;

			$this->sendOutput(libString::isUTF8($output)?$output:utf8_encode($output));
		}
	}

}
?>

