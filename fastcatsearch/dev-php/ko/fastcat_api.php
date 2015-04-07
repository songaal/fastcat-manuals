<?php
class SearchQueryStringer {

	const VERSION 			= 1;

	/**
	 * bitwise operations 
	 * SE_OPT_DEFAULT = SE_OPT_SYN|SE_OPT_STOP|SE_OPT_HL|SE_OPT_SUM;
	 **/
	const SE_OPT_SYN		= 0x01; // 유사어적용.
	const SE_OPT_STOP		= 0x02; // 불용어적용.
	const SE_OPT_HL			= 0x04; // 키워드 하이라이팅.
	const SE_OPT_SUM		= 0x08; // 키워드 중심의 요약생성.
	const SE_OPT_DEFAULT	= 0x0f; // 디폴트 옵션은 유사어와 하이라이팅과 요약.

	const KEYWORD_AND		= 1;
	const KEYWORD_OR		= 2;

	const RANK_ASC			= 1;
	const RANK_DESC			= 2;

	const FILTER_MATCH			= 1;
	const FILTER_SECTION		= 2;
	const FILTER_PREFIX			= 3;
	const FILTER_SUFFIX			= 4;
	const FILTER_MATCH_BOOST	= 5;
	const FILTER_SECTION_BOOST	= 6;
	const FILTER_PREFIX_BOOST	= 7;
	const FILTER_SUFFIX_BOOST	= 8;
	const GROUP_FREQ			= 1;
	const GROUP_SECTION_FREQ	= 2;
	const GROUP_SUM				= 3;
	const GROUP_MAX				= 4;
	const GROUP_SORT_KEY_ASC	= 1;
	const GROUP_SORT_KEY_DESC	= 2;
	const GROUP_SORT_FREQ_ASC	= 3;
	const GROUP_SORT_FREQ_DESC	= 4;

	private $collection;
	private $fieldListEntry;
	private $searchEntry;
	private $start = -1;
	private $length = -1;
	private $timeout = 5000;
	private $userDataEntry;
	private $highlightEntry;
	private $filterEntry;
	private $groupEntry;
	private $rankingEntry;
	private $urlencoded;
	private $fromEnc;// 키워드 인코딩 변환.
	private $toEnc;// 키워드 인코딩 변환.

	public function SearchQueryStringer ($collection) {
		$this->collection = $collection;
		$this->urlencoded = true;
	}

	public function setLength($start, $length) {
		$this->start = $start;
		$this->length = $length;
		return $this;
	}

	public function setTimeout($timeout) {
		$this->timeout = $timeout;
		return $this;
	}

	public function setCollection($collection) {
		$this->collection = $collection;
		return $this;
	}
	public function setSeEntry($se) {
		$this->searchEntry = $se;
		return $this;
	}

	public function setHighlight($startTag, $endTag) {
		$this->highlightEntry = $startTag.":".$endTag;
		return $this;
	}

	public function addUserData($key, $value) {
		if ($this->userDataEntry == null) {
			$this->userDataEntry = $key.":".$value;
		} else {
			$this->userDataEntry .= (",".$key.":".$value);
		}

		return $this;
	}

	public function setUserDataKeyword($keyword) {
		$newKeyword = null;

		if($this->urlencoded) {
			$keyword = urlencode($keyword);
		}

		if ($this->fromEnc != null && $this->toEnc != null) {
			$newKeyword = iconv($this->fromEnc, $this->toEnc, $keyword);
		} else {
			$newKeyword = $keyword;
		}

		$newKeyword = $this->escapeKeyword($newKeyword);

		if ($this->userDataEntry == null) {
			$this->userDataEntry = "keyword:".$newKeyword;
		} else {
			$this->userDataEntry += (",keyword:".$newKeyword);
		}

		return $this;
	}

	public function setFieldList($fieldListEntry) {
		$this->fieldListEntry = $fieldListEntry;
		return $this;
	}

