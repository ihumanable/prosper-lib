<?php
/**
 *  base include file for SimpleTest
 *  @package    SimpleTest
 *  @subpackage UnitTester
 *  @version    $Id: reporter.php 1702 2008-03-25 00:08:04Z lastcraft $
 */

/**#@+
 *  include other SimpleTest class files
 */
require_once(dirname(__FILE__) . '/scorer.php');
/**#@-*/

/**
 *    Sample minimal test displayer. Generates only
 *    failure messages and a pass count.
 *    @package SimpleTest
 *    @subpackage UnitTester
 */
class HtmlReporter extends SimpleReporter {
    var $_character_set;
    var $last_pass = 0;
    var $last_fail = 0;
    var $last_excs = 0;

    /**
     *    Does nothing yet. The first output will
     *    be sent on the first test start. For use
     *    by a web browser.
     *    @access public
     */
    function HtmlReporter($character_set = 'ISO-8859-1') {
        $this->SimpleReporter();
        $this->_character_set = $character_set;
    }

    /**
     *    Paints the top of the web page setting the
     *    title to the name of the starting test.
     *    @param string $test_name      Name class of test.
     *    @access public
     */
    function paintHeader($test_name) {
        $this->sendNoCacheHeaders();
        list($name, $extension) = explode('.', $test_name);
        echo "<!DOCTYPE HTML>\n";
        echo "<html>\n";
        echo "\t<head>\n";
        echo "\t\t<title>$name</title>\n";
        echo "\t\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset=\"{$this->_character_set}\">\n";
        echo "\t\t<style type=\"text/css\">\n";
        echo $this->_getCss();
        echo "\t\t</style>\n";
        echo "\t</head>\n";
        echo "\t<body>\n";
        echo "\t\t<h1>$name [ ";
        echo "<span id=\"fail-message\"></span>";
        echo "<span id=\"pass-message\"></span>";
        echo " ]</h1>\n";
    }

    /**
     *    Send the headers necessary to ensure the page is
     *    reloaded on every request. Otherwise you could be
     *    scratching your head over out of date test data.
     *    @access public
     *    @static
     */
    function sendNoCacheHeaders() {
        if (! headers_sent()) {
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
        }
    }

    /**
     *    Paints the CSS. Add additional styles here.
     *    @return string            CSS code as text.
     *    @access protected
     */
    function _getCSS() {
      $tabs = "\t\t\t";
      return "$tabs html { font-family: sans-serif } \n" .
             "$tabs body { margin-left: 200px; margin-right: 200px; background: #D7D7D7 } \n" .
             "$tabs h1 { background: #000000; color: #FFFFFF; padding: 10px; margin: 0px; -moz-border-radius-topleft: 10px; -moz-border-radius-topright: 10px; } \n" .
             "$tabs pre { background-color: lightgray; color: inherit; } \n" .
             "$tabs #fail-message { display:none; font-weight: bold; color: #FF0000; } \n" . 
             "$tabs #pass-message { display:none; font-weight: bold; color: #528CE0; } \n" .
             "$tabs .case { border-bottom: 1px solid #000000; padding-left: 10px; padding-right: 10px; padding-top: 5px; padding-bottom: 5px;} \n" . 
             "$tabs .case span.case-label { font-weight: bold } \n" .
             "$tabs .case div { margin-left: 20px; margin-right: 20px; } \n" .
             "$tabs .pass { background: #D2E0E6; color: #528CE0; padding: 5px; margin-bottom: 1px; font-weight: bold; -moz-border-radius: 10px } \n" .
             "$tabs .pass span { color: #000000; } \n" .
             "$tabs .fail { background: #EE5757; color: #000000; padding: 5px; margin-bottom: 1px; font-weight: bold; -moz-border-radius: 10px } \n" .
             "$tabs .fail span { color: #000000; } \n" .
             "$tabs .fail div.message { color: #000000; padding-left: 10px } \n" .
             "$tabs .fail span.location { font-size: small; color: #000000; } \n" .
             "$tabs .skip { background: #000000; color: white; padding: 5px; margin-bottom: 1px; font-weight: bold; -moz-border-radius: 10px } \n" .
             "$tabs .footer { background: black; padding: 10px; -moz-border-radius-bottomleft: 10px; -moz-border-radius-bottomright: 10px; } \n";
             
    }
  
