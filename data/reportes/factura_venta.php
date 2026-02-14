<?php
    include_once('../../fpdf/rotation.php');
    include_once('../../admin/class.php');
    include_once('../../admin/convertir.php');
    include_once('../../admin/funciones_generales.php');
    date_default_timezone_set('America/lima');
    setlocale (LC_TIME,"spanish");
    session_start();
    $fecha = date('Y-m-d H:i:s', time());  
    $class = new constante();
    $letras = new EnLetras();

class PDF extends PDF_Rotate {
    var $widths;
    var $aligns;

    function SetWidths($w) {
        //Set the array of column widths
        $this->widths=$w;
    }
    function Header() {                         
        $this->AddFont('Amble-Regular','','Amble-Regular.php');
        $this->SetFont('Amble-Regular','',10);        
        $fecha = date('Y-m-d', time());
        $this->SetX(1);
        $this->SetY(1);
        $this->Cell(20, 5, $fecha, 0,0, 'C', 0);                         
        $this->Cell(150, 5, "FACTURAS", 0,1, 'R', 0);      
        $this->SetFont('Arial','B',16);                                                    
        $this->Cell(190, 8, "EMPRESA: ".$_SESSION['empresa']['empresa'], 0,1, 'C',0);                                
        $this->Image('logo_empresa.jpg',1,8,40,30);
        $this->SetFont('Amble-Regular','',10);        
        $this->Cell(180, 5, "PROPIETARIO: ".utf8_decode($_SESSION['empresa']['propietario']),0,1, 'C',0);                                
        $this->Cell(70, 5, "TEL.: ".utf8_decode($_SESSION['empresa']['telefono1']),0,0, 'R',0);                                
        $this->Cell(60, 5, "CEL.: ".utf8_decode($_SESSION['empresa']['telefono2']),0,1, 'C',0);                                
        $this->Cell(170, 5, "DIR.: ".utf8_decode($_SESSION['empresa']['direccion']),0,1, 'C',0);                                
        $this->Cell(170, 5, "SLOGAN.: ".utf8_decode($_SESSION['empresa']['slogan']),0,1, 'C',0);                                
        $this->Cell(170, 5, utf8_decode( $_SESSION['empresa']['ciudad']),0,1, 'C',0);                                                                                                    
        $this->SetDrawColor(0,0,0);
        $this->SetLineWidth(0.4);            
        $this->Line(1,45,210,45);            
        $this->SetFont('Arial','B',12);                                                                            
        $this->Cell(190, 5, utf8_decode("FACTURA"),0,1, 'C',0);                                                                                                                            
        $this->SetFont('Amble-Regular','',10);        
        $this->Ln(3);
        $this->SetFillColor(255,255,225);            
        $this->SetLineWidth(0.2);                                        
    }

    function Footer(){            
        $this->SetY(-15);            
        $this->SetFont('Arial','I',8);            
        $this->Cell(0,10,'Pag. '.$this->PageNo().'/{nb}',0,0,'C');
    }               

    function SetAligns($a) {
        //Set the array of column alignments
        $this->aligns=$a;
    }
    
}



