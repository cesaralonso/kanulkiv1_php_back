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
          <img src="img/logo-sevilla.png" style="width: 100px;">
        </div> 
        <div class="col-md-9" style="border-bottom:solid 2px; text-align: right; padding-bottom: 15px;">
          <span>FRACCIONAMIENTO EL ENCUENTRO RESIDENCIAL</span><br>
          <span>PLAYA DEL CARMEN C.P 77724</span><br>
          <span>sevillaprivada@gmail.com</span><br>
        </div>
      </div>
    </header>

    <footer>
        <div class="row">
        <div class="col-md-12" style="border-top: solid 2px;">
          <span>"PRIVADA SEVILLA" Lote 002-8 Manzana 20, Supermanzana 81, Región 33 Avenida Playa Azul / Avenida Universidades y Avenida Xel-Ha Playa del Carmen, Municipio de Soliradidad, Quintana Roo, México Administración: (52)(33) 33 13537061.</span>
        </div>
      </div>
    </footer>
    <main>
      <div class="container"> 
        <span style="font-size: 22px">Pendiente de Pago</span><br><br>
        <div class="row">
          <div class="col-md-12">
            <table>
              <tr>
                 <td><span class="info">Fecha: {{$pending_charges->date}}</span></td>
                 <td><span class="info">Dirección: {{$pending_charges->address}}</span></td>
              </tr>
              <tr>
                <td><span class="info">Nombre: {{$pending_charges->name}}</span></td>
                <td><span class="info">Teléfono: {{$pending_charges->phone}}</span></td>
              </tr>
              <tr>
                <td><span class="info">Correo Electrónico: {{$pending_charges->email}}</span></td>
                <td><span class="info">RFC: {{$pending_charges->rfc}}</span></td>
              </tr>
              <tr>
               
              </tr>
            </table>
          </div>
        </div><br><br><br>
        <div class="row">
          <div class="col-md-12">
            <table class="table table-striped table-charges">
              <thead class="bg-gdanger">
                <tr>
                  <td>#</td>
                  <td>Fecha</td>
                  <td>Descripción</td>
                  <td>Cantidad</td>
                </tr>
              </thead>
              <tbody>
                  @foreach ($pending_charges as $i=>$charge)
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
                  <td style="text-align: right;">{{$pending_charges->amount}}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
  </body>
</html>