<?php
require_once "fastcat_api.php";

function searchMaster($sfrom,$collection,$fastcat,$query,$stype,$keyword,$hkeyword,$otype,$interval,$findCategory,$checkAdult,$chapterSize,$startItem,$lengthItem) {
	if($collection=="board" ) {
		return searchBoard($collection,$fastcat,$query,$stype,$keyword,$hkeyword,$otype,$interval,$findCategory,$checkAdult,$chapterSize,$startItem,$lengthItem);
	} else if($collection=="news") {
		return searchNews($collection,$fastcat,$query,$stype,$keyword,$hkeyword,$otype,$interval,$findCategory,$checkAdult,$chapterSize,$startItem,$lengthItem);
	} else if($collection=="faq") {
		return searchFAQ($collection,$fastcat,$query,$stype,$keyword,$hkeyword,$otype,$interval,$findCategory,$checkAdult,$chapterSize,$startItem,$lengthItem);
	}
}

function requireScript($collection) {

	} else if($collection=="board") {
		return "search_item_bbs.php";
	} else if($collection=="news") {
		return "search_item_news.php";
	} else if($collection=="faq") {
		return "search_item_faq.php";
	}
}

function searchBoard($collection,$fastcat,$query,$stype,$keyword,$hkeyword,$otype,$interval,$findCategory,$checkAdult,$chapterSize,$startItem,$lengthItem) {

	$searchField = array();
	if($stype=="all") {
		$searchField = array("title","content","member_name");
	} else if($stype=="subject") {
		$searchField = array("title");
	} else if($stype=="content") {
		$searchField = array("content");
	} else if($stype=="keyword") {
		$searchField = array("content");
	} else if($stype=="member") {
		$searchField = array("member_name");
	}

	if($otype=="date") {
		$query->addRankingEntry("wdate");
	} else {
		$query->addRankingEntry("_score");
	}

	preg_match_all("/([0-9]+)([a-z]+)/", $interval, $matches);

	if($interval=="all") {
	} else if($matches[2][0]=="d") {
		$query->addFilterEntry("wdate",SearchQueryStringer::FILTER_SECTION,date("YmdHis",strtotime("-".$matches[1][0]." days")),date("YmdHis"));
	} else if($matches[2][0]=="w") {
		$query->addFilterEntry("wdate",SearchQueryStringer::FILTER_SECTION,date("YmdHis",strtotime("-".$matches[1][0]." weeks")),date("YmdHis"));
	} else if($matches[2][0]=="m") {
		$query->addFilterEntry("wdate",SearchQueryStringer::FILTER_SECTION,date("YmdHis",strtotime("-".$matches[1][0]." months")),date("YmdHis"));
	} else if($matches[2][0]=="y") {
		$query->addFilterEntry("wdate",SearchQueryStringer::FILTER_SECTION,date("YmdHis",strtotime("-".$matches[1][0]." years")),date("YmdHis"));
	} else {
		$intervalArray = explode("~",$interval);
		if(count($intervalArray)>1) {
			$query->addFilterEntry("wdate",SearchQueryStringer::FILTER_SECTION,$intervalArray[0],$intervalArray[1]);
		} else if(count($intervalArray)>0) {
			$query->addFilterEntry("wdate",SearchQueryStringer::FILTER_SECTION,$intervalArray[0]);
		}
	}

	$query->andSearchEntry($searchField,$hkeyword." ".$keyword,SearchQueryStringer::KEYWORD_AND,10);
	if($stype=="all") {
		$query->orSearchEntry(array("title"),$hkeyword." ".$keyword,SearchQueryStringer::KEYWORD_AND,500);
	}

	$query->setCollection($collection)
		->setFieldList("idx,title,content:200,subject,bookimg,cnt_read,cnt_page_read,cnt_recom,wdate,member_name,member_id,category,bbsid,sub_bbsid,_score")
		->setUserDataKeyword($keyword)
		->setLength($startItem,$lengthItem);
	$jsonStr = $fastcat->communicate("/service/search.json",$query->getQueryString(),"");
	return json_decode($jsonStr,true);
}

