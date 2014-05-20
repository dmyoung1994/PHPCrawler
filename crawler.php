<?php
	class CrawlBase {
		private $timeout = 0;
		private $configName = "";
		private $seedUrl = "";
		private $crawlXpath = "";
		private $dataXpath = "";
		private $currentCrawlUrl = "";
		
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
		
		public function setSeedUrl($nSeedUrl){
			$this->seedUrl = $nSeedUrl;
		}
		
		public function getSeedUrl() {
			echo $this->seedUrl;
		}
		
	}
?>
}