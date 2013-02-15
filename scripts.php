<?php
	
	class Scripts_Task {		
		
		public $source 		= "js/src/";
		public $destination	= "js/";
		
		public $output			= "app.js";
		
		public $files			= array(
			// Foo, Bar, Foobar
		);	
		
		private $sleep = 1500;
		private $uglifyjs = '/usr/bin/uglifyjs';		
		private $modes	= array(
			'debug'	=> '-b'
		);
		
		public function __construct(){
			
		}
		
		private function getOptions($arguments){
			$mode 		= current($arguments);			
			$options 	= '';
			if(!empty($mode))
				$options 	= $this->modes[$mode];
			return $options;
		}
		
		private function output(){
			$stdout = fopen("php://stdout", "w");
		    fputs($stdout, implode(' ', func_get_args()) . PHP_EOL);
		    fclose($stdout);
		}
		
		
		public function watch(){
			
			$size = 0;			
		    $currentSize = 0;		    
		    
		    $stamps = array();
		    
		    while (true) {
		        clearstatcache();
		        
		        $localsize = 0;
		        foreach($this->files as $file){
		        	switch(true){
						case is_file(path('public') . $this->source. $file . '.js'):
							$localsize += filesize(path('public') . $this->source. $file . '.js');
							break;
					}
		        }
		        
		        $currentSize = $localsize;		        
		        if ($size == $currentSize) {
		            usleep($this->sleep);
		            continue;
		        }
		        		        
		        $size = $currentSize;
		        		        
		        $stamp = date('H:i:s');

		        if(!in_array($stamp, $stamps)){
			         $this->compress(func_get_args());
			         $this->output('Compressing ' . $this->destination . $this->output . ' at ' . $stamp);
			         $stamps[] = $stamp;
		        }        		       	        
		        
		        usleep($this->sleep);
		    }
		}
		
		public function compress(){
			
			$options = $this->getOptions(current(func_get_args()));
			
			$files	= array();			
			foreach($this->files as $file){
				switch(true){
					case is_file(path('public') . $this->source. $file . '.js'):
						$files[] = path('public') . $this->source. $file . '.js';
						break;
				}
			}
			
			$cmd = $this->uglifyjs . ' ' . $options .' -o ' . path('public') . $this->destination . $this->output . ' ' . implode(' ', $files);

			echo exec($cmd); 		
		}
				
		public function run($arguments){

		}
		
	}