	public function addFieldList($fieldName, $summarySize=0) {

		$newFieldList = null;
		if ($summarySize > 0) {
			$newFieldList = ($fieldName.":".$summarySize);
		} else {
			$newFieldList = $fieldName;
		}

		if ($this->fieldListEntry != null) {
			$this->fieldListEntry .= ("," . $newFieldList);
		} else {
			$this->fieldListEntry = $newFieldList;
		}
		return $this;
	}

	public function orSearchEntry($fieldName, $keyword, 
		$searchKeywordOp=SearchQueryStringer::KEYWORD_AND, 
		$weight=100, $option=SearchQueryStringer::SE_OPT_DEFAULT) {

		if($this->urlencoded) {
			$keyword = urlencode($keyword);
		}

		$newSe = $this->addSearchEntry($fieldName, $keyword, $searchKeywordOp, $weight, $option);

		if ($this->searchEntry != null) {
			$this->searchEntry = "{".$this->searchEntry."OR".$newSe."}";
		} else {
			$this->searchEntry = $newSe;
		}

		return $this;
	}

	public function andSearchEntry($fieldName, $keyword, 
		$searchKeywordOp=SearchQueryStringer::KEYWORD_AND, 
		$weight=100, $option=SearchQueryStringer::SE_OPT_DEFAULT) {
		$newSe = $this->addSearchEntry($fieldName, $keyword, $searchKeywordOp, $weight, $option);

		if($this->urlencoded) {
			$keyword = urlencode($keyword);
		}

		if ($this->searchEntry != null) {
			$this->searchEntry = "{".$this->searchEntry."AND".$newSe."}";
		} else {
			$this->searchEntry = $newSe;
		}

		return $this;
	}

	public function notSearchEntry($fieldName, $keyword, 
		$searchKeywordOp=SearchQueryStringer::KEYWORD_AND, 
		$weight=100, $option=SearchQueryStringer::SE_OPT_DEFAULT) {

		if($this->urlencoded) {
			$keyword = urlencode($keyword);
		}

		$newSe = $this->addSearchEntry($fieldName, $keyword, $searchKeywordOp, $weight, $option);
		if ($this->searchEntry != null) {
			$this->searchEntry = "{".$this->searchEntry."NOT".$newSe."}";
		} else {
			$this->searchEntry = "{"."NOT".$newSe."}";
		}
		return $this;
	}

	public function addSearchEntry($fieldName, $keyword, 
		$searchKeywordOp=SearchQueryStringer::KEYWORD_AND, 
		$weight=100, $option=SearchQueryStringer::SE_OPT_DEFAULT) {

		if($this->urlencoded) {
			$keyword = urlencode($keyword);
		}

		$sb = "";
		$sb.="{";
		for ($i = 0; $i < count($fieldName); $i++) {
			$sb.=$fieldName[$i];
			if ($i < count($fieldName) - 1) {
				$sb.=",";
			}
		}
		$sb.=":";

//		if ($searchKeywordOp == $this::KEYWORD_AND) {
//			$sb.="AND(";
//		} else if ($searchKeywordOp == $this::KEYWORD_OR) {
//			$sb.="OR(";
//		} else {
//			$sb.="AND(";
//		}

		$sb.="ALL(";

		if ($this->fromEnc != null && $this->toEnc != null) {
			$sb.=iconv($this->fromEnc, $this->toEnc, $keyword);
		} else {
			$sb.=$this->escapeKeyword($keyword);
		}
		$sb.="):";

		$sb.=$weight;
		$sb.=":";
		$sb.=$option;
		$sb.="}";

		return $sb;
	}

	private function escapeKeyword($keyword) {
//		if ($keyword == null) {
//			return "";
//		}
//
//		$keyword = str_replace(",", "\\\\,",$keyword);
//		$keyword = str_replace("&", "\\\\&",$keyword);
//		$keyword = str_replace("=", "\\\\=",$keyword);
//		$keyword = str_replace(":", "\\\\:",$keyword);

		return $keyword;
	}

