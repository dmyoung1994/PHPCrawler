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
		protected static $baseUrl = "";
		
		// This function will be overwritten by the user to specify what data 
		// they want to collect when the crawl is run.
		public function collectData($url) {
			
		}
		
		private function addCrawlUrlToDB($urls) {
			echo "Adding crawl url to DB: ".$this->normalizeUrl($url->nodeValue)."<br>";
		}
		
		private function addDataUrlToDB($urls) {
			echo "Adding data url to DB: ".$this->normalizeUrl($url->nodeValue)."<br>";
		}
		
		private function xPathEvalSingle($xml, $xpathExpression) {
			$xpathEval = new DOMXPath($xml);
			$resultsFromXpath = $xpathEval->query($xPathExpression);
			return $resultsFromXpath[0];
		}
		
		private function normalizeUrl($url) {
			if(!strpos($url, "/", 0)) {
				if(!strpos($url, "http://")) {
					return "http://".self::$baseUrl.$url;
				} else {
					return self::$baseUrl.$url;
				}
					
			} else {
				return $url;
			}
		}
		
		private function getPageHtml($url) {
			$normalizedUrl = $this->normalizeUrl($url);
			$pageSource = file_get_contents($normalizedUrl); // replace with curl in the furute.
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
					$newCrawlUrl = $this->xPathEvalSingle($page, $xPathExpression);
					// TODO: save this new url to a database
					$this->addCrawlUrlToDB($newCrawlUrl); // Must impliment this method
					// After the loop is done, the url that we need to evaulate the
					// data xPath expression will be set to $urlToGetData.
					echo $newCrawlUrl."</br>";
					$urlToGetData = $newCrawlUrl;
					$this->collectUrls($urlToGetData);
				}
				// This is where we will crawl the last pages to get urls for data pages.
				
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
		
		public function setBaseUrl($nUrl){
			self::$baseUrl = $nUrl;
		}
		
		public function getBaseUrl() {
			return self::$baseUrl;
		}
		
	}
	
	// Crawl urls will get all pages contain data pages.
	// The data xpath will have to pull up all of those crawl pages, and then evaluate the data xpath on all those pages.
	
	$crawl = new CrawlBase();
	$crawl->setConfigName("Test");
	$crawl->setBaseUrl("http://ziggyscycle.ca");
	$crawl->addSeedUrl("http://ziggyscycle.ca/product-list/bikes-1000/");
	$crawl->addCrawlXpath("//div[@class='seresultspagination']/a[@class='senextbatch']/@href");
	$crawl->setDataXpath("//p");
	$crawl->crawl();
?>