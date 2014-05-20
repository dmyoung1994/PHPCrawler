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
		public function collectData($url) {
			
		}
		
		// Applies the crawlXpaths on each of the urls in order to get to the data page.
		// Accumulates a list of urls that we need to use the data xPath on later on.
		public function collectUrls($url) {
			setCurrentCrawlUrl($url);
			$pageSource = file_get_contents($url);
			$page = new DOMDocument;
			$page->loadHTML($pageSource);
			$xpath = new DOMXPath($page);
			$urlToGetData = "";
			foreach($crawlXpath as $xPathExpression) {
				$newCrawlUrl = $xpath->query($xPathExpression);
				// TODO: save this new url to a database
				addCrawlUrlToDB($newCrawlUrl); // Must impliment this method
				// After the loop is done, the url that we need to evaulate the
				// data xPath expression will be set to $urlToGetData.
				$urlToGetData = $newCrawlUrl;
			}
			addDataUrlToDB($urlToGetData); // Must impliment this method
		}
		
		
		// Goes through seedUrl and calls collectUrls on them.
		// To be called after setup is complete
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
			return $this->timeout;
		}
		
		public function setConfigName($nConfigCame){
			$this->configName = $nConfigCame;
		}
		
		public function getConfigName() {
			return $this->configName;
		}
		
		public function addSeedUrl($nSeedUrl){
			$this->seedUrl = $nSeedUrl;
		}
		
		public function getSeedUrl() {
			return $this->seedUrl;
		}
		
		public function addCrawlXpath($nCrawlXpath){
			$this->crawlXpath = $nCrawlXpath;
		}
		
		public function getCrawlXpath() {
			return $this->crawlXpath;
		}
		
		public function setDataXpath($nDataXpath){
			$this->dataXpath = $nDataXpath;
		}
		
		public function getDataXpath() {
			return $this->dataXpath;
		}
		
		public function setCurrentCrawlUrl($nUrl){
			$this->currentCrawlUrl = $nUrl;
		}
		
		public function getCurrentCrawlUrl() {
			return $this->currentCrawlUrl;
		}
		
	}
?>