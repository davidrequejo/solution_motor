<?php 

class FechaEs {
    private $objFecha;
    private $M_es = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
    private $M_en = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
    private $D_es = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
    private $D_en = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");


    public function __construct($mFecha)
    {
        $this->objFecha=DateTime::createFromFormat('Y-m-d', $mFecha);
    }

    public function getDDDD()
    {
        $nombreDia=$this->objFecha->format('l');
        return str_replace($this->D_en,$this->D_es,$nombreDia);    
    }       

    public function getMM()
    {
        return $this->objFecha->format('m');    
    }

    public function getMMMM(){
        $nombreMes=$this->objFecha->format('F');
        return str_replace($this->M_en,$this->M_es,$nombreMes);    
    }

    public function getYYYY()
    {
        return $this->objFecha->format('Y');    
    }

    public function getYY()
    {
        return $this->objFecha->format('y');    
    }

    public function getdd()
    {
        return $this->objFecha->format('d');    
    }

    // public function getHora12()
    // {
    //     return $this->objFecha->format('g:i a');    
    // }

}