function searchNews($collection,$fastcat,$query,$stype,$keyword,$hkeyword,$otype,$interval,$findCategory,$checkAdult,$chapterSize,$startItem,$lengthItem) {

	$searchField = array();
	if($stype=="all") {
		$searchField = array("words","content","member_name");
	} else if($stype=="subject") {
		$searchField = array("words");
	} else if($stype=="content") {
		$searchField = array("content");
	} else if($stype=="keyword") {
		$searchField = array("content");
	} else if($stype=="member") {
		$searchField = array("member_name");
	}

	if($otype=="date") {
		$query->addRankingEntry("redate");
	} else if($otype=="score") {
		$query->addRankingEntry("_score");
	} else if($otype=="read") {
		$query->addRankingEntry("cnt_page_read");
	} else if($otype=="recom") {
		$query->addRankingEntry("cnt_recom");
	} else if($otype=="favor") {
		$query->addRankingEntry("cnt_favorite");
	}

	preg_match_all("/([0-9]+)([a-z]+)/", $interval, $matches);

	if($interval=="all") {
	} else if($matches[2][0]=="d") {
		$query->addFilterEntry("wdate",SearchQueryStringer::FILTER_SECTION,date("YmdHis",strtotime("-".$matches[1][0]." days")),date("YmdHis"));
	} else if($matches[2][0]=="w") {
		$query->addFilterEntry("wdate",SearchQueryStringer::FILTER_SECTION,date("YmdHis",strtotime("-".$matches[1][0]." weeks")),date("YmdHis"));
	} else if($matches[2][0]=="m") {
		$query->addFilterEntry("wdate",SearchQueryStringer::FILTER_SECTION,date("YmdHis",strtotime("-".$matches[1][0]." months")),date("YmdHis"));
	} else if($matches[2][0]=="y") {
		$query->addFilterEntry("wdate",SearchQueryStringer::FILTER_SECTION,date("YmdHis",strtotime("-".$matches[1][0]." years")),date("YmdHis"));
	} else {
		$intervalArray = explode("~",$interval);
		if(count($intervalArray)>1) {
			$query->addFilterEntry("wdate",SearchQueryStringer::FILTER_SECTION,$intervalArray[0],$intervalArray[1]);
		} else if(count($intervalArray)>0) {
			$query->addFilterEntry("wdate",SearchQueryStringer::FILTER_SECTION,$intervalArray[0]);
		}
	}

	$query->andSearchEntry($searchField,$hkeyword." ".$keyword,SearchQueryStringer::KEYWORD_AND,10);
	if($stype=="all") {
		$query->orSearchEntry(array("words"),$hkeyword." ".$keyword,SearchQueryStringer::KEYWORD_AND,500);
	}

	$query->setCollection($collection)
		->setFieldList("idx,words,content:200,member_name,member_id,wdate,_score")
		->setUserDataKeyword($keyword)
		->setLength($startItem,$lengthItem);
	$jsonStr = $fastcat->communicate("/service/search.json",$query->getQueryString(),"");
	return json_decode($jsonStr,true);
}

function searchFAQ($collection,$fastcat,$query,$stype,$keyword,$hkeyword,$otype,$interval,$findCategory,$checkAdult,$chapterSize,$startItem,$lengthItem) {

	$searchField = array();
	if($stype=="all") {
		$searchField = array("title","content");
	} else if($stype=="subject") {
		$searchField = array("title");
	} else if($stype=="content") {
		$searchField = array("content");
	} else if($stype=="keyword") {
		$searchField = array("title");
	} else if($stype=="member") {
		$searchField = array("content");
	}

	if($otype=="date") {
		$query->addRankingEntry("wdate");
	} else {
		$query->addRankingEntry("_score");
	}

	preg_match_all("/([0-9]+)([a-z]+)/", $interval, $matches);

	if($interval=="all") {
	} else if($matches[2][0]=="d") {
		$query->addFilterEntry("wdate",SearchQueryStringer::FILTER_SECTION,date("YmdHis",strtotime("-".$matches[1][0]." days")),date("YmdHis"));
	} else if($matches[2][0]=="w") {
		$query->addFilterEntry("wdate",SearchQueryStringer::FILTER_SECTION,date("YmdHis",strtotime("-".$matches[1][0]." weeks")),date("YmdHis"));
	} else if($matches[2][0]=="m") {
		$query->addFilterEntry("wdate",SearchQueryStringer::FILTER_SECTION,date("YmdHis",strtotime("-".$matches[1][0]." months")),date("YmdHis"));
	} else if($matches[2][0]=="y") {
		$query->addFilterEntry("wdate",SearchQueryStringer::FILTER_SECTION,date("YmdHis",strtotime("-".$matches[1][0]." years")),date("YmdHis"));
	} else {
		$intervalArray = explode("~",$interval);
		if(count($intervalArray)>1) {
			$query->addFilterEntry("wdate",SearchQueryStringer::FILTER_SECTION,$intervalArray[0],$intervalArray[1]);
		} else if(count($intervalArray)>0) {
			$query->addFilterEntry("wdate",SearchQueryStringer::FILTER_SECTION,$intervalArray[0]);
		}
	}

	$query->andSearchEntry($searchField,$hkeyword." ".$keyword,SearchQueryStringer::KEYWORD_AND,10);
	if($stype=="all") {
		$query->orSearchEntry(array("title"),$hkeyword." ".$keyword,SearchQueryStringer::KEYWORD_AND,500);
	}

	$query->setCollection($collection)
		->setFieldList("idx,bbsid,board_img,title,content:200,comment:200,wdate,enddate,_score")
		->setUserDataKeyword($keyword)
		->setLength($startItem,$lengthItem);
	$jsonStr = $fastcat->communicate("/service/search.json",$query->getQueryString(),"");
	return json_decode($jsonStr,true);
}

