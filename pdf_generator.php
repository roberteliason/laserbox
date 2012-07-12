<?php

require_once('tcpdf/config/lang/eng.php');
require_once('tcpdf/tcpdf.php');

// ********************
// 	 Useful functions
// ********************

function calculate_teeth_per_side($side_length, $wall_thickness) 
{

	// the basic width of a tooth is the same as the thickness of the material
	// if this is not evenly divisible, we will adjust for this later

	$num_teeth = intval($side_length/$wall_thickness); // how many teeth per side, no fractions of teeth
	if ($num_teeth%2) { // we need an even number of teeth per side for this to work
		$num_teeth-- ;
	} 

	return $num_teeth;

}

function calculate_remainder($num_teeth, $wall_thickness)
{

	// if the number of teeth aren't evenly divisible and/or we ended up 
	// with and odd number of teeth we need to know the remaining length
	// that's left so that we can divide this among the teeth widths

	return ($num_teeth*$wall_thickness)%$wall_thickness; // the remainder.

}

function calculate_tooth_width($inner_width, $remainder, $num_teeth, $press_fit) {

	// we are interested in the inner width of the box, that is the 
	// width of the material is subtracted since it is a constant
	// next we add the remainder to that and divide by the number 
	// of teeth on the given side. Now we have distributed the 
	// remainder nicely over the teeth on this side
	// if a press fit margin is added, add that as well

	return (($inner_width+$remainder)/$num_teeth)+$press_fit;

}

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

function turtle_x(&$points_array, &$current_x, $current_y, $x_factor, $direction) 
// emulate turtle draw, move the the turtle on the X-axis, update the x-pointer and add points for x & y to supplied points array
// $direction takes '+' or '-', x-factor is the distance
{

	if (strcmp($direction,'+')==0) { 
		$current_x += $x_factor;
	} else {
		$current_x -= $x_factor;
	}
	$points_array[] = $current_x;
	$points_array[] = $current_y;

}

function turtle_y(&$points_array, $current_x, &$current_y, $y_factor, $direction) 
// emulate turtle draw, move the the turtle on the Y-axis, update the y-pointer and add points for x & y to supplied points array
// $direction takes '+' or '-', y-factor is the distance
{

	if (strcmp($direction,'+')==0) { 
		$current_y += $y_factor;
	} else {
		$current_y -= $y_factor;
	}
	$points_array[] = $current_x;
	$points_array[] = $current_y;

}

function render_lid($height, $width, $wall_thickness, $press_fit) {

	// *******************************
	//    render points for 
	//    the lid and bottom
	// *******************************

	// Figuring out how many teeth per side (even integer), any remainder is divided evenly among the width teeth
	// width of teeth is the inner dimension + remainder / teeth count
	$tooth_count_x 	= calculate_teeth_per_side($height, $wall_thickness);
	$remainder_x 	= calculate_remainder($tooth_count_x, $wall_thickness); // the remainder.
	$tooth_width_x 	= calculate_tooth_width($height,$remainder_x,$tooth_count_x, $press_fit);
	$tooth_height_x = $wall_thickness;

	$tooth_count_y 	= calculate_teeth_per_side($width, $wall_thickness);
	$remainder_y 	= calculate_remainder($tooth_count_y, $wall_thickness); // the remainder.
	$tooth_width_y 	= calculate_tooth_width($width,$remainder_y,$tooth_count_y, $press_fit);
	$tooth_height_y = $wall_thickness;

	$current_x 		= $tooth_height_x; 				// starting X
	$current_y 		= 0; 							// starting Y
	$points 		= array();


	// *******************************
	//     start drawing
	// *******************************


	// draw top side
	for ($i=0;$i<$tooth_count_x/2;$i++) {

		turtle_y($points,$current_x, $current_y, $tooth_height_x, '+');
		turtle_x($points,$current_x, $current_y, $tooth_width_x, '+');
		turtle_y($points,$current_x, $current_y, $tooth_height_x, '-');
		turtle_x($points,$current_x, $current_y, $tooth_width_x, '+');

	}

	//final point on side
	turtle_y($points,$current_x, $current_y, $tooth_height_x, '+');

	// draw right side
	for ($i=0;$i<$tooth_count_y/2;$i++) {

		turtle_y($points,$current_x, $current_y, $tooth_width_y, '+');
		turtle_x($points,$current_x, $current_y, $tooth_height_y, '+');
		turtle_y($points,$current_x, $current_y, $tooth_width_y, '+');
		turtle_x($points,$current_x, $current_y, $tooth_height_y, '-');

	}


	// draw bottom side
	for ($i=0;$i<$tooth_count_x/2;$i++) {

		turtle_x($points,$current_x, $current_y, $tooth_width_x, '-');
		turtle_y($points,$current_x, $current_y, $tooth_height_x, '+');
		turtle_x($points,$current_x, $current_y, $tooth_width_x, '-');
		turtle_y($points,$current_x, $current_y, $tooth_height_x, '-');

	}


	// draw left side
	for ($i=0;$i<$tooth_count_y/2;$i++) {

		turtle_y($points,$current_x, $current_y, $tooth_width_y, '-');
		turtle_x($points,$current_x, $current_y, $tooth_height_y, '-');
		turtle_y($points,$current_x, $current_y, $tooth_width_y, '-');
		turtle_x($points,$current_x, $current_y, $tooth_height_y, '+');

	}

	return $points;

}