	public function setUrlEncode($urlencoded) {
		$this->urlencoded = $urlencoded;
	}

	public function setKeywordCharset($fromEnc, $toEnc) {
		$this->fromEnc = $fromEnc;
		$this->toEnc = $toEnc;
		return $this;
	}

	public function addRankingEntry($fieldName, $rankOp=SearchQueryStringer::RANK_DESC) {
		$rankStr = null;
		if ($rankOp == SearchQueryStringer::RANK_ASC) {
			$rankStr = "asc";
		} else if ($rankOp == SearchQueryStringer::RANK_DESC) {
			$rankStr = "desc";
		} else {
			$rankStr = "asc";
		}
		if ($this->rankingEntry == null) {
			$this->rankingEntry = $fieldName.":".$rankStr;
		} else {
			$this->rankingEntry .= (",".$fieldName.":".$rankStr);
		}

		return $this;
	}

	public function addFilterEntry($fieldName, $filterOp, $op1, $op2="") {
		$filerStr = null;
		if ($filterOp == SearchQueryStringer::FILTER_MATCH) {
			$filerStr = "match";
		} else if ($filterOp == SearchQueryStringer::FILTER_SECTION) {
			$filerStr = "section";
		} else if ($filterOp == SearchQueryStringer::FILTER_PREFIX) {
			$filerStr = "prefix";
		} else if ($filterOp == SearchQueryStringer::FILTER_SUFFIX) {
			$filerStr = "suffix";
		} else if ($filterOp == SearchQueryStringer::FILTER_MATCH_BOOST) {
			$filerStr = "match_boost";
		} else if ($filterOp == SearchQueryStringer::FILTER_SECTION_BOOST) {
			$filerStr = "section_boost";
		} else if ($filterOp == SearchQueryStringer::FILTER_PREFIX_BOOST) {
			$filerStr = "prefix_boost";
		} else if ($filterOp == SearchQueryStringer::FILTER_SUFFIX_BOOST) {
			$filerStr = "suffix_boost";
		}
		if ($filterOp == SearchQueryStringer::FILTER_SECTION || $filterOp == SearchQueryStringer::FILTER_SECTION_BOOST) {
			if ($this->filterEntry == null) {
				$this->filterEntry = $fieldName.":".$filerStr.":".$this->escapeKeyword($op1)."~".$this->escapeKeyword($op2);
			} else {
				$this->filterEntry .= ( "," .$fieldName.":". $filerStr.":"
						.$this->escapeKeyword($op1)."~".$this->escapeKeyword($op2));
			}
		} else {
			if ($this->filterEntry == null) {
				$this->filterEntry = $fieldName .":" .$filerStr. ":" . $op1;
			} else {
				$this->filterEntry .= ("," .$fieldName . ":" . $filerStr . ":" . $op1);
			}
		}
		return $this;
	}

