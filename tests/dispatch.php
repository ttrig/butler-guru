<?php

function dispatch($object)
{
    Butler\Guru\Tests\TestCase::$dispatched[] = $object;
}
