@component('mail::message')

@component('mail::panel')
<h1>Confirmación de Pago</h1>
<h2>Hola {{$tenant->name}}, gracias por tu pago</h2>
<h3>{{$tenant->house->name}}</h3>
<h3>Fecha del pago: {{$date}}</h3>
<h3>Factura: {{$payment->payment_description}}</h3>
<h3>Método de pago: {{$payment->getPaymentMethod()}}<h3>
<table>
<th></th>
@foreach($payment->charges as $charge)
<tr>
<td class="compressed"><h4 class="compressed">{{$charge->description}}</h4></td>
<td class="compressed"><h4 class="compressed">${{$charge->amount}}</h4></td>
</tr>
@endforeach
<td><h3 style="text-align: right; padding-right: 20px;">Total</h3></td>
<td><h3>${{$payment->amount}}</h3></td>
</table>
@endcomponent

@component('mail::promotion')
<h3 style="text-align: center;">Recuerda que puedes consultar tu historial de pagos y facturas desde nuestra app</h3>
<a href="#" data-abc="true"><img class="img-responsive" src="https://res.cloudinary.com/dxfq3iotg/image/upload/v1574317087/AAA/appstore.png" height="40"></a> <a href="#" class="d-block mb-2" data-abc="true"><img class="img-responsive" src="https://res.cloudinary.com/dxfq3iotg/image/upload/v1574317110/AAA/playmarket.png" height="40"></a>
@endcomponent
@endcomponent