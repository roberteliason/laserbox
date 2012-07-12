<?php
//============================================================+
// File name   : example_012.php
// Begin       : 2008-03-04
// Last Update : 2010-08-08
//
// Description : Example 012 for TCPDF class
//               Graphic Functions
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               Manor Coach House, Church Hill
//               Aldershot, Hants, GU12 4RQ
//               UK
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Graphic Functions
 * @author Nicola Asuni
 * @since 2008-03-04
 */

require_once('tcpdf/config/lang/eng.php');
require_once('tcpdf/tcpdf.php');

// ********************
// 	 Useful functions
// ********************

function shift_points(&$points_array, $X_offset, $Y_offset) // shift all points in array by given offset
{

	$array_pointer = 0;
	foreach ($points_array as &$point) {
		if($array_pointer%2) { // odd
			$point += $Y_offset;
		} else { // even
			$point += $X_offset;
		}
		$array_pointer++;
	}

}


// ********************
// 	   Basic params
// ********************

// TODO: add press fit params
// TODO: depth params

$box_height = $_REQUEST['height'];
$box_width = $_REQUEST['width'];
$wall_thickness = $_REQUEST['thickness']; // mm thickness of material

$box_inner_height = $box_height-($wall_thickness*2);
$box_inner_width = $box_width-($wall_thickness*2);

// Figuring out how many teeth per side (even integer), any remainder is divided evenly among the width teeth
$num_teeth_height = intval($box_inner_height/$wall_thickness); 
if ($num_teeth_height%2) {
	$num_teeth_height-- ;
} 
$remainder_height = ($num_teeth_height*$wall_thickness)%$wall_thickness; // the remainder.

$num_teeth_width = intval($box_inner_width/$wall_thickness);
if ($num_teeth_width%2) {
	$num_teeth_width-- ;
} 
$remainder_width = ($num_teeth_width*$wall_thickness)%$wall_thickness;

//print('Teeth(H): '.$num_teeth_height.' Teeth(W): '.$num_teeth_width);

$vertical_tooth_width = ($box_inner_width+$remainder_width)/$num_teeth_width; // width of teeth is the inner dimension + remainder / no teeth
$vertical_tooth_height = $wall_thickness;
$horizontal_tooth_width = ($box_inner_height+$remainder_height)/$num_teeth_height;
$horizontal_tooth_height = $wall_thickness;

$X_offset = 30; // mm offset from top left corner 
$Y_offset = 30;

$X_spacing = 5; // mm x-spacing between shapes
$Y_spacing = 5;

$points_lid = array();
$points_side = array();

$document_x = $X_offset*2 + $box_width*4 + $X_spacing*3; // add four box sides + spacing and right+left page margin
$document_y = $Y_offset*2 + $box_height*3 + $Y_spacing*2; // add three box side + spacing and page margins

// *******************


// create new PDF document
//$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf = new TCPDF('L', 'mm', array($document_x, $document_y),  true, 'UTF-8', false);


// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Robert Eliasson');
$pdf->SetTitle('Box generator public');
$pdf->SetSubject('');
$pdf->SetKeywords('');

// disable header and footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', '', 10);

// add a page
$pdf->AddPage();


// *******************************
//    render points for 
//    the lid and bottom
// *******************************

$current_x = 0+$vertical_tooth_height; // starting X
$current_y = 0; // starting Y


// draw top side
for ($i=0;$i<$num_teeth_width/2;$i++) {

	//point 1 of 4
	$current_y += $vertical_tooth_height;
	$points_lid[] = $current_x;
	$points_lid[] = $current_y;
	
	//point 2 of 4
	$current_x += $vertical_tooth_width;
	$points_lid[] = $current_x;
	$points_lid[] = $current_y;

	//point 3 of 4
	$current_y -= $vertical_tooth_height;
	$points_lid[] = $current_x;
	$points_lid[] = $current_y;

	//point 4 of 4
	$current_x += $vertical_tooth_width;
	$points_lid[] = $current_x;
	$points_lid[] = $current_y;

}

//end point
$current_y += $vertical_tooth_height;
$points_lid[] = $current_x;
$points_lid[] = $current_y;


// draw right side
for ($i=0;$i<$num_teeth_height/2;$i++) {

	//point 1 of 4
	$current_y += $horizontal_tooth_width;
	$points_lid[] = $current_x;
	$points_lid[] = $current_y;
	
	//point 2 of 4
	$current_x += $horizontal_tooth_height;
	$points_lid[] = $current_x;
	$points_lid[] = $current_y;

	//point 3 of 4
	$current_y += $horizontal_tooth_width;
	$points_lid[] = $current_x;
	$points_lid[] = $current_y;

	//point 4 of 4
	$current_x -= $horizontal_tooth_height;
	$points_lid[] = $current_x;
	$points_lid[] = $current_y;

}


