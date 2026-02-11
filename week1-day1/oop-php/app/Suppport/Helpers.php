<?php

function formatMoney(float $amount): string
{
    return number_format($amount, 2) . "USD";
}