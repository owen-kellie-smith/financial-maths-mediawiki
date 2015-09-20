<?php   
//require_once 'class-ct1-format.php';
define("_NEGLIGIBLE",0.0000000001);

class CT1_Marker{

public function score($actual, $guess){
      if ($actual<0) {
        $M = new CT1_Marker();
        return $M->score(-$actual, -$guess);
      }
      $_available = $this->no_sig_fig($actual);
      $_score = intval(round($_available * $this->score_answer($actual, $guess),0));
      return array('credit'=>$_score, 'available' => $_available);
}


public function yourscore($_score, $_available){
   if ( is_user_logged_in() ) { 
      $RT = $this->testscore();
      $out ="<p>You scored " . $_score . " out of " . $_available . " for your answer.";
      $out.="  Your running total is " . $RT['sumcredit'] . " out of " . $RT['sumavailable'] . ".</p>";
    } 
    else{
      if ($_score > 0){
      $out.="<p>If you had <a href=" . wp_login_url( current_page_url() )  . ">logged in</a> you would have scored " . $_score . " out of " . $_available . " for your answer.</p>";
      }
    }      
    return $out;
}

public function insert_mark( $questionid, $credit, $available) {
   global $wpdb;
   $table_name = $wpdb->prefix . "ct1";

   global $current_user;
   $current_user = wp_get_current_user();
   $userID = $current_user->ID;
   $rows_affected = $wpdb->insert( $table_name, array( 'time' => current_time('mysql'), 'userid' => $userID, 'questionid' => $questionid, 'credit' => $credit, 'available' => $available ) );
}


protected function score_answer($correct, $attempt){
	// return 1 for exact, otherwise a fraction from 0 to 1 for 'close'
	$n = $this->no_sig_fig($correct);
	if ($this->round_sig_fig($attempt, $n)==$correct) return 1;
	for ($i=$n-1; $i>0; $i--){
		if ($this->round_sig_fig($attempt, $i)==$this->round_sig_fig($correct,$i)) return $i/$n;
	}
	return 0;
}

public function no_dps($d){
	for ($i=0; $i<30; $i++){
		if (abs($d-round($d, $i)) < _NEGLIGIBLE ) return $i;
	}
	return 30;
}


public function no_sig_fig($d){
	if ($d < 0) return $this->no_sig_fig(-$d);
	for ($i=1; $i<30; $i++){
		if (abs($d-$this->round_sig_fig($d, $i)) < _NEGLIGIBLE ) return $i;
	}
	return 30;
}

protected function round_sig_fig($d, $n){
     	if ($d==0) return 0;
        if ($d<0) return -$this->round_sig_fig(-$d, $n);
  	return round($d, ceil(0 - log10($d)) + $n - 1); 
}


/*
function showsf($number){
	if ($number==0) return 0;
	$sf = no_sig_fig($number);
	$l = 1 + floor(log10(abs($number)));
	if ($l >= $sf){ return (int)$number;}
 	else{
		$m = pow(10,$sf-$l);
		$i = (int)($number * $m);
		return $i / $m;
	}
}
*/


protected function testscore(){
   global $wpdb;
   $table_name = $wpdb->prefix . "ct1";
   global $userdata;
   $score_SQL = $wpdb->prepare("SELECT SUM(credit) AS sumcredit, SUM(available) AS sumavailable FROM $table_name WHERE userid = %d", $userdata->ID);
  $score_res = $wpdb->get_row($score_SQL);
  return array( 'sumcredit'=>$score_res->sumcredit, 'sumavailable'=>$score_res->sumavailable);
}

/* 
public function tempSF($d){
   return $this->no_sig_fig($d);
}

public function tempDP($d){
   return $this->no_dps($d);
}
*/

}

/*
//TEST 
$m = new CT1_Marker();
echo $m->tempSF(0.005)."\r\n";
echo $m->tempDP(0.005)."\r\n";
echo $m->tempDP(0.005567798)."\r\n";
*/