// draw bottom side
for ($i=0;$i<$num_teeth_width/2;$i++) {

	//point 1 of 4
	$current_x -= $vertical_tooth_width;
	$points_lid[] = $current_x;
	$points_lid[] = $current_y;
	
	//point 2 of 4
	$current_y += $vertical_tooth_height;
	$points_lid[] = $current_x;
	$points_lid[] = $current_y;

	//point 3 of 4
	$current_x -= $vertical_tooth_width;
	$points_lid[] = $current_x;
	$points_lid[] = $current_y;

	//point 4 of 4
	$current_y -= $vertical_tooth_height;
	$points_lid[] = $current_x;
	$points_lid[] = $current_y;

}


// draw left side
for ($i=0;$i<$num_teeth_height/2;$i++) {

	//point 1 of 4
	$current_y -= $horizontal_tooth_width;
	$points_lid[] = $current_x;
	$points_lid[] = $current_y;
	
	//point 2 of 4
	$current_x -= $horizontal_tooth_height;
	$points_lid[] = $current_x;
	$points_lid[] = $current_y;

	//point 3 of 4
	$current_y -= $horizontal_tooth_width;
	$points_lid[] =$current_x;
	$points_lid[] = $current_y;

	//point 4 of 4
	$current_x += $horizontal_tooth_height;
	$points_lid[] = $current_x;
	$points_lid[] = $current_y;

}


// *******************************
//    render points for 
//    the sides
// *******************************

$current_x = 0;
$current_y = 0;
$points_side[] = $current_x;
$points_side[] = $current_y;

$current_x += $vertical_tooth_width*2;
$points_side[] = $current_x;
$points_side[] = $current_y;

$current_y += $vertical_tooth_height;
$points_side[] = $current_x;
$points_side[] = $current_y;

$current_x += $vertical_tooth_width;
$points_side[] = $current_x;
$points_side[] = $current_y;


// draw top side
for ($i=0;$i<($num_teeth_width/2)-1;$i++) {

	//point 1 of 4
	$current_y -= $vertical_tooth_height;
	$points_side[] = $current_x;
	$points_side[] = $current_y;
	
	//point 2 of 4
	$current_x += $vertical_tooth_width;
	$points_side[] = $current_x;
	$points_side[] = $current_y;

	//point 3 of 4
	$current_y += $vertical_tooth_height;
	$points_side[] = $current_x;
	$points_side[] = $current_y;

	//point 4 of 4
	$current_x += $vertical_tooth_width;
	$points_side[] = $current_x;
	$points_side[] = $current_y;

}

// draw right side
for ($i=0;$i<($num_teeth_height/2)-1;$i++) {

	//point 1 of 4
	$current_y += $horizontal_tooth_width;
	$points_side[] = $current_x;
	$points_side[] = $current_y;
	
	//point 2 of 4
	$current_x += $horizontal_tooth_height;
	$points_side[] = $current_x;
	$points_side[] = $current_y;

	//point 3 of 4
	$current_y += $horizontal_tooth_width;
	$points_side[] = $current_x;
	$points_side[] = $current_y;

	//point 4 of 4
	$current_x -= $horizontal_tooth_height;
	$points_side[] = $current_x;
	$points_side[] = $current_y;

}

$current_y += $horizontal_tooth_width;
$points_side[] = $current_x;
$points_side[] = $current_y;

$current_x += $horizontal_tooth_height;
$points_side[] = $current_x;
$points_side[] = $current_y;

$current_y += $horizontal_tooth_width*2;
$points_side[] = $current_x;
$points_side[] = $current_y;


// draw bottom side
$current_x -= $vertical_tooth_width*2;
$points_side[] = $current_x;
$points_side[] = $current_y;

$current_y -= $vertical_tooth_height;
$points_side[] = $current_x;
$points_side[] = $current_y;

$current_x -= $vertical_tooth_width;
$points_side[] = $current_x;
$points_side[] = $current_y;

for ($i=0;$i<($num_teeth_width/2)-1;$i++) {

	//point 1 of 4
	$current_y += $vertical_tooth_height;
	$points_side[] = $current_x;
	$points_side[] = $current_y;

	//point 2 of 4
	$current_x -= $vertical_tooth_width;
	$points_side[] = $current_x;
	$points_side[] = $current_y;
	
	//point 3 of 4
	$current_y -= $vertical_tooth_height;
	$points_side[] = $current_x;
	$points_side[] = $current_y;

	//point 4 of 4
	$current_x -= $vertical_tooth_width;
	$points_side[] = $current_x;
	$points_side[] = $current_y;

}


// draw left side
for ($i=0;$i<($num_teeth_height/2)-1;$i++) {

	//point 1 of 4
	$current_y -= $horizontal_tooth_width;
	$points_side[] = $current_x;
	$points_side[] = $current_y;
	
	//point 2 of 4
	$current_x -= $horizontal_tooth_height;
	$points_side[] = $current_x;
	$points_side[] = $current_y;

	//point 3 of 4
	$current_y -= $horizontal_tooth_width;
	$points_side[] = $current_x;
	$points_side[] = $current_y;

	//point 4 of 4
	$current_x += $horizontal_tooth_height;
	$points_side[] = $current_x;
	$points_side[] = $current_y;

}

