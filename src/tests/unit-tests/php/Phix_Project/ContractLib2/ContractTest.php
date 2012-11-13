<?php

/**
 * Copyright (c) 2011-present Stuart Herbert.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of the copyright holders nor the names of the
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package     Phix_Project
 * @subpackage  ContractLib2
 * @author      Stuart Herbert <stuart@stuartherbert.com>
 * @copyright   2011-present Stuart Herbert
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://phix-project.org
 * @version     @@PACKAGE_VERSION@@
 */


namespace Phix_Project\ContractLib2;

use Exception;
use ReflectionClass;
use PHPUnit_Framework_TestCase;

class ContractTest extends PHPUnit_Framework_TestCase
{
        public function testCannotInstantiate()
        {
                $refClass = new ReflectionClass('Phix_Project\ContractLib2\Contract');
                $refMethod = $refClass->getMethod('__construct');
                $this->assertFalse($refMethod->isPublic());
        }

        public function testPreconditionsMustBeTrue()
        {
                // prove that the precondition checks do not throw an
                // exception when they are passed the value of TRUE
                $this->assertTrue(Contract::Requires(true));
                $this->assertTrue(Contract::RequiresValue(0, true));

                // prove that the precondition checks do throw an exception
                // when they are passed the value of FALSE
                $caughtException = false;
                try
                {
                        Contract::Requires(false);
                }
                catch (E5xx_ContractFailedException $e)
                {
                        $caughtException = true;
                }
                $this->assertTrue($caughtException);

                // repeat the check with another of the precondition
                // check methods
                $caughtException = false;
                try
                {
                        Contract::RequiresValue(10, false);
                }
                catch (E5xx_ContractFailedException $e)
                {
                        $caughtException = true;
                }
                $this->assertTrue($caughtException);
        }

        public function testPostconditionsMustBeTrue()
        {
                // prove that the postcondition checks do not throw an
                // exception when they are passed the value of TRUE
                $this->assertTrue(Contract::Ensures(true));
                $this->assertTrue(Contract::EnsuresValue(0, true));

                // prove that the postcondition checks do throw an
                // exception when they are passed the value of FALSE
                $caughtException = false;
                try
                {
                        Contract::Ensures(false);
                }
                catch (E5xx_ContractFailedException $e)
                {
                        $caughtException = true;
                }
                $this->assertTrue($caughtException);

                // repeat the test for another of the postcondition
                // check methods
                $caughtException = false;
                try
                {
                        Contract::EnsuresValue(10, false);
                }
                catch (E5xx_ContractFailedException $e)
                {
                        $caughtException = true;
                }
                $this->assertTrue($caughtException);
        }

        public function testMidConditionsMustBeTrue()
        {
                // prove that the condition checks do not throw an
                // exception when they are passed the value of TRUE
                $this->assertTrue(Contract::Asserts(true));
                $this->assertTrue(Contract::AssertsValue(0, true));

                // prove that the condition checks do throw an exception
                // when they are passed the value of FALSE
                $caughtException = false;
                try
                {
                        Contract::Asserts(false);
                }
                catch (E5xx_ContractFailedException $e)
                {
                        $caughtException = true;
                }
                $this->assertTrue($caughtException);

                // repeat the test with another of the condition check
                // methods
                $caughtException = false;
                try
                {
                        Contract::AssertsValue(10, false);
                }
                catch (E5xx_ContractFailedException $e)
                {
                        $caughtException = true;
                }
                $this->assertTrue($caughtException);
        }

        public function testCanApplyConditionsToArrays()
        {
                $testData1 = array (1,2,3,4,5);
                $testData2 = array (6,7,8,9,10);

                // these contracts are satisfied
                Contract::ForAll($testData1, function($value) { Contract::Requires($value < 6); });
                $this->assertTrue(true);
                Contract::ForAll($testData2, function($value) { Contract::Requires($value > 5); });
                $this->assertTrue(true);

                // these contracts are not satisfied
                $caughtException = false;
                try
                {
                        Contract::ForAll($testData1, function($value) { Contract::Requires($value > 5); });
                }
                catch (E5xx_ContractFailedException $e)
                {
                        $caughtException = true;
                }
                $this->assertTrue($caughtException);

                // these contracts are not satisfied
                $caughtException = false;
                try
                {
                        Contract::ForAll($testData2, function($value) { Contract::Requires($value < 6); });
                }
                catch (E5xx_ContractFailedException $e)
                {
                        $caughtException = true;
                }
                $this->assertTrue($caughtException);
        }

