<?php
class SwimShop {
	public $advert = array();
	public $prevAdNum;
	public $adNum;
	protected $mySQL;
	function __construct($link){
		$this->mySQL = $link;
	}
	public function getAdverts(){
		$result = $this->mySQL['r']->prepare("SELECT `ID`,`trackingCode` FROM `core_swimshop` WHERE `enable`=1");
		$result->execute();
		$result->bind_result($ID,$trackingCode);
		while($result->fetch()){
			$this->advert[$ID] = $trackingCode;
		}
		$this->advert = array_values($this->advert);
	}
	
	function chooseAdNum(){
		if(isset($_SESSION['prevAdNum'])){
			$this->prevAdNum = $_SESSION['prevAdNum'];
		} else {
			$this->prevAdNum = 0;
		}
		if($this->prevAdNum==0){
		}elseif($this->prevAdNum<0){
			while($this->prevAdNum<0){
				$this->prevAdNum = $this->prevAdNum+1;
			}
		}elseif($this->prevAdNum>(count($this->advert)-1)){
			while($this->prevAdNum>(count($this->advert)-1)){
				$this->prevAdNum = $this->prevAdNum-1;
			}
		}
		$this->adNum = rand(0,count($this->advert)-1);
		if(count($this->advert)!=1){
		while ($this->adNum==$this->prevAdNum) {
			$this->adNum = rand(0,count($this->advert)-1);
		}
		}
		$_SESSION['prevAdNum'] = $this->adNum;
	}

	function advert(){
		return $this->advert[$this->adNum];
	}
}
?>
