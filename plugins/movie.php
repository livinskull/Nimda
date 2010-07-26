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

/**
 * moviePlugin
 * fetches information from ofdb.de using ofdbgw.org XML gateway
 * 
 * TODO: fix length bug.. on the console it send mroe text than arrives
 */

class movie extends Plugin {

	function isTriggered() {
		if(!isset($this->info['text'])) {
            $this->sendOutput(sprintf($this->CONFIG['help'],$this->info['triggerUsed']));
			return;
		}
		
		$term = $this->info['text'];
		
        if (isset($this->aPossibilities[strtolower($term)]) && !empty($this->aPossibilities[strtolower($term)])) {
            $aOut = $this->getMovie($this->aPossibilities[strtolower($term)]);
            for ($i=0; $i<count($aOut); $i++)
                $this->sendOutput($aOut[$i]);
        } else {
            $iRetries = 0;  // workaround for xml gateway bug not always returning sth
            do {
                $result = libHTTP::GET($this->CONFIG['host'], '/search/'.urlencode($term));
                $xml = simplexml_load_string($result['raw']);
           } while (!$xml && ++$iRetries < $this->CONFIG['max_retries']); 
           
            $number = isset($xml->resultat->eintrag)?(is_array($xml->resultat->eintrag)?count($xml->resultat->eintrag):1):0;
            
            if ($number) {
                $output = '';
                if ($number == 1) {
                    $aOut = $this->getMovie($xml->resultat->eintrag[0]->id);
                    for ($i=0; $i<count($aOut); $i++)
                        $this->sendOutput($aOut[$i]);
                    
                } else {
                    $this->sendOutput($this->CONFIG['several_text']);
                    
                    $this->aPossibilities = array();
                    for ($i=0; $i<$number; $i++) {
                        $output .= $xml->resultat->eintrag[$i]->titel . ' (' . $xml->resultat->eintrag[$i]->jahr .'); ';
                        $this->aPossibilities[strtolower($xml->resultat->eintrag[$i]->titel)] = $xml->resultat->eintrag[$i]->id;
                    }
                    
                    $output = mb_substr($output, 0, -2, 'UTF-8'); 
                    if (mb_strlen($output, 'UTF-8') > $this->CONFIG['max_length'])
                        $output = mb_substr($output,0,$this->CONFIG['max_length'], 'UTF-8').'...';
                }
                
                $this->sendOutput($output);
            } else {
                $this->sendOutput($this->CONFIG['notfound_text']);
            }
        }

        return;
	}
	
    /**
     * fetches information about a single movie
     * @param $id $id of movie
     * @return array $ret array containing messages to send back to user_error
     */
    function getMovie($id) {
        $ret = array();
        $iRetries = 0;
        do {
            $resMovie = libHTTP::GET($this->CONFIG['host'], '/movie/'.$id);
            $xmlMovie = simplexml_load_string($resMovie['raw']);
        } while (!$xmlMovie && ++$iRetries < $this->CONFIG['max_retries']);
        
        if ($xmlMovie && $xmlMovie->status->rcode == '0') {
            $output = $xmlMovie->resultat->titel.' ('.$xmlMovie->resultat->jahr.'), ';
            for ($i=0; $i<count($xmlMovie->resultat->genre); $i++)
                $output .= $xmlMovie->resultat->genre->titel[$i].($i<count($xmlMovie->resultat->genre)-1?', ':'; ');
                
            $output .= $xmlMovie->resultat->bewertung->note.'/10';
            $ret[] = $output;
            $output = $xmlMovie->resultat->beschreibung;
            if (mb_strlen($output, 'UTF-8') > $this->CONFIG['max_length'])
                $output = mb_substr($output, 0, $this->CONFIG['max_length'], 'UTF-8').'...';
            $ret[] = $output;
        } 
        
        return $ret;
    }

}

?>
