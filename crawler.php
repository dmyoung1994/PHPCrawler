<?php
	//require_once("DBConnect.php");
	/********************************************
		
		The CrawlBase class is the base class 
		that will be extended for specific 
		crawlers.
			
	*********************************************/
			
	class CrawlBase {
		protected static $timeout = 10000;
		protected static $configName = "";
		protected static $seedUrls = array();			// Data sctructre for now. Consider switching to array lists.
		protected static $crawlXpath = array();		// Data sctructre for now. Consider switching to array lists.
		protected static $dataXpath = "";
		protected static $currentCrawlUrl = "";
		protected static $baseUrl = "";
		protected static $saveToDB = false;
		protected static $saveToObject = true;
		protected static $urlStorage = array();
		protected static $dataUrls = array();
		protected static $urlsToCrawl = array();
		protected static $crawledUrls = array();
		
		// This function will be overwritten by the user to specify what data 
		// they want to collect when the crawl is run.
		public function collectData($url) {
			
		}
		
		private function addCrawlUrlToDB($url) {
			echo "Adding crawl url to DB: ".$this->normalizeUrl($url)."<br>";
			if(self::$saveToObject) {
				array_push(self::$urlStorage, $url);
			} else if (self::$saveToDB) {
				// TODO: Impliment this.
			}
		}
		
		private function addDataUrlToDB($url) {
			echo "Adding data url to DB: ".$this->normalizeUrl($url)."<br>";
			if(self::$saveToObject) {
				array_push(self::$dataUrls, $url);
			} else if (self::$saveToDB) {
				// TODO: Impliment this.
			}
		}
		
		private function xPathEvalSingle($source, $xpathExpression) {
			$resultsFromXpath = $source->evaluate($xPathExpression)->item(0)->textContent;
			return $resultsFromXpath;
		}
		
		private function normalizeUrl($url) {
			$returnUrl = $url;
			if(!strpos($url, "/")) {
				$returnUrl = self::$baseUrl.$url;	
			}
			if(strpos($url, "http://")) {
				$returnUrl = "http://".$url;
			}
			return $returnUrl;
		}
		
		private function getPageHtml($url) {
			$normalizedUrl = $this->normalizeUrl($url);
			$page = new DOMDocument();
			$page->strictErrorChecking = false;
			libxml_use_internal_errors(true);
			$page->loadHTMLFile($normalizedUrl);
			return $page;
		}
		
		// Applies the crawlXpaths on each of the urls in order to get to the data page.
		// Accumulates a list of urls that we need to use the data xPath on later on.
		public function collectUrls() {
			$this->addUrlsToCrawlArray(self::$seedUrls);
			if (count(self::$crawlXpath) != 0 && self::$dataXpath != "") {
				$length = count(self::$crawlXpath);
				for($i=0; $i<$length; $i++) {
					$crawlXpathToUse = self::$crawlXpath[$i];
					$urlsToCrawlThisRun = self::$urlsToCrawl[$i];
					$crawledUrlsThisRun = array();
					foreach ($urlsToCrawlThisRun as $url) {
						echo("Crawling: ". $url."<br>");
						$xml = $this->getPageHtml($url);
						echo"have page content"."<br>";
						$Xpath = new DOMXpath($xml);
						echo"using xpath: ". $crawlXpathToUse."<br>";
						$urlsFromXpathNodeList = $Xpath->evaluate($crawlXpathToUse);
						echo"xpath evaluated.";
						$urlsFromXpathArray = array();
						$length = $urlsFromXpathNodeList->length;
						for($j=0; $j<$length; $j++) {
							array_push($urlsFromXpathArray, $urlsFromXpathNodeList->item($j)->textContent);
							$this->addCrawlUrlToDb($urlsFromXpathNodeList->item($j)->textContent);
						}
						array_push(self::$urlsToCrawl, $urlsFromXpathArray);
					}
				} 
			} else if (count(self::$crawlXpath) == 0 && self::$dataXpath != "") {
			
			} else {
				echo "Data xPath not provided. Returning.";
				return;
			}
		}
		
		public function addUrlsToCrawlArray($urlArray) {
			array_push(self::$urlsToCrawl, $urlArray);
		}
		
		// Goes through seedUrl and calls collectUrls on them.
		// To be called after setup is complete
		public function crawl(){
			$this->collectUrls();
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
			array_push(self::$seedUrls, $nSeedUrl);
		}
		
		public function getSeedUrls() {
			return self::$seedUrls;
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
		
		public function saveToDB() {
			self::$saveToObject = false;
			self::$saveToDB = true;
		}
		
		public function saveToObject() {
			self::$saveToDB = false;
			self::$saveToObject = true;
		}
		
	}
	
	// Crawl urls will get all pages contain data pages.
	// The data xpath will have to pull up all of those crawl pages, and then evaluate the data xpath on all those pages.
	
	$crawl = new CrawlBase();
	$crawl->setConfigName("Test");
	$crawl->setBaseUrl("http://oneclass.com");
	$crawl->addSeedUrl("http://oneclass.com/sitemap/");
	$crawl->addCrawlXpath("//loc/text()");
	$crawl->addCrawlXpath("//loc/text()");
	$crawl->setDataXpath("//p");
	$crawl->crawl();
?>