	public function addGroupEntry($fieldName, $groupOp, 
			$groupSortOp=SearchQueryStringer::GROUP_SORT_KEY_ASC, 
			$sectionCount=-1,
			$aggregateField="") {
		$groupStr = null;
		$groupSortStr = null;

		if ($groupSortOp == SearchQueryStringer::GROUP_SORT_FREQ_ASC) {
			$groupSortStr = "freq_asc";
		} else if ($groupSortOp == SearchQueryStringer::GROUP_SORT_FREQ_DESC) {
			$groupSortStr = "freq_desc";
		} else if ($groupSortOp == SearchQueryStringer::GROUP_SORT_KEY_ASC) {
			$groupSortStr = "key_asc";
		} else if ($groupSortOp == SearchQueryStringer::GROUP_SORT_KEY_DESC) {
			$groupSortStr = "key_desc";
		}

		if ($groupOp == SearchQueryStringer::GROUP_FREQ) {
			$groupStr = "freq";
			if ($this->groupEntry == null) {
				$this->groupEntry = $fieldName . ":" . $groupStr . ":" . $groupSortStr;
			} else {
				$this->groupEntry .= ("," . $fieldName . ":" . $groupStr . ":" . $groupSortStr);
			}
		} else if ($groupOp == SearchQueryStringer::GROUP_SECTION_FREQ) {
			$groupStr = "section_freq";
			if ($this->groupEntry == null) {
				$this->groupEntry = $fieldName . ":" . $groupStr . ":" . $sectionCount . ":" . $groupSortStr;
			} else {
				$this->groupEntry .= ("," . $fieldName . ":" . $groupStr . ":" . $sectionCount . ":" . $groupSortStr);
			}
		} else if ($groupOp == SearchQueryStringer::GROUP_SUM || $groupOp == SearchQueryStringer::GROUP_MAX) {
			if ($groupOp == SearchQueryStringer::GROUP_SUM)
				$groupStr = "sum";
			if ($groupOp == SearchQueryStringer::GROUP_MAX)
				$groupStr = "max";
			$this->groupEntry .= ("," . $fieldName . ":" . $groupStr . ";" . $aggregateField);
		}

		return $this;
	}

	public function getSeEntry() {
		return $this->searchEntry;
	}

	public function getVersion() {
		return VERSION;
	}

	public function getCollection() {
		return $this->collection;
	}

	public function clearSeEntry() {
		$this->searchEntry = null;
		return $this;
	}

	public function clearFilterEntry() {
		$this->filterEntry = null;
		return $this;
	}

	public function clearGroupEntry() {
		$this->groupEntry = null;
		return $this;
	}

	// rankingEntry
	public function clearRankingEntry() {
		$this->rankingEntry = null;
		return $this;
	}

	public function getQueryString() {
		$sb = "";
		$sb.="sn=".$this->start."&ln=".$this->length;
		$sb.="&cn=".$this->collection;
		if ($this->highlightEntry != null) {
			$sb.="&ht=".$this->highlightEntry;
		}
		if ($this->fieldListEntry != null) {
			$sb.="&fl=".$this->fieldListEntry;
		}
		if ($this->searchEntry != null) {
			$sb.="&se=".$this->searchEntry;
		}

		if ($this->filterEntry != null) {
			$sb.="&ft=".$this->filterEntry;
		}

		if ($this->groupEntry != null) {
			$sb.="&gr=".$this->groupEntry;
		}

		if ($this->rankingEntry != null) {
			$sb.="&ra=".$this->rankingEntry;
		}

		if ($this->userDataEntry != null) {
			$sb.="&ud=".$this->userDataEntry;
		}

		return $sb;
	}
}

class FastcatCommunicator {

	private $baseAddr;

	public function FastcatCommunicator($baseAddr) {
		$this->baseAddr = $baseAddr;
	}

	public function communicate($url,$query,$tail="") {
		$method="GET";
		if($tail) {
			$query.=$tail;
		}
	
		$url = parse_url($this->baseAddr.$url."?".$query); 
		if (!$url) return "couldn't parse url";
		if (!isset($url["port"])) { $url["port"] = ""; }
		if (!isset($url["query"])) { $url["query"] = ""; }
		$fp = fsockopen($url["host"], $url["port"] ? $url["port"] : 80);
		if (!$fp) return "can not connect to ".$url["host"];

		fputs($fp, sprintf("$method %s%s%s HTTP/1.0\n", $url["path"], $url["query"] ? "?" : "", $url["query"]));
		fputs($fp, "Host: $url[host]\n");
		fputs($fp, "Content-type: application/x-www-form-urlencoded\n");
		fputs($fp, "Connection: close\n\n");

		$line = fgets($fp);
		if (!@eregi("^HTTP/1\.. 200", $line)) return;
		fgets($fp);
		fgets($fp);
		fgets($fp);
		$results = ""; 
		while(!feof($fp)) {
			$results .= fgets($fp);
		}
		fclose($fp);
		return $results;
	}
}

