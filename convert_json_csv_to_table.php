<?php

function date_compare($element1, $element2) { 
    $datetime1 = strtotime($element1[4]); 
    $datetime2 = strtotime($element2[4]); 
    return $datetime1 - $datetime2; 
}

function create_level_array($data, $level = '') {
	$data = json_decode($data);
	$arr = [];
	$arr_mod = [];
	$i = 0;
	
	foreach($data as $key=>$value) {
		if($value[14] == $level) {
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
	
	foreach($portfolio as $port) {
		array_push($merge, $port);

		foreach($program as $prgm) {
			if($prgm[13] == $port[0]) {
				array_push($merge, $prgm);

				foreach($project as $prj) {
					if($prj[13] == $prgm[0]) {
						array_push($merge, $prj);

						foreach($milestone as $ms) {
							if($ms[13] == $prj[0]) {
								array_push($merge,$ms);
							}
						}
						usort($merge,'date_compare');
					}
				}
				usort($merge,'date_compare');
			}
		}
		usort($merge,'date_compare');
	}

	return array_merge($header,$merge);
}

// Begin HTML Table Rendering

$i = 0;

print '<table class="pure-table pure-table-bordered">';

foreach(create_merge_array($_POST['data']) as $row=>$col) {

	$type = $col[14];
	

	if($i == 0){
		print '<thead><tr>';
	} else if($i == 1) {
		print '<tbody><tr class="'.$type.'">';
	} else {
		print '<tr class="'.$type.'">';
	}

	foreach($col as $key=>$cell) {

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

	if($i == 0){
		print '</tr></thead>';
	} else {
		print '</tr>';
	}

	$i++;

}

print '</tbody></table>';

// End HTML Table Rendering