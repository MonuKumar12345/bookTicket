import { Component, OnInit } from '@angular/core';
import { HttpClient,HttpHeaders,HttpRequest } from '@angular/common/http';
import { NgForm } from '@angular/forms';

@Component({
  selector: 'app-root',
  templateUrl: './ticket.component.html',
  styleUrls: ['./ticket.component.css']
})
export class TicketComponent implements OnInit {
  totalSeats:any;
  sSeatsNumber:any;
  aBookedSeats:any=[];
  aaTotalSeats:any=[];
  sMessage:any=String;
  bSuccess:any=Boolean;

  constructor(private http:HttpClient) {}

  ngOnInit(): void {
    this.http.get<any>('http://localhost/ticketBook/backend/getAllSeats.php').subscribe({
      next: (resp:any) =>  {
        this.bSuccess = (resp.sStatus=='success')?true:false;
        this.sMessage = resp.sMessage;
        this.aaTotalSeats=resp.aData.Total_seats;
        this.aBookedSeats=resp.aData.Booked_seats;
      },
      error: (error:any) => {
        console.log(error.error);
      }
    });
    }
    onbooking(form: NgForm){
      const data = new FormData();
      data.append("id", form.value.id);
      data.append("seats", form.value.seats);
      this.http.post<any>('http://localhost/ticketBook/backend/bookSeats.php',data).subscribe({
        next: (resp:any) =>  {
          this.bSuccess = (resp.sStatus=='success')?true:false;
          this.sMessage = resp.sMessage;
          if(resp.aData.user_seats.length>0){
            this.sSeatsNumber = resp.aData.user_seats.toString();
          }
          this.aaTotalSeats=resp.aData.Total_seats;
          this.aBookedSeats=resp.aData.Booked_seats;
        },
        error: (error:any) => {
          console.log(error.error);
        }
       });
     }
     seatBooked(iSeat:any){
       var bResult:any = false;
      if(this.aBookedSeats.length>0){
         bResult= this.aBookedSeats.find((seat:any) => seat==iSeat)
      }
      if(bResult){
        return true;
      }else{
        return false;
      }
     }
}
