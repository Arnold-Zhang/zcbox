<?php
function getPages($total, $curpage, $nums, $baseurl, $lang = ['prev'=>"上一页",'next' => "下一页"], $showwindow = ''){
	$page['range'] = 2;
	$page['max'] = ceil($total / $nums);

	$pagehtm = '<nav aria-label="Page navigation">
  <ul class="pagination">';

	$page['start'] = $curpage - $page['range'] > 0 ? $curpage - $page['range'] : 0 ;
	$page['end'] = $page['start']  + $page['range'] * 2 < $page['max'] ? $page['start']  + $page['range'] * 2 : $page['max'] - 1;
	$page['start'] = $page['end'] - $page['range'] * 2 > 0 ? $page['end'] - $page['range'] * 2 : 0;

	if($page['start'] > 1){
		$url = $baseurl."&page=1";
		$pagehtm.= '<li><a href="'.$url.'"'. $showwindow .'>1</a></li>';
		$pagehtm.= '<li>...</li>';
	}elseif($page['start'] == 1){
		$url = $baseurl."&page=1";
		$pagehtm.= '<li><a href="'.$url.'"'. $showwindow .'>1</a></li>';
	}


	for($i = $page['start']; $i <= $page['end']; $i++){
		$url = $baseurl."&page=".($i + 1);
		if($curpage == $i){
			$pagehtm.= '<li class="current"><a href="'.$url.'"'. $showwindow .'>'.($i + 1).'</a></li>';
		}else{
			$pagehtm.= '<li><a href="'.$url.'"'. $showwindow .'>'.($i + 1).'</a></li>';
		}
	}

	if($page['end'] + 2 == $page['max']){
		$url = $baseurl."&page=".$page['max'];
		$pagehtm.= '<li><a href="'.$url.'"'. $showwindow .'>'.$page['max'].'</a></li>';
	}elseif($page['end'] + 2 < $page['max']){
		$url = $baseurl."&page=".$page['max'];
		$pagehtm.= '<li>...</li>';
		$pagehtm.= '<li><a href="'.$url.'"'. $showwindow .'>'.$page['max'].'</a></li>';
	}

	$pagehtm.= ' </ul>
</nav>';

	return $pagehtm;
}
?>
