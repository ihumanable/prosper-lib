<?php
namespace Prosper;

class Token {
	const SQL_ENTITY  = "SQL_ENTITY";
	const LOGICAL_OP  = "LOGICAL_OP";
	const COMPARISON  = "COMPARISON";
	const OPEN_PAREN  = "OPEN_PAREN";
	const CLOSE_PAREN = "CLOSE_PAREN";
	const LITERAL     = "LITERAL";
	const PARAMETER   = "PARAMETER";
	const NAMED_PARAM = "NAMED_PARAM";
	const COMMA       = "COMMA";
	const KEYWORD     = "KEYWORD";
	
	static $whitespace = ' ';
	static $quote = "'";
	static $comma = ",";
	static $parameter = "?";
	static $named_param = ":";
	static $open_paren = '(';
	static $close_paren = ')';
	static $like_op = 'LIKE';
	static $comparisons = array('<', '>', '=', '!');
	static $logical = array('AND', 'OR', 'NOT');
	static $allowed = array('.', '_'); 
	static $keywords = array("IN", "BETWEEN");
	
	static function next(&$source) {
		$source = ltrim($source);
		$sample = $source[0];
		
		if(ctype_alnum($sample) || in_array($sample, self::$allowed)) {
			return self::sql_entity_parse($source);
		} else if(in_array($sample, self::$comparisons)) {
			return self::comparison_parse($source);
		} else if($sample == self::$quote) {
			return self::literal_parse($source);
		} else if($sample == self::$named_param) {
			return self::named_param_parse($source);		
		} else if($sample == self::$parameter) {
			return self::parse_char($source, self::PARAMETER);
		} else if($sample == self::$open_paren) {
			return self::parse_char($source, self::OPEN_PAREN);
		} else if($sample == self::$close_paren) {
			return self::parse_char($source, self::CLOSE_PAREN);
		} else if($sample == self::$comma) {
			return self::parse_char($source, self::COMMA);
		}
		
		return false;
		
	}
	
	static function generic_parse($source, $boundary_test) {
		$limit = strlen($source);
		
		for($i = 1; $i < $limit; ++$i) {
			if(call_user_func_array(array(__CLASS__, $boundary_test), array($source[$i - 1], $source[$i]))) {
				//Boundary has been found
				return $i;
			}
		}
		return $limit;
	}
	
	static function parse_token(&$source, $position) {
		if($position > strlen($source)) {
			$position = strlen($source);
		}
		$token = substr($source, 0, $position);
		$source = substr($source, $position);
		return $token;
	}

	static function parse_char(&$source, $type) {
		$result['type'] = $type;
		$result['token'] = substr($source, 0, 1);
		$source = substr($source, 1);
		return $result;
	}
	
	static function sql_entity_test($last, $char) {
		return !( ctype_alnum($char) ||
		          in_array($char, self::$allowed) );
		         
	}
	
	static function sql_entity_parse(&$source) { 
		$token = self::parse_token($source, self::generic_parse($source, "sql_entity_test"));
		
		if(in_array(strtoupper($token), self::$logical)) {
			$result['type'] = self::LOGICAL_OP;
		} else if(strtoupper($token) == self::$like_op) {
			$result['type'] = self::COMPARISON;
		} else if(in_array(strtoupper($token), self::$keywords)) {
			$result['type'] = self::KEYWORD;
		} else {
			$result['type'] = self::SQL_ENTITY;
		}
		
		$result['token'] = $token;
	
		return $result;
	}
		
	static function comparison_test($last, $char) {
			return !in_array($char, self::$comparisons);
	}	

	static function comparison_parse(&$source) {
		$result['type'] = self::COMPARISON;
		$result['token'] = self::parse_token($source, self::generic_parse($source, "comparison_test"));
		return $result;
	}

	static function literal_test($last, $char) {
		return $char == self::$quote && $last != self::$escape;
	}

	static function literal_parse(&$source) {
		$result['type'] = self::LITERAL;
		$result['token'] = self::parse_token($source, self::generic_parse($source, "literal_test") + 1);
		return $result;
	}

	static function named_param_parse(&$source) {
		$result['type'] = self::NAMED_PARAM;
		$result['token'] = self::parse_token($source, self::generic_parse($source, "sql_entity_test"));
		return $result;
	}

}


?>