    function paintCaseStart($name) {
      parent::paintCaseStart($name);
      echo "\t\t<div id=\"case_{$this->getTestCaseProgress()}\" class=\"case\">\n";
      echo "\t\t\t<span class=\"case-label\">$name</span>\n";
      
    }
    
    function paintCaseEnd($name) {
      $pass = ($this->getPassCount() - $this->last_pass);
      $fail = ($this->getFailCount() - $this->last_fail); 
      $exception = ($this->getExceptionCount() - $this->last_excs);
      
      $color = ($fail + $exception > 0) ? '#FF0000' : '#528CE0';
      $javascript = "<script type=\"text/javascript\">document.getElementById('case_{$this->getTestCaseProgress()}').style['backgroundColor'] = '$color'</script>";
      
      parent::paintCaseEnd($name);
      echo "\t\t\t<span><strong>" . $this->getTestCaseProgress() . "/" . $this->getTestCaseCount() . "</strong>: ";
      echo "<strong>" . $pass . "</strong> passed | ";
      echo "<strong>" . $fail . "</strong> failed | ";
      echo "<strong>" . $exception . "</strong> exceptions</span>\n";
      echo $javascript;
      echo "\t\t</div>\n";
      $this->last_pass = $this->getPassCount();
      $this->last_fail = $this->getFailCount();
      $this->last_excs = $this->getExceptionCount();
      $this->section++;
    }
    
  
    /**
     * Helper method to display the message nicely
     * @param string $message Message to display
     * @param string $css CSS class to display with
     * @param string $label Label to display
     * @return null                    
     */         
    function displayMessage($message, $css, $label) { 
      $parts = explode(' at ', $message);
      $location = substr(array_pop($parts), 1, -1);
      $message = implode(' at ', $parts);
      
      echo "\t\t<div class=\"$css\">\n";
      echo "\t\t\t<span>$label</span>: " . array_pop($this->getTestList()) . " <span class=\"location\">(" . $this->_htmlEntities($location) . ")</span><br />\n";
      echo "\t\t\t<div class=\"message\">" . $this->_htmlEntities($message) . "</div>\n";
      echo "\t\t</div>\n";
    }

    /**
     * Display a passing test
     * @param string $message Message to display
     * @return null          
     */         
    function paintPass($message) {
      parent::paintPass($message);
      echo "\t\t<div class=\"pass\"><span>Pass</span>: " . array_pop($this->getTestList()) . "</div>\n";
    }

    /**
     * Display a failing test
     * @param string $message Message to display
     * @return null
     * @see HtmlReporter::displayMessage($message, $css, $label)               
     */
    function paintFail($message) {
      parent::paintFail($message);
      $this->displayMessage($message, 'fail', 'Fail');
    }
    
    /**
     * Display an Error
     * @param string $message Message to display
     * @return null
     * @see HtmlReporter::displayMessage($message, $css, $label)
     */                        
    function paintError($message) {
        parent::paintError($message);
        $this->displayMessage($message, 'fail', 'Error');
    }

    /**
     * Display an Exception
     * @param object $exception Exception to display
     * @return null
     * @see HtmlReporter::displayMessage($message, $css, $label)                    
     */
    function paintException($exception) {
        parent::paintException($exception);
        $message = 'Unexpected exception of type [' . get_class($exception) .
                '] with message ['. $exception->getMessage() .
                '] in ['. $exception->getFile() .
                ' line ' . $exception->getLine() . ']';
        $this->displayMessage($message, 'fail', 'Exception');
    }
    
    /**
     * Display a skipped test
     * @param string $message Message to display
     * @return null
     * @see HtmlReporter::displayMessage($message, $css, $label)          
     */
    function paintSkip($message) {
        parent::paintSkip($message);
        $this->displayMessage($message, 'skip', 'Skipped');
    }

    /**
     *    Paints formatted text such as dumped variables.
     *    @param string $message        Text to show.
     *    @access public
     */
    function paintFormattedMessage($message) {
        echo '<pre>' . $this->_htmlEntities($message) . '</pre>';
    }
    
