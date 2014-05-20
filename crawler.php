<?php
	
	/********************************************
		
		The CrawlBase class is the base class 
		that will be extended for specific 
		crawlers.
			
	*********************************************/
			
	class CrawlBase {
		private $timeout = 10000;
		private $configName = "";
		private $seedUrl = new Array();			// Data sctructre for now. Consider switching to array lists.
		private $crawlXpath = new Array();		// Data sctructre for now. Consider switching to array lists.
		private $dataXpath = "";
		private $currentCrawlUrl = "";
		
		// This function will be overwritten by the user to specify what data 
		// they want to collect when the crawl is run.
		public function collectData($dataPage, $url) {
			
		}
		
		// Applies the crawlXpaths on each of the urls in order to get to the data page.
		// Assumes that at the end of the crawlXpath list, we are at the page before 
		// we need to start extracting all the data 
		public function collectUrls($url) {
			setCurrentUrl($url);
			
		}
		
		
		// Goes through seedUrl and calls collectUrls on them.
		public function crawl(){
			foreach($seedUrl as $url) {
				collectUrls($url);
			}
		}
		
		/********************************
		
			Getter and Setter Methods
			
		 ********************************/
		
		public function setTimeout($nTimeout){
			$this->timeout = $nTimeout;
		}
		
		public function getTimeout() {
			echo $this->timeout;
		}
		
		public function setConfigName($nConfigCame){
			$this->configName = $nConfigCame;
		}
		
		public function getConfigName() {
			echo $this->configName;
		}
		
		public function addSeedUrl($nSeedUrl){
			$this->seedUrl = $nSeedUrl;
		}
		
		public function getSeedUrl() {
			echo $this->seedUrl;
		}
		
		public function addCrawlXpath($nCrawlXpath){
			$this->crawlXpath = $nCrawlXpath;
		}
		
		public function getCrawlXpath() {
			echo $this->crawlXpath;
		}
		
		public function setDataXpath($nDataXpath){
			$this->dataXpath = $nDataXpath;
		}
		
		public function getDataXpath() {
			echo $this->dataXpath;
		}
		
		
	}
?>