class PageNavigator {
	//-----------------------------------필드---------------------------------------
	var $todayTotalRecord=0;//금일 등록된 레코드 갯수를저장
	var $totalRecord=0;		//전체 레코드의 갯수를 저장
	var $totalPage=0;		//전체 페이지의 갯수를 저장
	var $rowsOfScreen=0;	//한 화면에 표현 가능한 줄의 갯수
	var $navOfScren=0;		//한 화면에 표현 가능한 내비게이터의 갯수

	//----------------------------------생성자--------------------------------------
	public function PageNavigator($rowsOfScreen,$navOfScreen) {
		$this->rowsOfScreen=$rowsOfScreen;
		$this->navOfScreen=$navOfScreen;
	}

	//----------------------------------메서드--------------------------------------

	//--전체 레코드의 갯수를 입력받아 화면단위로 나누어 페이지 갯수를 계산해 냄
	public function setTotal($totalRecord) {
		//전체 레코드를 
		$this->totalRecord=$totalRecord;
		//화면단위로 나누어 페이지 갯수 계산
		//Trick : 올림수를 계산하기 위해 10을 곱하고 9를 더한뒤 다시 10으로 나눔
		//이렇게 함으로서 나누어 소숫점이하 자리수를 무조건 정수로 올려받는다.
		//원래의 계산은 totalRecord / rowsOfScreen
		if($this->rowsOfScreen != 0) {
			$this->totalPage=ceil($totalRecord / $this->rowsOfScreen);
		} else {
			$this->totalPage=0;	
		}
	}

	//--전체 레코드 갯수를 반환
	public function getTotal() {
		return $this->totalRecord;
	}

	//--금일 등록된 레코드 저장 (페이지 네비게이터와 관계없음)
	public function setTodayTotal($todayTotalRecord) {
		$this->todayTotalRecord=$todayTotalRecord;
	}

	public function getTodayTotal() {
		return $this->todayTotalRecord;
	}

	//--전체 페이지수를 반환
	public function getTotalPage() {
		return $this->totalPage;
	}

	//--현재 전체 레코드중 몇페이지 째 위치해 있는가를 알아보는 메서드
	public function currentPage($rowNumber) {
		//현재 출력되는 rowNumber가 몇번째 위치에 있는가 계산 후 
		//페이지로 나누어 지금 몇번째 페이지에 위치해 있는가를 알아낸다
		//생성자에서 쓰인 트릭을 그대로 이용한다.
		//원래의 계산은 (totalRecord - rowNumber) / rowsOfScreen
		return floor(($rowNumber+$this->rowsOfScreen)/$this->rowsOfScreen);
	}

	//--현재 페이지를 출력하려면 몇번째 데이터부터 출력해야 하는가를 계산
	public function endRow($pageNumber) {
		//현재 페이지를 입력받아 화면단위로 곱한후 전체 레코드갯수에서 빼면
		//현재 페이지의 시작위치를 구할수 있다
		//※ 게시판 순서는 위로갈수록 증가하기 때문에 전체 레코드 갯수에서
		// 지나간(상단의 지나온) 페이지의 데이터 갯수만큼만 빼준다.
		$ret=$pageNumber*$this->rowsOfScreen;
		//if($ret > $this->totalRecord) $ret=$this->totalRecord;
		return $ret;
	}

	//--현재 페이지의 마지막 데이터가 몇번째 인지 계산
	public function startRow($pageNumber) {
		//시작페이지에 화면단위를 빼준다(위로갈수록 순서값이 증가하기 때문..)
		$end=$this->endRow($pageNumber);
		$ret=$end-$this->rowsOfScreen;
		if($ret <0) $ret=0;
		return $ret;
	}

	public function getRows() {
		return $this->rowsOfScreen;
	}

