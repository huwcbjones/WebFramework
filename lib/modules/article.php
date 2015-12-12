<?php
/**
 * Article Classes
 *
 * Long description for file (if any)...
 *
 * @category   News.Article
 * @package    article.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

/*
 */

/**
 * Article Manager Class
 *
 * @category   News.Article.Manage
 */

/*
 */
class Article{
	
	protected $mySQL;
	
	function __construct($link){
		$this->mySQL = $link;
	}
	
	function article_add($title,$article,$username){
		global $user;
		if($user->accessPage(47)){
			$query = $this->mySQL['r']->prepare("SELECT `ID` from `news_articles` WHERE `title` LIKE CONCAT('%',?,'%')");
			$query->bind_param('s',$title);
			$query->execute();
			$query->store_result();
			if($query->num_rows==0){
				$this->mySQL['r']->autocommit(false);
				$stmt = $this->mySQL['w']->prepare("INSERT INTO `news_articles` (`title`,`pub_a`,`article`,`t_p`,`t_e`,`enable`) VALUES(?,?,?,NOW(),NOW(),0)");
				if($stmt!==false){
					$stmt->bind_param('sss',$title,$username,$article);
					$stmt->execute();
					$stmt->store_result();
					if($stmt->affected_rows==1){
						$this->mySQL['r']->commit();
						$this->mySQL['r']->autocommit(true);return 0;
					}else{
						$this->mySQL['r']->rollback();
						$this->mySQL['r']->autocommit(true);return 1;
					}
				}else{$this->mySQL['r']->autocommit(true);return 2;}
			}else{return 3;}
		}else{return 4;}
	}
	
	function article_del($ID){
		global $user;
		if($user->accessPage(50)){
			$query = $this->mySQL['r']->prepare("SELECT `ID` from `news_articles` WHERE `ID`=?");
			$query->bind_param('i',$ID);
			$query->execute();
			$query->store_result();
			if($query->num_rows!=0){
				$this->mySQL['r']->autocommit(false);
				$stmt = $this->mySQL['w']->prepare("DELETE FROM `news_articles` WHERE `ID`=?");
				if($stmt!==false){
					$stmt->bind_param('i',$ID);
					$stmt->execute();
					$stmt->store_result();
					if($stmt->affected_rows==1){
						$this->mySQL['r']->commit();
						$this->mySQL['r']->autocommit(true);return 0;
					}else{
						$this->mySQL['r']->rollback();
						$this->mySQL['r']->autocommit(true);return 1;
					}
				}else{$this->mySQL['r']->autocommit(true);return 2;}
			}else{return 3;}
		}else{return 4;}
	}
	
	function article_edit($ID,$title,$article,$username,$preview){
		global $user;
		if($user->accessPage(48)){
			$this->mySQL['r']->autocommit(false);
			$stmt = $this->mySQL['w']->prepare("UPDATE `news_articles` SET `title`=?,`edit_a`=?,`article`=?,`enable`=? WHERE `ID`=?");
			if($stmt!==false){
				$stmt->bind_param('sssii',$title,$username,$article,$preview,$ID);
				$stmt->execute();
				$stmt->store_result();
				if($stmt->affected_rows==1){
					$this->mySQL['r']->commit();
					$this->mySQL['r']->autocommit(true);return 0;
				}else{
					$this->mySQL['r']->rollback();
					$this->mySQL['r']->autocommit(true);return 1;
				}
			}else{
				$this->mySQL['r']->autocommit(true);return 2;
			}
		}else{return 3;}
	}
	
	function enable($ID,$mode){
		global $user;
		if($user->accessPage(48)){
			$stmt = $this->mySQL['w']->prepare("UPDATE `news_articles` SET `enable`=? WHERE `ID`=?");
			if($stmt!==false){
				$stmt->bind_param('ii',$mode,$ID);
				$stmt->execute();
				$stmt->store_result();
				if($stmt->affected_rows==1){
					$this->mySQL['r']->commit();
					$this->mySQL['r']->autocommit(true);return 0;
				}else{
					$this->mySQL['r']->rollback();
					$this->mySQL['r']->autocommit(true);return 1;
				}
			}else{
				$this->mySQL['r']->autocommit(true);return 2;
			}
		}else{return 3;}
	}
}

/**
 * Article Ietm Class
 *
 * @category   News.Article.Item
 */

/*
 */
class NewsItem{
	
	protected $mySQL;
	private $articleID;
	private $publish = true;
	private $preview = false;
	private $editPub = false;
	private $article;
	private $long;
	public $short;
	public $pub_date;
	
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
	
	function createArticle($enable = 1){
		$user = new User($this->mySQL);
		if($enable==1){
			$news_item = $this->mySQL['r']->prepare("SELECT `title`,`pub_a`,`edit_a`,`article`,`t_p`,`t_e` FROM `news_articles` WHERE `ID`=? AND `enable`=?");
			$news_item->bind_param('si',$this->articleID,$enable);
		}else{
			$news_item = $this->mySQL['r']->prepare("SELECT `title`,`pub_a`,`edit_a`,`article`,`t_p`,`t_e` FROM `news_articles` WHERE `ID`=?");
			$news_item->bind_param('s',$this->articleID);
		}
		$news_item->execute();
		$news_item->store_result();
		$news = array();
		if($news_item->num_rows!=0){
			$news_item->bind_result($title,$pub_a,$edit_a,$article_content,$t_p,$t_e);
			while($news_item->fetch()){
				$article['author'] = $user->create($pub_a);
				$article['author'] = $user->getName();
				$article['editer'] = $user->create($edit_a);
				$article['editer'] = $user->getName();
				$article['title'] = $title;
				$this->pub_date = $t_p;
				$this->short = substr($article_content,strpos($article_content,'<p>')+3,strpos($article_content,'</p>')-3);
				if($this->publish){
					$article['pub'] = "<small>Published: ".date("H:i:s, D d/m/Y",strtotime($t_p))."</small>";
					$article['edit'] = "<small>Edited: ".date("H:i:s, D d/m/Y",strtotime($t_e))."</small>";
					if($t_p!=$t_e){
						$this->editPub = true;
					}else{
						$this->editPub = false;
					}
				}else{
					$article['edit'] = $article['pub'] = "";
				}
				if(strlen($article_content)<250){
					$this->long=false;
					$article_content =  str_replace("<p>","",$article_content);
					$article['content'] = str_replace("</p>","",$article_content);
				}else{
					$this->long=true;
					if($this->preview){
						$article['content'] = substr($article_content,0,strpos(substr($article_content,0,250),'</p>')+4);
					}else{
						$article['content'] = '<p class="lead">'.substr($article_content,strpos($article_content,'<p>')+3,strpos($article_content,'</p>')-3).'</p>'.substr($article_content,strpos($article_content,'</p>')+4);
						$article['content'] = str_replace('</p>',"</p>\n        ",$article['content']);
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