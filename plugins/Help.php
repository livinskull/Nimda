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
		
		$sAvailCommands = '';
		foreach ($this->IRCBot->plugins as $oPlg) {
			if (isset($oPlg->CONFIG['help_triggers'])) {
				preg_match_all("/'(.*?[^\\\\])'/",$oPlg->CONFIG['help_triggers'],$arr);
				$htriggers = libArray::stripslashes($arr[1]);
				$sAvailCommands .= implode(', ', $htriggers) . ', ';
			} else {
				$sAvailCommands .= implode(', ', $oPlg->triggers) . ', ';
			}
			if (strlen($sAvailCommands) > 400) {
				$this->sendOutput($sAvailCommands, $this->info['nick']);
				$sAvailCommands = '';
			}
		}
			
		$sAvailCommands = substr($sAvailCommands, 0, strlen($sAvailCommands)-2);
		$this->sendOutput($sAvailCommands, $this->info['nick']);
		
		$this->sendOutput("Type !help <command> to get a function explained.",$this->info['nick']);
		$this->sendOutput("If you want Nimda in your channel, contact noother. (It's free!)",$this->info['nick']);
	}

	function displayHelp() {
		// TODO: manage capitalizm =P
		if (isset($this->IRCBot->plugins[$this->info['text']])) {
			$sHelp = 'Plugin ' . $this->IRCBot->plugins[$this->info['text']]->name;
			$sHelp .= ' v' . $this->IRCBot->plugins[$this->info['text']]->version;
			$sHelp .= ' by ' . $this->IRCBot->plugins[$this->info['text']]->author;
			$this->sendOutput($sHelp);
			$this->sendOutput($this->IRCBot->plugins[$this->info['text']]->description);
			
			if (isset($this->IRCBot->plugins[$this->info['text']]->CONFIG['help_usage']))
				$this->sendOutput('Usage: '.sprintf($this->IRCBot->plugins[$this->info['text']]->CONFIG['help_usage'], $this->info['text']));
		} else {
			$this->sendOutput($this->CONFIG['not_available_text']);
		}
	}

}

?>
