<?php
include_once("./common.php");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods:POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

$connection=mysqli_connect('localhost','root','','ticket');

$iTotalSeats=80;
$iSeatsPerRow=7;
$aaSeatsNumber = [];
$aaRemainingSeatsNumber = [];
$imaxSeats = 0;
$iSeatsToBook = 0;

$aaData['sStatus'] = 'failure';
$aaData['sMessage']= "Something went Wrong";
$aaData['aData']= [];

$aaSeatsNumber = getTotalseats($iTotalSeats, $iSeatsPerRow);

function seatsCanBeBookedOrNot($aaTotalRemainingSeatsNumber){
    global $imaxSeats, $iSeatsToBook, $iSeatsPerRow;
    $bflag = true;
    foreach($aaTotalRemainingSeatsNumber as $aTotalRemainingSeatsNumber ){
        if(count($aTotalRemainingSeatsNumber)<$iSeatsToBook){
            if($imaxSeats<count($aTotalRemainingSeatsNumber)){
                $imaxSeats = count($aTotalRemainingSeatsNumber);
            }
            $bflag = false;
        }else{
            $imaxSeats = $iSeatsPerRow;
            $bflag = true;
            break;
        }
    }
    return  $bflag;
}
if(isset($_POST['id']) && !empty($_POST['id']) &&  isset($_POST['seats']) && !empty($_POST['seats'])){
    $id=$_POST['id'];
    $iSeatsToBook=$_POST['seats'];
    if($iSeatsToBook<=$iSeatsPerRow){
        if(!userIdAlreadyExist($id)){
            $aaRemainingSeatsNumber = getRemainingSeats($aaSeatsNumber);
            if(seatsCanBeBookedOrNot($aaRemainingSeatsNumber)){
                foreach($aaRemainingSeatsNumber as $key=>$aSeatsNumber){
                    if($iSeatsToBook<=count($aSeatsNumber))
                    {
                        $aSeatsToBook=array_slice($aaRemainingSeatsNumber[$key],0,$iSeatsToBook);
                        $aSeatsToBookJson = json_encode($aSeatsToBook);
                        break;
                    }  
                }
                $query_result=mysqli_query($connection,"INSERT INTO `seat_book`(`userId`, `seats`, `seats_no`) VALUES ('$id','$iSeatsToBook',' $aSeatsToBookJson')");

                if($query_result){
                    $aaData['sStatus'] = 'success';
                    $aaData['sMessage']= "Seats booked Successfully";
                    $aaData['aData']['user_seats']= $aSeatsToBook;
                    $aaData['aData']['Total_seats']= $aaSeatsNumber;
                    $aaData['aData']['Booked_seats']= getAllBookedSeats();
                    $aaData['aData']['maxSeats']= $imaxSeats;   
                }
                $booking_result=[$aaRemainingSeatsNumber,$aSeatsToBook];
            }
        }else{
            $aaRemainingSeatsNumber = getRemainingSeats($aaSeatsNumber);
            seatsCanBeBookedOrNot($aaRemainingSeatsNumber);
            $aaData['sStatus'] = 'failure';
            $aaData['sMessage']= "You already booked the seats";
            $aaData['aData']['user_seats']= getUserBookedSeats($id);
            $aaData['aData']['Total_seats']= $aaSeatsNumber;
            $aaData['aData']['Booked_seats']= getAllBookedSeats(); 
            $aaData['aData']['maxSeats']= $imaxSeats; 
        }
    }else{
        $aaRemainingSeatsNumber = getRemainingSeats($aaSeatsNumber);
        seatsCanBeBookedOrNot($aaRemainingSeatsNumber);
        $aaData['sStatus'] = 'failure';
        $aaData['sMessage'] = "Please enter correct number of seats";
        $aaData['aData']['user_seats']= [];
        $aaData['aData']['Total_seats']= $aaSeatsNumber;
        $aaData['aData']['Booked_seats']= getAllBookedSeats(); 
        $aaData['aData']['maxSeats']= $imaxSeats;  
    }
}else{
    $aaRemainingSeatsNumber = getRemainingSeats($aaSeatsNumber);
    seatsCanBeBookedOrNot($aaRemainingSeatsNumber);
    $aaData['sStatus'] = 'failure';
    $aaData['sMessage']= "Please enter id or seats";
    $aaData['aData']['user_seats']= [];
    $aaData['aData']['Total_seats']= $aaSeatsNumber;
    $aaData['aData']['Booked_seats']= getAllBookedSeats();  
    $aaData['aData']['maxSeats']= $imaxSeats;
}

echo json_encode($aaData);

?>