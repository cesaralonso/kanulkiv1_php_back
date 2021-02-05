<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDF</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <style type="text/css">
      span{
        font-size: 12px;
      }
      footer {
        position: fixed; 
        bottom: -60px; 
        left: 0px; 
        right: 0px;
        height: 80px; 

        text-align: center;
        line-height: 18px;
      }
      .info{
        font-size: 14px;
        padding-left: 20px;
      }
      main{
        width: 100%;
        margin: 0;
        padding: 0;
      }

      .table-charges{
        font-size: 14px;
      }
    </style>

  </head>
  
  <body>
    <header>
      <div class="row"> 
        <div class="col-md-3">
          <img src="img/logo-plumbago.png" style="width: 100px;">
        </div> 
        <div class="col-md-9" style="text-align: right; padding-top: 15px; line-height: 15px;">
          <span style="font-weight: bold;">Coto del Plumbago P. en C.</span><br>
          <span>cotoplumbago1@hotmail.com</span><br>
          <span>incidenciasplumbago@gmail.com</span><br>
          <span>Teléfono oficina: 33-31-10-05-75</span><br>
        </div>
      </div>
    </header>

    <footer>
        <div class="row">
        <div class="col-md-12">
          <span>Avenida del Ahuehuete #251 Colonia Puertas del Tule, Zapopan, Jalisco. C.P. 45017 </span>
        </div>
      </div>
    </footer>
    <main>
      <div class="container"> 
        <span style="font-size: 22px">Resumen de Pago</span><br><br>
        <div class="row">
          <div class="col-md-12">
            <table>
              <tr>
                 <td><span class="info">Pago Folio: {{$payment->payment_description}}</span></td>
                 <td><span class="info">Fecha: {{$payment->date}}</span></td>
              </tr>
              <tr>
                <td><span class="info">Nombre: {{$payment->name}}</span></td>
                <td><span class="info">Teléfono: {{$payment->phone}}</span></td>
              </tr>
              <tr>
                <td><span class="info">Correo Electrónico: {{$payment->email}}</span></td>
                <td><span class="info">RFC: {{$payment->rfc}}</span></td>
              </tr>
              <tr>
               
              </tr>
            </table>
          </div>
        </div><br><br><br>
        <div class="row">
          <div class="col-md-12">
            <table class="table table-striped table-charges">
              <thead>
                <tr>
                  <td>#</td>
                  <td>Fecha</td>
                  <td>Descripción</td>
                  <td>Cantidad</td>
                </tr>
              </thead>
              <tbody>
                  @foreach ($payment->charges as $i=>$charge)
                  <tr> 
                    <td style="text-align: center;">{{$i+1}}</td>
                    <td>{{$charge->getDate()}}</td>
                    <td>{{$charge->description}}</td>
                    <td style="text-align: right;">{{$charge->amount}}</td>
                  </tr>
                  @endforeach
                <tr>
                  <td></td>
                  <td></td>
                  <td style="text-align: right;">Total</td>
                  <td style="text-align: right;">{{$payment->amount}}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
  </body>
</html>