$pdf = new PDF('P','mm','a4');
    $pdf->AddPage();
    $pdf->SetMargins(0,0,0,0);
    $pdf->SetFont('Arial','',10);	   
    $resultado = $class->consulta("SELECT C.nombres_completos, C.identificacion, C.direccion, C.telefono2, C.ciudad, F.fecha_actual, F.forma_pago, F.tarifa0, F.tarifa, F.iva_venta, F.descuento_venta, F.total_venta, F.estado, F.id, C.correo, C.telefono1 from factura_venta F, clientes C WHERE F.id_cliente = C.id AND F.id = '$_GET[id]'");    
    while ($row = $class->fetch_array($resultado)) { 
    

        $pdf->SetX(1);
        $pdf->Cell(20, 6, utf8_decode('Cliente: '),0,0, 'L',0);    
        $pdf->Cell(85, 6, maxCaracter(utf8_decode($row[0]),40),0,0, 'L',0);                                                                      
        $pdf->Cell(15, 6, utf8_decode('CI/RUC: '),0,0, 'L',0);                                     
        $pdf->Cell(35, 6, utf8_decode($row[1]),0,0, 'L',0);                                     
        $pdf->Cell(25, 6, utf8_decode('N°. Pedido: '),0,0, 'L',0);  
        $pdf->Cell(30, 6, utf8_decode($row[13]),0,1, 'L',0);                                                                                                        
        $pdf->Ln(1);
        $pdf->SetX(1);
        $pdf->Cell(20, 6, utf8_decode('Dirección:'),0,0, 'L',0);                                     
        $pdf->Cell(60, 6, maxCaracter(utf8_decode($row[2]),30),0,0, 'L',0);                                             
        $pdf->Cell(20, 6, utf8_decode('Email:'),0,0, 'L',0);                                     
        $pdf->Cell(60, 6, maxCaracter(utf8_decode($row[14]),30),0,0, 'L',0);  
        $pdf->Cell(20, 6, utf8_decode('Celular:'),0,0, 'L',0);    
        $pdf->Cell(25, 6, utf8_decode($row[15]),0,1, 'L',0);                                                                             
        $pdf->Ln(1);
        $pdf->SetX(1);  
    }

    $pdf->SetFont('Arial','',9);	   
    $pdf->SetWidths(array(10, 120, 25, 35,30));//TAMAÑOS DE LA TABLA DE DETALLES PRODUCTOS
    $pdf->SetFillColor(85, 107, 47);
    $resultado = $class->consulta("SELECT D.cantidad, P.codigo, P.descripcion, D.precio, D.total, D.descuento, F.tarifa0, F.tarifa, F.iva_venta, F.descuento_venta, F.total_venta FROM factura_venta F, detalle_factura_venta D, productos P WHERE F.id = D.id_factura_venta AND D.id_producto = P.id AND D.id_factura_venta = '$_GET[id]'");

       

    $pdf->SetX(1);
    $pdf->Cell(40, 6, utf8_decode('Código'),1,0, 'C',0);                                     
    $pdf->Cell(65, 6, utf8_decode('Producto'),1,0, 'C',0);                                     
    $pdf->Cell(25, 6, utf8_decode('Cantidad'),1,0, 'C',0);                                                             
    $pdf->Cell(25, 6, utf8_decode('PVP'),1,0, 'C',0);                                     
    $pdf->Cell(25, 6, utf8_decode('Descuento'),1,0, 'C',0);                                         
    $pdf->Cell(25, 6, utf8_decode('Total'),1,1, 'C',0); 

    while ($row = $class->fetch_array($resultado)) {  
        
    $temp1=$row[6]; //TARIFA  0 %
    $temp2=$row[7];
    $temp3=$row[8];
    $temp4=$row[9];
    $temp5=$row[10];    
        
        $pdf->SetX(1);
        $pdf->Cell(40, 6, maxCaracter(utf8_decode($row[1]),15),0,0, 'L',0);                                     
        $pdf->Cell(65, 6, maxCaracter(utf8_decode($row[2]),30),0,0, 'L',0);                                     
        $pdf->Cell(25, 6, utf8_decode($row[0]),0,0, 'C',0);                                     
        $pdf->Cell(25, 6, utf8_decode($row[3]),0,0, 'C',0);                                     
        $pdf->Cell(25, 6, utf8_decode($row[5]),0,0, 'C',0);                                     
        $pdf->Cell(25, 6, utf8_decode($row[4]),0,1, 'C',0);                                            
    }


    $pdf->SetX(1);   
    $pdf->Ln(5);
    $pdf->Cell(207, 0, utf8_decode(""),1,1, 'R',0);
    $pdf->Cell(181, 6, utf8_decode("Tarifa 0%"),0,0, 'R',0);
    $pdf->Cell(25, 6, maxCaracter((number_format($temp1,2,',','.')),20),0,1, 'C',0);                                                    
    $pdf->SetX(1);       
    $pdf->Cell(181, 6, utf8_decode("Tarifa IVA"),0,0, 'R',0);
    $pdf->Cell(25, 6, maxCaracter((number_format($temp2,2,',','.')),20),0,1, 'C',0);                                                    
    $pdf->SetX(1);       
    $pdf->Cell(181, 6, utf8_decode("Iva 18%"),0,0, 'R',0);
    $pdf->Cell(25, 6, maxCaracter((number_format($temp3,2,',','.')),20),0,1, 'C',0);    
    $pdf->SetX(1);                                                       
    $pdf->Cell(181, 6, utf8_decode("Descuento"),0,0, 'R',0);
    $pdf->Cell(25, 6, maxCaracter((number_format($temp4,2,',','.')),20),0,1, 'C',0);                                                    
    $pdf->SetX(1);       
    $pdf->Cell(181, 6, utf8_decode("Totales"),0,0, 'R',0);
    $pdf->Cell(25, 6, maxCaracter((number_format($temp5,2,',','.')),20),0,1, 'C',0);                                                    
    
    $pdf->Ln(3);              
    $pdf->Output();

?>