function searchCompletion($fastcat,$query,$keyword,$from,$startItem,$lengthItem) {
	$query->setHighlight("<b>","</b>");
	$query->addRankingEntry("_score");
	$query->setUrlEncode(false);

	$query->andSearchEntry(array("subject"),$keyword,SearchQueryStringer::KEYWORD_AND,100);
	$query->orSearchEntry(array("search"),$keyword,SearchQueryStringer::KEYWORD_AND,0);

	$query->setCollection("completion")
		->setFieldList("book_code,subject,bookimg")
		->setLength($startItem,$lengthItem);

	$jsonStr = $fastcat->communicate("/service/search.json",$query->getQueryString(),"");
	return json_decode($jsonStr,true);
}

function dateAdd($time,$type,$value) {
	if($type=="Y") {
		$time += ($value*60*60*24*365);
	} else if($type=="m") {
		$time += ($value*60*60*24*30);
	} else if($type=="w") {
		$time += ($value*60*60*24*7);
	} else if($type=="d") {
		$time += ($value*60*60*24);
	} else if($type=="H") {
		$time += ($value*60*60);
	} else if($type=="i") {
		$time += ($value * 60);
	} else if($type=="s") {
		$time += $value;
	}
}

function parseTimeMillis($timeStr) {
	$value = 0;
	if(endsWith($timeStr,"ms")) {
		$value = trim(substr($timeStr, 0, strlen($timeStr) - 2)) * 1;
	} else if(endsWith($timeStr,"s")) {
		$value = trim(substr($timeStr, 0, strlen($timeStr) - 2)) * 1000;
	} else if(endsWith($timeStr,"m")) {
		$value = trim(substr($timeStr, 0, strlen($timeStr) - 2)) * 1000 * 60;
	} else if(endsWith($timeStr,"h")) {
		$value = trim(substr($timeStr, 0, strlen($timeStr) - 2)) * 1000 * 60 * 60;
	}
	return $value;
}

function startsWith($haystack, $needle) {
	return $needle === "" || strpos($haystack, $needle) === 0;
}

function endsWith($haystack, $needle) {
	return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

function sendLog($logger, $keyword, $prevKeyword, $sfrom, $service, $sort, $cpage, $resptime) {

	$prevKeyword = urlencode($prevKeyword);
	$prmService = urlencode($service);
	$prmPage = $page;
	$resptime = $resptime;
	$prmSort = urlencode($sort);
	$prmCategory = "";

	$prmLogin = "로그인";
	$prmAge = "30대";
	$prmGender = "남성";

	$prmLogin = urlencode($prmLogin);
	$prmAge = urlencode($prmAge);
	$prmGender = urlencode($prmGender);

	$keyword = urlencode($keyword);
	$paramStr = "type=search&siteId=total&categoryId=".$sfrom."&keyword=".$keyword."&prev=".$prevKeyword;
	$paramStr .= "&resptime=".$resptime."&page=".$prmPage."&sort=".$prmSort."&service=".$prmService;
	$paramStr .= "&age=".$prmAge."&login=".$prmLogin."&gender=".$prmGender."&category=".$prmCategory;
	
	$logger->communicate("/service/keyword/hit/post.json",$paramStr);
}

function getDailyRankKeyword($logger,$sfrom,$interval) {
	$jsonStr = $logger->communicate("/service/keyword/popular.json",
		"siteId=total&categoryId=".$sfrom."&timeType=D&interval=".$interval);
	return json_decode($jsonStr,true);
}

function getPopularKeyword($logger, $sfrom) {
	$jsonStr = $logger->communicate("/service/keyword/popular/rt.json","type=search&siteId=total&categoryId=".$sfrom);
	return json_decode($jsonStr,true);
}

function getRelateKeyword($logger,$sfrom, $keyword) {
	$keyword = urlencode($keyword);
	$jsonStr = $logger->communicate("/service/keyword/relate.json","siteId=total&category=".$sfrom."&keyword=".$keyword."");
	return json_decode($jsonStr,true);
}
?>
