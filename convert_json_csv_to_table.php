<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function iterate_array($arr) {
    $result = [];
    $i = 0;

    foreach($arr as $item) {
        if(count($item) != 17) {
            foreach($item as $key => $value) {
                $result[$i][] = $value;
            }
            $i++;
        }
    }

    return $result;
}

function iterate_array_convert_to_unix($date) {
    $result_arr = [];
    $result_str = '';

    if(is_array($date)) {
        foreach($date as $key=>$value) {
            $result_arr[] = strtotime($value);
        }
    } else {
        $result_str = strtotime($date);
    }

    if($result_arr) {
        return $result_arr;
    } else {
        return $result_str;
    }
}
function sort_by_start_date($arr,$sort=SORT_ASC) {

    $iterate = iterate_array($arr);

    foreach($arr as $key=>$value) {

        $count = count((array)$value);
        if($count != 17) {
            $dates = array_column($arr, 4);
            foreach($iterate as $itr_key=>$itr_value) {
                $dates[] = array_column($itr_value, 4);
            }
            break;
        } else {
            $dates = array_column($arr, 4);
        }
    }

    $dates_unix = [];
    foreach($dates as $date) {
        $dates_unix[] = iterate_array_convert_to_unix($date);
    }

    array_multisort($dates_unix, $sort, $arr);

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
		if($key == 0) {
            $value[14] = '_type';
        }

        if($value[14] == $level) {
            $value['rank'] = $rank;
            $arr[] = $value;
        }
	}

	unset($key,$value);

	foreach($arr[0] as $key=>$value) {
		if(in_array($key, array(0,1,2,4,5,7,8))) {
			$arr_mod[] = $value;
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
		$merge_port[] = $port;

		foreach($program as $prgm) {
			if($prgm[13] == $port[0]) {
				$merge_prgm[] = $prgm;

				foreach($project as $prj) {
					if($prj[13] == $prgm[0]) {
						$merge_prj[] = $prj;

						foreach($milestone as $ms) {
							if($ms[13] == $prj[0]) {
								$merge_ms[] = $ms;
								$merge_ms = sort_by_start_date($merge_ms);
							}
						}
					$merge_prj = sort_by_start_date($merge_prj);
					$merge_prj[] = $merge_ms;
					$merge_ms = [];
					}
				}
			$merge_prgm = sort_by_start_date($merge_prgm);
			$merge_prgm[] = $merge_prj;
			$merge_prj = [];
			}
		}
		$merge_port = sort_by_start_date($merge_port);
		$merge_port[] = $merge_prgm;
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

function check_similar_text($post_value,$exp_value,$exp_perc=75) {
    $sim = similar_text(strtolower($post_value), $exp_value, $perc);

    if($perc >= $exp_perc) {
        return $exp_value;
    } else {
        return null;
    }
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
					switch (strtolower($cell)) {
						case 'program/project/milestone':
							$cell = 'Activity';
							break;
						case 'portfolio/program/project/milestone':
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