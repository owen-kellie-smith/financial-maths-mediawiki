<?php

interface FinMathConcept{
	public function get_calculator( $parameters );
	public function get_solution();
	public function get_controller( $_INPUT);
	public function set_obj( FinMathObject $parameters );
}
