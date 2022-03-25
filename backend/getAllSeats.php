<?php
include_once("./common.php");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods:POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
$connection=mysqli_connect('localhost','root','','ticket');

$iTotalSeats=80;
$iSeatsPerRow=7;
$aaSeatsNumber = [];
$imaxSeats = 0;
$iSeatsToBook = 0;

$aaSeatsNumber = getTotalseats($iTotalSeats, $iSeatsPerRow);

function seatsCanBeBookedOrNot($aaTotalRemainingSeatsNumber){
    global $imaxSeats;
    foreach($aaTotalRemainingSeatsNumber as $aTotalRemainingSeatsNumber ){
        if(count($aTotalRemainingSeatsNumber)>$imaxSeats){
            $imaxSeats = count($aTotalRemainingSeatsNumber);
        }
    }
}

$aaRemainingSeatsNumber = getRemainingSeats($aaSeatsNumber);
seatsCanBeBookedOrNot($aaRemainingSeatsNumber);

$aaData['sStatus'] = 'success';
$aaData['sMessage'] = "";
$aaData['aData']['user_seats']= [];
$aaData['aData']['Total_seats']= $aaSeatsNumber;
$aaData['aData']['Booked_seats']= getAllBookedSeats(); 
$aaData['aData']['maxSeats']= $imaxSeats;

echo json_encode($aaData);
?>