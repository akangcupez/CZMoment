<?php
/*
 * Created by   : Aji Subastian (aKanG cuPez)
 * Mobile Phone : +62 812 888 33996
 * Email        : akangcupez@gmail.com
 * Website      : http://akangcupez.com
 * Date         : 4/1/2016 10:26 AM
 */
require_once('../CZMoment.php');

$publish_date = '2016-01-01 12:15:00';

$moment = new CZMoment('en');
echo $moment->FromNow($publish_date);