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

class Contract
{
        /**
         * Are we currently enforcing any contracts passed to 
         * self::Enforce()?
         * 
         * By default, we do not!
         * 
         * @var boolean
         */
        static protected $enforcing = false;                
        
        /**
         * Stateless library; cannot instantiate
         */
        protected function __construct()
        {
                // do nothing
        }
        
        /**
         * Precondition: is the expression $expr true?
         * 
         * Use this method at the start of your method to make sure you're
         * happy with the data that you have been passed, and with the
         * current state of your object
         * 
         * Throws an E5xx_ContractPreconditionException if the parameter
         * passed in is false
         * 
         * @throw E5xx_ContractPreconditionException
         * @param boolean $expr 
         * @return boolean true on success
         */
        static public function Requires($expr)
        {
                if (!$expr)
                {
                        throw new E5xx_ContractFailedException('Requires');
                }
                
                return true;
        }
        
        /**
         * Precondition: is the expression $expr true?
         * 
         * Use this method at the start of your method to make sure you're
         * happy with the data that you have been passed, and with the
         * current state of your object
         * 
         * Throws an E5xx_ContractPreconditionException if $expr is false,
         * and adds $value to the exception's error message so that you
         * can see which value failed the test
         * 
         * @param mixed $value 
         * @param boolean $expr
         * @return boolean true on success
         */
        static public function RequiresValue($value, $expr)
        {
                if (!$expr)
                {
                        throw new E5xx_ContractFailedException('RequiresValue', true, $value);
                }
                
                return true;
        }
        
        /**
         * Postcondition: is the expression $expr true?
         * 
         * Use this method at the end of your method to make sure you're
         * happy with the results before your method returns to the caller
         * 
         * Throws an E5xx_ContractPostconditionException if $expr is false.
         * 
         * @param boolean $expr 
         * @return boolean true on success
         */
        static public function Ensures($expr)
        {
                if (!$expr)
                {
                        throw new E5xx_ContractFailedException('Ensures');
                }
                
                return true;
        }
        
        /**
         * Postcondition: is the expression $expr true?
         * 
         * Use this method at the end of your method to make sure you're
         * happy with the results before your method returns to the caller
         * 
         * Throws an E5xx_ContractPostConditionException if $expr is false,
         * and adds $value to the exception's error message so that you
         * can see which value failed the test
         * 
         * @param mixed $value 
         * @param boolean $expr
         * @return boolean true on success
         */
        static public function EnsuresValue($value, $expr)
        {
                if (!$expr)
                {
                        throw new E5xx_ContractFailedException('EnsuresValue', true, $value);
                }
                
                return true;
        }
        
        /**
         * Condition: is the expr $expr true?
         * 
         * Use this method in the middle of your method, to check the
         * workings of your method before continuing.
         * 
         * Throws an E5xx_ContractConditionException if $expr is false.
         * 
         * @param boolean $expr 
         * @return boolean true on success
         */
        static public function Asserts($expr)
        {
                if (!$expr)
                {
                        throw new E5xx_ContractFailedException('Asserts');
                }
                
                return true;
        }
        
        /**
         * Condition: is the expr $expr true?
         * 
         * Use this method in the middle of your method, to check the
         * workings of your method before continuing.
         * 
         * Throws an E5xx_ContractConditionException if $expr is false,
         * and adds $value to the exception's error message so that you
         * can see which value failed the test
         * 
         * @param mixed $value
         * @param boolean $expr 
         * @return boolean true on success
         */
        static public function AssertsValue($value, $expr)
        {
                if (!$expr)
                {
                        throw new E5xx_ContractFailedException('AssertsValue', true, $value);
                }
                
                return true;
        }
        
        /**
         * Apply the same condition (or set of conditions) to the values
         * in an array
         * 
         * @param array $values
         * @param callback $callback 
         * @param boolean true on success
         */
        static public function ForAll($values, $callback)
        {
                array_walk($values, $callback);
                
                return true;
        }

        // ================================================================
        //
        // Wrapped contract support
        //
        // ----------------------------------------------------------------
        
        /**
         * Tell us to enforce contracts passed to self::Enforce()
         */
        static public function EnforceWrappedContracts()
        {
                self::$enforcing = true;
        }

        /**
         * Tell us to enforce only calls made directly to the individual
         * contract conditions: Requires, Assert, Ensures et al
         */
        static public function EnforceOnlyDirectContracts()
        {
                self::$enforcing = false;
        }
        
        /**
         * Check a set of preconditions *if* we are enforcing wrapped
         * contracts.
         * 
         * This exists as a performance boost, allowing us to leave
         * contracts in the code even in production environments
         * 
         * @param callback $callback
         * @param array $params 
         * @return boolean true on success
         */
        static public function Preconditions($callback, $params = array())
        {
                if (self::$enforcing)
                {
                        call_user_func_array($callback, $params);
                }
                
                return true;
        }
        
        /**
         * Check a set of postconditions *if* we are enforcing wrapped
         * contracts.
         * 
         * This exists as a performance boost, allowing us to leave
         * contracts in the code even in production environments
         * 
         * @param callback $callback
         * @param array $params 
         * @return boolean true on success
         */
        static public function Postconditions($callback, $params = array())
        {
                if (self::$enforcing)
                {
                        call_user_func_array($callback, $params);
                }
                
                return true;
        }
        
        /**
         * Check a set of conditions mid-method *if* we are enforcing
         * wrapped contracts.
         * 
         * This exists as a performance boost, allowing us to leave
         * contracts in the code even in production environments
         * 
         * @param callback $callback
         * @param array $params 
         * @return boolean true on success
         */
        static public function Conditionals($callback, $params = array())
        {
                if (self::$enforcing)
                {
                        call_user_func_array($callback, $params);
                }
                
                return true;
        }
}