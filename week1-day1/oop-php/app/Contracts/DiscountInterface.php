<?php

namespace App\Contracts;

interface DiscountInterface
{
    function getDiscountPercent();
    function getName();
}