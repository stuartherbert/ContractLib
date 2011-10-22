<?php

/**
 * Copyright (c) 2011 Stuart Herbert.
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
 * @subpackage  ContractLib
 * @author      Stuart Herbert <stuart@stuartherbert.com>
 * @copyright   2011 Stuart Herbert
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://phix-project.org
 * @version     @@PACKAGE_VERSION@@
 */


namespace Phix_Project\ContractLib;

use Exception;
use ReflectionClass;
use PHPUnit_Framework_TestCase;

use Phix_Project\ContractLib\Contract;

class ContractTest extends PHPUnit_Framework_TestCase
{
        public function testCannotInstantiate()
        {
                $refClass = new ReflectionClass('Phix_Project\ContractLib\Contract');
                $refMethod = $refClass->getMethod('__construct');
                $this->assertFalse($refMethod->isPublic());
        }
        
        /*
        public function testCanInstantiate()
        {
                $obj = new Contract();
                $this->assertTrue($obj instanceof Contract);
        }
        */
        
        public function testPreconditionsMustBeTrue()
        {
                // preconditional testing
                Contract::Requires(true);
                $this->assertTrue(true);
                
                $caughtException = false;
                try
                {
                        Contract::Requires(false);
                }
                catch (E5xx_ContractPreconditionException $e)
                {
                        $caughtException = true;
                }
                $this->assertTrue($caughtException);
        }

        public function testPostconditionsMustBeTrue()
        {
                // postconditional testing
                Contract::Ensures(true);
                $this->assertTrue(true);
                
                $caughtException = false;
                try
                {
                        Contract::Ensures(false);
                }
                catch (E5xx_ContractPostconditionException $e)
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
                catch (E5xx_ContractPreconditionException $e)
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
                catch (E5xx_ContractPreconditionException $e)
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
                        Contract::RequiresValue(false, 5);
                }
                catch (E5xx_ContractPreconditionException $e)
                {
                        $caughtException = $e->getMessage();
                }
                
                // did we catch the exception?
                $this->assertTrue($caughtException !== false);
                
                // did we get the message we expect?
                $expected = "Internal server error: Contract::RequiresValue() failed with value '5'";
                $this->assertEquals($expected, $caughtException);
        }

        public function testCanSeeTheValueThatFailedThePostcondition()
        {
                $caughtException = false;
                try
                {
                        Contract::EnsuresValue(false, 5);
                }
                catch (E5xx_ContractPostconditionException $e)
                {
                        $caughtException = $e->getMessage();
                }
                
                // did we catch the exception?
                $this->assertTrue($caughtException !== false);
                
                // did we get the message we expect?
                $expected = "Internal server error: Contract::EnsuresValue() failed with value '5'";
                $this->assertEquals($expected, $caughtException);
        }
        
        public function testCanWrapContractUpForPeformance()
        {
                Contract::EnforceWrappedContracts();
                
                $x = 1;
                $y = 2;
                $z = 3;
                
                Contract::Preconditions(function($x, $y, $z) {
                        Contract::Requires($x < $y);
                        Contract::Requires($y < $z);
                }, array($x, $y, $z));
                
                Contract::Conditionals(function() {
                        Contract::Asserts(2 > 1);
                        Contract::Asserts(5 > 4);
                });
                
                Contract::Postconditions(function($x, $y, $z) {
                        Contract::Ensures($x < $z);
                        Contract::Ensures($z > $x);
                }, array($x, $y, $z));
        }
}