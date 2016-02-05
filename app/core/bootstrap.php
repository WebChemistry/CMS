<?php

require_once __DIR__ . '/error/Error.php';
// Own error template
if (!Tracy\Debugger::$errorTemplate) {
	Tracy\Debugger::$errorTemplate = __DIR__ . '/error/template.phtml';
}
/**
 * Tracy\Debugger::barDump() shortcut.
 *
 * @tracySkipLocation
 * @param mixed $var
 * @param int $length
 * @param int $depth
 */
function bb($var, $length = NULL, $depth = NULL) {
	$backtrace = debug_backtrace();
	if (isset($backtrace[1]['class'])) {
		$source = $backtrace[1]['class'] . '::' . $backtrace[1]['function'];
	} else {
		$source = basename($backtrace[0]['file']);
	}
	$line = $backtrace[0]['line'];
	Tracy\Debugger::barDump($var, $source . ' (' . $line . ')', [
		Tracy\Dumper::TRUNCATE => $length ? : Tracy\Debugger::$maxLen,
		Tracy\Dumper::DEPTH => $depth ? : Tracy\Debugger::$maxDepth
	]);
}

/**
 * Tracy\Debugger::dump() shortcut.
 *
 * @tracySkipLocation
 * @param mixed $var
 * @param int $length
 * @param int $depth
 */
function dd($var, $length = NULL, $depth = NULL) {
	Tracy\Debugger::dump($var, [
		Tracy\Dumper::TRUNCATE => $length ? : Tracy\Debugger::$maxLen,
		Tracy\Dumper::DEPTH => $depth ? : Tracy\Debugger::$maxDepth
	]);
}

/**
 * @param string $name
 */
function timer($name = NULL) {
	Tracy\Debugger::timer($name);
}

/**
 * @param string $name
 */
function endTimer($name = NULL) {
	bb(Tracy\Debugger::timer($name));
}
