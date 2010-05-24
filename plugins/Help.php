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

class Help extends Plugin {

	function isTriggered() {
		if(!isset($this->info['text']))
			$this->displayOverview();
		else 
			$this->displayHelp();
	}
	
	function displayOverview() {
		$this->sendOutput("Available commands:",$this->info['nick']);
		$this->sendOutput("Type a '!' in front of each command.",$this->info['nick']);
		
		$aHelp = array(); // aHelp[$category] => array()
		foreach ($this->IRCBot->plugins as $oPlg) {
            $cat = isset($oPlg->CONFIG['help_category'])?$oPlg->CONFIG['help_category']:'misc';
            if (!isset($aHelp[$cat]))
                $aHelp[$cat] = array();
        
			if (isset($oPlg->CONFIG['help_triggers'])) {
				preg_match_all("/'(.*?[^\\\\])'/",$oPlg->CONFIG['help_triggers'],$arr);
				$htriggers = libArray::stripslashes($arr[1]);
                foreach ($htriggers as $t)
                    $aHelp[$cat][] = substr($t, 1);
			} else {
                foreach ($oPlg->triggers as $t)
                    $aHelp[$cat][] = substr($t, 1);
			}	
		}
			
        foreach ($aHelp as $cat => $arr) {
            $output = $cat.': ';
            sort($arr);
            $output .= implode(', ', $arr);
            $this->sendOutput($output, $this->info['nick']);
        }
		
		$this->sendOutput("Type !help <command> to get a function explained.",$this->info['nick']);
		$this->sendOutput("If you want Nimda in your channel, contact noother. (It's free!)",$this->info['nick']);
	}

	function displayHelp() {
        if ($this->info['text'][0] != '!')
            $this->info['text'] = '!'.$this->info['text'];
        
        if ($plg = $this->findPlugin($this->info['text'])) {
			$sHelp = 'Plugin ' . $plg->name;
			$sHelp .= ' v' . $plg->version;
			if ($this->CONFIG['show_author'] != '0')
                $sHelp .= ' by ' . $plg->author;
			$this->sendOutput($sHelp);
			$this->sendOutput($plg->description);
			
            $trg = substr($this->info['text'], 1);
            
            if (isset($plg->CONFIG['help_tr_'.$trg])) {
                $this->sendOutput($plg->CONFIG['help_tr_'.$trg]);
            } elseif (isset($plg->CONFIG['help'])) {
                $this->sendOutput(sprintf($plg->CONFIG['help'], $this->info['text']));
            }
		} else {
			$this->sendOutput($this->CONFIG['not_available_text']);
		}
	}

    function findPlugin($trigger) {
        foreach ($this->IRCBot->plugins as $oPlg) {
            if (in_array($trigger, $oPlg->triggers))
                return $oPlg;
        }
        return NULL;
    }
    
}

?>