$current_y -= $horizontal_tooth_width;
$points_side[] = $current_x;
$points_side[] = $current_y;

$current_x -= $horizontal_tooth_height;
$points_side[] = $current_x;
$points_side[] = $current_y;

$current_y -= $horizontal_tooth_width;
$points_side[] = $current_x;
$points_side[] = $current_y;



// ***********************************
//     Drawing routines
// ***********************************

function draw_grid() {

	// Draw a grid for testing
	$shape_style = array('color' => array(0,0,0), 'dash' => '');
	$grid_style = array('color' => array(255,0,0), 'dash' => '2,1');
	$inner_style = array('color' => array(0,100,100), 'dash' => '2,1');
	$margin_style = array('color' => array(100,0,100));

	$pdf->SetLineStyle($margin_style);

	//horizontal grid lines
	$grid_y = $Y_offset;
	$grid_x = 0;

	$pdf->line($grid_x,$grid_y,$document_x,$grid_y);

	$pdf->SetLineStyle($grid_style);

	$grid_y += $box_height;
	$pdf->line($grid_x,$grid_y,$document_x,$grid_y);

	$grid_y += $Y_spacing;
	$pdf->line($grid_x,$grid_y,$document_x,$grid_y);

	$grid_y += $box_height;
	$pdf->line($grid_x,$grid_y,$document_x,$grid_y);

	$grid_y += $Y_spacing;
	$pdf->line($grid_x,$grid_y,$document_x,$grid_y);

	$pdf->SetLineStyle($margin_style);

	$grid_y += $box_height;
	$pdf->line($grid_x,$grid_y,$document_x,$grid_y);

	//vertical grid lines
	$grid_y = 0;
	$grid_x = $X_offset;

	$pdf->line($grid_x,$grid_y,$grid_x,$document_y);

	$pdf->SetLineStyle($grid_style);

	$grid_x += $box_width;
	$pdf->line($grid_x,$grid_y,$grid_x,$document_y);

	$grid_x += $X_spacing;
	$pdf->line($grid_x,$grid_y,$grid_x,$document_y);

	$grid_x += $box_width;
	$pdf->line($grid_x,$grid_y,$grid_x,$document_y);

	$grid_x += $X_spacing;
	$pdf->line($grid_x,$grid_y,$grid_x,$document_y);

	$grid_x += $box_width;
	$pdf->line($grid_x,$grid_y,$grid_x,$document_y);

	$grid_x += $X_spacing;
	$pdf->line($grid_x,$grid_y,$grid_x,$document_y);

	$pdf->SetLineStyle($margin_style);

	$grid_x += $box_width;
	$pdf->line($grid_x,$grid_y,$grid_x,$document_y);

	//box grid lines
	$pdf->SetLineStyle($inner_style);

	$grid_y = $Y_offset+$wall_thickness;
	$grid_x = $X_offset+$box_width+$X_spacing+$wall_thickness;

	$pdf->rect($grid_x, $grid_y, $box_inner_width, $box_inner_height);

	$grid_y += ($box_height + $Y_spacing)*2;

	$pdf->rect($grid_x, $grid_y, $box_inner_width, $box_inner_height);

	$grid_y = $Y_offset+$wall_thickness+$Y_spacing+$box_height;
	$grid_x = $X_offset+$wall_thickness;

	$pdf->rect($grid_x, $grid_y, $box_inner_width, $box_inner_height);

	$grid_x += $box_width + $X_spacing;

	$pdf->rect($grid_x, $grid_y, $box_inner_width, $box_inner_height);

	$grid_x += $box_width + $X_spacing;

	$pdf->rect($grid_x, $grid_y, $box_inner_width, $box_inner_height);

	$grid_x += $box_width + $X_spacing;

	$pdf->rect($grid_x, $grid_y, $box_inner_width, $box_inner_height);

}


// draw top and bottom
$pdf->SetLineStyle($shape_style);

shift_points($points_lid, $box_width+$X_spacing+$X_offset, $Y_offset);
$pdf->Polygon($points_lid, '', $shape_style, array(), true); // true denotes a closed polygon, very important

shift_points($points_lid, 0, ($box_height+$Y_spacing)*2);
$pdf->Polygon($points_lid, '', $shape_style, array(), true);


// draw four sides
shift_points($points_side, $X_offset, $box_height+$Y_spacing+$Y_offset);
$pdf->Polygon($points_side, '', $shape_style, array(), true);

shift_points($points_side, $box_width+$X_spacing, 0);
$pdf->Polygon($points_side, '', $shape_style, array(), true);

shift_points($points_side, $box_width+$X_spacing, 0);
$pdf->Polygon($points_side, '', $shape_style, array(), true);

shift_points($points_side, $box_width+$X_spacing, 0);
$pdf->Polygon($points_side, '', $shape_style, array(), true);

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('box_generator.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
