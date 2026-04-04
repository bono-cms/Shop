$(function(){
    // Use the specific form as the root context
    var $form = $('.img-upload-form');
    
    /**
     * Creates row in the image table
     * 
     * @param string img blob image data
     * @return void
     */
    function createRow(img){
        // Temporary solution
        return `
            <tr>
                <td><img data-image="preview" src="${img}"></td>
                <td></td>
                <td></td>
                <td></td>
                <td>
                    <a data-button="delete" href="#">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </td>
            </tr>`;
    }

    /**
     * Create a new file input element
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
                // Scope the container search to this form
                $form.find("[data-container='image']").append(createRow(data));
            });
        });

        return input;
    }
    
    // Scope the upload button listener
    $form.find('[data-button="upload"]').click(function(event){
        event.preventDefault();
        
        var input = createFileElement();
        
        // Scope the file input container search to this form
        $form.find("#file-input-container").append(input);
        
        input.click();
    });
    
    // Scope the edit button listener
    $form.find("[data-button='edit']").click(function(event){
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
    
    // Scope the delete logic to the form container
    $form.on('click', "[data-button='delete']", function(event){
        event.preventDefault();
        var $row = $(this).parent().parent();
        
        // Scope the removal to the input container within this form
        $form.find("#file-input-container > input:last-child").remove();
        
        $row.empty();
    });
});