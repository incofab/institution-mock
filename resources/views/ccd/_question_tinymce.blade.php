<script src="https://cdn.tiny.cloud/1/x5fywb7rhiv5vwkhx145opfx4rsh70ytqkiq2mizrg73qwc2/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<script type="text/javascript">

function initTinymce() {
    	
	tinymce.init({
		selector:'.useEditor',
		valid_elements : '*[*]',
		browser_spellcheck : true,
    plugins: 'image code charmap',

    charmap_append: [
        [0x2600, 'sun'],
        [0x20A6, 'naira'],
        [0x2601, 'cloud']
    ],

    // urlconverter_callback: 'myCustomURLConverter',
    // convert_urls: false, 
    
    // document_base_url: '{{$imagePath}}',

    toolbar: 'tiny_mce_wiris_formulaEditor | tiny_mce_wiris_formulaEditorChemistry | undo redo | link image | code | bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,',
    
    // enable title field in the Image dialog
    image_title: true, 
    
    // enable automatic uploads of images represented by blob or data URIs
    automatic_uploads: true,

    // URL of our upload handler (for more details check: https://www.tinymce.com/docs/configure/file-image-upload/#images_upload_url)
    // images_upload_url: 'postAcceptor.php',
    // here we add custom filepicker only to Image dialog
    file_picker_types: 'image', 

    file_picker_callback: function(cb, value, meta) {
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');

            input.onchange = function() {
              var file = this.files[0];

              var reader = new FileReader();
              reader.onload = function () {
                var base64 = reader.result.split(',')[1];
                const blobInfo = {
                    blobUri: () => reader.result,
                    filename: () => file.name
                 }
                cb(blobInfo.blobUri(), { title: blobInfo.filename()});
              };
              reader.readAsDataURL(file);
            };
            input.click();
        },
        images_upload_handler: function (blobInfo, progress) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onloadend = () => {
                    const base64data = reader.result;
                    resolve(base64data);
                };
                reader.onerror = () => {
                    reject('Image could not be read.');
                };
                reader.readAsDataURL(blobInfo.blob());
                
            });
        }
	});
	
	tinyMCE.triggerSave();
}

$(function() {
  // if(window.handleImages){
  //   handleImages();
  // }
	initTinymce();
});
</script>