    /**
     *    Paints the end of the test with a summary of
     *    the passes and failures.
     *    @param string $test_name        Name class of test.
     *    @access public
     */
    function paintFooter($test_name) {
        $fail = ($this->getFailCount() + $this->getExceptionCount() > 0); 
        $color = ($fail ? "#FF0000" : "#528CE0");
        $elem = ($fail ? "fail-message" : "pass-message");
        $count = ($fail ? $this->getFailCount() : $this->getPassCount());
        $message = $count . ($count == 1 ? ' test' : ' tests') . ($fail ? ' failed' : ' passed'); 
        echo "\t\t<div class=\"footer\" style=\"color: $color;\">\n";
        echo "\t\t\t<strong>Summary</strong>: ";
        echo "<strong>" . $this->getPassCount() . "</strong> passed | ";
        echo "<strong>" . $this->getFailCount() . "</strong> failed | ";
        echo "<strong>" . $this->getExceptionCount() . "</strong> exceptions.\n";
        echo "<script type=\"text/javascript\">var m = document.getElementById('$elem'); m.innerHTML = '$message'; m.style.display = 'inline'</script>";
        echo "\t\t</div>\n";
        echo "\t</body>\n";
        echo "</html>\n";
    }

    /**
     *    Character set adjusted entity conversion.
     *    @param string $message    Plain text or Unicode message.
     *    @return string            Browser readable message.
     *    @access protected
     */
    function _htmlEntities($message) {
        return htmlentities($message, ENT_COMPAT, $this->_character_set);
    }
}

/**
 *    Sample minimal test displayer. Generates only
 *    failure messages and a pass count. For command
 *    line use. I've tried to make it look like JUnit,
 *    but I wanted to output the errors as they arrived
 *    which meant dropping the dots.
 *    @package SimpleTest
 *    @subpackage UnitTester
 */
class TextReporter extends SimpleReporter {

    /**
     *    Does nothing yet. The first output will
     *    be sent on the first test start.
     *    @access public
     */
    function TextReporter() {
        $this->SimpleReporter();
    }

    /**
     *    Paints the title only.
     *    @param string $test_name        Name class of test.
     *    @access public
     */
    function paintHeader($test_name) {
        if (! SimpleReporter::inCli()) {
            header('Content-type: text/plain');
        }
        print "$test_name\n";
        flush();
    }

    /**
     *    Paints the end of the test with a summary of
     *    the passes and failures.
     *    @param string $test_name        Name class of test.
     *    @access public
     */
    function paintFooter($test_name) {
        if ($this->getFailCount() + $this->getExceptionCount() == 0) {
            print "OK\n";
        } else {
            print "FAILURES!!!\n";
        }
        print "Test cases run: " . $this->getTestCaseProgress() .
                "/" . $this->getTestCaseCount() .
                ", Passes: " . $this->getPassCount() .
                ", Failures: " . $this->getFailCount() .
                ", Exceptions: " . $this->getExceptionCount() . "\n";
    }

    /**
     *    Paints the test failure as a stack trace.
     *    @param string $message    Failure message displayed in
     *                              the context of the other tests.
     *    @access public
     */
    function paintFail($message) {
        parent::paintFail($message);
        print $this->getFailCount() . ") $message\n";
        $breadcrumb = $this->getTestList();
        array_shift($breadcrumb);
        print "\tin " . implode("\n\tin ", array_reverse($breadcrumb));
        print "\n";
    }

    /**
     *    Paints a PHP error or exception.
     *    @param string $message        Message to be shown.
     *    @access public
     *    @abstract
     */
    function paintError($message) {
        parent::paintError($message);
        print "Exception " . $this->getExceptionCount() . "!\n$message\n";
        $breadcrumb = $this->getTestList();
        array_shift($breadcrumb);
        print "\tin " . implode("\n\tin ", array_reverse($breadcrumb));
        print "\n";
    }

    /**
     *    Paints a PHP error or exception.
     *    @param Exception $exception      Exception to describe.
     *    @access public
     *    @abstract
     */
    function paintException($exception) {
        parent::paintException($exception);
        $message = 'Unexpected exception of type [' . get_class($exception) .
                '] with message ['. $exception->getMessage() .
                '] in ['. $exception->getFile() .
                ' line ' . $exception->getLine() . ']';
        print "Exception " . $this->getExceptionCount() . "!\n$message\n";
        $breadcrumb = $this->getTestList();
        array_shift($breadcrumb);
        print "\tin " . implode("\n\tin ", array_reverse($breadcrumb));
        print "\n";
    }
    
