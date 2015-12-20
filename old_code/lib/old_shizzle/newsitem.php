<?php

class NewsItem{
	
	protected $mySQL;
	private $articleID;
	private $publish = true;
	private $preview = false;
	private $editPub = false;
	private $article;
	private $long;
	
	function __construct($link){
		$this->mySQL = $link;
	}
	
	function setID($ID){
		$this->articleID = $ID;
	}
	function getID(){
		if($this->articleID!=""){
			return $this->articleID;
		}else{
			return false;
		}
	}
	function setPublish($enable){
		if($enable===true){
			$this->publish = true;
		}else{
			$this->publish = false;
		}
	}
	function setPreview($enable){
		if($enable===true){
			$this->preview = true;
		}else{
			$this->preview = false;
		}
	}
	
	function createArticle(){
		$user = new User($this->mySQL);
		$news_item = $this->mySQL['r']->query("SELECT * FROM `news_articles` WHERE `ID`='".$this->articleID."'");
		$news = array();
		if($news_item!==false&&$news_item->num_rows!=0){
			while($item = $news_item->fetch_array()){
				$article['author'] = $user->create($item['pub_a']);
				$article['author'] = $user->getName();
				$article['editer'] = $user->create($item['edit_a']);
				$article['editer'] = $user->getName();
				$article['title'] = $item['title'];
				if($this->publish){
					$article['pub'] = "<small>Published: ".date("H:i:s, D d/m/Y",strtotime($item['t_p']))."</small>";
					$article['edit'] = "<small>Edited: ".date("H:i:s, D d/m/Y",strtotime($item['t_e']))."</small>";
					if($item['t_p']!=$item['t_e']){
						$this->editPub = true;
					}else{
						$this->editPub = false;
					}
				}else{
					$article['edit'] = $article['pub'] = "";
				}
				if($item['long']==""){
					$this->long = false;
				}else{
					$this->long = true;
				}
				if($this->preview){
					$article['content'] = $item['short'];
				}else{
					if($item['long']!=""){
						$article['content'] = "<b>".$item['short']."</b><br />"."\n".$item['long'];
					}else{
						$article['content'] = $item['short'];
					}
				}
			}
		}else{
			$article = false;
		}
		$this->article = $article;
		if($article===false){
			return false;
		}else{
			return true;
		}
	}
	function getContent(){
		return $this->article['content'];
	}
	function getTitle(){
		return $this->article['title'];
	}
	function getPublish(){
		return $this->article['pub'];
	}
	function getPublishState(){
		return $this->publish;
	}
	function getEdit(){
		return $this->article['edit'];
	}
	function getLonger(){
		return $this->long;
	}
	function getAuthor(){
		return $this->article['author'];
	}
	function getEdited(){
		if($this->article['editer']!=""){
			if($this->article['author']!=$this->article['editer']){
				return $this->article['editer'];
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
}
?>