        public function testCanSeeTheValueThatFailedThePrecondition()
        {
                $caughtException = false;
                try
                {
                        Contract::RequiresValue(5, false);
                }
                catch (E5xx_ContractFailedException $e)
                {
                        $caughtException = $e->getMessage();
                }

                // did we catch the exception?
                $this->assertTrue($caughtException !== false);

                // did we get the message we expect?
                $expected = "Internal server error: Contract::RequiresValue() failed; value tested was '5'";
                $this->assertEquals($expected, $caughtException);
        }

        public function testCanSeeTheValueThatFailedThePostcondition()
        {
                $caughtException = false;
                try
                {
                        Contract::EnsuresValue(5, false);
                }
                catch (E5xx_ContractFailedException $e)
                {
                        $caughtException = $e->getMessage();
                }

                // did we catch the exception?
                $this->assertTrue($caughtException !== false);

                // did we get the message we expect?
                $expected = "Internal server error: Contract::EnsuresValue() failed; value tested was '5'";
                $this->assertEquals($expected, $caughtException);
        }

        public function testCanSeeTheValueThatFailedTheMidCondition()
        {
                $caughtException = false;
                try
                {
                        Contract::AssertsValue(5, false);
                }
                catch (E5xx_ContractFailedException $e)
                {
                        $caughtException = $e->getMessage();
                }

                // did we catch the exception?
                $this->assertTrue($caughtException !== false);

                // did we get the message we expect?
                $expected = "Internal server error: Contract::AssertsValue() failed; value tested was '5'";
                $this->assertEquals($expected, $caughtException);
        }

        public function testCanSeeTheReasonWhenThePreconditionFailed()
        {
                $caughtException = false;
                try
                {
                        Contract::Requires(false, "my reason");
                }
                catch (E5xx_ContractFailedException $e)
                {
                        $caughtException = $e->getMessage();
                }

                // did we catch the exception?
                $this->assertTrue($caughtException !== false);

                // did we get the message we expect?
                $expected = "Internal server error: Contract::Requires() failed; reason for failure was: my reason";
                $this->assertEquals($expected, $caughtException);

                $caughtException = false;
                try
                {
                        Contract::RequiresValue(5, false, "my reason");
                }
                catch (E5xx_ContractFailedException $e)
                {
                        $caughtException = $e->getMessage();
                }

                // did we catch the exception?
                $this->assertTrue($caughtException !== false);

                // did we get the message we expect?
                $expected = "Internal server error: Contract::RequiresValue() failed; value tested was '5'; reason for failure was: my reason";
                $this->assertEquals($expected, $caughtException);
        }

        public function testCanSeeTheReasonWhenThePostconditionFailed()
        {
                $caughtException = false;
                try
                {
                        Contract::Ensures(false, 'my reason');
                }
                catch (E5xx_ContractFailedException $e)
                {
                        $caughtException = $e->getMessage();
                }

                // did we catch the exception?
                $this->assertTrue($caughtException !== false);

                // did we get the message we expect?
                $expected = "Internal server error: Contract::Ensures() failed; reason for failure was: my reason";
                $this->assertEquals($expected, $caughtException);

                $caughtException = false;
                try
                {
                        Contract::EnsuresValue(5, false, 'my reason');
                }
                catch (E5xx_ContractFailedException $e)
                {
                        $caughtException = $e->getMessage();
                }

                // did we catch the exception?
                $this->assertTrue($caughtException !== false);

                // did we get the message we expect?
                $expected = "Internal server error: Contract::EnsuresValue() failed; value tested was '5'; reason for failure was: my reason";
                $this->assertEquals($expected, $caughtException);
        }

