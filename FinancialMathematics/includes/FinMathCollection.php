<?php   
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2015 Owen Kellie-Smith
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @file
 */

/**
 * Abstract Collection object that holds groups of objects of the same class
 */
abstract class FinMathCollection extends FinMathObject {

	protected $objects;
	protected $class; // read-only, set by first object;

	protected function is_acceptable_class( $c ){
		return true;
		// this test is over-written by the child classes
	}

/**
 * A candidate is in the collection if it has the same value as one of the objects in the collection
 * 
 * @param $candidate
 * @return bool
 *
 */
	public function is_in_collection( $candidate ){
		if ( $this->get_count() > 0) {
			foreach ( $this->get_objects() as $obj ){
				if ( $this->is_acceptable_class( $candidate ) && $obj == $candidate ){
					return true;
				}
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

	public function get_values_as_array( $name_label = "FinMathCollection" ){
		$hidden = array();
		if ( count( $this->get_values() ) > 0 ) {
			$i = 0;
			foreach ($this->get_values() as $v ){
				if ( is_array( $v ) ){
					foreach (array_keys( $v ) as $key){
						$name =  $name_label . "[" . $i . "][" . $key . "]";
						$value = $v[ $key ];
						$hidden[ $name ] = $value;
						$name = null;
						$value = null;
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

	public function add_object( FinMathObject $c, $duplicates_allowed = false, $re_sort = false ){
		if( !$this->is_acceptable_class( $c ) ){
			throw new Exception( __FILE__ . 
				self::myMessage( 'fm-error-invalid-object', 
					get_class( $c ), 
					get_class( $this )		
				)
			);
		}
		if( 0 == $this->get_count() ){
			$this->class = get_class( $c );
		}
		if( get_class( $c ) != $this->class ){
			throw new Exception( __FILE__ . 
				self::myMessage( 'fm-error-invalid-object', 
					get_class( $c ), 
					$this->class 
				)  
			);
		}
		if ( !$duplicates_allowed ) {
			$this->remove_object( $c );
		}
		if ( method_exists( $c, 'get_index' ) ){
			$this->objects[ $c->get_index() ] = $c;
		} else {
			$this->objects[] = $c;
		}
		if ( $re_sort ){
			$this->sort_objects();
		}
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

	public function remove_object( FinMathObject $c, $remove_all = false ){
		if ( 0 < $this->get_count() ){
			$this_objects = $this->get_objects();
			foreach ( array_keys($this_objects) as $key ){
				if ( $c == $this_objects[ $key ] ){
					unset( $this->objects[ $key ] );
					if ( !$remove_all ){
						return;
					}
				}
			}
		}
	}
	

}

