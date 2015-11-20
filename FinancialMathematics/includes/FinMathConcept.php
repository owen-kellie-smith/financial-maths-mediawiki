<?php

interface CT1_Concept{
	public function get_calculator( $parameters );
	public function get_solution();
	public function get_controller( $_INPUT);
	public function set_obj( CT1_Object $parameters );
}
