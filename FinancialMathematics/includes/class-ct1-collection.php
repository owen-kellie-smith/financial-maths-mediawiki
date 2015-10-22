<?php   

abstract class CT1_Collection extends CT1_Object {

	protected $objects;
	protected $class; // read-only, set by first object;

	protected function is_acceptable_class( $c ){
		return true;
		// this test is over-written by the child classes
	}

	public function is_in_collection( $candidate ){
		if ( $this->get_count() > 0) {
			foreach ( $this->get_objects() as $obj ){
				if ( $obj == $candidate )
					return true;
			}
		}
		return false;
	}

	public function __toString()
	{
		$return = array();
		if ( $this->get_count() > 0 ) {
			$o = $this->get_objects();
			foreach ( array_keys( $o ) as $key ){
				$return[ $key ] = print_r( $o[ $key ], 1 );
			}
		}
		return print_r( $return, 1);
	}

	public function get_values_as_array( $name_label = "CT1_collection" ){
		$hidden = array();
		if ( count( $this->get_values() ) > 0 ) {
			$i = 0;
			foreach ($this->get_values() as $v ){
				if ( is_array( $v ) ){
					foreach (array_keys( $v ) as $key){
						$name =  $name_label . "[" . $i . "][" . $key . "]";
						$value = $v[ $key ];
						$hidden[ $name ] = $value;
					}
					$i++;
				}
			}
		}
		return $hidden;
	}

	public function get_values(){ 
		$r = array();
		$o = $this->get_objects();
		if ( 0 < $this->get_count() ){
			foreach ( array_keys( $o ) as $key ){
				$r[ $key ] = $o[ $key ]->get_values();
			}
		}
		return $r; 
	}
	
	public function get_count(){
		return count( $this->get_objects() );
	}

	public function get_objects(){
		return $this->objects;
	}

	public function set_objects( $array ){
		$this->objects = $array;
	}

	public function add_object( CT1_Object $c, $duplicates_allowed = false, $re_sort = false ){
		if( !$this->is_acceptable_class( $c ) ){
			throw new Exception( __FILE__ . self::myMessage( 'fm-error-invalid-object', get_class( $c ), get_class( $this )));
		}
		if( 0 == $this->get_count() ){
			$this->class = get_class( $c );
		}
		if( get_class( $c ) != $this->class ){
			throw new Exception( __FILE__ . self::myMessage( 'fm-error-invalid-object', get_class( $c ), $this->class )  );
		}
		if ( !$duplicates_allowed ) {
			$this->remove_object( $c );
		}
		if ( method_exists( $c, 'get_index' ) ){
			$this->objects[ $c->get_index() ] = $c;
		} else {
			$this->objects[] = $c;
		}
		if ( $re_sort )
			$this->sort_objects();
	}

	public function sort_objects(){
		$old_objects = $this->get_objects();
		$sorted_keys = array_keys( $old_objects );
		sort( $sorted_keys );
		if ( count( $sorted_keys ) > 0 ){
			foreach ( $old_objects as $o ){
				$this->remove_object( $o );
			}
			foreach ( $sorted_keys as $key ){
				$this->add_object( $old_objects[ $key ] );
			}
		return;
		}
	}


	public function remove_object( CT1_Object $c, $remove_all = false ){
		if ( 0 < $this->get_count() ){
			$this_objects = $this->get_objects();
			foreach ( array_keys($this_objects) as $key ){
				if ( $c == $this_objects[ $key ] ){
					unset( $this->objects[ $key ] );
					if ( !$remove_all )
						return;
				}
			}
		}
	}
	

}


