<html>
<head>
	<title>3D Box Generator for 2D laser cutters</title>
	<link rel="stylesheet" type="text/css" href="css/forms.css" />
</head>
</body>

<form action="pdf_generator.php" target="_blank">
	<h3>Box Generator</h3>
	<fieldset>
		<div>
			<label for="width">Width (mm)</label>
			<input type="text" name="width" id="width" value="" size="6" maxlength="5">
		</div>
		<div>
			<label for="height">Height (mm)</label>
			<input type="text" name="height" id="height" value="" size="6" maxlength="5">
		</div>
		<div>
			<label for="depth">Depth (mm)</label>
			<input type="text" name="depth" id="depth" value="" size="6" maxlength="5">
		</div>
		<div>
			<label for="thickness">Material thickness (mm)</label>
			<input type="text" name="thickness" id="thickness" value="" size="5" maxlength="4">
		</div>
	</fieldset>
	<input type="submit" value="Generate" id="generate"> 
</form>

<div id="info">
	<h3>Beta 2.something</h3>
	<p>This hack was hacked during Hackathon at STPLN 2012.<br/>
	There are NO copyrights on this code.<br/>
	</p><p>If you want it, mail me at robert(at)pardalis.se<br/>
	If you have comments, you know what to do ;)<p>
	<p>Suggestions are welcome!</p>
	<p><b>TODO:</b><br>
	Add ability to add a press fit margin for no glue assembly.<br/>
	Fix the orientation of the shapes so that the surface on top of the material becomes the outside of the box.<br/>
	Decimal values must be tested.
	</p>
</div>

</body>
</html>