<?php

$connection=mysqli_connect('localhost','root','','ticket');

function getTotalseats($iTotalSeats, $iSeatsPerRow){
    $aaTotalSeatsNumber = [];
    for($i=0;$i<ceil($iTotalSeats/$iSeatsPerRow);$i++){
        for($j=1;$j<=$iSeatsPerRow;$j++){
            if($iSeatsPerRow*$i+$j<=$iTotalSeats){
                $aaTotalSeatsNumber[$i][]=$iSeatsPerRow*$i+$j;
            }else{
                break;
            }
        }
    }
    return $aaTotalSeatsNumber;
}
function userIdAlreadyExist($id){
    global $connection;
    $bAlreadyExist = true;
    $user_result=mysqli_query($connection,"SELECT * FROM `seat_book` WHERE userId='$id'");
    if(mysqli_num_rows($user_result)==0){
        $bAlreadyExist = false;
    }
    return $bAlreadyExist;
}
function getUserBookedSeats($id){
    global $connection;
    $aTotalBookedSeats = [];
    $user_result=mysqli_query($connection,"SELECT * FROM `seat_book` WHERE userId='$id'");
    if($user_result){
        $aTotalBookedSeats=json_decode(mysqli_fetch_assoc($user_result)['seats_no'],true);
    }
    return $aTotalBookedSeats;
}
function getRemainingSeats($aaTotalSeatsNumber){
    global $connection;
    $rsBookedSeats=mysqli_query($connection,"SELECT * FROM `seat_book`");
    if(mysqli_num_rows($rsBookedSeats)==0){
        array_merge([], $aaTotalSeatsNumber);
    }
    else{
        while($rows=mysqli_fetch_assoc($rsBookedSeats)){  
            foreach($aaTotalSeatsNumber as $key=>$aSeatsNumber){
                $aaTotalSeatsNumber[$key] = array_diff($aSeatsNumber, json_decode($rows['seats_no'],true));
            }
        };
    }
    return $aaTotalSeatsNumber;
}
function getAllBookedSeats(){
    global $connection;
    $aTotalBookedSeats = [];
    $rsBookedSeats=mysqli_query($connection,"SELECT * FROM `seat_book`");
    while($rows=mysqli_fetch_assoc($rsBookedSeats)){
        $aTotalBookedSeats = array_merge($aTotalBookedSeats,json_decode($rows['seats_no'], true));
        sort($aTotalBookedSeats);
    };
    return $aTotalBookedSeats; 
}
?>