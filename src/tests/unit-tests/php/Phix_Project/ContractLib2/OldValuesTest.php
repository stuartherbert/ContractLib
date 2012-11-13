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

use Phix_Project\ContractLib2\Contract;

class OldValuesTest extends PHPUnit_Framework_TestCase
{
        public function testCanInstantiate()
        {
                // you *can* create an OldValues object directly yourself,
                // but tbh it isn't much use
                //
                // the right way to use this object is by inference,
                // through the OldValue methods on Contract::

                $obj = new OldValues();
                $this->assertTrue($obj instanceof OldValues);
        }

        public function testOldValuesCanBeRememberedInPreconditions()
        {
                // make sure wrapped contracts are enabled
                Contract::EnforceWrappedContracts();

                // some test data
                $arg1 = __LINE__;

                // stash the value
                Contract::Preconditions(function() use ($arg1)
                {
                        Contract::RememberOldValue('arg1', $arg1);
                });

                // if we get here, the test has passed
                $this->assertTrue(true);

                // let's forget that value now :)
                Contract::Postconditions(function()
                {
                        Contract::ForgetOldValues();
                });
        }

        public function testCannotBeRememberedInPostconditions()
        {
                // tell PHPUnit that this test causes an exception
                $this->setExpectedException('RuntimeException');

                // make sure wrapped contracts are enabled
                Contract::EnforceWrappedContracts();

                // some test data
                $arg1 = __LINE__;

                // stash the value
                Contract::Postconditions(function() use ($arg1)
                {
                        Contract::RememberOldValue('arg1', $arg1);
                });

                // this should never be reached, but just in case ...
                $this->assertTrue(false);
        }

        public function testCannotBeRememberedOutsideWrappedContracts()
        {
                // tell PHPUnit that this test causes an exception
                $this->setExpectedException('RuntimeException');

                // some test data
                $arg1 = __LINE__;

                // stash the value
                Contract::RememberOldValue('arg1', $arg1);

                // this should never be reached, but just in case ...
                $this->assertTrue(false);
        }

        public function testCanStashValues()
        {
                // make sure wrapped contracts are enabled
                Contract::EnforceWrappedContracts();

                // some test data
                $origArg1 = __LINE__;
                $origArg2 = __LINE__;

                // take a copy ... because we need to change these variables
                // in a bit to prove that we're remembering the original
                // value
                $arg1 = $origArg1;
                $arg2 = $origArg2;

                // stash some values
                Contract::Preconditions(function() use ($arg1, $arg2)
                {
                        Contract::RememberOldValue('arg1', $arg1);
                        Contract::RememberOldValue('arg2', $arg2);
                });

                // change variables in this scope
                $arg1 = __LINE__;
                $arg2 = __LINE__;

                // now, did we get the values?
                //
                // for this to work propery, we have to test it inside
                // the postconditions wrapper
                Contract::PostConditions(function($obj) use($origArg1, $origArg2, $arg1, $arg2)
                {
                        // the remembered values should be the originals
                        $obj->assertEquals($origArg1, Contract::OldValue('arg1'));
                        $obj->assertEquals($origArg2, Contract::OldValue('arg2'));

                        // the remembered values should not be the same
                        // values that our changed variables now have
                        $obj->assertNotEquals($arg1, Contract::OldValue('arg1'));
                        $obj->assertNotEquals($arg2, Contract::OldValue('arg2'));

                        // release the memory
                        Contract::ForgetOldValues();
                }, array($this));
        }

        public function testReturnsNullWhenNoOldValuesHaveBeenRemembered()
        {
                // make sure wrapped contracts are enabled
                Contract::EnforceWrappedContracts();

                // do the test
                // we never remembered any values in this scope!!
                Contract::PostConditions(function($obj)
                {
                        $obj->assertNull(Contract::OldValue('arg1'));
                }, array($this));
        }

        public function testReturnsNullForOldValuesThatHaveNotBeenRemembered()
        {
                // make sure wrapped contracts are enabled
                Contract::EnforceWrappedContracts();

                $arg1 = __LINE__;

                Contract::Preconditions(function() use($arg1)
                {
                        Contract::RememberOldValue('arg1', $arg1);
                });

                // do the test
                // we never remembered a value for arg2
                Contract::PostConditions(function($obj) use($arg1)
                {
                        $obj->assertEquals($arg1, Contract::OldValue('arg1'));
                        $obj->assertNull(Contract::OldValue('arg2'));

                        // remember to forget the remembered values!
                        Contract::ForgetOldValues();
                }, array($this));
        }

        public function testCanForgetValuesToSameMemory()
        {
                // make sure wrapped contracts are enabled
                Contract::EnforceWrappedContracts();

                // make sure there are no remembered scopes atm
                $this->assertEquals(0, count(Contract::_rememberedScopes()));

                // some test data
                $arg1 = 'fred';
                $arg2 = 'alice';

                // stash some values
                Contract::Preconditions(function() use ($arg1, $arg2)
                {
                        Contract::RememberOldValue('arg1', $arg1);
                        Contract::RememberOldValue('arg2', $arg2);
                });

                // make sure we have remembered the scope
                $this->assertEquals(1, count(Contract::_rememberedScopes()));

                // now, did we get the values?
                //
                // for this to work propery, we have to test it inside
                // the postconditions wrapper
                Contract::PostConditions(function($obj) use($arg1, $arg2)
                {
                        $obj->assertEquals($arg1, Contract::OldValue('arg1'));
                        $obj->assertEquals($arg2, Contract::OldValue('arg2'));

                        // forget these values, freeing up the memory
                        Contract::ForgetOldValues();
                }, array($this));

                // make sure we have forgotten the scope
                $this->assertEquals(0, count(Contract::_rememberedScopes()));
        }

        public function testScopeIsUniqueToCaller()
        {
                // how many scopes have previous tests left in memory?
                $currentScopeCount = count(Contract::_rememberedScopes());

                // remember some values, in their own unique scope
                // doing so increases the number of scopes that Contract::
                // now remembers
                $this->rememberSomeValues();
                $this->assertEquals($currentScopeCount + 1, count(Contract::_rememberedScopes()));

                // try to recall those values
                // this does not affect the number of scopes that Contact::
                // remembers
                $this->recallSomeValues();
                $this->assertEquals($currentScopeCount + 1, count(Contract::_rememberedScopes()));

                // let's remember those values now
                $this->rememberSomeValues(true);
                $this->assertEquals($currentScopeCount, count(Contract::_rememberedScopes()));
        }

        protected function rememberSomeValues($check = false)
        {
                if (!$check)
                {
                        Contract::Preconditions(function()
                        {
                                // the scope for these values is unique
                                Contract::RememberOldValue('arg1', __LINE__);
                                Contract::RememberOldValue('arg2', __LINE__);
                        });
                }

                if ($check)
                {
                        Contract::Postconditions(function($obj)
                        {
                                $obj->assertNotNull(Contract::OldValue('arg1'));
                                $obj->assertNotNull(Contract::OldValue('arg2'));

                                // clean up after ourselves
                                Contract::ForgetOldValues();
                        }, array($this));
                }
        }

        protected function recallSomeValues()
        {
                Contract::PostConditions(function($obj)
                {
                        // these values will be null, because they were
                        // never ever set in this scope
                        $obj->assertNull(Contract::OldValue('arg1'));
                        $obj->assertNull(Contract::OldValue('arg2'));
                }, array($this));
        }
}