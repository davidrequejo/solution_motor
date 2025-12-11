<?php
require __DIR__ . '/vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

try {
    $connector = new NetworkPrintConnector("192.168.0.100", 9100); // cambia IP
    $printer = new Printer($connector);

    // ENCABEZADO
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->setEmphasis(true);
    $printer->text("DIAZ CHICKEN RESTAURANT\n");
    $printer->setEmphasis(false);
    $printer->text("RUC: 10471861907\n\n");

    $printer->text("JR. FREDDY ALIAGA CUADRA 2 NRO. S/N\n");
    $printer->text("(COSTADO MINIMARKET TAMBITO), SAN\n");
    $printer->text("MARTIN - TOCACHE - TOCACHE\n\n");

    $printer->text("BOLETA DE VENTA ELECTRONICA\n");
    $printer->setEmphasis(true);
    $printer->text("BX01-00000340\n\n");
    $printer->setEmphasis(false);

    // INFORMACIÓN DE LA VENTA
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("FECHA DE EMISION: 26-07-2025 09:05 PM\n");
    $printer->text("TIPO DE ATENCION: SALON - MESA: M06\n\n");

    $printer->text("CLIENTE: JUNIOR CERCADO VASQUEZ\n");
    $printer->text("DNI: 75867189\n");
    $printer->text("TELEFONO: 0\n");
    $printer->text("DIRECCION: CPM. NUEVO BAMBAMARCA, SAN MARTIN -\n");
    $printer->text("TOCACHE - TOCACHE\n\n");

    // DETALLE DE PRODUCTOS
    $printer->text("CANT. PRODUCTO             P.U. DESC.  TOTAL\n");
    $printer->text("------------------------------------------------\n");

    // PRODUCTOS
    $printer->text("4.00 BRASAS 1/8 COMBINADO 13.00 0.00 52.00\n");
    $printer->text("1.00 REFRESCOS JARRA DE MARACUYA 1L 8.00 0.00 8.00\n");
    $printer->text("------------------------------------------------\n");

    // TOTALES
    $printer->setJustification(Printer::JUSTIFY_RIGHT);
    $printer->text("SUB TOTAL      60.00\n");
    $printer->text("TOTAL S/       60.00\n\n");

    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("SON: SESENTA CON 00/100 Soles\n\n");
    $printer->text("YAPE S/ 60.00\n");
    $printer->text("CONDICIÓN DE PAGO: CONTADO\n");
    $printer->text("------------------------------------------------\n\n");

    // PIE DE PÁGINA
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->text("Representación impresa de la\n");
    $printer->text("BOLETA DE VENTA ELECTRONICA\n");
    $printer->text("consulte en:\n");
    $printer->text("https://diazchicken.brainpos.pe/consulta\n");
    $printer->text("\"Emitido por: www.brainpos.pe\"\n\n");
    $printer->setEmphasis(true);
    $printer->text("GRACIAS POR SU PREFERENCIA\n");
    $printer->setEmphasis(false);

    $printer->cut();
    $printer->close();

} catch (Exception $e) {
    echo "Error al imprimir: " . $e->getMessage();
}