function render_side($height, $width, $wall_thickness, $press_fit) {

	// *******************************
	//    render points for the 
	//    sides of the box
	// *******************************

	$current_x = 0;
	$current_y = 0;
	$points = array($current_x, $current_y);

	// Figuring out how many teeth per side (even integer), any remainder is divided evenly among the width teeth
	// width of teeth is the inner dimension + remainder / teeth count
	$tooth_count_x 	= calculate_teeth_per_side($height, $wall_thickness);
	$remainder_x 	= calculate_remainder($tooth_count_x, $wall_thickness); // the remainder.
	$tooth_width_x 	= calculate_tooth_width($height,$remainder_x,$tooth_count_x, $press_fit);
	$tooth_height_x = $wall_thickness;

	$tooth_count_y 	= calculate_teeth_per_side($width, $wall_thickness);
	$remainder_y 	= calculate_remainder($tooth_count_y, $wall_thickness); // the remainder.
	$tooth_width_y 	= calculate_tooth_width($width,$remainder_y,$tooth_count_y, $press_fit);
	$tooth_height_y = $wall_thickness;


	// *******************************
	//     start drawing
	// *******************************

	// the start of the top & bottom and the end of the sides have a wider tooth which covers the corners
	turtle_x($points,$current_x, $current_y, $tooth_width_x*2, '+');
	turtle_y($points,$current_x, $current_y, $tooth_height_x, '+');
	turtle_x($points,$current_x, $current_y, $tooth_width_x, '+');


	// draw top side
	for ($i=0;$i<($tooth_count_x/2)-1;$i++) {

		turtle_y($points,$current_x, $current_y, $tooth_height_x, '-');
		turtle_x($points,$current_x, $current_y, $tooth_width_x, '+');
		turtle_y($points,$current_x, $current_y, $tooth_height_x, '+');
		turtle_x($points,$current_x, $current_y, $tooth_width_x, '+');

	}

	// draw right side
	for ($i=0;$i<($tooth_count_y/2)-1;$i++) {

		turtle_y($points,$current_x, $current_y, $tooth_width_y, '+');
		turtle_x($points,$current_x, $current_y, $tooth_height_y, '+');
		turtle_y($points,$current_x, $current_y, $tooth_width_y, '+');
		turtle_x($points,$current_x, $current_y, $tooth_height_y, '-');

	}

	turtle_y($points,$current_x, $current_y, $tooth_width_y, '+');
	turtle_x($points,$current_x, $current_y, $tooth_height_y, '+');
	turtle_y($points,$current_x, $current_y, $tooth_width_y*2, '+');


	// draw bottom side
	turtle_x($points,$current_x, $current_y, $tooth_width_x*2, '-');
	turtle_y($points,$current_x, $current_y, $tooth_height_x, '-');
	turtle_x($points,$current_x, $current_y, $tooth_width_x, '-');

	for ($i=0;$i<($tooth_count_x/2)-1;$i++) {

		turtle_y($points,$current_x, $current_y, $tooth_height_x, '+');
		turtle_x($points,$current_x, $current_y, $tooth_width_x, '-');
		turtle_y($points,$current_x, $current_y, $tooth_height_x, '-');
		turtle_x($points,$current_x, $current_y, $tooth_width_x, '-');

	}

	// draw left side
	for ($i=0;$i<($tooth_count_y/2)-1;$i++) {

		turtle_y($points,$current_x, $current_y, $tooth_width_y, '-');
		turtle_x($points,$current_x, $current_y, $tooth_height_y, '-');
		turtle_y($points,$current_x, $current_y, $tooth_width_y, '-');
		turtle_x($points,$current_x, $current_y, $tooth_height_y, '+');

	}

	turtle_y($points,$current_x, $current_y, $tooth_width_y, '-');
	turtle_x($points,$current_x, $current_y, $tooth_height_y, '-');
	turtle_y($points,$current_x, $current_y, $tooth_width_y, '-');

	return $points;

}