	//--하단부 내비게이션 바의 첫번째 위치할 페이지의 번호를 결정
	public function getPageMargine ($pageNumber) {
		//내비게이션 바는 처음에 1페이지부터 시작하며 점점 오른쪽으로 갈수록
		//위치에 맞추어 시작위치가 증가한다. 즉
		//    ◀[1][2] 3 [4][5]▶   여기서 4번을 클릭하면
		//    ◀[2][3] 4 [5][6]▶   이렇게
		//이런식으로 현재 페이지가 3에서 4로 증가될 때 맨 앞의 내비게이션 
		//숫자가 같이 증가하게 된다
		$halfLine= floor($this->navOfScreen/2);
		$st=$pageNumber-$halfLine;
		if($st < 1) { $st=1; }
		if($this->totalPage > $this->navOfScreen) {
			$end=$st+$this->navOfScreen-1;
			if($end > $this->totalPage)  {
				$st=$this->totalPage-$this->navOfScreen+1;
				$ed=$this->totalPage;
			}
		}else{
			$st=1;
			$ed=$this->totalPage;
			if($ed==0) { $ed=1; }
		}
		$ret[0]=$st;
		$ret[1]=$ed;
		return $ret;
	}

	//--하단부 내비게이션 바의 첫번째 위치할 페이지의 번호를 결정
	public function startPage($pageNumber) {
		//내비게이션 바는 처음에 1페이지부터 시작하며 점점 오른쪽으로 갈수록
		//위치에 맞추어 시작위치가 증가한다. 즉
		//    ◀[1][2] 3 [4][5]▶   여기서 4번을 클릭하면
		//    ◀[2][3] 4 [5][6]▶   이렇게
		//이런식으로 현재 페이지가 3에서 4로 증가될 때 맨 앞의 내비게이션 
		//숫자가 같이 증가하게 된다
		$halfLine= floor($this->navOfScreen/2);
		$ret=$pageNumber-$halfLine;
		if($ret < 1) $ret=1;
		$end=$ret+$this->navOfScreen-1;
		if($this->totalPage > $this->navOfScreen) {
			if($end > $this->totalPage) $ret=$this->totalPage-$this->navOfScreen+1;
		}else
			$ret=1;
		return $ret;
	}

	//--내비게이션이 끝나는 위치계산. 시작위치에 내비게이션 갯수를 더해줌
	public function endPage($pageNumber) {
		$ret=$this->startPage($pageNumber)+$this->navOfScreen;
		//전체 페이지 갯수가 내비게이션 바 보다 작을경우는
		//있는만큼만 표시;
		if($this->totalPage < $this->navOfScreen)
			$ret=$this->totalPage;
		else
			if($ret > $this->totalPage)
				$ret=$this->totalPage+1;
		return $ret;
	}

	//--다음페이지의 위치 계산
	public function nextPage($pageNumber) {
		//다음페이지가 전체페이지보다 크면 마지막페이지 리턴
		$ret=$pageNumber+1;
		if($ret > $this->totalPage) $ret=$this->totalPage;
		if(!$pageNumber) $ret=1;
		if($ret < 1) $ret=1;
		return $ret;
	}

	//--이전페이지의 위치 계산
	public function prevPage($pageNumber) {
		//이전페이지가 1페이지보다 작으면 1리턴
		$ret=$pageNumber-1;
		if(!$pageNumber) $ret=1;
		if($ret < 1) $ret=1;
		return $ret;
	}

	//--다음페이지가 있는지 계산
	public function isNext($pageNumber) {
		if($ret < $this->totalPage) return true;
		return false;
	}

	//--이전페이지가 있는지 계산
	public function isPrev($pageNumber) {
		if($ret > 1) return true;
		return false;
	}

	//--레코드번호얻기
	public function rownum($page,$index) {
		$ret=$this->getTotal()-(($page-1)*$this->getRows())-$index;
		return $ret;
	}
}
?>
