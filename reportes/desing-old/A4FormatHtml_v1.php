<?php 



?>

<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>%nombre_archivo%</title>
  <!-- Css --> 
  <style>
    :root {
      --bs-font-sans-serif: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", "Noto Sans", "Liberation Sans", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
      --bs-font-monospace: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    }

    html { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; -webkit-tap-highlight-color: transparent; 
      font-size: 10px;
      margin: 0;
    }

    body {
      font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", "Noto Sans", "Liberation Sans", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji" !important;
      /* font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace !important; */
    }

    .tabla_borde { border: 1px solid #666; border-radius: 10px; }
    tr.border_bottom td { border-bottom: 1px solid #000; }
    tr.border_top td { border-top: 1px solid #666; }
    td.border_right { border-right: 1px solid #666; }
  </style>
  
</head>

<body class="white-bg">
  
  <table width="100%">
    <tbody>
      <tr>
        <td style="padding-left:25px !important; padding-right:25px !important;">
          <table width="100%" height="200px" border="0" aling="center" cellpadding="0" cellspacing="0">
            <tbody>
              <tr>
                <td width="50%" height="90" align="center">
                  <span><img src="%logo_empresa%" height="80" style="text-align:center" border="0"></span>
                </td>
                <td width="5%" height="40" align="center"></td>
                <td width="45%" rowspan="2" valign="bottom" style="padding-left:0">
                  <div class="tabla_borde">
                    <table width="100%" border="0" height="200" cellpadding="6" cellspacing="0">
                      <tbody>
                        <tr>
                          <td align="center">
                            <span style="font-family:Tahoma, Geneva, sans-serif; font-size:19px" text-align="center">BOLETA DE VENTA</span>
                            <br>
                            <span style="font-family:Tahoma, Geneva, sans-serif; font-size:19px" text-align="center">E L E C T R Ó N I C A</span>
                          </td>
                        </tr>
                        <tr>
                          <td align="center"><span style="font-size:15px" text-align="center">R.U.C.: 20123456789</span></td>
                        </tr>
                        <tr>
                          <td align="center"><span style="font-size:24px">B001-1</span></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </td>
              </tr>
              <tr>
                <td valign="bottom" style="padding-left:0; ">
                  <div class="tabla_borde" style="padding: 5px;" >
                    <table width="96%" height="100%" border="0" border-radius="" cellpadding="1" cellspacing="0">
                      <tbody>
                        <tr>
                          <td align="center"><strong><span style="font-size:15px">GREENTER S.A.C.</span></strong></td>
                        </tr>
                        <tr>
                          <td align="left"><strong>Dirección: </strong>AV NEW DEÁL 123</td>
                        </tr>
                        <tr>
                          <td align="left">Telf: <b>(056) 123375</b></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
          <div class="tabla_borde" style="margin-top: 7px; padding: 5px;">
            <table width="100%" border="0" cellpadding="1" cellspacing="0">
              <tbody  >
                <tr>
                  <td width="70%" align="left"><strong>Razón Social:</strong> NIPAO GUVI</td>
                  <td width="30%" align="left"><strong>DNI:</strong> 48285071</td>
                </tr>
                <tr>
                  <td width="70%" align="left"><strong>Fecha Emisión: </strong> 05/05/2024 10:03:12</td>
                  <td width="30%" align="left"><strong>Moneda: </strong> SOLES </td>
                </tr>                
                <tr><td width="100%" align="left" colspan="2" ><strong>Dirección: </strong> Calle fusión 453, SAN MIGUEL - LIMA - PERU</td></tr>
                <tr><td width="100%" align="left" colspan="2" ><strong>Observación: </strong> Calle fusión 453, SAN MIGUEL - LIMA - PERU</td></tr>
              </tbody>
            </table>
          </div><br>
          <div class="tabla_borde">
            <table width="100%" border="0" cellpadding="5" cellspacing="0">
              <tbody>
                <tr>
                  <td align="center" class="bold">Cant.</td>
                  <td align="center" class="bold">Código</td>
                  <td align="center" class="bold">Descripción</td>
                  <td align="center" class="bold">P/U</td>
                  <td align="center" class="bold">Subtotal</td>
                </tr>               
                %tbody_producto%
              </tbody>
            </table>
          </div>
          <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tbody>
              <tr>
                <td width="50%" valign="top">
                  <table width="100%" border="0" cellpadding="5" cellspacing="0">
                    <tbody>
                      <tr>
                        <td colspan="4">
                          <br>
                          <br>
                          <span style="font-family:Tahoma, Geneva, sans-serif; font-size:12px"
                            text-align="center"><strong>SON CIEN CON 00/100 SOLES</strong></span>
                          <br>
                          <br>
                          <strong>Información Adicional</strong>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <table width="100%" border="0" cellpadding="5" cellspacing="0">
                    <tbody>
                      <tr class="border_top">
                        <td width="30%" style="font-size: 10px;">
                          LEYENDA:
                        </td>
                        <td width="70%" style="font-size: 10px;">
                          <p>
                          </p>
                        </td>
                      </tr>
                      <tr class="border_top">
                        <td width="30%" style="font-size: 10px;">
                          FORMA DE PAGO:
                        </td>
                        <td width="70%" style="font-size: 10px;">
                          Contado
                        </td>
                      </tr>
                      <tr class="border_top">
                        <td width="30%" style="font-size: 10px;">
                          VENDEDOR:
                        </td>
                        <td width="70%" style="font-size: 10px;">
                          GITHUB SELLER
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </td>
                <td width="50%" valign="top">
                  <br>
                  <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table table-valores-totales">
                    <tbody>
                      <tr class="border_bottom">
                        <td align="right"><strong>Op. Gravadas:</strong></td>
                        <td width="120" align="right"><span>S/ 200.00</span></td>
                      </tr>
                      <tr>
                        <td align="right"><strong>I.G.V.:</strong></td>
                        <td width="120" align="right"><span>S/ 36.00</span></td>
                      </tr>
                      <tr>
                        <td align="right"><strong>Precio Venta:</strong></td>
                        <td width="120" align="right"><span id="ride-importeTotal" class="ride-importeTotal">S/
                            100.00</span></td>
                      </tr>
                    </tbody>
                  </table>
                </td>
              </tr>
            </tbody>
          </table>
          <br>
          <br>
          <div>
            <hr style="display: block; height: 1px; border: 0; border-top: 1px solid #666; margin: 20px 0; padding: 0;">
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
              <tbody>
                <tr>
                  <td width="85%">
                    <blockquote>
                      <div>consulte en <a href="https://github.com/giansalex/sufel">sufel.com</a></div>
                      <strong>Resumen:</strong> ygS1I5AyeUktAjfSf7Z48imce0E=<br>
                      <span>Representación Impresa de la BOLETA DE VENTA ELECTRÓNICA.</span>
                    </blockquote>
                  </td>
                  <td width="15%" align="right">
                    <img
                      src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZlcnNpb249IjEuMSIgd2lkdGg9IjEyMCIgaGVpZ2h0PSIxMjAiIHZpZXdCb3g9IjAgMCAxMjAgMTIwIj48cmVjdCB4PSIwIiB5PSIwIiB3aWR0aD0iMTIwIiBoZWlnaHQ9IjEyMCIgZmlsbD0iI2ZlZmVmZSIvPjxnIHRyYW5zZm9ybT0ic2NhbGUoMy4yNDMpIj48ZyB0cmFuc2Zvcm09InRyYW5zbGF0ZSgwLDApIj48cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik04IDBMOCAxTDkgMUw5IDJMOCAyTDggNEw5IDRMOSA1TDggNUw4IDhMNSA4TDUgOUw0IDlMNCA4TDMgOEwzIDlMNCA5TDQgMTBMNSAxMEw1IDExTDcgMTFMNyAxMkw1IDEyTDUgMTNMNCAxM0w0IDExTDMgMTFMMyAxMEwxIDEwTDEgMTFMMCAxMUwwIDEyTDEgMTJMMSAxM0wyIDEzTDIgMTJMMSAxMkwxIDExTDMgMTFMMyAxNEw0IDE0TDQgMTVMMiAxNUwyIDE3TDAgMTdMMCAxOEwxIDE4TDEgMjBMMCAyMEwwIDIyTDIgMjJMMiAyM0wwIDIzTDAgMjVMMSAyNUwxIDI2TDAgMjZMMCAyN0wxIDI3TDEgMjhMMCAyOEwwIDI5TDUgMjlMNSAyOEw0IDI4TDQgMjdMNSAyN0w1IDI2TDYgMjZMNiAyN0w3IDI3TDcgMjhMNiAyOEw2IDI5TDcgMjlMNyAyOEw5IDI4TDkgMjlMOCAyOUw4IDMyTDkgMzJMOSAzM0w4IDMzTDggMzRMOSAzNEw5IDM1TDggMzVMOCAzNkw5IDM2TDkgMzdMMTAgMzdMMTAgMzZMOSAzNkw5IDM1TDEwIDM1TDEwIDM0TDExIDM0TDExIDMzTDEyIDMzTDEyIDM0TDEzIDM0TDEzIDM1TDE0IDM1TDE0IDM2TDE1IDM2TDE1IDM3TDE2IDM3TDE2IDM2TDE1IDM2TDE1IDM1TDE0IDM1TDE0IDM0TDEzIDM0TDEzIDMzTDE4IDMzTDE4IDMyTDE5IDMyTDE5IDM0TDIwIDM0TDIwIDM1TDIyIDM1TDIyIDMzTDIzIDMzTDIzIDM0TDI0IDM0TDI0IDMzTDI1IDMzTDI1IDM0TDI2IDM0TDI2IDM1TDI1IDM1TDI1IDM2TDI0IDM2TDI0IDM1TDIzIDM1TDIzIDM2TDI0IDM2TDI0IDM3TDI3IDM3TDI3IDM2TDI2IDM2TDI2IDM1TDI3IDM1TDI3IDMzTDI2IDMzTDI2IDMyTDI4IDMyTDI4IDM0TDMwIDM0TDMwIDMzTDMxIDMzTDMxIDM1TDMzIDM1TDMzIDM2TDMyIDM2TDMyIDM3TDMzIDM3TDMzIDM2TDM0IDM2TDM0IDM1TDMzIDM1TDMzIDM0TDMyIDM0TDMyIDMzTDMzIDMzTDMzIDMyTDM1IDMyTDM1IDMzTDM3IDMzTDM3IDMwTDM2IDMwTDM2IDMyTDM1IDMyTDM1IDMxTDM0IDMxTDM0IDI5TDMzIDI5TDMzIDI4TDMyIDI4TDMyIDI3TDM0IDI3TDM0IDI4TDM1IDI4TDM1IDI5TDM3IDI5TDM3IDI3TDM2IDI3TDM2IDI4TDM1IDI4TDM1IDI3TDM0IDI3TDM0IDI2TDM2IDI2TDM2IDI1TDM3IDI1TDM3IDIyTDM2IDIyTDM2IDIwTDM3IDIwTDM3IDE4TDM1IDE4TDM1IDIwTDM0IDIwTDM0IDIxTDMzIDIxTDMzIDIzTDMxIDIzTDMxIDIyTDMyIDIyTDMyIDIxTDMxIDIxTDMxIDE5TDMyIDE5TDMyIDE4TDMzIDE4TDMzIDE5TDM0IDE5TDM0IDE4TDMzIDE4TDMzIDE3TDM0IDE3TDM0IDE2TDM1IDE2TDM1IDE1TDM2IDE1TDM2IDE3TDM3IDE3TDM3IDEwTDM2IDEwTDM2IDExTDM1IDExTDM1IDEwTDM0IDEwTDM0IDlMMzUgOUwzNSA4TDMzIDhMMzMgOUwzMiA5TDMyIDhMMjkgOEwyOSA0TDI4IDRMMjggM0wyOSAzTDI5IDJMMjggMkwyOCAxTDI5IDFMMjkgMEwyOCAwTDI4IDFMMjcgMUwyNyAyTDI2IDJMMjYgMUwyNSAxTDI1IDBMMjQgMEwyNCAxTDIyIDFMMjIgMkwyMSAyTDIxIDFMMjAgMUwyMCAyTDIxIDJMMjEgNEwyMiA0TDIyIDVMMjMgNUwyMyA2TDIyIDZMMjIgN0wyMSA3TDIxIDZMMjAgNkwyMCA3TDE5IDdMMTkgNkwxOCA2TDE4IDNMMTkgM0wxOSA0TDIwIDRMMjAgM0wxOSAzTDE5IDJMMTggMkwxOCAxTDE5IDFMMTkgMEwxOCAwTDE4IDFMMTcgMUwxNyAyTDE2IDJMMTYgNEwxNSA0TDE1IDJMMTQgMkwxNCAzTDEzIDNMMTMgMUwxNSAxTDE1IDBMMTMgMEwxMyAxTDEyIDFMMTIgMEwxMSAwTDExIDFMMTAgMUwxMCAwWk0xMSAxTDExIDJMMTAgMkwxMCA2TDkgNkw5IDdMMTAgN0wxMCA2TDExIDZMMTEgOEwxMCA4TDEwIDlMOSA5TDkgMTBMMTAgMTBMMTAgMTJMMTIgMTJMMTIgMTNMMTEgMTNMMTEgMTRMOSAxNEw5IDEzTDggMTNMOCAxNUw2IDE1TDYgMTRMNyAxNEw3IDEzTDUgMTNMNSAxNUw0IDE1TDQgMTZMMyAxNkwzIDE3TDIgMTdMMiAyMEwxIDIwTDEgMjFMMiAyMUwyIDIyTDMgMjJMMyAyNUwyIDI1TDIgMjZMMSAyNkwxIDI3TDIgMjdMMiAyNkw0IDI2TDQgMjVMNiAyNUw2IDI2TDcgMjZMNyAyN0w5IDI3TDkgMjhMMTAgMjhMMTAgMjlMMTEgMjlMMTEgMjhMMTIgMjhMMTIgMjdMMTMgMjdMMTMgMjlMMTIgMjlMMTIgMzNMMTMgMzNMMTMgMzJMMTQgMzJMMTQgMzFMMTUgMzFMMTUgMjhMMTYgMjhMMTYgMjZMMTcgMjZMMTcgMjdMMTggMjdMMTggMjlMMTYgMjlMMTYgMzJMMTcgMzJMMTcgMzFMMTkgMzFMMTkgMzJMMjAgMzJMMjAgMzNMMjIgMzNMMjIgMzJMMjAgMzJMMjAgMzFMMjIgMzFMMjIgMzBMMjEgMzBMMjEgMjlMMjIgMjlMMjIgMjhMMjEgMjhMMjEgMjdMMjAgMjdMMjAgMjZMMjEgMjZMMjEgMjVMMjIgMjVMMjIgMjRMMTkgMjRMMTkgMjNMMjEgMjNMMjEgMjFMMjIgMjFMMjIgMjNMMjQgMjNMMjQgMjRMMjMgMjRMMjMgMjZMMjQgMjZMMjQgMjdMMjMgMjdMMjMgMjlMMjggMjlMMjggMjhMMzAgMjhMMzAgMjdMMjkgMjdMMjkgMjZMMjggMjZMMjggMjdMMjcgMjdMMjcgMjZMMjYgMjZMMjYgMjVMMjggMjVMMjggMjRMMjkgMjRMMjkgMjNMMzAgMjNMMzAgMjJMMzEgMjJMMzEgMjFMMzAgMjFMMzAgMjJMMjkgMjJMMjkgMjBMMzAgMjBMMzAgMThMMzEgMThMMzEgMTdMMzAgMTdMMzAgMTVMMjkgMTVMMjkgMTRMMzEgMTRMMzEgMTVMMzIgMTVMMzIgMTZMMzMgMTZMMzMgMTVMMzIgMTVMMzIgMTRMMzEgMTRMMzEgMTNMMzMgMTNMMzMgMTJMMzUgMTJMMzUgMTRMMzQgMTRMMzQgMTVMMzUgMTVMMzUgMTRMMzYgMTRMMzYgMTJMMzUgMTJMMzUgMTFMMzMgMTFMMzMgMTBMMzIgMTBMMzIgOUwzMCA5TDMwIDEwTDI5IDEwTDI5IDlMMjggOUwyOCA2TDI3IDZMMjcgOEwyNSA4TDI1IDdMMjYgN0wyNiA1TDI1IDVMMjUgNEwyNyA0TDI3IDVMMjggNUwyOCA0TDI3IDRMMjcgM0wyNiAzTDI2IDJMMjUgMkwyNSA0TDIzIDRMMjMgM0wyNCAzTDI0IDJMMjIgMkwyMiA0TDIzIDRMMjMgNUwyNCA1TDI0IDZMMjMgNkwyMyA3TDIyIDdMMjIgOEwyMSA4TDIxIDdMMjAgN0wyMCA4TDE5IDhMMTkgN0wxOCA3TDE4IDZMMTcgNkwxNyA0TDE2IDRMMTYgNUwxNSA1TDE1IDRMMTQgNEwxNCA1TDExIDVMMTEgMkwxMiAyTDEyIDFaTTE3IDJMMTcgM0wxOCAzTDE4IDJaTTEyIDNMMTIgNEwxMyA0TDEzIDNaTTEyIDZMMTIgOEwxMyA4TDEzIDEwTDE0IDEwTDE0IDExTDEyIDExTDEyIDEyTDE2IDEyTDE2IDExTDE3IDExTDE3IDEzTDE2IDEzTDE2IDE0TDE0IDE0TDE0IDE1TDEzIDE1TDEzIDE2TDEyIDE2TDEyIDE4TDEzIDE4TDEzIDIwTDExIDIwTDExIDIyTDEwIDIyTDEwIDE5TDggMTlMOCAxOEw5IDE4TDkgMTdMOCAxN0w4IDE2TDYgMTZMNiAxNUw1IDE1TDUgMTZMNCAxNkw0IDE3TDUgMTdMNSAxOEwzIDE4TDMgMjBMMiAyMEwyIDIxTDMgMjFMMyAyMkw0IDIyTDQgMjRMNSAyNEw1IDIyTDYgMjJMNiAyM0w5IDIzTDkgMjJMMTAgMjJMMTAgMjRMMTEgMjRMMTEgMjVMOSAyNUw5IDI0TDYgMjRMNiAyNUw3IDI1TDcgMjZMMTAgMjZMMTAgMjdMMTEgMjdMMTEgMjZMMTMgMjZMMTMgMjdMMTQgMjdMMTQgMjVMMTUgMjVMMTUgMjJMMTcgMjJMMTcgMjNMMTYgMjNMMTYgMjRMMTcgMjRMMTcgMjVMMTggMjVMMTggMjZMMjAgMjZMMjAgMjVMMTkgMjVMMTkgMjRMMTcgMjRMMTcgMjNMMTkgMjNMMTkgMjJMMTcgMjJMMTcgMjBMMTggMjBMMTggMTlMMTkgMTlMMTkgMjFMMjAgMjFMMjAgMTlMMjIgMTlMMjIgMTdMMjMgMTdMMjMgMThMMjUgMThMMjUgMTlMMjYgMTlMMjYgMjBMMjcgMjBMMjcgMTlMMjggMTlMMjggMThMMjkgMThMMjkgMTdMMjYgMTdMMjYgMTZMMjcgMTZMMjcgMTVMMjggMTVMMjggMTZMMjkgMTZMMjkgMTVMMjggMTVMMjggMTNMMjkgMTNMMjkgMTJMMzAgMTJMMzAgMTNMMzEgMTNMMzEgMTBMMzAgMTBMMzAgMTFMMjkgMTFMMjkgMTBMMjggMTBMMjggMTFMMjkgMTFMMjkgMTJMMjggMTJMMjggMTNMMjYgMTNMMjYgMTRMMjUgMTRMMjUgMTVMMjYgMTVMMjYgMTZMMjUgMTZMMjUgMTdMMjMgMTdMMjMgMTVMMjIgMTVMMjIgMTRMMjQgMTRMMjQgMTNMMjUgMTNMMjUgMTJMMjYgMTJMMjYgMTFMMjcgMTFMMjcgMTBMMjYgMTBMMjYgOUwyNSA5TDI1IDhMMjIgOEwyMiA5TDIxIDlMMjEgOEwyMCA4TDIwIDlMMTkgOUwxOSAxMUwxOCAxMUwxOCAxMEwxNiAxMEwxNiA5TDE3IDlMMTcgOEwxOCA4TDE4IDdMMTcgN0wxNyA2TDE2IDZMMTYgN0wxNSA3TDE1IDZMMTQgNkwxNCA3TDE1IDdMMTUgMTBMMTQgMTBMMTQgOEwxMyA4TDEzIDZaTTI0IDZMMjQgN0wyNSA3TDI1IDZaTTE2IDdMMTYgOEwxNyA4TDE3IDdaTTEgOEwxIDlMMiA5TDIgOFpNMzYgOEwzNiA5TDM3IDlMMzcgOFpNNSA5TDUgMTBMNyAxMEw3IDlaTTExIDlMMTEgMTBMMTIgMTBMMTIgOVpNMjAgOUwyMCAxMUwxOSAxMUwxOSAxM0wxOCAxM0wxOCAxNUwxOSAxNUwxOSAxOUwyMCAxOUwyMCAxNUwyMSAxNUwyMSAxN0wyMiAxN0wyMiAxNUwyMSAxNUwyMSAxNEwyMCAxNEwyMCAxM0wyMiAxM0wyMiAxMkwyMSAxMkwyMSA5Wk0yMiA5TDIyIDExTDIzIDExTDIzIDEwTDI0IDEwTDI0IDlaTTE1IDEwTDE1IDExTDE2IDExTDE2IDEwWk0yNSAxMEwyNSAxMUwyNCAxMUwyNCAxMkwyMyAxMkwyMyAxM0wyNCAxM0wyNCAxMkwyNSAxMkwyNSAxMUwyNiAxMUwyNiAxMFpNOCAxMUw4IDEyTDkgMTJMOSAxMVpNMzIgMTFMMzIgMTJMMzMgMTJMMzMgMTFaTTExIDE0TDExIDE1TDEyIDE1TDEyIDE0Wk0xNiAxNEwxNiAxNkwxNyAxNkwxNyAxN0wxOCAxN0wxOCAxNkwxNyAxNkwxNyAxNFpNMTkgMTRMMTkgMTVMMjAgMTVMMjAgMTRaTTAgMTVMMCAxNkwxIDE2TDEgMTVaTTE0IDE1TDE0IDE2TDEzIDE2TDEzIDE3TDE0IDE3TDE0IDE5TDE1IDE5TDE1IDIxTDE0IDIxTDE0IDIyTDE1IDIyTDE1IDIxTDE2IDIxTDE2IDE4TDE1IDE4TDE1IDE3TDE0IDE3TDE0IDE2TDE1IDE2TDE1IDE1Wk0xMCAxNkwxMCAxN0wxMSAxN0wxMSAxNlpNNiAxN0w2IDE4TDcgMThMNyAxN1pNNiAxOUw2IDIwTDcgMjBMNyAyMUw2IDIxTDYgMjJMNyAyMkw3IDIxTDggMjFMOCAyMkw5IDIyTDkgMjBMOCAyMEw4IDE5Wk00IDIwTDQgMjFMNSAyMUw1IDIwWk0yMyAyMEwyMyAyMkwyNiAyMkwyNiAyM0wyNSAyM0wyNSAyNEwyNCAyNEwyNCAyNkwyNSAyNkwyNSAyOEwyNyAyOEwyNyAyN0wyNiAyN0wyNiAyNkwyNSAyNkwyNSAyNEwyNiAyNEwyNiAyM0wyNyAyM0wyNyAyMkwyNiAyMkwyNiAyMUwyNSAyMUwyNSAyMFpNMzQgMjFMMzQgMjJMMzUgMjJMMzUgMjNMMzQgMjNMMzQgMjRMMzMgMjRMMzMgMjVMMzAgMjVMMzAgMjZMMzQgMjZMMzQgMjRMMzUgMjRMMzUgMjVMMzYgMjVMMzYgMjRMMzUgMjRMMzUgMjNMMzYgMjNMMzYgMjJMMzUgMjJMMzUgMjFaTTI4IDIyTDI4IDIzTDI5IDIzTDI5IDIyWk0xMiAyM0wxMiAyNUwxMyAyNUwxMyAyNEwxNCAyNEwxNCAyM1pNMTkgMjdMMTkgMjhMMjAgMjhMMjAgMjdaTTEzIDI5TDEzIDMwTDE0IDMwTDE0IDI5Wk0xOCAyOUwxOCAzMEwyMCAzMEwyMCAyOVpNMjkgMjlMMjkgMzJMMzIgMzJMMzIgMjlaTTkgMzBMOSAzMUwxMCAzMUwxMCAzM0w5IDMzTDkgMzRMMTAgMzRMMTAgMzNMMTEgMzNMMTEgMzFMMTAgMzFMMTAgMzBaTTIzIDMwTDIzIDMyTDI2IDMyTDI2IDMxTDI1IDMxTDI1IDMwWk0yNyAzMEwyNyAzMUwyOCAzMUwyOCAzMFpNMzAgMzBMMzAgMzFMMzEgMzFMMzEgMzBaTTM2IDM0TDM2IDM1TDM1IDM1TDM1IDM3TDM3IDM3TDM3IDM0Wk0xNyAzNUwxNyAzN0wxOCAzN0wxOCAzNVpNMTEgMzZMMTEgMzdMMTMgMzdMMTMgMzZaTTE5IDM2TDE5IDM3TDIwIDM3TDIwIDM2Wk0yOCAzNkwyOCAzN0wzMCAzN0wzMCAzNlpNMCAwTDAgN0w3IDdMNyAwWk0xIDFMMSA2TDYgNkw2IDFaTTIgMkwyIDVMNSA1TDUgMlpNMzAgMEwzMCA3TDM3IDdMMzcgMFpNMzEgMUwzMSA2TDM2IDZMMzYgMVpNMzIgMkwzMiA1TDM1IDVMMzUgMlpNMCAzMEwwIDM3TDcgMzdMNyAzMFpNMSAzMUwxIDM2TDYgMzZMNiAzMVpNMiAzMkwyIDM1TDUgMzVMNSAzMloiIGZpbGw9IiMwMDAwMDAiLz48L2c+PC9nPjwvc3ZnPgo="
                      alt="Qr Image">
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</body>

</html>