<!-- saved from url=(0094)https://ww1.sunat.gob.pe/ol-ti-itconscpemype/consultar.do?action=verImprimirFactura&rowIndex=0 -->
<html class="dj_quirks dj_webkit dj_chrome dj_contentbox">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7">

	<title>.:: Nota Credito - Impresion ::.</title>

	<!-- Bootstrap Css -->
	<link id="style" href="../assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<!-- Style Css -->
	<link href="../assets/css/styles.min.css" rel="stylesheet">
	<link href="../assets/css/style_new.css" rel="stylesheet">
	<!-- Style Sunat -->
	<link rel="stylesheet" type="text/css" href="./css_sunat/NotaCreditoFE_print.css">

	<style type="text/css" media="print">
		.oculto {
			visibility: hidden
		}

		.tm_hide_print {
			display: none !important;
		}

		.margen_print {
			margin-left: 20px;
			margin-right: 50px;
		}

		/* tambiï¿½n puedes poner display:none */
	</style>

</head>

<body style=" display: flex;  justify-content: start;  align-items: center;">

	<div class="d-block align-items-center justify-content-between tm_hide_print">
		<a type="button" class="btn btn-outline-info p-1 mb-2 m-l-5px w-40px" href="javascript:window.print()" data-bs-toggle="tooltip" title="Imprimir Ticket">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer">
				<polyline points="6 9 6 2 18 2 18 9"></polyline>
				<path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
				<rect x="6" y="14" width="12" height="8"></rect>
			</svg>
		</a>

		<button type="button" class="btn btn-warning p-1 mb-2 m-l-5px w-40px" data-bs-toggle="tooltip" title="Imprimir Ticket" onclick="descargar_imagen();">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image">
				<rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
				<circle cx="8.5" cy="8.5" r="1.5"></circle>
				<polyline points="21 15 16 10 5 21"></polyline>
			</svg>
		</button>
	</div>

	<!-- codigo imprimir -->
	<div class="margen_print" id="iframe-img-descarga" style="background-color: #f0f1f7; border-radius: 5px;">
		<table class="comprobante" align="center" cellspacing="4" width="100%">
			<tbody>
				<tr>
					<td>
						<table width="100%">
							<tbody>
								<tr>
									<td width="58%">
										<table width="100%" class="emisor">
											<tbody>
												<tr>
													<td><b> BRARTNET S.A.C </b>&nbsp;</td>
												</tr>
												<tr>
													<td><b>CORPORACION BRARTNET Y ASOCIADOS S.A.C.</b>&nbsp;</td>
												</tr>
												<tr>
													<td> JR. DAUDILIO ZAVALETA C 13 S.N S.N &nbsp;</td>
												</tr>
												<tr>
													<td>TOCACHE - SAN MARTIN - TOCACHE&nbsp;</td>
												</tr>
												<tr align="left">
													<td>Fecha de Emisión :<b>12/04/2024</b></td>
												</tr>
											</tbody>
										</table>
									</td>
									<td width="42%">
										<table class="numeracion" align="right" width="81%">
											<tbody>
												<tr>
													<td align="center">NOTA DE CREDITO ELECTRONICA</td>
												</tr>
												<tr>
													<td align="center">RUC: 20610630431&nbsp;</td>
												</tr>
												<tr>
													<td align="center">E001-3&nbsp;</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
						<table width="100%" border="0" cellpadding="0">
							<tbody>
								<tr>
									<td><b>Documento que modifica: </b></td>
								</tr>
							</tbody>
						</table>
						<table width="100%" class="division" border="0">

							<tbody>
								<tr>
									<td width="49%">
										<table width="100%">
											<tbody>
												<tr align="left">
													<td>Factura Electrónica</td>
													<td>:</td>
													<td><b>E001 - 73 </b>&nbsp;</td>
												</tr>
												<tr>
													<td>Señor(es) </td>
													<td>:</td>
													<td><b>OJANAMA GURIS REYNA</b>&nbsp;</td>
												</tr>
												<!-- Ini AVN PAS20221U210700228 -->



												<tr>
													<td>RUC&nbsp;</td>
													<td>:</td>
													<td><b>10056110599</b>&nbsp;</td>
												</tr>


												<!-- Fin AVN PAS20221U210700228 -->


												<tr>
													<td>Tipo de Moneda </td>
													<td>:</td>
													<!--<td><b></b>&nbsp;</td>-->
													<td><b>SOLES</b>&nbsp;</td>
												</tr>
												<tr>
													<td>Observación</td>
													<td>:</td>
													<td><b>ANULACION DE LA OPERACION</b>&nbsp;</td>
												</tr>
											</tbody>
										</table>
									</td>
									<td width="51%" valign="top" align="right">
										<div align="right">
											<table>
												<tbody>
													<tr>
														<td><b>ANULACIÓN DE LA OPERACIÓN</b></td>
													</tr>
													<!-- INI AVN PAS20221U210700228 -->

													<!-- FIN AVN PAS20221U210700228  -->

												</tbody>
											</table>
										</div>
									</td>
								</tr>
							</tbody>
						</table>

						<!-- INI AVN PAS20221U210700228  Se agrega a la condicion el subTipoComprobante 27 para poder solucionar bug de visualizacion de items: http://jira.insi.sunat.peru:8080/browse/MNP-54347 -->


						<!-- FIN AVN PAS20221U210700228  -->
						<table class="detalle">
							<tbody>
								<tr>
									<td width="5%" class="header "><strong>Cantidad</strong></td>



									<td width="10%" class="header "><strong>Unidad Medida </strong></td>
									<td width="70%" class="header "><strong>Descripción</strong></td>
									<td width="15%" class="header "><strong>Valor Unitario</strong></td>






									<!-- PAS20191U210100126 -->


								</tr>

								<tr>
									<td align="right" valign="top">1.00</td>



									<td align="center" valign="top">UNIDAD</td>
									<td> SERVICIO DE INTERNET AL 30 DE FEBRERO </td>
									<td align="right" valign="top">70.00</td>






									<!-- PAS20191U210100126 -->


								</tr>

							</tbody>
						</table>


						<!-- INI: PAS20201U210100230 -->

						<!-- FIN: PAS20201U210100230 -->


						<table width="100%">
							<tbody>
								<tr>
									<td width="50%">


										<table width="100%">
											<tbody>
												<tr align="left">
													<td><b></b>&nbsp;</td>
												</tr>
											</tbody>
										</table>
										<table width="100%">
											<tbody>
												<tr align="left">
													<td><b></b>&nbsp;</td>
												</tr>
											</tbody>
										</table>

										<table width="100%">
											<tbody>
												<tr align="left">
													<td><b>SON: SETENTA Y 00/100 SOLES</b></td>
												</tr>
											</tbody>
										</table>
										<p>&nbsp;</p>
									</td>
									<!--Inicio PAS20165E210300142-->

									<!--Fin PAS20165E210300142-->
									<td width="50%">
										<table width="360" align="right" class="totales">
											<tbody>
												<tr>
													<td align="right">Sub Total Ventas </td>
													<td>:</td>
													<td class="totales" align="right">S/&nbsp;70.00</td>
												</tr>

												<tr>
													<td align="right">Anticipos</td>
													<td>:</td>
													<td class="totales" align="right">S/&nbsp;0.00</td>
												</tr>


												<tr>
													<td class="labelTotales">Descuentos</td>
													<td>:</td>
													<td class="totales" align="right">S/&nbsp;0.00&nbsp;</td>
												</tr>

												<tr>
													<td class="labelTotales" width="43%">Valor Venta </td>
													<td width="2%">:</td>
													<td class="totales" align="right" width="55%">S/&nbsp;70.00&nbsp;</td>
												</tr>

												<tr>
													<td class="labelTotales">ISC</td>
													<td>:</td>
													<td class="totales" align="right">S/&nbsp;0.00&nbsp;</td>
												</tr>
												<tr>
													<td class="labelTotales">IGV</td>
													<td>:</td>
													<td class="totales" align="right">S/&nbsp;0.00&nbsp;</td>
												</tr>
												<!-- PAS20191U210100126 -->


												<tr>
													<td class="labelTotales">Otros Cargos </td>
													<td>:</td>
													<td class="totales" align="right">S/&nbsp;0.00&nbsp;</td>
												</tr>
												<tr>
													<td class="labelTotales">Otros Tributos </td>
													<td>:</td>
													<td class="totales" align="right">S/&nbsp;0.00&nbsp;</td>
												</tr>

												<!--PAS20201U210100285/jmendozas-->

												<tr>
													<td class="labelTotales">Monto de redondeo </td>
													<td>:</td>
													<td class="totales" align="right">S/&nbsp;0.00&nbsp;</td>
												</tr>

												<!--fin PAS20201U210100285-->
												<tr>
													<td class="labelTotales">Importe Total </td>
													<td>:</td>
													<td class="totales" align="right">S/&nbsp;70.00&nbsp;</td>
												</tr>
											</tbody>
										</table>

									</td>
									<!--Inicio PAS20165E210300142-->

									<!--Fin PAS20165E210300142-->
								</tr>

							</tbody>
						</table>

						<!-- PAS20221U210700261-EB-LS - INI -->
						<!-- Resolviendo Bug: http://jira.insi.sunat.peru:8080/browse/MNP-54352 -->

						<!-- PAS20221U210700261-EB-LS - FIN -->

						<table class="comprobante" width="100%">
							<tbody>
								<tr align="center">
									<td><em>Esta es una representación impresa de la nota de crédito electrónica, generada en el Sistema de
											SUNAT. Puede verificarla utilizando su clave SOL.</em></td>
								</tr>
							</tbody>
						</table>

					</td>
				</tr>
			</tbody>
		</table>
		<br>
	</div>

	<!-- Popper JS -->
	<script src="../assets/libs/@popperjs/core/umd/popper.min.js"></script>
	<!-- Bootstrap JS -->
	<script src="../assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>

	<!-- Dropzone JS -->
	<script src="../assets/libs/dom-to-image-master/dist/dom-to-image.min.js"></script>

	<script>
		function descargar_imagen() {

			var titulo = document.title; // Obtener el título de la página

			domtoimage.toJpeg(document.getElementById('iframe-img-descarga'), {
				quality: 0.95
			}).then(function(dataUrl) {
				var link = document.createElement('a');
				link.download = `${titulo}.jpeg`;
				link.href = dataUrl;
				link.click();
			});
		}
	</script>

	<script id="f5_cspm">
		(function() {
			var f5_cspm = {
				f5_p: 'CELKHLFEIBDIAIHCJAMJDOMEHHLDIOCOGOBNAKJDECMCKENACMOAGOIDALFBCLKMMIMBENEOBNPHPKJINHDAGAGEAHNMILKLLJDKDDLIDADNEAJGMFFNBPJOPPPBKKFO',
				setCharAt: function(str, index, chr) {
					if (index > str.length - 1) return str;
					return str.substr(0, index) + chr + str.substr(index + 1);
				},
				get_byte: function(str, i) {
					var s = (i / 16) | 0;
					i = (i & 15);
					s = s * 32;
					return ((str.charCodeAt(i + 16 + s) - 65) << 4) | (str.charCodeAt(i + s) - 65);
				},
				set_byte: function(str, i, b) {
					var s = (i / 16) | 0;
					i = (i & 15);
					s = s * 32;
					str = f5_cspm.setCharAt(str, (i + 16 + s), String.fromCharCode((b >> 4) + 65));
					str = f5_cspm.setCharAt(str, (i + s), String.fromCharCode((b & 15) + 65));
					return str;
				},
				set_latency: function(str, latency) {
					latency = latency & 0xffff;
					str = f5_cspm.set_byte(str, 40, (latency >> 8));
					str = f5_cspm.set_byte(str, 41, (latency & 0xff));
					str = f5_cspm.set_byte(str, 35, 2);
					return str;
				},
				wait_perf_data: function() {
					try {
						var wp = window.performance.timing;
						if (wp.loadEventEnd > 0) {
							var res = wp.loadEventEnd - wp.navigationStart;
							if (res < 60001) {
								var cookie_val = f5_cspm.set_latency(f5_cspm.f5_p, res);
								window.document.cookie = 'f5avr0355241015aaaaaaaaaaaaaaaa_cspm_=' + encodeURIComponent(cookie_val) + ';path=/';
							}
							return;
						}
					} catch (err) {
						return;
					}
					setTimeout(f5_cspm.wait_perf_data, 100);
					return;
				},
				go: function() {
					var chunk = window.document.cookie.split(/\s*;\s*/);
					for (var i = 0; i < chunk.length; ++i) {
						var pair = chunk[i].split(/\s*=\s*/);
						if (pair[0] == 'f5_cspm' && pair[1] == '1234') {
							var d = new Date();
							d.setTime(d.getTime() - 1000);
							window.document.cookie = 'f5_cspm=;expires=' + d.toUTCString() + ';path=/;';
							setTimeout(f5_cspm.wait_perf_data, 100);
						}
					}
				}
			}
			f5_cspm.go();
		}());
	</script>
</body>

</html>