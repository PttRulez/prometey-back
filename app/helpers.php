<?php

use App\Models\Affiliate;
use App\Models\Network;

function getMonthList()
{
    return [
        '1' => 'Январь',
        '2' => 'Февраль',
        '3' => 'Март',
        '4' => 'Апрель',
        '5' => 'Май',
        '6' => 'Июнь',
        '7' => 'Июль',
        '8' => 'Август',
        '9' => 'Сентябрь',
        '10' => 'Октябрь',
        '11' => 'Ноябрь',
        '12' => 'Декабрь'
    ];
}

function getYearList()
{
    return [
        '2016' => '2016',
        '2017' => '2017',
        '2018' => '2018',
        '2019' => '2019',
        '2020' => '2020',
        '2021' => '2021',
        '2022' => '2022',
        '2023' => '2023',
        '2024' => '2024',
        '2025' => '2025',
    ];
}

function getAffiliateList()
{
    return Affiliate::pluck('name', 'id');
}

function getNetworkList()
{
    return Network::all()->pluck('name', 'id')->toArray();
}

function stringInArray($s, $accept)
{
    for ($i = 0; $i < count($accept); $i++) {
        if (strpos($s, $accept[$i]) !== FALSE)
            return TRUE;
    }

    return FALSE;
}
