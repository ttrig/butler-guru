<?php

function dispatch(object $object)
{
    Butler\Guru\Tests\TestCase::$dispatched[] = $object;
}
