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

class Ping extends Plugin {

	function isTriggered() {
		
		switch($this->info['triggerUsed']) {
			case '!pong':
				$this->sendOutput('Ping?');
				break;
			case '!pang':
				$this->sendOutput('Peng!');
				break;
			case '!peng':
				$this->sendOutput('Pang!');
				break;
			case '!pung':
				$this->sendOutput('Pyng?');
				break;
			default:
				$this->sendOutput($this->CONFIG['pong_text']);
		}
		
	}

}

?>
