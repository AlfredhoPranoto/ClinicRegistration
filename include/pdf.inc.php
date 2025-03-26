<?php
//include connection file
session_start();
include_once("dbh.inc.php");
include_once('libs/fpdf.php');

$query = $_SESSION['filter'];

$cellWidth = 30;
$cellHeight = 5;
$line = 1;

$pdf = new FPDF('L','mm','A3');
$pdf->AddPage('L');
$pdf->SetFont("Arial","B",10);
$pdf->SetFillColor(193,229,252);

$pdf->Cell(15,10,'Time',1,0,'C',true);
$pdf->Cell(30,10,'Company',1,0,'C',true);
$pdf->Cell(35,10,'Department',1,0,'C',true);
$pdf->Cell(35,10,'Pasient Name',1,0,'C',true);
$pdf->Cell(22,10,'Birth Date',1,0,'C',true);
$pdf->Cell(22,10,'Treatment Date',1,0,'C',true);
$pdf->Cell(35,10,'Diagnosis',1,0,'C',true);
$pdf->Cell(45,10,'Medicine',1,0,'C',true);
$pdf->Cell(40,10,'Action',1,0,'C',true);
$pdf->Cell(30,10,'Description',1,0,'C',true);
$pdf->Cell(22,10,'Doctor',1,0,'C',true);
$pdf->Cell(45,10,'Complaints',1,1,'C',true);

$pdf->SetFont("Arial","",10);
$font = 10;
$tempFont = $font;
$pdf->SetFillColor(235,236,236);
$fill = false;
foreach($conn->query($query) as $row){
    $pdf->Cell(15,6,$row['waktu'],1,0,'C',$fill);

    // fitting data perusahaan
    while($pdf->GetStringWidth($row['perusahaan']) > 29){
        $pdf->SetFontSize($tempFont -= 0.1);
    }
    $pdf->Cell(30,6,$row['perusahaan'],1,0,'C',$fill);
    $tempFont = $font;
    $pdf->SetFontSize($font);
    // fitting data departemen
    while($pdf->GetStringWidth($row['dept']) > 34){
        $pdf->SetFontSize($tempFont -= 0.1);
    }
    $pdf->Cell(35,6,$row['dept'],1,0,'C',$fill);
    $tempFont = $font;
    $pdf->SetFontSize($font);
    // fitting data nama
    while($pdf->GetStringWidth($row['nama']) > 34){
        $pdf->SetFontSize($tempFont -= 0.1);
    }
    $pdf->Cell(35,6,$row['nama'],1,0,'C',$fill);
    $tempFont = $font;
    $pdf->SetFontSize($font);

    $pdf->Cell(22,6,$row['lahir'],1,0,'C',$fill);
    $pdf->Cell(22,6,$row['berobat'],1,0,'C',$fill);

    while($pdf->GetStringWidth($row['diagnosa']) > 34){
        $pdf->SetFontSize($tempFont -= 0.1);
    }
    $pdf->Cell(35,6,$row['diagnosa'],1,0,'L',$fill);
    $tempFont = $font;
    $pdf->SetFontSize($font);

    while($pdf->GetStringWidth($row['obat']) > 44){
        $pdf->SetFontSize($tempFont -= 0.1);
    }
    $pdf->Cell(45,6,$row['obat'],1,0,'L',$fill);
    $tempFont = $font;
    $pdf->SetFontSize($font);

    while($pdf->GetStringWidth($row['tindak']) > 39){
        $pdf->SetFontSize($tempFont -= 0.1);
    }
    $pdf->Cell(40,6,$row['tindak'],1,0,'L',$fill);
    $tempFont = $font;
    $pdf->SetFontSize($font);

    while($pdf->GetStringWidth($row['keterangan']) > 29){
        $pdf->SetFontSize($tempFont -= 0.1);
    }
    $pdf->Cell(30,6,$row['keterangan'],1,0,'L',$fill);
    $tempFont = $font;
    $pdf->SetFontSize($font);

    $pdf->Cell(22,6,$row['dokter'],1,0,'L',$fill);

    while($pdf->GetStringWidth($row['keluhan']) > 44){
        $pdf->SetFontSize($tempFont -= 0.1);
    }
    $pdf->Cell(45,6,$row['keluhan'],1,1,"L",$fill);
    $tempFont = $font;
    $pdf->SetFontSize($font);

    $fill = !$fill;
}
$pdf->Output();