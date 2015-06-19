<?php

namespace SocialAPI\Lib\Util\Tests;

/**
 * Class ConstructorTester
 *
 * @package SocialAPI\Lib\Util\Tests
 */
trait ConstructorTester
{
    /**
     * Method for testing constructor with valid and invalid arrays of data, with callback function
     * @param array $valid
     * @param array $invalid
     * @param callable $userFunc
     */
    public function checkConstructor(array $valid, array $invalid, callable $userFunc)
    {
        // Prepare data
        $validPrepared = [];
        $maxCountValid = 0;
        foreach ($valid as $name => $elmPool) {
            if (($count = count($elmPool)) > $maxCountValid) {
                $maxCountValid = $count;
            }

            $validPrepared[$name] = reset($elmPool);
        }

        // Check valid params
        for ($num = 0; $num < $maxCountValid; $num++) {
            try {
                if ($num !== 0) {
                    $validPrepared = [];
                    foreach ($valid as $name => $values) {
                        $validPrepared[$name] = isset($values[$num]) ? $values[$num] : reset($values);
                    }
                }

                call_user_func_array($userFunc, $validPrepared);
                $this->assertTrue(true);
            } catch (\InvalidArgumentException $e) {
                $this->fail("Test error with correct data: " . $e->getMessage());
            }
        }

        // Check invalid data
        foreach ($invalid as $num => $elmPool) {
            foreach ($elmPool as $elm) {
                try {
                    $invalidParams          = $validPrepared;
                    $invalidParams[$num]    = $elm;

                    call_user_func_array($userFunc, $invalidParams);
                    $this->fail(
                        "Test didnt fall down with incorrect value with id - {$num},  and type - " . gettype($elm)
                    );
                } catch (\InvalidArgumentException $e) {
                    $this->assertTrue(true);
                }
            }
        }
    }
}
