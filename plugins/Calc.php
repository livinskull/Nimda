<?php

	// a little calculator using google	
	
	class Calc extends Plugin {
	
	function isTriggered() {
		if(!isset($this->info['text'])) {
			$this->sendOutput("usage:".$this->CONFIG['usage']);
			return;
		}
		
		$google = libInternet::googleSearch($this->info['text']);
		if(!is_array($google)) {
			$this->sendOutput($this->CONFIG['connection_error']);
			return;
		}
		$result = preg_match_all("@<h2 class=r style=\"font-size:138%\"><b>(.*)</b></h2>@",$google['raw'],$matches);
		if($result == 0 || $result == false) {
			$this->sendOutput($this->CONFIG['parse_error']);
			return;
		}	
		
		$result = preg_replace("/<sup>/",'^',$matches[1][0]);
		
		$this->sendOutput(html_entity_decode(strip_tags($result)));
		
	return;
	}	
}