    /**
     *    Prints the message for skipping tests.
     *    @param string $message    Text of skip condition.
     *    @access public
     */
    function paintSkip($message) {
        parent::paintSkip($message);
        print "Skip: $message\n";
    }

    /**
     *    Paints formatted text such as dumped variables.
     *    @param string $message        Text to show.
     *    @access public
     */
    function paintFormattedMessage($message) {
        print "$message\n";
        flush();
    }
}

/**
 *    Runs just a single test group, a single case or
 *    even a single test within that case.
 *    @package SimpleTest
 *    @subpackage UnitTester
 */
class SelectiveReporter extends SimpleReporterDecorator {
    var $_just_this_case = false;
    var $_just_this_test = false;
    var $_on;
    
    /**
     *    Selects the test case or group to be run,
     *    and optionally a specific test.
     *    @param SimpleScorer $reporter    Reporter to receive events.
     *    @param string $just_this_case    Only this case or group will run.
     *    @param string $just_this_test    Only this test method will run.
     */
    function SelectiveReporter(&$reporter, $just_this_case = false, $just_this_test = false) {
        if (isset($just_this_case) && $just_this_case) {
            $this->_just_this_case = strtolower($just_this_case);
            $this->_off();
        } else {
            $this->_on();
        }
        if (isset($just_this_test) && $just_this_test) {
            $this->_just_this_test = strtolower($just_this_test);
        }
        $this->SimpleReporterDecorator($reporter);
    }

    /**
     *    Compares criteria to actual the case/group name.
     *    @param string $test_case    The incoming test.
     *    @return boolean             True if matched.
     *    @access protected
     */
    function _matchesTestCase($test_case) {
        return $this->_just_this_case == strtolower($test_case);
    }

    /**
     *    Compares criteria to actual the test name. If no
     *    name was specified at the beginning, then all tests
     *    can run.
     *    @param string $method       The incoming test method.
     *    @return boolean             True if matched.
     *    @access protected
     */
    function _shouldRunTest($test_case, $method) {
        if ($this->_isOn() || $this->_matchesTestCase($test_case)) {
            if ($this->_just_this_test) {
                return $this->_just_this_test == strtolower($method);
            } else {
                return true;
            }
        }
        return false;
    }
    
    /**
     *    Switch on testing for the group or subgroup.
     *    @access private
     */
    function _on() {
        $this->_on = true;
    }
    
    /**
     *    Switch off testing for the group or subgroup.
     *    @access private
     */
    function _off() {
        $this->_on = false;
    }
    
    /**
     *    Is this group actually being tested?
     *    @return boolean     True if the current test group is active.
     *    @access private
     */
    function _isOn() {
        return $this->_on;
    }

    /**
     *    Veto everything that doesn't match the method wanted.
     *    @param string $test_case       Name of test case.
     *    @param string $method          Name of test method.
     *    @return boolean                True if test should be run.
     *    @access public
     */
    function shouldInvoke($test_case, $method) {
        if ($this->_shouldRunTest($test_case, $method)) {
            return $this->_reporter->shouldInvoke($test_case, $method);
        }
        return false;
    }

    /**
     *    Paints the start of a group test.
     *    @param string $test_case     Name of test or other label.
     *    @param integer $size         Number of test cases starting.
     *    @access public
     */
    function paintGroupStart($test_case, $size) {
        if ($this->_just_this_case && $this->_matchesTestCase($test_case)) {
            $this->_on();
        }
        $this->_reporter->paintGroupStart($test_case, $size);
    }

    /**
     *    Paints the end of a group test.
     *    @param string $test_case     Name of test or other label.
     *    @access public
     */
    function paintGroupEnd($test_case) {
        $this->_reporter->paintGroupEnd($test_case);
        if ($this->_just_this_case && $this->_matchesTestCase($test_case)) {
            $this->_off();
        }
    }
}

/**
 *    Suppresses skip messages.
 *    @package SimpleTest
 *    @subpackage UnitTester
 */
class NoSkipsReporter extends SimpleReporterDecorator {
    
    /**
     *    Does nothing.
     *    @param string $message    Text of skip condition.
     *    @access public
     */
    function paintSkip($message) { }
}
?>