// ********************
// 	   Basic params
// ********************

// TODO: add press fit params (Beta 1) requires testing, effect to slight to be observed by the naked eye ;)
/* Press fit: compensate for the amount of material that the laser burns away when cutting the shapes out. */
/* Adding a slight amount of width to the teeth should be sufficient to makes the pieces fit without glue. */
/* Assuming the added margin should be something like half the width of the laser beam. Needs comfirmation. */

$box_width = $_REQUEST['width'];   			// X
$box_height = $_REQUEST['height']; 			// Y
$box_depth = $_REQUEST['depth'];   			// Z
$wall_thickness = $_REQUEST['thickness']; 	// mm thickness of material
$press_fit = $_REQUEST['press_fit']; 		// adjusting for material removed by laser to achieve press fit

$box_inner_height = $box_height-($wall_thickness*2);
$box_inner_width = $box_width-($wall_thickness*2);
$box_inner_depth = $box_depth-($wall_thickness*2);

$X_offset = 30; // mm offset from top left corner 
$Y_offset = 30;

$X_spacing = 5; // mm x-spacing between shapes
$Y_spacing = 5;

$points_lid = render_lid($box_inner_width, $box_inner_depth, $wall_thickness, $press_fit);
$points_side = render_side($box_inner_depth, $box_inner_height, $wall_thickness, $press_fit);
$points_front = render_side($box_inner_width, $box_inner_height, $wall_thickness, $press_fit);

$document_x = $X_offset*2 + $box_width*2 + $box_depth + $X_spacing*3; 
if ($box_height*2 < $box_height+$box_depth) {
	$document_y = $Y_offset*2 + $box_height + $box_depth + $Y_spacing; 
} else {
	$document_y = $Y_offset*2 + $box_height*2 + $Y_spacing; 
}
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



// ***********************************
//     Drawing routines
// ***********************************


// draw top and bottom
$pdf->SetLineStyle($shape_style);

shift_points($points_lid, $X_offset, $Y_offset);
$pdf->Polygon($points_lid, '', $shape_style, array(), true); // true denotes a closed polygon, very important
shift_points($points_lid, $box_width+$X_spacing, 0);
$pdf->Polygon($points_lid, '', $shape_style, array(), true);

// draw front and back
shift_points($points_front, $X_offset, $Y_offset+$box_depth+$Y_spacing);
$pdf->Polygon($points_front, '', $shape_style, array(), true);
shift_points($points_front, $box_width+$X_spacing, 0);
$pdf->Polygon($points_front, '', $shape_style, array(), true);

// draw two sides
shift_points($points_side, $X_offset+($box_width+$X_spacing)*2, $Y_offset);
$pdf->Polygon($points_side, '', $shape_style, array(), true);

shift_points($points_side, 0, $box_height+$Y_spacing);
$pdf->Polygon($points_side, '', $shape_style, array(), true);

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('tmp/box_generator.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
