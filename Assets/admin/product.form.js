$(function(){
    $("#categories").select2();

    /**
	 * Creates row in the image table
	 * 
	 * @param string img blob image data
	 * @return void
	 */
	function createRow(img){
		// Temporary solution
		var content = 
		'<tr>' + 
			'<td><img data-image="preview" src="' + img + '"'  + '></td>' + 
			'<td></td>' + 
			'<td></td>' +
			'<td></td>' + 
			'<td>' +
				'<a data-button="delete" href="#"><i class="glyphicon glyphicon-remove"></i></a>' +
			'</td>' +
		'</tr>';

		return content;
	}

	/**
	 * Create a new file input element
	 * 
	 * @return DOMElement
	 */
	function createFileElement(){
		var input = document.createElement('input');

		$(input).attr({
			type : "file",
			name : "file[]",
			accept : 'image/x-png, image/gif, image/jpeg'
		});

		// Attach the click listener now to the created file element
		$(input).click(function(){
			$(this).preview(function(data){
				$("[data-container='image']").append(createRow(data));
			});
		});

		return input;
	}
	
	$('[data-button="upload"]').click(function(event){
		event.preventDefault();
		
		var input = createFileElement();
		
		// Now append prepared DOM element into our container
		$("#file-input-container").append(input);
		
		input.click();
	});
	
	$("[data-button='edit']").click(function(event){
		event.preventDefault();
		
		var id = $(this).data('image');
		var $file = $(this).parent().find("input[type='file']");
		var $img = $(this).parent().parent().find("td img");
		
		$file.preview(function(imgData){
			$img.attr({
				'data-image' : 'preview',
				'src' : imgData
			});
		});
		
		$file.click();
	});
	
	$(document).on('click', "[data-button='delete']", function(event){
		event.preventDefault();
		var $row = $(this).parent().parent();
		
		// Remove last input from container
		$("#file-input-container > input:last-child").remove();
		
		$row.empty();
	});
});