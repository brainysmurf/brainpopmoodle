<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Unit tests for some mod brainpop lib stuff.
 *
 * @package    mod_brainpop
 * @category   phpunit
 * @copyright  2013 Anthony Kuske (anthonykuske.com). Based on URL module copyright 2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * mod_brainpop tests
 *
 * @package    mod_brainpop
 * @category   phpunit
 * @copyright  2013 Anthony Kuske (anthonykuske.com). Based on URL module copyright 2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_brainpop_lib_testcase extends basic_testcase {

    /**
     * Prepares things before this test case is initialised
     * @return void
     */
    public static function setUpBeforeClass() {
        global $CFG;
        require_once($CFG->dirroot . '/mod/brainpop/locallib.php');
    }

    /**
     * Tests the brainpop_appears_valid_url function
     * @return void
     */
    public function test_brainpop_appears_valid_url() {
        $this->assertTrue(brainpop_appears_valid_url('http://example'));
        $this->assertTrue(brainpop_appears_valid_url('http://www.example.com'));
        $this->assertTrue(brainpop_appears_valid_url('http://www.exa-mple2.com'));
        $this->assertTrue(brainpop_appears_valid_url('http://www.example.com/~nobody/index.html'));
        $this->assertTrue(brainpop_appears_valid_url('http://www.example.com#hmm'));
        $this->assertTrue(brainpop_appears_valid_url('http://www.example.com/#hmm'));
        $this->assertTrue(brainpop_appears_valid_url('http://www.example.com/žlutý koníček/lala.txt'));
        $this->assertTrue(brainpop_appears_valid_url('http://www.example.com/žlutý koníček/lala.txt#hmmmm'));
        $this->assertTrue(brainpop_appears_valid_url('http://www.example.com/index.php?xx=yy&zz=aa'));
        $this->assertTrue(brainpop_appears_valid_url('https://user:password@www.example.com/žlutý koníček/lala.txt'));
        $this->assertTrue(brainpop_appears_valid_url('ftp://user:password@www.example.com/žlutý koníček/lala.txt'));

        $this->assertFalse(brainpop_appears_valid_url('http:example.com'));
        $this->assertFalse(brainpop_appears_valid_url('http:/example.com'));
        $this->assertFalse(brainpop_appears_valid_url('http://'));
        $this->assertFalse(brainpop_appears_valid_url('http://www.exa mple.com'));
        $this->assertFalse(brainpop_appears_valid_url('http://www.examplé.com'));
        $this->assertFalse(brainpop_appears_valid_url('http://@www.example.com'));
        $this->assertFalse(brainpop_appears_valid_url('http://user:@www.example.com'));

        $this->assertTrue(brainpop_appears_valid_url('lalala://@:@/'));
    }
}