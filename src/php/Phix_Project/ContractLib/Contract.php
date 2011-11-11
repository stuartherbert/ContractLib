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
         * A set of OldValues objects
         *
         * You can get the right object for your code's current scope by
         * calling Contract::OldValues()
         *
         * Objects in this array that are no longer required are nuked by
         * the Contract::Postconditions() wrapper
         *
         * @var array(OldValues)
         */
        static protected $oldValues = array();

        /**
         * Global library; cannot instantiate
         *
         * You can't instantiate this library because it needs to preserve
         * state even as the execution scope in your code changes :(
         *
         * @codeCoverageIgnore
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
         * @param string $reason error message to show on failure
         * @return boolean true on success
         */
        static public function Requires($expr, $reason = null)
        {
                if (!$expr)
                {
                        throw new E5xx_ContractFailedException('Requires', $reason);
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
         * @param string $reason error message to show on failure
         * @return boolean true on success
         */
        static public function RequiresValue($value, $expr, $reason = null)
        {
                if (!$expr)
                {
                        throw new E5xx_ContractFailedException('RequiresValue', $reason, true, $value);
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
         * @param string $reason error message to show on failure
         * @return boolean true on success
         */
        static public function Ensures($expr, $reason = null)
        {
                if (!$expr)
                {
                        throw new E5xx_ContractFailedException('Ensures', $reason);
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
         * @param string $reason error message to show on failure
         * @return boolean true on success
         */
        static public function EnsuresValue($value, $expr, $reason = null)
        {
                if (!$expr)
                {
                        throw new E5xx_ContractFailedException('EnsuresValue', $reason, true, $value);
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
         * @param string $reason error message to show on failure
         * @return boolean true on success
         */
        static public function Asserts($expr, $reason = null)
        {
                if (!$expr)
                {
                        throw new E5xx_ContractFailedException('Asserts', $reason);
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
         * @param string $reason error message to show on failure
         * @return boolean true on success
         */
        static public function AssertsValue($value, $expr, $reason = null)
        {
                if (!$expr)
                {
                        throw new E5xx_ContractFailedException('AssertsValue', $reason, true, $value);
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
        // Old values support
        //
        // ----------------------------------------------------------------

        /**
         * Obtain a value, suitable for use as an array key, based on the
         * current execution scope in an app
         *
         * @return array(string, string)
         *      The caller, plus the scope
         */
        static protected function determineScope()
        {
                // the scope comes from interpreting the current
                // execution stack
                //
                // we are looking for the function or method that has
                // called either Contract::Preconditions or
                // Contract::Postconditions
                //
                // this algorithm is annoyingly expensive, but it should
                // ensure that there are no problems with actions in one
                // scope ever affecting the old values remembered in any
                // other scope

                $debug_backtrace = debug_backtrace();

                $caller   = null;
                $maxIndex = count($debug_backtrace);
                $i = 0;

                while ($caller == null && $i < $maxIndex)
                {
                        // var_dump(substr($debug_backtrace[$i]['function'], -10, 10));

                        if (isset($debug_backtrace[$i]['class'])
                            && $debug_backtrace[$i]['class'] == 'Phix_Project\ContractLib\Contract'
                            && substr($debug_backtrace[$i]['function'], -10, 10) == 'conditions'
                        )
                        {
                                $caller = $debug_backtrace[$i]['function'];
                        }
                        $i++;
                }

                // did we find what we are looking for?
                if ($caller == null)
                {
                        // no - throw an exception
                        throw new \RuntimeException('You can only use ContractLib Old Values support inside Preconditions or Postconditions');
                }

                // at this point, $debug_backtrace[0] points at the caller
                // to the precondition or postcondition
                //
                // we want to remember the file and function, but not the
                // specific line number
                //
                // this makes sure that we return the same result when
                // called in both the preconditions and postconditions
                // in the same function, with the same callstack
                if (isset($debug_backtrace[$i]['line']))
                {
                        // this never seems to get called, but I assume
                        // that one day the backtrace might get 'fixed'
                        // @codeCoverageIgnoreStart
                        unset($debug_backtrace[$i]['line']);
                        // @codeCoverageIgnoreEnd
                }

                // now, let's build up this scope
                $scope = '';
                for (;$i < $maxIndex; $i++)
                {
                        foreach (array('file', 'line', 'function') as $key)
                        {
                                if (isset($debug_backtrace[$i][$key]))
                                {
                                        $scope .= $debug_backtrace[$i][$key];
                                }
                        }
                }

                // var_dump($scope);

                return array($caller, $scope);
        }

        /**
         * Obtain the old value of an argument
         *
         * @param string $argName
         * @return mixed
         */
        static public function OldValue($argName)
        {
                // work out the current scope
                list($caller, $scope) = self::determineScope();

                // do we have an existing OldValues object for this scope?
                if (!isset(self::$oldValues[$scope]))
                {
                        return null;
                }

                // do we have a stashed value for this argument name?
                if (self::$oldValues[$scope]->hasStashed($argName))
                {
                        // yes we do
                        return self::$oldValues[$scope]->unpack($argName);
                }

                // no we don't
                return null;
        }

        /**
         * Remember an old value for future comparisons
         *
         * @param string $argName
         *      The name of the value to remember
         * @param mixed $argValue
         *      The value to remember
         */
        static public function RememberOldValue($argName, $argValue)
        {
                // work out the current scope
                list($caller, $scope) = self::determineScope();

                // we must have been called from Preconditions
                if ($caller !== 'Preconditions')
                {
                        throw new \RuntimeException('You can only remember old values inside Contract::Preconditions');
                }

                // do we have an existing OldValues object for this scope?
                if (!isset(self::$oldValues[$scope]))
                {
                        // no - create one!
                        self::$oldValues[$scope] = new OldValues();
                }

                // stash the value
                self::$oldValues[$scope]->stash($argName, $argValue);
        }

        /**
         * Free up memory by forgetting the old values we may have
         * remembered in the current scope
         */
        static public function ForgetOldValues()
        {
                // work out the current scope
                list($caller, $scope) = self::determineScope();

                // release an object, if we have one
                if (isset(self::$oldValues[$scope]))
                {
                        unset(self::$oldValues[$scope]);
                }
        }

        /**
         * Get the internal list of scopes that are remembering old
         * values
         *
         * This method exists only to help with debugging
         *
         * @return array
         */
        static public function _rememberedScopes()
        {
                return self::$oldValues;
        }

        // ================================================================
        //
        // Unreachable code support
        //
        // ----------------------------------------------------------------

        static public function Unreachable($file, $line)
        {
                throw new E5xx_ContractFailedException('Unreachable', "Unreachable code in file $file at line $line has somehow been reached. Go figure!");
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

                // success!
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