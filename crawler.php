<?php
	
	/********************************************
		
		The CrawlBase class is the base class 
		that will be extended for specific 
		crawlers.
			
	*********************************************/
			
	class CrawlBase {
		protected static $timeout = 10000;
		protected static $configName = "";
		protected static $seedUrl = array();			// Data sctructre for now. Consider switching to array lists.
		protected static $crawlXpath = array();		// Data sctructre for now. Consider switching to array lists.
		protected static $dataXpath = "";
		protected static $currentCrawlUrl = "";
		
		// This function will be overwritten by the user to specify what data 
		// they want to collect when the crawl is run.
		public function collectData($url) {
			
		}
		
		private function addCrawlUrlToDB($urls) {
			foreach ($urls as $url) {
			    echo "Adding crawl url to DB: ".$url->nodeValue."<br>";
			}
		}
		
		private function addDataUrlToDB($urls) {
			foreach ($urls as $url) {
			    echo "Adding data url to DB: ".$url->nodeValue."<br>";
			}
		}
		
		private function getPageHtml($url) {
			$pageSource = file_get_contents("http://".$url); // replace with curl in the furute.
			$page = new DOMDocument();
			libxml_use_internal_errors(true);
			$page->loadHTML($pageSource);
			return $page;
		}
		
		// Applies the crawlXpaths on each of the urls in order to get to the data page.
		// Accumulates a list of urls that we need to use the data xPath on later on.
		public function collectUrls($url) {
			$this->setCurrentCrawlUrl($url);
			$urlToGetData = "";
			$newCrawlUrl = $url;
			if (count(self::$crawlXpath) != 0 && self::$dataXpath != "") {
				foreach(self::$crawlXpath as $xPathExpression) {
					$page = $this->getPageHtml($newCrawlUrl);
					$xpath = new DOMXPath($page);
					$newCrawlUrl = $xpath->query($xPathExpression);
					// TODO: save this new url to a database
					$this->addCrawlUrlToDB($newCrawlUrl); // Must impliment this method
					// After the loop is done, the url that we need to evaulate the
					// data xPath expression will be set to $urlToGetData.
					$urlToGetData = $newCrawlUrl;
				}
				$this->addDataUrlToDB($urlToGetData); // Must impliment this method
			} else if (self::$dataXpath != "") {
				$this->setCurrentCrawlUrl($url);
				$page = $this->getPageHtml($url);
				$xpath = new DOMXPath($page);
				$newDataUrl = $xpath->query($dataXpath);
				echo($newDataUrl);
				$this->addDataUrlToDB($newDataUrl);
			} else {
				echo "Data xPath not provided. Returning.";
				return;
			}
		}
		
		
		// Goes through seedUrl and calls collectUrls on them.
		// To be called after setup is complete
		public function crawl(){
			$seedUrls = $this->getSeedUrls();
			foreach($seedUrls as $url) {
				$this->collectUrls($url);
			}
		}
		
		/********************************
		
			Getter and Setter Methods
			
		 ********************************/
		
		public function setTimeout($nTimeout){
			self::$timeout = $nTimeout;
		}
		
		public function getTimeout() {
			return self::$timeout;
		}
		
		public function setConfigName($nConfigName){
			self::$configName = $nConfigName;
		}
		
		public function getConfigName() {
			return self::$configName;
		}
		
		public function addSeedUrl($nSeedUrl){
			array_push(self::$seedUrl, $nSeedUrl);
		}
		
		public function getSeedUrls() {
			return self::$seedUrl;
		}
		
		public function addCrawlXpath($nCrawlXpath){
			array_push(self::$crawlXpath, $nCrawlXpath);
		}
		
		public function getCrawlXpath() {
			return self::$crawlXpath;
		}
		
		public function setDataXpath($nDataXpath){
			self::$dataXpath = $nDataXpath;
		}
		
		public function getDataXpath() {
			return self::$dataXpath;
		}
		
		public function setCurrentCrawlUrl($nUrl){
			self::$currentCrawlUrl = $nUrl;
		}
		
		public function getCurrentCrawlUrl() {
			return self::$currentCrawlUrl;
		}
		
	}
	
	$crawl = new CrawlBase();
	$crawl->setConfigName("Test");
	$crawl->addSeedUrl("www.reddit.com");
	$crawl->addCrawlXpath("//p[@class='title']/a/@href");
	$crawl->setDataXpath("//p");
	$crawl->crawl();
?>