        public function testCanSeeTheReasonTheMidConditionFailed()
        {
                $caughtException = false;
                try
                {
                        Contract::Asserts(false, 'my reason');
                }
                catch (E5xx_ContractFailedException $e)
                {
                        $caughtException = $e->getMessage();
                }

                // did we catch the exception?
                $this->assertTrue($caughtException !== false);

                // did we get the message we expect?
                $expected = "Internal server error: Contract::Asserts() failed; reason for failure was: my reason";
                $this->assertEquals($expected, $caughtException);

                $caughtException = false;
                try
                {
                        Contract::AssertsValue(5, false, 'my reason');
                }
                catch (E5xx_ContractFailedException $e)
                {
                        $caughtException = $e->getMessage();
                }

                // did we catch the exception?
                $this->assertTrue($caughtException !== false);

                // did we get the message we expect?
                $expected = "Internal server error: Contract::AssertsValue() failed; value tested was '5'; reason for failure was: my reason";
                $this->assertEquals($expected, $caughtException);
        }

        public function testWrappedContractsCanBeDisabled()
        {
                // ensure wrapped contracts are switched off
                Contract::EnforceOnlyDirectContracts();

                // some data to test
                $x = 1;
                $y = 2;
                $z = 3;

                // check wrapped preconditions
                $executed = false;
                Contract::Preconditions(function($x, $y, $z) use (&$executed) {
                        Contract::Requires($x < $y);
                        Contract::Requires($y < $z);
                        $executed = true;
                }, array($x, $y, $z));
                $this->assertFalse($executed);

                // check wrapped mid-conditions
                $executed = false;
                Contract::Conditionals(function() use (&$executed) {
                        Contract::Asserts(2 > 1);
                        Contract::Asserts(5 > 4);
                        $executed = true;
                });
                $this->assertFalse($executed);

                // check wrapped postconditions
                $executed = false;
                Contract::Postconditions(function($x, $y, $z) use (&$executed) {
                        Contract::Ensures($x < $z);
                        Contract::Ensures($z > $x);
                        $executed = true;
                }, array($x, $y, $z));
                $this->assertFalse($executed);
        }

        public function testCanWrapContractsForPeformance()
        {
                // enable wrapped contracts
                Contract::EnforceWrappedContracts();

                // some data to test
                $x = 1;
                $y = 2;
                $z = 3;

                // check wrapped preconditions
                $executed = false;
                Contract::Preconditions(function($x, $y, $z) use (&$executed) {
                        Contract::Requires($x < $y);
                        Contract::Requires($y < $z);
                        $executed = true;
                }, array($x, $y, $z));
                $this->assertTrue($executed);

                // check wrapped mid-conditions
                $executed = false;
                Contract::Conditionals(function() use (&$executed) {
                        Contract::Asserts(2 > 1);
                        Contract::Asserts(5 > 4);
                        $executed = true;
                });
                $this->assertTrue($executed);

                // check wrapped postconditions
                $executed = false;
                Contract::Postconditions(function($x, $y, $z) use (&$executed) {
                        Contract::Ensures($x < $z);
                        Contract::Ensures($z > $x);
                        $executed = true;
                }, array($x, $y, $z));
                $this->assertTrue($executed);
        }

        public function testCanDisabledWrappedContracts()
        {
                // enable wrapped contracts
                Contract::EnforceWrappedContracts();

                // execute a wrapped contract
                $executed = false;
                Contract::Preconditions(function() use (&$executed)
                {
                        $executed = true;
                });
                $this->assertTrue($executed);

                // now, disable wrapped contracts
                Contract::EnforceOnlyDirectContracts();

                // repeat the test
                $executed = false;
                Contract::Preconditions(function() use (&$executed)
                {
                        $executed = true;
                });
                $this->assertFalse($executed);
        }

        public function testCanTrackUnreachableCode()
        {
                $caughtException = false;
                $expectedMessage =
                $actualMessage = "Internal server error: Contract::Unreachable() failed; reason for failure was: "
                        . 'Unreachable code in file ' . __FILE__
                        . ' at line ' . (__LINE__ + 5)
                        . ' has somehow been reached. Go figure!';

                try
                {
                        Contract::Unreachable(__FILE__, __LINE__);
                }
                catch (E5xx_ContractFailedException $e)
                {
                        $caughtException = true;
                        $actualMessage = $e->getMessage();
                }

                // what happened?
                $this->assertTrue($caughtException);
                $this->assertEquals($expectedMessage, $actualMessage);
        }
}