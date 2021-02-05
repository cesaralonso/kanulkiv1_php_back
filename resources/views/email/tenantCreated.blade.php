@component('mail::message')

@component('mail::panel')
<h1>Bienvenido(a) a Kanulki Coto {{$tenant->condo->name}}</h1>
<h2>{{$tenant->name." ".$tenant->last_name}}</h2>
<span>Email: {{$user->email}}</span><br><br>
<span>{{$tenant->house->name}}</span>
@endcomponent

@component('mail::promotion')
<h1>Descarga Nuestra App</h1>
<a href="#" data-abc="true"><img class="img-responsive" src="https://res.cloudinary.com/dxfq3iotg/image/upload/v1574317087/AAA/appstore.png" height="40"></a> <a href="#" class="d-block mb-2" data-abc="true"><img class="img-responsive" src="https://res.cloudinary.com/dxfq3iotg/image/upload/v1574317110/AAA/playmarket.png" height="40"></a>
@endcomponent
@endcomponent