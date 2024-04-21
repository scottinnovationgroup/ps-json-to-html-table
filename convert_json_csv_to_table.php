<?php

function sort_by_start_date($arr,$sort=SORT_ASC) {
	$dates = array_column($arr, 4);
	$rank = array_column($arr, 'rank');

	array_multisort($dates, $sort, $arr);

	return $arr;
}

function create_level_array($data, $level = '') {
	$data = json_decode($data);
	$arr = [];
	$arr_mod = [];
	$i = 0;

	switch ($level) {
		case 'portfolio':
			$rank = 1;
			break;
		case 'program':
			$rank = 2;
			break;
		case 'project':
			$rank = 3;
			break;
		case 'milestone':
			$rank = 4;
			break;
		default:
			$rank = '';
	}
	
	foreach($data as $key=>$value) {
		if($value[14] == $level) {
			$value['rank'] = $rank;
			array_push($arr, $value);
		}
	}

	unset($key,$value);

	foreach($arr[0] as $key=>$value) {
		if(in_array($key, array(0,1,2,4,5,7,8))) {
			array_push($arr_mod, $value);
		}
		$i++;
	}

	return $arr;
}

function create_merge_array($post){
	$header = create_level_array($post,'_type');
	$portfolio = create_level_array($post,'portfolio');
	$program = create_level_array($post,'program');
	$project = create_level_array($post,'project');
	$milestone = create_level_array($post,'milestone');
	$merge = [];

	$merge_ms = [];
	$merge_prj = [];
	$merge_prgm = [];
	$merge_port = [];
	
	foreach($portfolio as $port) {
		array_push($merge_port, $port);

		foreach($program as $prgm) {
			if($prgm[13] == $port[0]) {
				array_push($merge_prgm, $prgm);

				foreach($project as $prj) {
					if($prj[13] == $prgm[0]) {
						array_push($merge_prj, $prj);

						foreach($milestone as $ms) {
							if($ms[13] == $prj[0]) {
								array_push($merge_ms, $ms);
								$merge_ms = sort_by_start_date($merge_ms);
							}
						}
					$merge_prj = sort_by_start_date($merge_prj);
					array_push($merge_prj, $merge_ms);
					$merge_ms = [];
					}
				}
			$merge_prgm = sort_by_start_date($merge_prgm);
			array_push($merge_prgm, $merge_prj);
			$merge_prj = [];
			}
		}
		$merge_port = sort_by_start_date($merge_port);
		array_push($merge_port, $merge_prgm);
		$merge_prgm = [];
	}

	return array_merge($header,$merge_port);
}

function fix_array_level($arr) {

	$result = [];

	foreach($arr as $key=>$value) {
		
		if(count($value) != 17) {

			if(is_array($value)) {
				$result =  array_merge($result, fix_array_level($value));
			}
		} else {
			$result[] = $value;
			
		}
	}

	return $result;

}


// Begin HTML Table Rendering

$i = 0;

print '<table class="pure-table pure-table-bordered">';

foreach(fix_array_level(create_merge_array($_POST['data'])) as $row=>$col) {

	$type = $col[14];
	

	if($i == 0){
		print '<thead><tr>';
	} else if($i == 1) {
		print '<tbody><tr class="'.$type.'">';
	} else {
		print '<tr class="'.$type.'">';
	}

	foreach($col as $key=>$cell) {

		if($key != 'rank') {
			if(in_array($key, array(0,1,2,4,5,7,8))) {
				if($i == 0){
					switch ($cell) {
						case 'program/project/milestone':
							$cell = 'Activity';
							break;
						case 'start date':
							$cell = 'Start Date';
							break;
						case 'target completion date':
							$cell = 'Complete Date';
							break;
						case 'estimated cost minimum':
							$cell = 'Min. Cost';
							break;
						case 'estimated cost maximum':
							$cell = 'Max. Cost';
							break;
					}

					print '<th>'.ucwords($cell).'</th>';
				} else {
					if(in_array($key, array(7,8))) {
						print '<td>$'.number_format(preg_replace('/[$,]/', '', $cell)).'</td>';	
					} else {
						print '<td>'.$cell.'</td>';
					}
				}
			}
		}
		
	}

	if($i == 0){
		print '</tr></thead>';
	} else {
		print '</tr>';
	}

	$i++;

}

print '</tbody></table>';

// End HTML Table Rendering